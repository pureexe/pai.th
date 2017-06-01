<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Manage for use
* for sample for password change
* @class: UserCtrl
**/
class UserCtrl extends CI_Controller {
  /**
  * redirect traveller to promise land
  * @method index
  **/
  private $user;
  public function __construct()
	{
    parent::__construct();
    $this->load->model("User")->model("Rest");
    $this->load->library('form_validation');
    $this->user = $this->User->get();
	}
  public function index()
  {
    $user = $this->User->get();
    if(!empty($user)){
      $this->Rest->render($user);
    }else{
      $this->Rest->error("signin required");
    }
  }
  public function create()
  {
    if(empty($this->user) || $this->user['type'] != 'admin'){
      return $this->Rest->error('only admin can create user');
    }
    $this->form_validation
      ->set_rules('username', 'username', 'required|trim|alpha_numeric|is_unique[user.username]')
      ->set_rules('email', 'email', 'required|trim|valid_email|is_unique[user.email]');
    if ($this->form_validation->run() == FALSE){
      return $this->Rest->error(validation_errors(),1);
    }
    $username = $this->input->post("username");
    $email = $this->input->post("email");
    $password = $this->input->post("password");
    //create random string if password not set
    if(empty($password)){
      $this->load->helper('string');
      $password = random_string('alnum',8);
    }
    $id = $this->User->create($username,$email,$password);
    $token = $this->User->generateInviteToken($id);
    $this->Rest->render(array(
      "id" => $id,
      "invite_token" => $token
    ));
  }
  public function remove($uid)
  {
    if(empty($this->user) || $this->user['type'] != 'admin'){
      return $this->Rest->error('only admin can remove user');
    }
    if(!$this->User->isExist($uid)){
      return $this->Rest->error('can\'t remove non-exist user');
    }
    $this->User->remove($uid);
    $this->Rest->render(array(
        "id" => intval($uid)
    ));
  }
  public function update($uid)
  {
    if(empty($this->user) || $this->user['type'] != 'admin'){
      return $this->Rest->error('only admin can change user data');
    }
    $quota = $this->input->put('quota');
    $role = $this->input->put('role');
  }
  public function invite($uid)
  {
    if(empty($this->user) || $this->user['type'] != 'admin'){
      return $this->Rest->error('only admin can issue invite token');
    }
    if(!$this->User->isExist($uid)){
      return $this->Rest->error('can\'t issue invite token for non-exist user');
    }
    $token = $this->User->generateInviteToken($uid);
    $this->Rest->render(array(
      "invite_token" => $token
    ));
  }
}
