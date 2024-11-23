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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//api
$route['api/login'] = 'api/v1/auth/login';
$route['api/signUp'] = 'api/v1/auth/signUp';
$route['api/checkUserExistByUsername'] = 'api/v1/auth/checkUserExistByUsername';
$route['api/checkUserSideByUsername'] = 'api/v1/auth/checkUserSideByUsername';

$route['api/home'] = 'api/v1/home/home';
$route['api/depositList'] = 'api/v1/home/depositList';
$route['api/withdrawList'] = 'api/v1/home/withdrawList';
$route['api/bonusList'] = 'api/v1/home/bonusList';
$route['api/userDetails'] = 'api/v1/home/userDetails';
$route['api/rewardBonusList'] = 'api/v1/home/rewardBonusList';
$route['api/updateProfile'] = 'api/v1/home/updateProfile';
$route['api/updatePassword'] = 'api/v1/home/updatePassword';
$route['api/packagesList'] = 'api/v1/home/packagesList';
$route['api/userPackageList'] = 'api/v1/home/userPackageList';
$route['api/cryptoBalance'] = 'api/v1/home/cryptoBalance';
$route['api/checkPackageByAmount'] = 'api/v1/home/checkPackageByAmount';
$route['api/withdrawOtp'] = 'api/v1/home/withdrawOtp';
$route['api/verifyWithdrawOtp'] = 'api/v1/home/verifyWithdrawOtp';
$route['api/withdraw'] = 'api/v1/home/withdraw';
$route['api/referList'] = 'api/v1/home/referList';
$route['api/teamListForBinary'] = 'api/v1/home/teamListForBinary';
$route['api/deposit'] = 'api/v1/home/deposit';
$route['api/checkTransactionStatus'] = 'api/v1/home/checkTransactionStatus';
$route['api/send_test_email'] = 'api/v1/auth/send_test_email';
$route['api/send_verify_email/(:any)/(:any)/(:any)'] = 'api/v1/auth/send_verify_email/$1/$2/$3';

$route['api/bmxPrice'] = 'api/v1/home/bmxPrice';
