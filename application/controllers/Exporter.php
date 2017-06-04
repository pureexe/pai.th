<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: RedirectorCtrl
**/
class Exporter extends CI_Controller {
  public function __construct()
  {
    parent::__construct();
  }
  public function firebase()
  {
    if(!is_cli()){
      throw new Exception("This method is only working on CLI mode", 1);
    }
  }
  public function jekyll()
  {
    if(!is_cli()){
      throw new Exception("This method is only working on CLI mode", 1);
    }
  }
}
