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
 
class ObjectUpdation{

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

 public function updateAdCampaign($adCampaignParams){
  $campaign  = new AdCampaign($adCampaignParams['ad_campaign_id'], self::$account_id);
  $campaign->setData(array(
  	AdCampaignFields::NAME => $adCampaignParams['name'],
	AdCampaignFields::OBJECTIVE => $adCampaignParams['objective'],
	AdCampaignFields::STATUS => $adCampaignParams['campaign_group_status'],
	AdCampaignFields::BUYING_TYPE => $adCampaignParams['buying_type'],
  ));
  $campaign->update();
  echo "Campaign ID:" . $campaign->id . "\n";
  return $campaign;
 }

 public function updateAdSet($adSetCreationParams){
  $adset = new AdSet($adSetCreationParams['ad_set_id'], self::$account_id);

  $adsetFieldArray = array(
//        AdSetFields::NAME => $adSetCreationParams['name'],
//        AdSetFields::CAMPAIGN_GROUP_ID => $adSetCreationParams['campaign_id'],
//        AdSetFields::CAMPAIGN_STATUS => $adSetCreationParams['campaign_status'],
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
  $adset->update();
  echo 'AdSet  ID: '. $adset->id . "\n";
  return $adset; 
 }

 public function createTargetingAudience($locationSpec, $customAudSpec){
  $targeting = new TargetingSpecs();
  $targeting->{TargetingSpecsFields::GEO_LOCATIONS}
      	= $locationSpec;
  $targeting->{TargetingSpecsFields::CUSTOM_AUDIENCES}
      	= $customAudSpec;
//  $targeting->{TargetingSpecsFields::EXCLUDED_CUSTOM_AUDIENCES}
//        = $customAudExclusion;
  return $targeting;
  }

 public function updateAdCreative($creativeCreationParams){
  $creative = new AdCreative($creativeCreationParams['ad_creative_id'], self::$account_id);
  
  $creativeCreationParams['image_hash'] = self::uploadImage($creativeCreationParams['image_location'])->hash;
  $creativeCreationParams['object_story_spec'] = self::createObjectSpec($creativeCreationParams);
  
  $creative->setData(array(
	AdCreativeFields::NAME => $creativeCreationParams['name'],
  	AdCreativeFields::BODY => $creativeCreationParams['body'],
  	AdCreativeFields::IMAGE_HASH => $creativeCreationParams['image_hash'],
  	AdCreativeFields::OBJECT_STORY_SPEC => $creativeCreationParams['object_story_spec'],
  	AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  ));

  $creative->create();
  echo 'Creative ID: '.$creative->id . "\n";
  return $creative;
 }

 public function uploadImage($image_location){
  $image = new AdImage(null, self::$account_id);
  $image->{AdImageFields::FILENAME} = $image_location;
  $image->create();
  echo 'Image Hash: '.$image->hash . "\n";
  return $image;
 }

 public function createObjectSpec($creativeCreationParams){
  $linkdata = new LinkData(null, self::$account_id);
  $linkdata->setData(array(
   	LinkDataFields::CALL_TO_ACTION => $creativeCreationParams['call_to_action'],
   	LinkDataFields::CAPTION=>$creativeCreationParams['caption'],
   	LinkDataFields::DESCRIPTION=>$creativeCreationParams['ad_description'],
   	LinkDataFields::IMAGE_HASH=>$creativeCreationParams['image_hash'],
   	LinkDataFields::LINK=>$creativeCreationParams['link'],
   	LinkDataFields::MESSAGE=>$creativeCreationParams['message'],
   	LinkDataFields::NAME=>$creativeCreationParams['ad_name'],
  ));

  $objectstoryspec = new ObjectStorySpec(null, self::$account_id);
  $objectstoryspec->setData(array(
   	ObjectStorySpecFields::LINK_DATA=>$linkdata,
   	ObjectStorySpecFields::PAGE_ID=>$creativeCreationParams['page_id'],
  ));
  return $objectstoryspec;
 }

 public function updateAdGroup($adgroupCreationParams){
  $adgroup = new AdGroup($adgroupCreationParams['ad_group_id'], self::$account_id);
  $adgroupData = array(
    AdGroupFields::CREATIVE => $adgroupCreationParams['creative_array'],
    AdGroupFields::NAME => $adgroupCreationParams['name'],
    AdGroupFields::ADGROUP_STATUS =>  $adgroupCreationParams['adgroup_status'],
    AdGroupFields::CAMPAIGN_ID => $adgroupCreationParams['adcampaign_id'],
  );

  if (array_key_exists('bid_info', $adgroupCreationParams)){
	$adgroupCreationParams[AdGroupFields::BID_TYPE] = $adgroupCreationParams['bid_type'];
	$adgroupCreationParams[AdGroupFields::BID_INFO] = $adgroupCreationParams['bid_info'];
  }
  $adgroup->setData($adgroupData);
  $adgroup->update();
 }

}
ObjectUpdation::init();
ObjectUpdation::api_init();
/*ObjectUpdation::createAdGroup(array(
  'name' => 'nestle_ad',
  'creative_array' => array('creative_id' => '6028369949520'), 
  'adgroup_status' => AdGroup::STATUS_PAUSED,
  'adcampaign_id' => '6028369967720'
));
*/
/*
ObjectUpdation::updateAdCreative(array(
  'ad_creative_id' => null,
  'name' => 'nestle_adcreative_ew',
  'body' => 'nestle_ad_desc_eew',
  'call_to_action_type' => 'LEARN_MORE',
  'image_location' =>  '../../Desktop/Castiel.jpg',
  'call_to_action' => '{\'type\':\'LEARN_MORE\',\'value\':{\'link\':\'https://nestle.in\'}}',
  'caption' => 'Call',
  'ad_description' => 'Nestle description call',
  'link' => 'http://nestle.in/',
  'message' => 'Nestle message Call',
  'ad_name' => 'Nestle Call',
  'page_id' => '761120863969761',
));
*/
/*
ObjectUpdation::updateAdSet(array(
  'ad_set_id' => '6028413247720',
//  'name' => 'adset1',
//  'campaign_id' => '6028317881120',
//  'campaign_status' => AdSet::STATUS_ACTIVE,
  'daily_budget' => '50000',
//  'lifetime_budget' => '40000',
  'start_time' => (new \DateTime("+1 day"))->format(\DateTime::ISO8601),
  'end_time' => (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
  'targeting' => ObjectUpdation::createTargetingAudience(
	array('countries' => array('IN')), 
        array(
        array(
               'id' => 6028415055920,
               'name' => 'Lookalike \(IN, 2%\) - 70K emails'),)),
          /*array(
                'id' => 6028246387120,
                'name' => 'custom_ad_e2'),*/
/*
	array(
      	  array(
        	'id' => 6028240949520,
	        'name' => 'My Custom Audiece'),
      	  array(
        	'id' => 6028233327320,
	        'name' => 'Custom'),)
    	
  'bid_type' => BidTypes::BID_TYPE_CPC,
  'bid_info' => array(AdGroupBidInfoFields::CLICKS => 200),
));
*/

ObjectUpdation::updateAdCampaign(array(
  'name' => 'campaign_cr1',
  'ad_campaign_id' => '6028463426920',
  'objective' => AdObjectives::LOCAL_AWARENESS,
  'campaign_group_status' => AdCampaign::STATUS_PAUSED,
  'buying_type' => AdBuyingTypes::MIXED,
));

?>
