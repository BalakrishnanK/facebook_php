<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdPreviewFields;

use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
 
class AdPreviews{

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
  $config_path = '../config1.php';
     if (!is_readable($config_path)) {
       throw new \RuntimeException("Could not read config.php");
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

 public function parseArguments($argv){
  $AdPreviewsArguments = array();
  $k = array_search("-t", $argv);
  $AdPreviewsArguments['type'] = $argv[$k+1];
  $k = array_search("-id", $argv);
  $AdPreviewsArguments['id'] = $argv[$k+1];
  $k = array_search("-adf", $argv);
  $AdPreviewsArguments['ad_format'] = $argv[$k+1];

  if($AdPreviewsArguments['type'] == 'adcreativeid') {
	self::generatePreviewUsingAdcreative($AdPreviewsArguments);
  }else if($AdPreviewsArguments['type'] == 'adgroupid') {
	self::generatePreviewUsingAdgroup($AdPreviewsArguments);
  }else if($AdPreviewsArguments['type'] == 'accountid') {
        self::generatePreviewUsingAdaccount($AdPreviewsArguments);
  }
 }

 public function generatePreviewUsingAdgroup($AdPreviewArguments){
  $adgroup = new AdGroup($AdPreviewArguments['id'], self::$account_id);
  $params = array(
	'ad_format' => $AdPreviewArguments['ad_format'],
  );
  $previews = $adgroup->getAdPreviews(array(),$params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($previews->getResponse()->getContent());
 }

 public function generatePreviewUsingAdCreative($AdPreviewArguments){
  $adcreative = new AdCreative($AdPreviewArguments['id'], self::$account_id);
  $params = array(
        'ad_format' => $AdPreviewArguments['ad_format'],
  );
  $previews = $adcreative->getAdPreviews(array(),$params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($previews->getResponse()->getContent());
 }

 public function generatePreviewUsingAdaccount($AdPreviewArguments){
  $adaccount = new AdAccount(self::$account_id);
  $params = array(
        'ad_format' => $AdPreviewArguments['ad_format'],
  );    
  $previews = $adaccount->getAdPreviews(array(),$params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($previews->getResponse()->getContent());
 }

}
AdPreviews::init();
AdPreviews::api_init();
AdPreviews::parseArguments($argv);
?>
