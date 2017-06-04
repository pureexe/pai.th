<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* OverrideCtrl Controller
* for prevent session conflict
* this will not load user model to
* @class AuthCtrl
**/
class OverrideCtrl extends CI_Controller {

  private $userid;
  public function __construct()
	{
    parent::__construct();
    $this->load->database()->model("Rest");
    $this->lang->load("subth","thai");
    $this->load->library('session');
    try{
      $this->userid = $this->session->userid;
      if(empty($this->userid)){
        throw new Exception($this->lang->line("only_admin_can_do"), 1);
      }
      $userType = $this->db
        ->select('type')
        ->from('user')
        ->where('id',$this->userid)
        ->get()
        ->result_array();
      if(empty($userType) || $userType[0]['type'] != 'admin'){
          throw new Exception($this->lang->line("only_admin_can_do"), 1);
      }
    }catch(Exception $e){
      session_write_close();
      $this->Rest->error($e->getMessage());
      $this->output->_display();
      exit();
    }
	}
  public function create()
  {
    $uid = $this->input->post("uid");
    if($this->userid == $uid){
      session_write_close();
      return $this->Rest->error($this->lang->line('cant_override_yourself'));
    }
    $test = $this->db
      ->select('id')
      ->from('user')
      ->where('id',$uid)
      ->get()
      ->result_array();
    if(empty($test)){
      session_write_close();
      return $this->Rest->error($this->lang->line('cant_modify_non_exist_user'));
    }else{
      $this->session->set_userdata('userid_override',intval($uid));
      $this->Rest->render(array(
        'id' => intval($uid)
      ));
      session_write_close();
    }
  }
  public function remove()
  {
    try{
      if(empty($this->session->userid_override)){
        throw new Exception($this->lang->line('cant_switch_to_main_account'), 1);
      }
      $this->session->unset_userdata('userid_override');
      $this->Rest->render(array(
        'id' => $this->userid
      ));
    }catch(Exception $e){
      $this->Rest->error($e->getMessage());
    }finally{
      session_write_close();
    }
  }
}
