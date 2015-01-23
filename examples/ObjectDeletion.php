<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\Values\AdBuyingTypes;
use FacebookAds\Object\Values\AdObjectives;
use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\AdCampaignFields;

use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\AdGroupBidInfoFields;
use FacebookAds\Object\Values\BidTypes;

use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Fields\TargetingSpecsFields;

use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\Fields\ObjectStorySpecFields;
use FacebookAds\Object\Traits\FieldValidation;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;
use FacebookAds\Object\Values\CallToActionTypes;

use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;

use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
 
class ObjectDeletion{

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
  $config_path = __DIR__ . '/config.php';
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

 public function deleteAdCampaign($adCampaignParams){
  $campaign  = new AdCampaign($adCampaignParams['ad_campaign_id'], self::$account_id);
  $campaign->delete();
  echo "Campaign ID:" . $campaign->id . "\n";
  return $campaign;
 }

 public function deleteAdSet($adSetCreationParams){
  $adset = new AdSet($adSetCreationParams['ad_set_id'], self::$account_id);
  $adset->delete();
  echo 'AdSet  ID: '. $adset->id . "\n";
  return $adset; 
 }


 public function deleteAdCreative($creativeCreationParams){
  $creative = new AdCreative($creativeCreationParams['ad_creative_id'], self::$account_id);
  $creative->delete();
  echo 'Creative ID: '.$creative->id . "\n";
  return $creative;
 }

 public function delteAdGroup($adgroupCreationParams){
  $adgroup = new AdGroup($adgroupCreationParams['ad_group_id'], self::$account_id);
  $adgroup->delete();
 }

}
ObjectDeletion::init();
ObjectDeletion::api_init();
/*ObjectDeletion::deleteAdGroup(array(
  'ad_group_id' => '6028369967720'
));
*/
/*
ObjectDeletion::deleteAdCreative(array(
  'ad_creative_id' => '6028369949520',
));
*/
/*
ObjectDeletion::deleteAdSet(array(
  'ad_set_id' => '6028317882120',
));
*/
/*
ObjectDeletion::deleteAdCampaign(array(
  'ad_campaign_id' => '6028344016520',
));
*/
?>
