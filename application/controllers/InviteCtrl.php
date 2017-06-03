<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InviteCtrl extends CI_Controller {
	private $invite_token;
	public function __construct()
  {
    parent::__construct();
		$this->load->model('User');
		$this->lang->load('subth','thai');
	}
	public function register()
	{
		$invite_token = $this->input->get('บัตรเชิญ');
		try{
			if(empty($invite_token)){
				throw new Exception($this->lang->line('site_is_require_invite_card_for_registation'), 1);
			}
			$user = $this->User->getByInviteToken($invite_token);
			if(empty($user)){
				throw new Exception($this->lang->line('invalid_invite_card'), 1);
			}
			$user['invite_token'] = $invite_token;
			$this->load->view('invite_register',$user);
		}catch(Exception $e){
			$this->load->view('invite_register_error',array(
				'error_message' => $e->getMessage()
			));
		}
	}
	public function register_post()
	{
		$this->load->library('form_validation');
		$password = $this->input->post('password');
		$this->invite_token = $this->input->post('invite_token');
		$username = $this->input->post('username');
		try{
			if(empty($password) || empty($this->invite_token)){
				throw new Exception($this->lang->line('you_dont_have_invite_card'), 1);
			}
			if(!empty($username)){
				$this->form_validation
					->set_rules('username', 'ชื่อผู้ใช้', 'required|trim|alpha_numeric|callback_is_fresh_account|is_unique[user.username]');
				if($this->form_validation->run() == FALSE){
					throw new Exception(validation_errors(), 1);
				}
			}
			if($this->User->setPasswordInvite($this->invite_token,$password,$username)){
				throw new Exception($this->lang->line('invalid_invite_card'), 1);
			}
			$this->load->view('invite_register_success');
		}catch(Exception $e){
			$this->load->view('invite_register_error',array(
				'error_message' => $e->getMessage()
			));
		}
	}
	public function is_fresh_account($username)
	{
		$this->load->database();
		$users = $this->db
			->select('username')
			->from('user')
			->where('invite_token',$this->invite_token)
			->get()->result_array();
		if(empty($users)){
			$this->form_validation->set_message('is_fresh_account',$this->lang->line('invalid_invite_card'));
			return false;
		}else if($users[0]['username'] != ''){
			$this->form_validation->set_message('is_fresh_account',$this->lang->line('cant_change_username'));
			return false;
		}else{
			return true;
		}
	}
}
