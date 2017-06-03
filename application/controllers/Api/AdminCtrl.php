<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* for Admin delete or update user data or create new invite
* @class: AdminCtrl
**/
class AdminCtrl extends CI_Controller {
  private $user;
  public function __construct()
	{
    parent::__construct();
    $this->load->model("User")->model("Rest");
    $this->load->library('form_validation');
    $this->lang->load("subth","thai");
    $this->user = $this->User->getReal();
    if(empty($this->user) || $this->user['type'] != 'admin'){
      $this->Rest->error($this->lang->line("only_admin_can_do"));
      $this->output->_display();
      exit();
    }
	}
  public function create()
  {
    $this->form_validation
      ->set_rules('username', 'username', 'required|trim|alpha_numeric|is_unique[user.username]');
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
    if(!$this->User->isExist($uid)){
      return $this->Rest->error($this->lang->line("cant_remove_non_exist_user"));
    }
    $this->User->remove($uid);
    $this->Rest->render(array(
        "id" => intval($uid)
    ));
  }
  public function update($uid)
  {
    $quota = $this->input->post('quota');
    $type = $this->input->post('type');
    $user = $this->User->get($uid);
    if(empty($user)){
        return $this->Rest->error($this->lang->line('cant_modify_non_exist_user'));
    }
    if(!empty($quota) && is_numeric($quota)){
      $this->load->driver('cache',array('adapter' => 'apc','backup' => 'file'));
      $this->cache->delete('quota_shorten_'.$user['username']);
      $this->User->setQuota($uid,intval($quota));
    }
    if(!empty($type) && in_array($type,array('admin','user','ban','disable'))){
      $this->User->setType($uid,$type);
    }
    $this->Rest->render(array(
      "id" => intval($uid)
    ));
  }
  public function invite($uid = 0)
  {
    if(empty($uid)){
      $note = $this->input->post("note");
      $token = $this->User->inviteTokenForNewUser($note);
      return $this->Rest->render($token);
    }else if(!$this->User->isExist($uid)){
      return $this->Rest->error($this->lang->line('cant_issue_invite_token_for_non_exist_user'));
    }else{
      $token = $this->User->generateInviteToken($uid);
      return $this->Rest->render(array(
        "id" => intval($uid),
        "invite_token" => $token
      ));
    }
  }
  public function all()
  {
    $limit = $this->input->get('limit');
    $page = $this->input->get('page');
    $page = empty($page)?1:$page;
    $limit = empty($limit)?10:$limit;
    $limit = $limit > 1000?10:$limit;
    $this->Rest->render(array("user"=>$this->User->all($page,$limit)));
  }
}
