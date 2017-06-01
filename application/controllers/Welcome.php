<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
	 	parent::__construct();
		$this->load->model("User");
    $this->load->model("Rest");
		$this->load->library('form_validation');
 	}
	public function index()
	{
		$this->form_validation->set_rules('username','username','required');
		$this->load->view('welcome_message');
	}
	public function write()
	{
		$this->load->library("session");
		$this->load->library("session");
		$this->session->set_userdata(array(
			'userid' => 12355
		));
		session_write_close();
	}
	public function read()
	{
		$a = parse_url("http://www.google.com", PHP_URL_HOST);
		print_r($a);
	}
}
