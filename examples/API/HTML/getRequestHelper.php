<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

use FacebookAds\Object\Page;


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
use FacebookAds\Object\Fields\AdPreviewFields;

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

class getRequestHelper{

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
//  echo "---------------------------------------------------------------------------";
  print_r($campaigns->getResponse()->getContent()['data']);
  return $campaigns->getResponse()->getContent()['data'];
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

  public function getAdSets(){
  $adaccount = new AdAccount(self::$account_id);
  $fields = array(
        'name',
  );
  $adsets = $adaccount->getAdSets($fields, array());
//echo "---------------------------------------------------------------------------"."<br>";
  print_r($adsets->getResponse()->getContent()['data']);
  return $adsets->getResponse()->getContent()['data'];
 }

 public function getAdPagePosts($page_id){
  $page = new Page($page_id);
  $fields = array('id','name',);
  $posts = $page->getPages($fields,array());
//  print_r($posts->getResponse()->getContent()['data']);
  echo json_encode($posts->getResponse()->getContent()['data']);
  return $posts->getResponse()->getContent()['data'];
 }

 public function getConnectionObjects($argv){
//  print_r($argv);
  $adaccount = new AdAccount(self::$account_id);
  $fields = array(
        'name',
        'url',
        'type',
  );
  $connectionobjects1 = $adaccount->getConnectionObjects($fields, array());
  $connectionobjects = $connectionobjects1->getResponse()->getContent()['data'];
  $ii = 0;
  foreach ($connectionobjects as $connectionobject){
	if ($connectionobject['type'] != 1 ){
		unset($connectionobjects[$ii]);
  	}else{
//	   echo $connectionobject['id']."  ".$ii."\n";
	   if (!in_array($connectionobject['id'], $argv)){
                unset($connectionobjects[$ii]);
	   }
	}
	$ii = $ii + 1;
  }

//  echo "---------------------------------------------------------------------------";
//  print_r($connectionobjects);
  echo json_encode($connectionobjects);
  return $connectionobjects1->getResponse()->getContent()['data'];
 }

 public function getAdPreviews($creative_id, $ad_format){
  $creative = new AdCreative($creative_id, self::$account_id);
  $previews = $creative->getAdPreviews(array(), array(AdPreviewFields::AD_FORMAT => $ad_format));

  print_r($previews->getResponse()->getContent()['data'][0]['body']);

 }

 public function getAdVideos($video_id){
  $video = new AdVideo($video_id, self::$account_id);
  echo "<br>".$video->id."<br>";
  $video_thumbnail = $video->read(array('thumbnails'), array());
  print_r($video_thumbnail->getResponse()->getContent());
 }

 public function parse_arguments($argv){
  if(count($argv) == 0) {return 0;}
  if ($argv['type'] == 'adcampaign') {
	getRequestHelper::getAdCampaigns(); 
  }else if($argv['type'] == 'customaudiences'){
	getRequestHelper::getCustomAudiences();	
  }else if($argv['type'] == 'adsets'){
        getRequestHelper::getAdSets();
  }else if($argv['type'] == 'pageposts'){
        getRequestHelper::getAdPagePosts($argv['page_id']);
  }else if($argv['type'] == 'pages'){
  //      getRequestHelper::getConnectionObjects($argv);
  }else if($argv['type'] == 'adpreviews'){
        getRequestHelper::getAdPreviews($argv['creative_id'], $argv['ad_format']);
  }else if($argv['type'] == 'getThumbnailImages'){
	print_r($argv);
        getRequestHelper::getAdVideos($argv['video_id']);
  }
 }

 public function parse_argumentsPost($argv){
	if(count($argv) == 0) {return 0;}
        getRequestHelper::getConnectionObjects($argv);
 }

}
getRequestHelper::init();
getRequestHelper::api_init();
getRequestHelper::parse_arguments($_GET);
//print_r($_POST);
getRequestHelper::parse_argumentsPost($_POST);


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
