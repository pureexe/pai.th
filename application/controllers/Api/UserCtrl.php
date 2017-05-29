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
  public function index()
	{

	}
  public function create()
  {
    $username = $this->input->post("username");
    $email = $this->input->post("email");
    $password = $this->input->post("password");
    if(empty($user) || empty($email)){
      //render error
    }
    if(empty($password)){
      //create random string?
      //$password =
    }
    //encrypted password
  }
}
