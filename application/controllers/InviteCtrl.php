<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InviteCtrl extends CI_Controller {
	public function register()
	{
		$this->load->model('User');
		$invite_token = $this->input->get('บัตรเชิญ');
		try{
			if(empty($invite_token)){
				throw new Exception("เว็บไชต์นี้จำเป็นต้องใช้บัตรเชิญในการสมัครสมาชิก", 1);
			}
			$user = $this->User->getByInviteToken($invite_token);
			if(empty($user)){
				throw new Exception("บัตรเชิญของคุณไม่ถูกต้อง", 1);
			}
			$this->load->view('invite_register');
		}catch(Exception $e){
			$this->load->view('invite_register_error',array(
				'error_message' => $e->getMessage()
			));
		}
	}
	public function register_post()
	{
		$password = $this->input->post('password');
		$invite_token = $this->input->post('invite_token');
		try{
			if(empty($password) || empty($invite_token)){
				throw new Exception("คุณไม่ได้ใช้บัตรเชิญในการสมัครสมาชิก", 1);
			}
			if($this->User->setPasswordInvite($invite_token,$password)){
				throw new Exception("บัตรเชิญของคุณไม่ถูกต้อง", 1);			
			}
			$this->load->view('invite_register_success');
		}catch(Exception $e){
			$this->load->view('invite_register_error',array(
				'error_message' => $e->getMessage()
			));
		}
	}
}
