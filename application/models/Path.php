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
    // if have performace problem then we should implement Cache
    // but we have to less inode so i will skip cache and believe
    // in sql server performance
    $query = $this->db
      ->select('full,owner')
      ->from('path')
      ->where('path.short',$path);
    $data = $query->get()->result_array();
    if(empty($data)){
      return null;
    }
    $owner = $this->db
      ->select('type')
      ->from('user')
      ->where('id',$data[0]['owner'])
      ->get()->result_array();
    if(!empty($owner) && $owner[0]['type'] == 'ban'){
      return "https://ซับ.ไทย/ระงับ";
    }
    return $data[0]['full'];
  }
  /**
  * use for get user list;
  * @method list
  **/
  public function all($uid,$page = 1,$limit = 10)
  {
    $page = ($page-1)*$limit;
    $query = $this->db
      ->select('id,full,short,updated_time')
      ->from('path');
    if(!empty($uid)){
      $query->where('owner',$uid);
    }
    $query
      ->order_by('updated_time','DESC')
      ->order_by('id','DESC')
      ->limit($limit, $page);
    $output = $query->get()->result_array();
    foreach ($output as &$o) {
      $o['id'] = intval($o['id']);
    }
    return $output;
  }
  public function shorten($uid,$fullPath,$customShortestPath)
  {
    $this->load->helper("thaistring");
    $shortPath = "";
    if(empty($customShortestPath)){
      $shortPath = random_thai_string('carnum',5);
      while($this->isExist($shortPath)){
        $shortPath = random_thai_string('carnum',5);
      }
    }else{
      if($this->isExist($customShortestPath)){
        throw new Exception("Path: ".$customShortestPath." is already exist.", 1);
      }else{
        $shortPath = $customShortestPath;
      }
    }
    $this->point($fullPath,$shortPath,$uid);
    return $shortPath;
  }
  public function getShortByFull($full,$uid)
  {
    $query = $this->db
      ->select('short')
      ->from('path')
      ->where('full',$full)
      ->where('owner',$uid);
    $result = $query->get()->result_array();
    return empty($result)?null:$result[0]['short'];
  }
  public function updateTime($short,$uid)
  {
    $query = $this->db
      ->where('owner',$uid)
      ->where('short',$short)
      ->update('path',array(
        'updated_time' => date('Y-m-d H:i:s')
      ));
  }
  public function isExist($short)
  {
    $cnt = $this->db
        ->where('short',$short)
        ->count_all_results('path');
    return $cnt>0;
  }
  public function point($fullUrl,$shortUrl,$uid)
  {
    $this->load->model('PathFirebase');
    $this->PathFirebase->point($fullUrl,$shortUrl);
    $this->db->insert('path',array(
      'owner' => empty($uid)?0:$uid,
      'full' => $fullUrl,
      'short' => $shortUrl
    ));
  }
  public function count($uid)
  {
    if(empty($uid)){
      return $this->db->count_all_results('path');
    }else{
      return $this->db
          ->where('owner',$uid)
          ->count_all_results('path');
    }
  }
  public function remove($pathId)
  {
    if(ENVIRONMENT === 'production'){
      $p = $this->db
        ->select('short')
        ->from('path')
        ->where('id',$pathId)
        ->get()->result_array();
      if(!empty($p)){
        $this->load->model('PathFirebase');
        $this->PathFirebase->unlink($p['short']);
      }
    }
    $this->db
      ->where('id',$pathId)
      ->delete('path');
    return $this->db->affected_rows() > 0;
  }
}
