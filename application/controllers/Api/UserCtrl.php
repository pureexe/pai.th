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
  private $realUser;
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
    if(!empty($this->user) && $this->user['type'] != 'ban' && $this->user['type'] != 'disable'){
      $this->Rest->render($this->user);
    }else if(!empty($this->user)){
      $this->realUser = $this->User->getReal();
      if(empty($this->realUser) || $this->realUser['type'] != 'admin'){
        $this->Rest->error($this->lang->line('your_account_is_suppend'),401);
      }else{
        $this->Rest->render($this->user);
      }
    }else{
      $this->lang->load('paith','thai');
      $this->Rest->error($this->lang->line('signin_required'));
    }
  }
}
