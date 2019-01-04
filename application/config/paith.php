<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$config['shorten_quota'] = 33;
$config['http_redirect_code'] = ENVIRONMENT !== 'production'?302:301;
$config['firebase_backup'] = false;
$config['firebase_config'] = 'firebase_adminsdk.json';
$config['firebase_database'] = 'https://โดเมน.firebaseio.com';
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
