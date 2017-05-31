<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('use_cache_header'))
{
  function use_cache_header($time)
  {
    if(ENVIRONMENT !== 'production'){
      return;
    }
    if(empty($time)){
      header("Cache-Control: max-age=31536000, min-fresh=7200, public");
    }else{
      header("Cache-Control: max-age=".$time.", min-fresh=".$time.", public");
    }
  }
}
