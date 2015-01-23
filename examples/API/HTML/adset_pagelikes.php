<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

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
use FacebookAds\Object\ObjectStory\VideoData;
use FacebookAds\Object\Fields\ObjectStory\VideoDataFields;
use FacebookAds\Object\ObjectStory\OfferData;
use FacebookAds\Object\Fields\ObjectStory\OfferDataFields;
use FacebookAds\Object\ObjectStory\PhotoData;
use FacebookAds\Object\Fields\ObjectStory\PhotoDataFields;
use FacebookAds\Object\ObjectStory\TextData;
use FacebookAds\Object\Fields\ObjectStory\TextDataFields;
use FacebookAds\Object\Values\CallToActionTypes;

use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;

use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
 
use FacebookAds\Object\AdVideo;
use FacebookAds\Object\Fields\AdVideoFields;

class adsethelper{

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
  $config_path = __DIR__ . '/../../config1.php';
  echo $config_path;
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
  define('SDK_DIR', __DIR__ . '/../../..'); // Path to the SDK directory
  $loader = include SDK_DIR.'/vendor/autoload.php';
  Api::init(self::$app_id, self::$app_secret, self::$access_token);

 }

 public function getAdCampaigns(){
  $adaccount = new AdAccount(self::$account_id);
  $fields = array(
	'name',
  );  
  $campaigns = $adaccount->getAdCampaigns($fields, array());
  echo "---------------------------------------------------------------------------";
  print_r($campaigns->getResponse()->getContent()['data']);
  return $campaigns->getResponse()->getContent()['data'];
 }

 public function getConnectionObjects(){
  $adaccount = new AdAccount(self::$account_id);
  $fields = array(
        'name',
	'url',
	'type',
  );
  $connectionobjects = $adaccount->getConnectionObjects($fields, array());
  echo "---------------------------------------------------------------------------";
  print_r($connectionobjects->getResponse()->getContent()['data']);
  return $connectionobjects->getResponse()->getContent()['data'];
 }


 public function getCustomAudiences(){
  $adaccount = new AdAccount(self::$account_id);
  $fields = array(
        'name',
  );
  $customaudiences = $adaccount->getCustomAudiences($fields, array());
  print_r($customaudiences->getResponse()->getContent()['data']);
  return $customaudiences->getResponse()->getContent()['data'];
 }

 public function createAdSetHtml(){
  echo "<html><body>"."\n";
  $s = "<form action=\"../../ObjectCreation.php\" method=\"post\">"."\n";
  $s = $s."<input type=\"hidden\" name=\"type\" value=\"adset\">"."\n";
  $s = $s."<input type=\"hidden\" name=\"adset_type\" value=\"promotion_page_likes\">"."\n";
  $s = $s."Adset name : "."\n";
  $s = $s."<input type=\"text\" name=\"adsetname\"><br>"."\n";
  $s = $s."Adset status"."\n";
  $s = $s."<input type=\"radio\" name=\"status\" value=\"ACTIVE\" checked>ACTIVE"."\n";
  $s = $s."<input type=\"radio\" name=\"status\" value=\"PAUSED\" >PAUSED<br>"."\n";
  $s = $s."Select Campaign"."\n";
  $s = $s."<select name=\"campaign_id\">"."\n";
  $campaigns = self::getAdCampaigns();
  foreach ($campaigns as $campaign){
	$s = $s. "<option value=".$campaign['id'].">".$campaign['name']."</option>"."\n";
  }
  $s = $s."</select> <br>"."\n";

  $s = $s."Select Page to promote"."\n";
  $s = $s."<select name=\"page_id\">"."\n";
  $connectionobjects = self::getConnectionObjects();
  foreach ($connectionobjects as $connectionobject){
	if ($connectionobject['type'] == 1){
        	$s = $s. "<option value=".$connectionobject['id'].">".$connectionobject['name']."</option>"."\n";
	}
  }
  $s = $s."</select> <br>"."\n";

  $s = $s."Daily Budget"."\n";
  $s = $s."<input type=\"text\" name=\"daily_budget\"><br>"."\n";
  $s = $s."Start Time (in s)"."\n";
  $s = $s."<input type=\"text\" name=\"start_time\"><br>"."\n";
  $s = $s."End Time (in s)"."\n";
  $s = $s."<input type=\"text\" name=\"end_time\"><br>"."\n";
  $s = $s."Select Bid type"."\n";
  $s = $s."<select name=\"bid_type\">"."\n";
  $s = $s. "<option value=".BidTypes::BID_TYPE_ABSOLUTE_OCPM.",ACTIONS".">Page Likes</option>"."\n";
  $s = $s. "<option value=".BidTypes::BID_TYPE_CPC.">Clicks </option>"."\n";
  $s = $s."</select> <br>"."\n";
  $s = $s."Bid value"."\n";
  $s = $s."<input type=\"text\" name=\"bid_value\"><br>"."\n";

  $s = $s."Select custom audience"."\n";
  $s = $s."<select name=\"custom_audience\">"."\n";
  $customaudiences = self::getCustomAudiences();
  foreach ($customaudiences as $customaudience){
        $s = $s. "<option value=".$customaudience['id'].",".$customaudience['name'].">".$customaudience['name']."</option>"."\n";
  }
  $s = $s."</select> <br>"."\n";
  $s = $s."<br><input type=\"submit\" value=\"Submit\">"."\n";
  echo $s;
  echo "</html></body>";
 }

 public function createAdSet($adSetCreationParams){
  $adset = new AdSet(null, self::$account_id);

  $adsetFieldArray = array(
        AdSetFields::NAME => $adSetCreationParams['name'],
        AdSetFields::CAMPAIGN_GROUP_ID => $adSetCreationParams['campaign_id'],
        AdSetFields::CAMPAIGN_STATUS => $adSetCreationParams['campaign_status'],
        AdSetFields::TARGETING => $adSetCreationParams['targeting'],
        AdSetFields::BID_TYPE => $adSetCreationParams['bid_type'],
        AdSetFields::BID_INFO => $adSetCreationParams['bid_info'],
  );
  if (array_key_exists('daily_budget', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::DAILY_BUDGET] = $adSetCreationParams['daily_budget'];
  }else if (array_key_exists('lifetime_budget', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::LIFETIME_BUDGET] = $adSetCreationParams['lifetime_budget'];
  }
  if (array_key_exists('start_time', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::START_TIME] = $adSetCreationParams['start_time'];
  }
  if (array_key_exists('end_time', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::END_TIME] = $adSetCreationParams['end_time'];
  }else {
      if (array_key_exists('lifetime_budget', $adSetCreationParams)){
             throw new \Exception(
                'You must set end time for lifetime');
        }
  }
  $adset->setData($adsetFieldArray);
  $adset->create();
  echo 'AdSet  ID: '. $adset->id . "\n";
  return $adset; 
 }

 public function createTargetingAudience($locationSpec, $customAudSpec){
  $targeting = new TargetingSpecs();
  $targeting->{TargetingSpecsFields::GEO_LOCATIONS}
      	= $locationSpec;
  $targeting->{TargetingSpecsFields::CUSTOM_AUDIENCES}
      	= $customAudSpec;
  return $targeting;
  }

 public function parse_arguments($argv){
  if ($argv['type'] == 'adcampaign') {
	$adcampaign_params = array(
 	 'name' => $argv['adcampaignname'],
	 'objective' => $argv['objective'],
	 'campaign_group_status' => $argv['status'],
	 'buying_type' => $argv['buying_type'],
	);

   adsethelper::createAdCampaign($adcampaign_params); 
  }

 }
}
adsethelper::init();
adsethelper::api_init();
adsethelper::createAdSetHtml();
//adset::parse_arguments($_POST);

/*
adset::createAdGroup(array(
  'name' => 'Roy trailer ad',
  'creative_array' => array('creative_id' => '6028413481720'), 
  'adgroup_status' => AdGroup::STATUS_PAUSED,
  'adcampaign_id' => '6028415978720'
));
*/
/*
adset::createAdCreative(array(
  'name' => 'Roy creative',
  'body' => 'Roy creative desc',
  'call_to_action_type' => 'NO_BUTTON',
  'image_location' =>  '../../Desktop/Roy-Movie-Official-Trailer.jpg',
  'call_to_action' => '{\'type\':\'NO_BUTTON\',\'value\':{\'link\':\'https://www.youtube.com/watch?v=ahz3kTiGo3s/\'}}',
  'caption' => 'Roy trailer',
  'ad_description' => 'Presenting the TRAILER of Bhushan Kumar\'s \'Roy\', a T-Series Film, Directed by Vikramjit Singh, Produced by Divya Khosla Kumar, Bhushan Kumar and Krishan Kumar Co-Produced by Ajay Kapoor, starring Ranbir Kapoor in a Dynamic Role, Arjun Rampal and Jacqueline Fernandez',
  'link' => 'https://www.youtube.com/watch?v=ahz3kTiGo3s/',
  'message' => 'The trailer of Ranbir Kapoor\'s latest film \'ROY\' is here. Watch it now! ',
  'ad_name' => 'Exclusive: \'Roy\' Trailer ',
  'page_id' => '476563889048827',
//  'video_location' => '../test/misc/video.mp4'
));
*/

/*
adset::createAdSet(array(
  'name' => 'Roy AdSet Trailer',
  'campaign_id' => '6028411240920',
  'campaign_status' => AdSet::STATUS_PAUSED,
  'daily_budget' => '50000',
  'targeting' => adset::createTargetingAudience(
	array('countries' => array('IN') //'regions': [{'key':'region_key'}] // ),
        array(
        array(
               'id' => 6028415055920,
               'name' => 'Lookalike \(IN, 2%\) - 70K emails'),)),
  'bid_type' => BidTypes::BID_TYPE_CPC,
  'bid_info' => array(AdGroupBidInfoFields::CLICKS => 300),
  'start_time' => (new \DateTime("+1 day"))->format(\DateTime::ISO8601),
  'end_time' => (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
));
*/
/*
adset::createAdCampaign(array(
  'name' => 'Test Campaign',
  'objective' => AdObjectives::WEBSITE_CLICKS,
  'campaign_group_status' => AdCampaign::STATUS_PAUSED,
  'buying_type' => AdBuyingTypes::AUCTION,
));
*/

?>
