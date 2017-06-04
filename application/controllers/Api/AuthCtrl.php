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
    $this->lang->load('subth', 'thai');
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    if(empty($username) || empty($password)){
      return $this->Rest->error($this->lang->line('username_and_password_required'));
    }
    $query = $this->db
      ->select('id,password,type,ban_note')
      ->from('user')
      ->where('username',$username);
    $users = $query->get()->result_array();
    if(empty($users)){
      return $this->Rest->error($this->lang->line('username_or_password_incorrent'));
    }
    if($users[0]['type'] == 'ban' || $users[0]['type'] == 'disable'){
      return $this->Rest->error($this->lang->line('user_disable').": ".$users[0]['ban_note']);
    }
    $this->load->library('phpass');
    if($this->phpass->check($password,$users[0]['password'])){
      $this->load->library('session');
      $this->session->set_userdata('userid',intval($users[0]['id']));
  		session_write_close();
      return $this->Rest->render(array(
        'id' => intval($users[0]['id'])
      ));
    }else{
      return $this->Rest->error($this->lang->line('username_or_password_incorrent'));
    }
  }
  public function logout()
  {
    $this->load->database()->model('Rest');
    $this->load->library('session');
    $this->lang->load('subth', 'thai');
    $this->session->unset_userdata('userid');
    $this->session->unset_userdata('userid_override');
    session_write_close();
    return $this->Rest->render(array(
      'message' => $this->lang->line('logout_successfully')
    ));
  }
}
