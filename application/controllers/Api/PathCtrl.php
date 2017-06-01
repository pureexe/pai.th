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
      $this->Rest->error('your account is suppend.');
    }
  }
  public function add()
  {
    $fullUrl = $this->input->post("full");
    $shortUrl = $this->input->post("short");
    if((!empty($shortUrl)) && $this->user['type'] != 'admin'){
      return $this->Rest->error('only admin can create custom shortlink');
    }
    if(strpos($fullUrl, '://') === false){
      $fullUrl = "http://".$fullUrl;
    }
    $shortUrl = $this->input->post("short");
    $this->load->library('form_validation');
    $this->form_validation->set_rules('full', 'FullUrl', 'valid_url|callback_fullurl_check');
    if($this->form_validation->run() == FALSE){
      return $this->Rest->error(validation_errors(),1);
    }
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
      $path = $this->Path->shorten(
        $this->user['id'],
        $fullUrl,
        $shortUrl
      );
      $quota_use = $this->cache->save(
        $this->user['username'],
        $quota_use+1,
        strtotime('tomorrow')-time()
      );
      return $this->Rest->render(array(
        'path' => $path
      ));
    }catch(Exception $e){
      $this->Rest->error($e->getMessage());
    }
  }
  public function fullurl_check($fullURL)
  {
    $this->load->config('subth');
    $hostname = $this->config->item('shorten_whitelist');
    if($this->user['type'] == 'admin' && !in_array(parse_url($fullURL, PHP_URL_HOST),$hostname)){
      $this->form_validation->set_message('fullurl_check', 'Only accepeted domainlist');
      return false;
    } else {
      return true;
    }
  }
  public function list()
  {
    $page = $this->input->get('page');
    $limit = $this->input->get('limit');
    $page = empty($page)?1:$page;
    $limit = empty($limit)?10:$limit;
    $limit = $limit > 100?10:$limit;
    $forceUid = $this->input->get('uid');
    if($this->user['type'] != 'admin'){
      $uid = $this->user['id'];
    }else{
      if(empty($forceUid) && $forceUid=='0'){
        $uid = 0;
      }else if(!empty($forceUid)){
        $uid = $forceUid;
      }else{
        $this->user['id'];
      }
      $uid = $forceUid=='0'?0:$this->user['id'];
    }
    $this->Rest->render(array("path"=>$this->Path->list($uid,$page,$limit)));
  }
  public function count()
  {
    $forceUid = $this->input->get('uid');
    if(empty($forceUid) && $forceUid=='0'){
      $uid = 0;
    }else if(!empty($forceUid)){
      $uid = $forceUid;
    }else{
      $uid = $this->user['id'];
    }
    $this->Rest->render(array(
      'count' => $this->Path->count($uid),
    ));
  }
}
