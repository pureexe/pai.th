<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: RedirectorCtrl
**/
class RedirectorCtrl extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
    $this->load
      ->model('Path')
      ->helpers("cloudflare");
  }
  /**
  * redirect traveller to promise land
  * @method index
  **/
  public function index()
	{
    $fullUrl = $this->Path->getFull(urldecode($this->uri->uri_string()));
    if(!empty($fullUrl)){
      $this->load
        ->helper('url')
        ->config('paith');
      use_cache_header();
      redirect($fullUrl, 'location', $this->config->item('http_redirect_code'));
    }else{
      //Only Cache for 10-minute if 404
      use_cache_header(600);
      $this->output->set_status_header(404);
      $this->load->view('notfound');
    }
	}
  public function notfound()
  {
    $this->output->set_status_header(404);
    $this->load->view('notfound');
  }
}
