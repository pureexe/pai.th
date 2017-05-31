<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* create new path
* create custom path for admin / update / delete for admin
* @class: PathCtrl
**/
class PathCtrl extends CI_Controller {
  private $user;
  public function __construct()
  {
    parent::__construct();
    $this->load
      ->model('Rest')
      ->model('Path')
      ->model('User');
    $this->user = $this->User->get();
    if(empty($this->user)){
      $this->Rest->error('Login is required for this action');
    }
    if($this->user['type'] == 'ban' || $this->user['type'] == 'disable'){
      $this->Rest->error('access denied');
    }
  }
  public function add()
  {
    $fullUrl = $this->input->post("full");
    //admin can create own custom short url
    if($this->User['type'] == 'admin'){
      $shortUrl = $this->input->post("short");
    }
    $shortUrl = $this->input->post("short");
    $this->form_validation->set_rules('full', 'FullUrl', 'valid_url|callback_fullurl_check');
    if($this->form_validation->run() == FALSE){
      return $this->Rest->error(validation_errors(),1);
    }
    //ตรวจถ้าไม่เป็นแอดมินแล้วโควต้าน้อยกว่าที่กำหนดด้วยนะ
    $this->load->driver('cache',
        array(
          'adapter' => 'apc',
          'backup' => 'file',
          'key_prefix' => 'quota_shorten_'
        )
		);
    $quota_use = $this->cache->get($this->user['username']);
    $quota_use = empty($quota_use)?0:$quota_use;
    if($this->user['type'] != 'admin' && $quota_use > $this->user['shorten_quota']){
      return $this->Rest->error("Your quota is running out. Please contact admin for increase.",1);
    }
    $timeToMidNight = strtotime('tomorrow') - time();
    try{
      $this->Path->shorten($uid,$fullUrl,$shortURL);
    }catch(Exception $e){
      $this->Rest->error($e->getMessage());
    }
  }
  public function fullurl_check($fullURL)
  {
    $hostname = array(
      'tafasu.com',
      'www.tafasu.com',
      'mega.nz',
      'mega.co.nz',
      'drive.google.com',
      'docs.google.com',
      'app.koofr.net',
      'k00.fr'
    );
    if(!in_array(parse_url($str, PHP_URL_HOST))){
      //Admin can break the rule
      if($this->User['type'] == 'admin'){
        return true;
      }
      $this->form_validation->set_message('fullurl_check', 'Onky accepeted url from tafasu/koofr/mega/google drive');
      return false;
    } else {
      return true;
    }
  }
}
