<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* Model: Rest
* make output is valid json rest api such as set json header and allow cross origin
* "don't lazy" by using just echo it will make a very big problem to api adaptation
**/
class Rest extends CI_Model  {
  public function render($data,$code = 200)
  {
    $this
      ->output
  //    ->set_header('Access-Control-Allow-Origin: *') //remvove because very dangerous for private api
      ->set_content_type('application/json')
      ->set_status_header($code)
      ->set_output(json_encode($data));
  }
  public function error($message = 'Unknown',$code = 400,$statusCode = 400)
  {
    $data = array(
      'error' => array(
        'code'=>$code,
        'message'=>$message
      )
    );
    $this
      ->output
    //  ->set_header('Access-Control-Allow-Origin: *') //remove because very dangerous for private api
      ->set_content_type('application/json')
      ->set_status_header($statusCode)
      ->set_output(json_encode($data));
  }
}
