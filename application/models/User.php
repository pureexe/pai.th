<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: User
**/
class User extends CI_Model {
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }
  public function hello()
  {
    echo "hello";
  }
  public function isAdmin()
  {

  }
  public function isSignIn()
  {

  }
  public function getUserId()
  {

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
    $hash = $this->phpass->hash($invite_token);
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
}
