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
  /**
  * Generate backup for firebase
  **/
  public function firebase()
  {
    if(!is_cli()){
      show_404();
    }
    echo('sub.th to Firebase Exporter'.PHP_EOL);
    echo('============================'.PHP_EOL);
    if(!file_exists('_output/firebase')) {
      mkdir('_output/firebase', 0777, true);
    }
    $this->load->database();
    $pathList = $this->db
      ->select('full,short')
      ->from('path')
      ->where('status !=','ban')
      ->order_by('short')
      ->get()
      ->result_array();
    $output = array();
    foreach ($pathList as $path) {
      $path['short'] = str_replace('/','|',$path['short']);
      $output[$path['short']] = $path['full'];
    }
    $file = fopen('_output/firebase/subth_export.json', 'w') or die('unable to open file!');
    fwrite($file, json_encode(array('path' => $output)));
    fclose($file);
    echo("export complete.". PHP_EOL);
    echo("please upload file at _output/firebase/subth_export.json to firebase");
  }
  /**
  * Generate backup for jekyll
  **/
  public function jekyll()
  {
    if(!is_cli()){
      show_404();
    }
    echo('sub.th to Jekyll Exporter'.PHP_EOL);
    echo('============================'.PHP_EOL);
    if(!file_exists('_output/jekyll')) {
      mkdir('_output/jekyll', 0777, true);
    }
    $this->load->database();
    $pathList = $this->db
      ->select('full,short')
      ->from('path')
      ->where('status !=','ban')
      ->order_by('short')
      ->get()
      ->result_array();
    foreach ($pathList as $path) {
      $filename = '';
      $folder = '';
      if(strpos($path['short'], '/') !== false){
        $folder = explode('/',$path['short']);
        $filename = array_pop($folder);
        $folder = implode('/',$folder);
      }else{
        $filename = $path['short'];
      }
      if(!file_exists('_output/jekyll/_path/'.$folder)) {
        mkdir('_output/jekyll/_path/'.$folder, 0777, true);
      }
      $file = fopen('_output/jekyll/_path/'.$folder.'/'.$filename.'.md', 'w') or die('unable to open file!');
      fwrite($file,'---'.PHP_EOL.'full:"'.$path['full'].'"'.PHP_EOL.'---');
      fclose($file);
    }
    echo("export complete.". PHP_EOL);
    echo("please copy all file at _output/jekyll and put it to jekyll repo");
  }
}
