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
  public function shorten($fullPath,$customShortestPath)
  {
    $this->load->helper("thaistring");
    if(empty($customShortestPath)){
      $shortPath = random_thai_string('carnum',5);
      while()
    }else{
      if($this->isExist($customShortestPath)){
        throw new Exception("Path: "+$customShortestPath+" is already exist.", 1);
      }else{
        $this->point($fullUrl,$customShortestPath);
      }
    }
  }
}
