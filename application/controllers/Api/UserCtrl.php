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
    if(!empty($this->user)){
      $this->Rest->render($this->user);
    }else{
      $this->Rest->error("signin required");
    }
  }
}
