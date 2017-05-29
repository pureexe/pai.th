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
    echo "redirector: ".urldecode($this->uri->uri_string());
	}
}
