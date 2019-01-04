<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* create new path
* create custom path for admin / update / delete for admin
* @class: PathCtrl
**/
class PathCtrl extends CI_Controller {
  private $user;
  private $realUser;
  public function __construct()
  {
    parent::__construct();
    $this->load
      ->model('Rest')
      ->model('Path')
      ->model('User');
    $this->lang->load("paith","thai");
    $this->user = $this->User->get();
    if(empty($this->user)){
      $this->Rest->error($this->lang->line('signin_required'),401);
      $this->output->_display();
      exit();
    }
    if($this->user['type'] == 'ban' || $this->user['type'] == 'disable'){
      $this->realUser = $this->User->getReal();
      if(empty($this->realUser) || $this->realUser['type'] != 'admin'){
        $this->Rest->error($this->lang->line('your_account_is_suppend'),401);
        $this->output->_display();
        exit();
      }
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
    $oldshort = $this->Path->getShortInfoByFull($fullUrl,$this->user['id']);
    if(!empty($oldshort)){
      $this->Path->updateTime($oldshort['short'],$this->user['id']);
      return $this->Rest->error($oldshort,20);
    }
    $this->load->driver('cache',array('adapter' => 'apc','backup' => 'file'));
    $quota_use = $this->cache->get('quota_shorten_'.$this->user['username']);
    $quota_use = empty($quota_use)?0:$quota_use;
    if($this->user['type'] != 'admin' && $quota_use >= $this->user['shorten_quota']){
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
        'quota_shorten_'.$this->user['username'],
        $quota_use+1,
        strtotime('tomorrow')-time()
      );
      return $this->Rest->render($path);
    }catch(Exception $e){
      $this->Rest->error($e->getMessage());
    }
  }
  public function fullurl_check($fullURL)
  {
    $this->load->config('paith');
    if(strpos($fullURL, '://') === false){
      $fullURL = "http://".$fullURL;
    }
    $hostname = $this->config->item('shorten_whitelist');
    if($this->user['type'] != 'admin' && !in_array(parse_url($fullURL, PHP_URL_HOST),$hostname)){
      $this->form_validation->set_message('fullurl_check', $this->lang->line('only_accept_domainlist'));
      return false;
    } else {
      return true;
    }
  }
  public function all()
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
    $this->Rest->render(array("path"=>$this->Path->all($uid,$page,$limit)));
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
  public function remove($pathId){
    $this->user = $this->User->getReal();
    if($this->user['type'] != 'admin'){
      return $this->Rest->error($this->lang->line('only_admin_can_do'));
    }
    if($this->Path->remove($pathId)){
      return $this->Rest->render(array('id' => intval($pathId)));
    }else{
      return $this->Rest->error($this->lang->line('cant_remove_non_exist_path'));
    }
  }
  public function search()
  {
    if(empty($this->realUser)){
      $this->realUser = $this->User->getReal();
    }
    if($this->realUser['type'] != 'admin'){
      return $this->Rest->error($this->lang->line('only_admin_can_do'));
    }
    $q = $this->input->get('q');
    $q = str_replace("http://","",$q);
    $q = str_replace("https://","",$q);
    $q = str_replace("ซับ.ไทย/","",$q);
    if(empty($q)){
      return $this->Rest->render(array(
        "path" => array()
      ));
    }
    $data = $this->Path->simpleSearch($q);
    if(!empty($data)){
      return $this->Rest->render(array(
        'path' => $data
      ));
    }
    return $this->Rest->render(array(
      "path" => array()
    ));
  }
}
