<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Auth Controller
* for prevent session conflict
* this will not load user model to
* @class AuthCtrl
**/
class AuthCtrl extends CI_Controller {

  public function signin()
  {
    $this->load->database()->model('Rest');
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    if(empty($username) || empty($password)){
      return $this->Rest->error('username and password is required for signin.');
    }
    $query = $this->db
      ->select('id,password')
      ->from('user')
      ->where('username',$username)
      ->or_where('email',$username);
    $users = $query->get()->result_array();
    if(empty($users)){
      return $this->Rest->error('username and password is incorect');
    }
    $this->load->library('phpass');
    if($this->phpass->check($password,$users[0]['password'])){
      $this->load->library('session');
      $this->session->set_userdata(array(
  			'userid' => intval($users[0]['id'])
  		));
  		session_write_close();
      return $this->Rest->render(array(
        'id' => intval($users[0]['id'])
      ));
    }else{
      return $this->Rest->error('username and password is incorect');
    }
  }
  public function logout()
  {
    $this->load->library('session');
    $this->session->unset_userdata('userid');
    session_write_close();
    return $this->Rest->render(array(
      'message' => 'logout successfully'
    ));
  }
}
