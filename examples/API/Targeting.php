<?php

namespace FacebookAds\examples;

use FacebookAds\Api;

use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Search\TargetingSearchTypes;
 
class Targeting{

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

 public function parseArguments($argv){
  $targetingArguments = array();
  $k = array_search("-t", $argv);
  $targetingArguments['type'] = $argv[$k+1];
  $k = array_search("-q", $argv);
  $targetingArguments['query'] = $argv[$k+1];

  $k = array_search("-c", $argv);
  if ($k!=null){
	  $targetingArguments['class'] = $argv[$k+1];
  }else{
	  $targetingArguments['class'] = null;
  }

  $targetingArguments['params'] = array();

  $k = array_search("-l", $argv);
  if(array_key_exists("-l"), $targetingArguments){
//	  $targetingArguments['params']['']
  }
  
  
  $targetres = TargetingSearch::search(
  $type = $targetingArguments['type'],
  $class = $targetingArguments['class'],
  $query = $targetingArguments['query'],
  $targetingArguments['params']);

  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($targetres->getResponse()->getContent());  

 }


}
Targeting::init();
Targeting::api_init();
Targeting::parseArguments($argv);
?>
