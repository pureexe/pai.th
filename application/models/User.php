<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: User
**/
class User extends CI_Model {
  private $userid;
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
  * create user by username email and password
  * @method create
  * @param username,email,password (plain-text!)
  **/
  public function create($username,$email,$password)
  {
    $this->load->library('phpass');
    $his->load->config('subth');
    $hash = $this->phpass->hash($password);
    $this->db->insert('user',array(
      'username' => $username,
      'email' => $email,
      'password' => $hash,
      'type' => 'user',
      'shorten_quota' => $this->config->item('shorten_quota')
    ));
    return intval($this->db->insert_id());
  }
  public function generateInviteToken($userId)
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
    $this->db
      ->where('id',$userId)
      ->update('user',array(
        'invite_token' => $token
      ));
    return $token;
  }
  public function setPasswordInvite($invite_token,$password)
  {
    $this->load->library('phpass');
    $hash = $this->phpass->hash($password);
    $this->db
      ->where('invite_token',$invite_token)
      ->update('user',array(
        'invite_token' => '',
        'password' => $hash
      ));
    return empty($this->db->affected_rows());
  }
  public function getByInviteToken($invite_token){
    $query = $this->db
      ->select('username,email')
      ->from('user')
      ->where('invite_token',$invite_token);
    $data = $query->get()->result_array();
    return empty($data)?null:$data[0];
  }
  public function getId()
  {
    if(empty($this->userid)){
      $this->load->library('session');
      session_write_close();
      $this->userid = $this->session->userid;
    }
    return $this->userid;
  }
  public function get($uid)
  {
    if(empty($uid)){
      $uid = $this->getId();
    }
    $query = $query = $this->db
      ->select('id,username,email,type,shorten_quota')
      ->from('user')
      ->where('id',$uid);
    $users = $query->get()->result_array();
    if(empty($users)){
      return null;
    }else{
        $users[0]['id'] = intval($users[0]['id']);
        return $users[0];
    }
  }
}
