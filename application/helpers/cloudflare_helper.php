<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* use real php header function instead $CI->output->set_header
* because url helper isn't flush output.
**/

if (!function_exists('use_cache_header'))
{
  function use_cache_header($time = 0)
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

if (!function_exists('use_nocache_header'))
{
  function use_nocache_header()
  {
    if(ENVIRONMENT !== 'production'){
      return;
    }
    header('Cache-Control: no-store, no-cache, must-revalidate');
  }
}
