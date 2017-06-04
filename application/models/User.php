<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: User
**/
class User extends CI_Model {
  private $userid;
  private $realUserId;
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }
  public function isAdmin()
  {
    $user = $this->get();
    if(empty($user)){
      return false;
    }else{
      return $user['type'] == 'admin';
    }
  }
  public function isSignIn()
  {
    return !empty($this->getId());
  }
  /**
  * Warning form_validation is required before call this method
  * @see Api/UserCtrl/createUser
  * create user by username and password
  * @method create
  * @param username,password (plain-text!)
  **/
  /*
  public function create($username,$password)
  {
    $this->load->library('phpass');
    $this->load->config('subth');
    $hash = $this->phpass->hash($password);
    $this->db->insert('user',array(
      'username' => $username,
      'password' => $hash,
      'type' => 'user',
      'shorten_quota' => $this->config->item('shorten_quota')
    ));
    return intval($this->db->insert_id());
  }*/
  public function getUniqueInvite()
  {
    $this->load->helper('thaistring');
    while(true){
      $token = random_thai_string('car');
      $cnt = $this->db
          ->where('invite_token',$token)
          ->count_all_results('user');
      if($cnt == 0){
        break;
      }
    }
    return $token;
  }
  public function generateInviteToken($userId)
  {
    $token = $this->getUniqueInvite();
    $this->db
      ->where('id',$userId)
      ->update('user',array(
        'invite_token' => $token
      ));
    return $token;
  }
  public function removeInviteToken($uid)
  {
    $this->db
      ->where('id',$uid)
      ->update('user',array(
        'invite_token' => ''
      ));
  }
  public function inviteTokenForNewUser($note = '')
  {
    $this->load->config('subth');
    $token = $this->getUniqueInvite();
    $data = array(
      'type' => 'user',
      'invite_token' => $token,
      'shorten_quota' => $this->config->item('shorten_quota')
    );
    if(!empty($note)){
      $data['note'] = $note;
    }
    $this->db->insert('user',$data);
    $uid = intval($this->db->insert_id());
    return array(
      'id' => $uid,
      'invite_token' => $token
    );
  }
  public function setPasswordInvite($invite_token,$password,$username)
  {

    $this->load->library('phpass');
    $hash = $this->phpass->hash($password);
    $data = array(
      'invite_token' => '',
      'password' => $hash
    );
    if(!empty($username)){
      $data['username'] = $username;
    }
    $this->db
      ->where('invite_token',$invite_token)
      ->update('user',$data);
    return empty($this->db->affected_rows());
  }
  public function getByInviteToken($invite_token){
    $query = $this->db
      ->select('id,username')
      ->from('user')
      ->where('invite_token',$invite_token);
    $data = $query->get()->result_array();
    return empty($data)?null:$data[0];
  }
  public function getReal()
  {
    $uid = $this->getRealId();
    return $this->get($uid);
  }
  public function getRealId(){
    if(empty($this->realUserId)){
      $this->load->library('session');
      session_write_close();
      $this->realUserId = $this->session->userid;
    }
    return $this->realUserId;
  }
  public function getId()
  {
    if(empty($this->userid)){
      $this->load->library('session');
      session_write_close();
      $this->userid = $this->session->userid_override;
      if(empty($this->userid)){
        $this->userid = $this->session->userid;
      }else{
        $this->realUserId = $this->session->userid;
      }
    }
    return $this->userid;
  }
  public function get($uid = 0)
  {
    if(empty($uid)){
      $uid = $this->getId();
    }
    $query = $query = $this->db
      ->select('id,username,type,shorten_quota,note')
      ->from('user')
      ->where('id',$uid);
    $users = $query->get()->result_array();
    if(empty($users)){
      return null;
    }else{
      if(!empty($this->realUserId)){
        $users[0]['override_by'] = $this->realUserId;
      }
      $users[0]['id'] = intval($users[0]['id']);
      $users[0]['shorten_quota'] = intval($users[0]['shorten_quota']);
      return $users[0];
    }
  }
  public function isExist($uid)
  {
    $cnt = $this->db
        ->where('id',$uid)
        ->count_all_results('user');
    return $cnt > 0;
  }
  public function remove($uid)
  {
    $this->db
      ->where('id', $uid)
      ->delete('user');
  }
  public function setType($uid,$type)
  {
    $this->db
      ->where('id',$uid)
      ->update('user',array(
        'type' => $type
      ));
  }
  public function setQuota($uid,$cnt)
  {
    $this->db
      ->where('id',$uid)
      ->update('user',array(
        'shorten_quota' => $cnt
      ));
  }
  public function all($page,$limit)
  {
    $page = ($page-1)*$limit;
    $query = $this->db
      ->select('id,username,type,note,invite_token,shorten_quota')
      ->from('user')
      ->order_by('id','DESC')
      ->limit($limit, $page);
    $output = $query->get()->result_array();
    foreach ($output as &$o) {
      $o['id'] = intval($o['id']);
      $o['shorten_quota'] = intval($o['shorten_quota']);
    }
    return $output;
  }
}
