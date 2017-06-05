<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: Path
**/
class Path extends CI_Model {
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }
  /**
  * สำหรับดึง URL เต็มจากพาธของลิงค์ย่อ
  * @param String ลิงค์ย่อ
  * @return null|String ลิ้งค์เต็มที่มี
  * @method getFull
  **/
  public function getFull($path)
  {
    // if have performace problem then we should implement Cache
    // but we have to less inode so i will skip cache and believe
    // in sql server performance
    $query = $this->db
      ->select('full,status')
      ->from('path')
      ->where('path.short',$path);
    $data = $query->get()->result_array();
    if(empty($data)){
      return null;
    }
    //ถ้าหากโดนแบนให้ระงับลิ้งค์ทันที
    if($data[0]['status'] == 'ban'){
      return 'https://ซับ.ไทย/ระงับ';
    }else{
      return $data[0]['full'];
    }
  }
  /**
  * ดึงลิงก์ที่ผู้ใช้มีมาแสดงให้ผู้ใช้ดู
  * @param int รหัสผู้ใช้, int หน้า,int จำนวนต่อหน้า
  * @return assoc_array ข้อมูลของลิงค์นั้นๆ
  * @method all
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
  /**
  * สำหรับลบ / ด้านหน้า และด้านหลัง
  * @param String
  * @param String ที่ลบ / แล้ว
  **/
  public function path_normalize($path)
  {
    $patterns = array('~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~');
    $replacements = array('/', '/', '', '');
    $path =preg_replace($patterns, $replacements, $path);
    $patterns = array('~^/~', '~/$~');
    $replacements = array('','');
    $path = preg_replace($patterns, $replacements, $path);
    return $path;
  }
  /**
  * สำหรับย่อลิ้งค์ หากยังไม่มีตัวอักษรย่อ จะทำการสุ่มภาษาไทยมา 5 ตัวอักษร
  * @param int รหัสผู้ใช้,String URLเต็ม,String เส้นทางที่ย่อแล้ว (ถ้ามี)
  * @method assoc_array รหัสของพาธที่ย่อและพาธที่ย่อแล้ว
  **/
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
      $customShortestPath = $this->path_normalize($customShortestPath);
      if($this->isExist($customShortestPath)){
        throw new Exception("เส้นทาง ".$customShortestPath." ถูกใช้ไปแล้ว", 1);
      }else{
        $shortPath = $customShortestPath;
      }
    }
    $id = $this->point($fullPath,$shortPath,$uid);
    return array(
      'id' => $id,
      'path' => $shortPath
    );
  }
  /**
  * ดึงข้อมูลรหัสและพาธย่อจากพารธเต็ฒ
  * @param String URLเต็ม, int รหัสผู้ใช้
  * @return assoc_array รหัสของพาธและพาธย่อ
  * @method getShortInfoByFull
  **/
  public function getShortInfoByFull($full,$uid)
  {
    $query = $this->db
      ->select('id,short')
      ->from('path')
      ->where('full',$full)
      ->where('owner',$uid);
    $result = $query->get()->result_array();
    if(empty($result)){
      return null;
    }else{
      $result[0]['id'] = intval($result[0]['id']);
      return $result[0];
    }
  }
  /**
  * อัปเดตเวลาของพาธย่อ (กรณีใส่พาธเดิมซ้ำ)
  * @param String พาธย่อ, int รหัสผู้ใช้ที่เป็นเจ้าขอ
  * @method updateTime
  **/
  public function updateTime($short,$uid)
  {
    $query = $this->db
      ->where('owner',$uid)
      ->where('short',$short)
      ->update('path',array(
        'updated_time' => date('Y-m-d H:i:s')
      ));
  }
  /**
  * ตรวจสอบว่าลิ้งค์สั้นดังกล่าวมีอยู่ในระบบแล้วหรือไม่
  * @param String พาธสั้น
  * @return boolean ถ้าเจอตอบว่าจริง
  * @method isExist
  **/
  public function isExist($short)
  {
    $cnt = $this->db
        ->where('short',$short)
        ->count_all_results('path');
    return $cnt>0;
  }
  /**
  * สำหรับเขียน ลิ้งค์สั้นและลิ้งค์ยาวคู่กันไว้ในฐานข้อมูล
  * หมายเหตุ: เมธอดนี้จะส่งขึ้น Firebase ด้วย
  * @param String ลิ้งเต็ม,String พาธย่อ, int รหัสผู้ใช้
  * @return int ไอดีของพาธ
  **/
  public function point($fullUrl,$shortUrl,$uid)
  {
    $this->load->model('PathFirebase');
    $this->PathFirebase->point($fullUrl,$shortUrl);
    $this->db->insert('path',array(
      'owner' => empty($uid)?0:$uid,
      'full' => $fullUrl,
      'short' => $shortUrl
    ));
    return intval($this->db->insert_id());
  }
  /**
  * นับพาธทั้งหมดที่ผู้ใช้ถืออยู่
  * @param int รหัสผู้ใช้
  * @return int จำนวนพาธที่มี
  **/
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
  /**
  * ลบพาธ
  * หมายเหตุ: วิ่งไปลบบน firebase ด้วย
  * @param int รหัสของพาธ
  * @return boolean เป็นจริงถ้าลบสำเร็จ
  **/
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
  /**
  * ลบพาธโดยชื่อผู้ใช้
  * หมายเหตุ: เนื่องจากเป็นการลบพาธปริมาณมากจึงไม่ลบบน Firebase
  * @param int รหัสของพาธ
  * @method removeByOwner
  **/
  public function removeByOwner($uid)
  {
    $this->db
      ->where('owner', $uid)
      ->delete('path');
  }
  /**
  * ค้นหาโดยส่วนในส่วนหนึ่งของลิงค์
  * @param String ส่วนใดส่วนหนึ่งของพาธ
  * @return assoc_array พาธที่เจอ
  * @method simpleSearch
  **/
  public function simpleSearch($path)
  {
      $output = $this->db
        ->select('path.id,path.full,path.short,path.updated_time,user.username,user.note')
        ->from('path')
        ->join('user','path.owner = user.id','LEFT')
        ->like('short',$path)
        ->or_like('full',$path)
        ->limit(20)
        ->order_by('updated_time','DESC')
        ->order_by('id','DESC')
        ->get()
        ->result_array();
      foreach ($output as &$o) {
        $o['id'] = intval($o['id']);
      }
      return $output;
  }
}
