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
      ->select('full')
      ->from('path')
      ->join('user','path.owner = user.id','left')
      ->where('path.short',$path)
      ->where('user.type !=','ban');
    $data = $query->get()->result_array();
    return empty($data)?null:$data[0]['full'];
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
  public function isExist($short)
  {
    $cnt = $this->db
        ->where('short',$short)
        ->count_all_results('path');
    return $cnt>0;
  }
  public function point($fullUrl,$shortUrl,$uid)
  {
    $this->load->model('User');
    $this->db->insert('path',array(
      'owner' => empty($uid)?0:$uid,
      'full' => $fullUrl,
      'short' => $shortUrl
    ));
  }
}
