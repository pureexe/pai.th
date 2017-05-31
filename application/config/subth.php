<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config["shorten_quota"] = 33;
$config["http_redirect_code"] = 302 ; //Don't forgot change to 301 before prodcution
$config['shorten_whitelist'] = array(
  'tafasu.com',
  'www.tafasu.com',
  'mega.nz',
  'mega.co.nz',
  'drive.google.com',
  'docs.google.com',
  'app.koofr.net',
  'k00.fr'
);
