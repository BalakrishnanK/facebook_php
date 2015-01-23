<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceTypes;
 
class CustomAudienceCreation{

 private static $config = array();
  
 private static $access_token;
 private static $app_id;
 private static $app_secret;
 private static $account_id;

 private static function idxt(array $array, $key) {
   if (!array_key_exists($key, $array) || !$array[$key]) {
     throw new \Exception("Missing mandatory config '{$key}'");
   }

   return $array[$key];
 }

 private static function confx($key, $default = null) {
   return self::idx(self::$config, $key, $default);
 }

 private static function confxt($key) {
    return self::idxt(self::$config, $key);
 }

 public static function init(){
  define('SDK_DIR1', __DIR__ . '');
  $config_path = SDK_DIR1.'/../config1.php';
 // print_r($config_path);
     if (!is_readable($config_path)) {
       throw new \RuntimeException("Could not read config1.php");
  }
 
  self::$config=include $config_path; 
  self::$app_id = self::confxt('app_id');
  self::$account_id = self::confxt('account_id');
  self::$app_secret = self::confxt('app_secret');
  self::$access_token = self::confxt('access_token');
 }

 public function api_init(){
  define('SDK_DIR', __DIR__ . '/../..'); // Path to the SDK directory
  $loader = include SDK_DIR.'/vendor/autoload.php';
  Api::init(self::$app_id, self::$app_secret, self::$access_token);
 }

 public function parseArgumentsPOST($argv, $emails){
//	print_r($argv);
//	die;
//	if ($argv['type'] == 'createaudience'){
	self::createCustomAudienceFromArray($argv, $emails);
//	}
 }

 public function parseArguments($argv){
  $customAudienceCreationParams = array();
  $k = array_search("-f", $argv);
  $customAudienceCreationParams['file_path'] = $argv[$k+1];
  $k = array_search("-n", $argv); 
  $customAudienceCreationParams['cust_aud_name'] = $argv[$k+1];
  $k = array_search("-desc", $argv);
  $customAudienceCreationParams['cust_aud_desc'] = $argv[$k+1];
  $k = array_search("-datatype", $argv);
  $customAudienceCreationParams['data_type'] = $argv[$k+1];
  $k = array_search("-del", $argv);
  $customAudienceCreationParams['delimiter'] = $argv[$k+1];
  $k = array_search("-custid", $argv);
  $customAudienceCreationParams['cust_aud_id'] = $argv[$k+1];
  $k = array_search("-type", $argv);
  $customAudienceCreationParams['cust_aud_processing'] = $argv[$k+1];
  $k = array_search("-lookn", $argv);
  $customAudienceCreationParams['lookalike_name'] = $argv[$k+1];
  $k = array_search("-country", $argv);
  $customAudienceCreationParams['country'] = $argv[$k+1];
  $k = array_search("-ratio", $argv);
  $customAudienceCreationParams['ratio'] = $argv[$k+1];
 
 
 
  if ($customAudienceCreationParams['cust_aud_processing'] == 'create'){
	self::createCustomAudience($customAudienceCreationParams);
  }else if ($customAudienceCreationParams['cust_aud_processing'] == 'update'){
        self::updateCustomAudience($customAudienceCreationParams);
  }else if ($customAudienceCreationParams['cust_aud_processing'] == 'addusers'){
        self::addUsersToCustomAudience($customAudienceCreationParams);
  }else if ($customAudienceCreationParams['cust_aud_processing'] == 'removeusers'){
        self::removeUsersFromCustomAudience($customAudienceCreationParams);
  }else if ($customAudienceCreationParams['cust_aud_processing'] == 'lookalike'){
        self::createLookalikeAudience($customAudienceCreationParams);
  }else if ($customAudienceCreationParams['cust_aud_processing'] == 'deleteAudience'){
        self::deleteAudience($customAudienceCreationParams);
  }


 }

 public function createCustomAudienceFromArray($customAudienceCreationParams, $emails){
  $audience = new CustomAudience(null, self::$account_id);
  $audience->setData(array(
  CustomAudienceFields::NAME => $customAudienceCreationParams['data']['Advertiser']['name'],
  CustomAudienceFields::DESCRIPTION => $customAudienceCreationParams['data']['Advertiser']['description'],
  ));

  $audience->create();
  $audience->addUsers($emails, 'EMAIL_SHA256');
  echo $audience->id;
 }


 public function createCustomAudience($customAudienceCreationParams){
  $audience = new CustomAudience(null, self::$account_id);
  $audience->setData(array(
  CustomAudienceFields::NAME => $customAudienceCreationParams['cust_aud_name'],
  CustomAudienceFields::DESCRIPTION => $customAudienceCreationParams['cust_aud_desc'],
  ));
  
  $audience->create();
  
  $file_path = $customAudienceCreationParams['file_path'];

  $file = fopen( $file_path, "r" );
  if ($file == false){
   	echo ( "Error in opening file" );
   	exit();
  }
  $filesize = filesize($file_path);
  $filetext = fread($file, $filesize);
  $data_array = explode($customAudienceCreationParams['delimiter'],$filetext);

  echo count($data_array).'\n';
  $audience->addUsers($data_array, $customAudienceCreationParams['data_type']);
 } 

 public function updateCustomAudience($customAudienceUploadParams){
  $audience = new CustomAudience($customAudienceUploadParams['cust_aud_id'], self::$account_id);
  $audience->setData(array(
  CustomAudienceFields::NAME => $customAudienceCreationParams['cust_aud_name'],
  CustomAudienceFields::DESCRIPTION => $customAudienceCreationParams['cust_aud_desc'],
  ));
  $audience->update();
 }

 public function addUsersToCustomAudience($customAudienceUploadParams){
  $audience = new CustomAudience($customAudienceUploadParams['cust_aud_id'], self::$account_id);
  $file_path = $customAudienceUploadParams['file_path'];

  $file = fopen( $file_path, "r" );
  if ($file == false){
        echo ( "Error in opening file" );
        exit();
  }
  $filesize = filesize($file_path);
  $filetext = fread($file, $filesize);
  $data_array = explode($customAudienceUploadParams['delimiter'],$filetext);

  echo count($data_array).'\n';
  $audience->addUsers($data_array, $customAudienceUploadParams['data_type']);
 }

 public function removeUsersFromCustomAudience($customAudienceRemoveUsersParams){
  $audience = new CustomAudience($customAudienceRemoveUsersParams['cust_aud_id'], self::$account_id);
  $file_path = $customAudienceRemoveUsersParams['file_path'];

  $file = fopen( $file_path, "r" );
  if ($file == false){
        echo ( "Error in opening file" );
        exit();
  }
  $filesize = filesize($file_path);
  $filetext = fread($file, $filesize);
  $data_array = explode($customAudienceRemoveUsersParams['delimiter'],$filetext);

  echo count($data_array).'\n';
  $audience->addUsers($data_array, $customAudienceRemoveUsersParams['data_type']);
 }

 public function createLookalikeAudience($lookalikeAudienceCreationParams){
  $lookalike = new CustomAudience(null, self::$account_id);
  $lookalike->setData(array(
  	  CustomAudienceFields::NAME => $lookalikeAudienceCreationParams['lookalike_name'],
	  CustomAudienceFields::ORIGIN_AUDIENCE_ID => $lookalikeAudienceCreationParams['cust_aud_id'],
	  CustomAudienceFields::LOOKALIKE_SPEC => array (
        	'ratio' => $lookalikeAudienceCreationParams['ratio'],
	        'country' => $lookalikeAudienceCreationParams['country'],
    	  )
       ));
  $lookalike->create();
  echo "lookalike ID:" . $lookalike->id . "\n";
 }

 public function deleteAudience($deleteAudParams){
  $audience = new CustomAudience($deleteAudParams['cust_aud_id'],self::$account_id);
  $audience->delete();
 }

}
CustomAudienceCreation::init();
CustomAudienceCreation::api_init();

$mongo = new \Mongo('localhost');
$couchcast_db = $mongo->couchcast->v_t_genre;
//$query_fields = array("value.duration" => array('$gt' => 999),
//		      "value.genre" => array('$eq' => )) 
		
$emails = $couchcast_db->distinct("_id.email", array("value.duration" => array('$gt' => 999), 
							));
//print_r($emails);
CustomAudienceCreation::parseArgumentsPOST($_POST, $emails);
?>
