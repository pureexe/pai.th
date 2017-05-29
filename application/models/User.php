<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* @class: User
**/
class User extends CI_Controller {
  public function __construct()
  {
  }
  public function isAdmin()
  {

  }
  public function isSignIn()
  {

  }
  public function getUserId()
  {

  }
  public function createUser($username,$email,$password)
  {

  }
  public function createInviteToken($userId)
  {
    return $token;
  }
  public function setPasswordInvite($token,$password)
  {
  }
}
