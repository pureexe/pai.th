<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('use_cache_header'))
{
  function use_cache_header()
  {
    if(ENVIRONMENT !== 'production'){
      return;
    }
    //Implement cache header later
  }
}
