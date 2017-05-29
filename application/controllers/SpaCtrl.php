<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* use to serve single page app
* @class: SpaCtrl
**/
class RedirectorCtrl extends CI_Controller {
  public function __construct()
  {
    $this->load->helpers("cloudflare");
    use_cache_header();
  }
  /**
  * redirect traveller to promise land
  * @method index
  **/
  public function index()
	{

	}
  public function user_manage()
  {
    $this->load->view('user_manage');
  }
  public function admin_manage(){
    $this->load->vuew('admin_manage');
  }
}
