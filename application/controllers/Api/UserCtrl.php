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
    $this->load->model('User')->model('Rest');
	}
  public function index()
  {
    $this->load->library('session');
    $useRealUser = $this->input->get('real');
    if(!empty($useRealUser)){
      $this->user = $this->User->getReal();
    }else{
      $this->user = $this->User->get();
    }
    if(!empty($this->user)){
      $this->Rest->render($this->user);
    }else{
      $this->lang->load('subth','thai');
      $this->Rest->error($this->lang->line('signin_required'));
    }
  }
}
