<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: PathFirebase
**/
require __DIR__.'/../../vendor/autoload.php';
use Kreait\Firebase;

class PathFirebase extends CI_Model {
  private $firebase;
  private $database;
  public function __construct()
  {
    parent::__construct();
    $this->load->config('paith');
    $this->firebase = (new Firebase\Factory)
      ->withCredentials(__DIR__.'/../config/'.$this->config->item('firebase_config'))
      ->withDatabaseUri($this->config->item('firebase_database'))
      ->create();
    $this->database = $this->firebase->getDatabase();
  }
  /**
  * ส่งข้อมูลไปเก็บที่ firebase เผื่อเหตุฉุกเฉิน
  * @param String URLเต็ม, String Pathที่ย่อแล้ว
  * @method point
  **/
  public function point($full,$short)
  {
    //Only use firebase backup on production only!
    if(ENVIRONMENT !== 'production'){
      return;
    }
    $short = str_replace("/","|",$short);
    $this->database
        ->getReference('path/'.$short)
        ->set($full);
  }
  /**
  * ลบข้อมูลออกจาก Firebase เมื่อลบลิ้งค์
  * หมายเหตุ: หากมีการลบจำนวนมาก อย่าเรียกเมธอดนี้ ให้ export แล้วไป
  * อัปโหลดขึ้น Firebase เอง
  * @param  String Pathที่ย่อแล้ว
  * @method unlink
  **/
  public function unlink($short)
  {
    //Only use firebase backup on production only!
    if(ENVIRONMENT !== 'production'){
      return;
    }
    $short = str_replace("/","|",$short);
    $this->database
        ->getReference('path/'.$short)
        ->remove();
  }
}
