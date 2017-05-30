<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: User
**/
class Path extends CI_Model {
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }
  public function getFull($path)
  {

  }
  public function shorten($fullPath)
  {

  }
}
