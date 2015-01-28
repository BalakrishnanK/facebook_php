<?php

namespace FacebookAds\examples;

use FacebookAds\Api;

use FacebookAds\Object\AdAccount;

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



 public function getAdCampaignStats($argv){
  if(count($argv) == 0){
	return 0;
  }
  $adaccount = new AdAccount(self::$account_id);
  $fields = array('name','id','created_time','spend_cap','objective','campaign_group_status','buying_type','can_use_spend_cap','promoted_object','start_time','stop_time','topline_id','updated_time','adgroups','stats','account_id', 'adcampaigns');

  $reportStats = $adaccount->getAdCampaigns($fields, array( 'limit' => 200))->getResponse()->getContent()['data'];

  $adGroupStats = $adaccount->getAdCampaignsWithStats($fields, array( 'limit' => 200))->getResponse()->getContent()['data'];

//  print_r($reportStats);

//  print_r($adGroupStats);

//  die;
//  print_r($argv);

  $i = 0;

  $campaign_ids = $argv;//array('6028601278720','6028411240920',);
  foreach ($reportStats as $report_stat){
	if (!in_array($report_stat['id'],$campaign_ids)){
		unset($reportStats[$i]);
	}else{
        }
	$i = $i+1;
  }

  $reportStats = array_values($reportStats);

//  print_r($reportStats);

  $i=0;
  $adset_ids = array();
  foreach ($reportStats as $report_stat){
	$adset_ids[$i] = $report_stat['adcampaigns']['data'][0]['id'];
        $i = $i+1;
  }

  $i=0;
  foreach ($adGroupStats as $report_stat){
	if (!in_array($report_stat['id'],$adset_ids)){
                unset($adGroupStats[$i]);
        }else{
           $key = array_search($report_stat['id'], $adset_ids);
	   $reportStats[$key]['adcampaigns_data'] = $adGroupStats[$i];
        }
        $i = $i+1;
  }

  $adGroupStats = array_values($adGroupStats);
//  print_r($adGroupStats);

//  $reportStats = $adaccount->getAdCampaignStats();
  print_r(json_encode($reportStats));
 }

public function readAdCampaign($argv){
  if(count($argv) == 0) {return 0;}
 // print_r($argv['id']);
  $adcampaign = new AdSet($argv['id'], self::$account_id);
  $fields = array('bid_info','budget_remaining','bid_type','campaign_group_id','campaign_status','created_time','creative_sequence','campaign_schedule','daily_budget','end_time','external_bid','inflation','lifetime_budget','promoted_object','start_time','targeting','topline_id','activities','name','adcreatives','stats', 'adgroups');

  $adcampaign_data = $adcampaign->read($fields, array())->getData();
  if (isset($adcampaign_data['adgroups']['data'])){
  $adgroup_data = self::readAdGroup($adcampaign_data['adgroups']['data'][0]['id']);
  $adcampaign_data['adgroups'] = $adgroup_data;
  }
  print_r(json_encode($adcampaign_data));
 }

public function readAdGroup($adgroup_id){
//  if(count($argv) == 0) {return 0;}
//  print_r($argv['id']);
  $adgroup = new AdGroup($adgroup_id, self::$account_id);
  $fields = array('adgroup_review_feedback','adgroup_status','bid_info','bid_type','campaign_group_id','campaign_id','created_time','creative','creative_ids','holdout_start','id','name','objective','priority','show_end','show_start','targeting','tracking_and_conversion_with_defaults','tracking_specs','updated_time','adcreatives');
  return $adgroup->read($fields, array())->getData();
 }


public function readGetParams($argv){
  if(count($argv) == 0) {return 0;}
//  print_r($argv);
  if($argv['type'] == 'adcampaign'){
	ObjectDeletion::readAdCampaign($argv);
  }else if($argv['type'] == 'adgroup'){
        ObjectDeletion::readAdGroup($argv);
  }

}

}

ObjectDeletion::init();
ObjectDeletion::api_init();
ObjectDeletion::getAdCampaignStats($_POST);
ObjectDeletion::readGetParams($_GET);

//ObjectDeletion::readAdCampaign($_POST);



/*ObjectDeletion::deleteAdGroup(array(
  'ad_group_id' => '6028369967720'
));
*/
/*
/ObjectDeletion::deleteAdCreative(array(
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
