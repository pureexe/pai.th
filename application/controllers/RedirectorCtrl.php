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
    use_cache_header();
  }
  /**
  * redirect traveller to promise land
  * @method index
  **/
  public function index()
	{
    $fullUrl = $this->Path->getFull(urldecode($this->uri->uri_string()));
    if(!empty($fullUrl)){
      $this->load->helper('url');
      $this->load->config('subth');
      redirect($fullUrl, 'location', $this->config->item('http_redirect_code'));
    }else{
      show_404();
    }
	}
}
