<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
/**
* sub.th note:
* codeigniter not support UTF-8 routing. so we have no choice.
**/
$route['default_controller'] = 'welcome';
$route[urlencode('จัดการ')] = 'SpaCtrl/user_manage';
$route[urlencode('ผู้ดูแล')] = 'SpaCtrl/admin_manage';
$route[urlencode('สมัคร')]['get'] = 'InviteCtrl/register';
$route[urlencode('สมัคร')]['post'] = 'InviteCtrl/register_post';

//API for private use only!
$route['api/v1/user']['get'] = 'Api/UserCtrl';
$route['api/v1/user/list']['get'] = 'Api/UserCtrl/list'; //admin only!
$route['api/v1/user']['put'] = 'Api/UserCtrl/update'; //admin only!
$route['api/v1/user']['post'] = 'Api/UserCtrl/create'; //admin only!
$route['api/v1/auth']['post'] = 'Api/AuthCtrl/signin';
$route['api/v1/auth']['delete'] = 'Api/AuthCtrl/logout';
$route['api/v1/path']['get'] = 'Api/PathCtrl'; //getlist
$route['api/v1/path']['post'] = 'Api/PathCtrl/shorten';

//debug
$route['write'] =  'welcome/write';
$route['read'] =  'welcome/read';

$route['(.+)'] = 'RedirectorCtrl';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
