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
    $this->firebase = (new Firebase\Factory)
      ->withCredentials(__DIR__.'/../config/subs-e3db8-firebase-adminsdk-zz64o-b0b7281d2c.json')
      ->withDatabaseUri('https://subs-e3db8.firebaseio.com')
      ->create();
    $this->database = $this->firebase->getDatabase();
  }
  public function point($full,$short)
  {
    //Only use firebase backup on production only!
    if(ENVIRONMENT !== 'production'){
      return;
    }
    $short = str_replace("/","\\",$short);
    $this->database
        ->getReference('path/'.$short)
        ->set($full);
  }
}
