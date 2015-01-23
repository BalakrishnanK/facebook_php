<?php

namespace FacebookAds\examples;

use FacebookAds\Api;

use FacebookAds\Object\AdAccount;

use FacebookAds\Object\ReachFrequencyPrediction as RF;

Class ReachFrequency{

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
  $config_path = __DIR__ . '/config1.php';
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
  define('SDK_DIR', __DIR__ . '/..'); // Path to the SDK directory
  $loader = include SDK_DIR.'/vendor/autoload.php';
  Api::init(self::$app_id, self::$app_secret, self::$access_token);

 }

public function getReachFrequencyPrediction(){
    $prediction = new ReachFrequencyPrediction(null, self::$account_id);

    $targeting = array(
    'geo_locations' => array('countries' => array('US')),
    'custom_audiences' => array(array('id' => '6029131452920')),
//      'age_max' => 35,
      'age_min' => 18,
      'genders' =>  array('2'),
      'page_types' => array('feed'),
    );

    $prediction->setData(array(
      RF::BUDGET => 50000,
      RF::TARGET_SPEC => $targeting,
      RF::START_TIME => strtotime('midnight + 2 weeks'),
      RF::END_TIME => strtotime('midnight + 3 weeks'),
      RF::FREQUENCY_CAP => 1,
//      RF::DESTINATION_ID => $this->getPageId(),
      RF::PREDICTION_MODE => ReachFrequencyPrediction::PREDICTION_MODE_REACH,
      RF::OBJECTIVE => AdObjectives::POST_ENGAGEMENT,
      RF::STORY_EVENT_TYPE => 128,
    ));

    $prediction->reserve();    

}
}

ReachFrequency::init();
ReachFrequency::api_init();
ReachFrequency::getReachFrequencyPrediction();

?>
