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

class ObjectCreation{

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

 public function createAdCampaign($adCampaignParams){
  $campaign  = new AdCampaign(null, self::$account_id);
  $campaign->setData(array(
  	AdCampaignFields::NAME => $adCampaignParams['name'],
	AdCampaignFields::OBJECTIVE => $adCampaignParams['objective'],
	AdCampaignFields::STATUS => $adCampaignParams['campaign_group_status'],
	AdCampaignFields::BUYING_TYPE => $adCampaignParams['buying_type'],
  ));
  $campaign->create();
  $campaign_read = $campaign->read(array("name",), array());
  // "" . $campaign->id . "_" .$campaign_read->name. "\n";
  $data = array("campaign_id" => $campaign->id,"campaign_name" => $campaign->id . "_" .$campaign_read->name);
//  print_r($data);
  echo json_encode($data);
  return $data;
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
 

  if ($adSetCreationParams['adset_type'] == 'promotion_page_likes'){
	$adsetFieldArray['promoted_object'] = "{\"page_id\": ".$adSetCreationParams['promotion_object_id']."}";
  }else if ($adSetCreationParams['adset_type'] == 'promotion_app_installs'){
	$ss = explode(",", $adSetCreationParams['promotion_object_id']);
	$adsetFieldArray['promoted_object'] = "{\"application_id\": \"".$ss[0]."\", \"object_store_url\" : \"".$ss[1]."\"}";
  }
  $adset->setData($adsetFieldArray);
  $adset->create();
  echo $adset->id;
  return $adset; 
 }

 public function createTargetingAudience($locationSpec, $customAudSpec){
  $targeting = new TargetingSpecs();
  $targeting->{TargetingSpecsFields::GEO_LOCATIONS}
      	= $locationSpec;
  $targeting->{TargetingSpecsFields::CUSTOM_AUDIENCES}
      	= $customAudSpec;
//  $targeting->{TargetingSpecsFields::USER_OS} = array('Android_ver_4.0_and_above',);
  return $targeting;
  }

 public function createAdCreative($creativeCreationParams){
  $creative = new AdCreative(null, self::$account_id);
  $adaccount = new AdAccount(self::$account_id);

/*  if($creativeCreationParams['image_location'] != null){
	  $creativeCreationParams['image_hash'] = self::uploadImage($creativeCreationParams['image_location'])->hash;
  }
*/
  if ($creativeCreationParams['spec_type'] != "post_based" && $creativeCreationParams['spec_type'] != "page_likes"
	&& $creativeCreationParams['spec_type'] != 'promotion_app_installs'){
 
//  $creativeCreationParams['object_story_spec'] = self::createObjectSpec($creativeCreationParams);

  if($creativeCreationParams['video_location'] != null){
  	$creativeCreationParams['video_id'] = self::uploadVideo($creativeCreationParams['video_location'], 
						$creativeCreationParams['name']."_video")->id;
  }else{
//  	$creativeCreationParams['video_id'] = '865199283531087';
  }

//  echo  $creativeCreationParams['call_to_action']."<br>\n";
  $link_array_fields = array(
      LinkDataFields::CALL_TO_ACTION => $creativeCreationParams['call_to_action'],
        LinkDataFields::CAPTION=>$creativeCreationParams['caption'],
        LinkDataFields::DESCRIPTION=>$creativeCreationParams['ad_description'],
        LinkDataFields::IMAGE_HASH=>$creativeCreationParams['image_hash'],
        LinkDataFields::LINK=>$creativeCreationParams['link'],
        LinkDataFields::MESSAGE=>$creativeCreationParams['message'],
        LinkDataFields::NAME=>$creativeCreationParams['ad_header'],
  );
  
  $video_array_fields = array(
        VideoDataFields::DESCRIPTION=>$creativeCreationParams['ad_description'],
	VideoDataFields::VIDEO_ID=>$creativeCreationParams['video_id'],
  );

  $photo_array_fields = array(
        PhotoDataFields::IMAGE_HASH=>$creativeCreationParams['image_hash'],
        PhotoDataFields::CAPTION=>$creativeCreationParams['caption'],
  );

  $text_array_fields = array(
        TextDataFields::MESSAGE=>$creativeCreationParams['message'],
  );


  $offer_array_fields = array(
//	OfferDataFields::BARCODE_TYPE => 1,
//	OfferDataFields::BARCODE => '123456789012',
	OfferDataFields::CLAIM_LIMIT => 2,
	OfferDataFields::COUPON_TYPE => 'online_only',
	OfferDataFields::EXPIRATION_TIME => (new \DateTime("+2 week"))->format(\DateTime::ISO8601),
	'image_url' => 'https://fbcdn-creative-a.akamaihd.net/hads-ak-xpa1/t45.1600-4/10737133_6028413307920_1948919571_n.png',
	OfferDataFields::MESSAGE => 'Redeem offer msg',
	OfferDataFields::REMINDER_TIME => (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
	OfferDataFields::REDEMPTION_LINK => 'https://zapr.in/',
	OfferDataFields::REDEMPTION_CODE => '124432',	
	OfferDataFields::TITLE	=> 'Redeem Offer title',
  );

  if($creativeCreationParams['image_hash'] != null){
        $link_array_fields[LinkDataFields::IMAGE_HASH]=$creativeCreationParams['image_hash'];
  }else if($creativeCreationParams['image_url'] != null){
        $link_array_fields[LinkDataFields::PICTURE]=$creativeCreationParams['image_url'];
  }


  $object_spec_fields = array(
        ObjectStorySpecFields::LINK_DATA=>$link_array_fields,
//	ObjectStorySpecFields::VIDEO_DATA=>$video_array_fields,
//	ObjectStorySpecFields::OFFER_DATA=>$offer_array_fields,
//	ObjectStorySpecFields::PHOTO_DATA=>$photo_array_fields,
//	ObjectStorySpecFields::TEXT_DATA=>$text_array_fields,
        ObjectStorySpecFields::PAGE_ID=>$creativeCreationParams['page_id'],
  );

  if ($creativeCreationParams['spec_type'] == "video_views"){	
	if($creativeCreationParams['image_hash'] != null){
		$video_array_fields[VideoDataFields::IMAGE_HASH]=$creativeCreationParams['image_hash'];
	}else if($creativeCreationParams['image_url'] != null){
                $video_array_fields[VideoDataFields::IMAGE_URL]=$creativeCreationParams['image_url'];
        }
	
	$object_spec_fields[ObjectStorySpecFields::VIDEO_DATA] = $video_array_fields;
	unset($object_spec_fields[ObjectStorySpecFields::LINK_DATA]);
  }
 
  $creative_spec_fields = array(
        AdCreativeFields::NAME => $creativeCreationParams['name'],
        AdCreativeFields::BODY => $creativeCreationParams['body'],
        AdCreativeFields::IMAGE_HASH => $creativeCreationParams['image_hash'],
        AdCreativeFields::OBJECT_STORY_SPEC => $creativeCreationParams['object_story_spec'],
        AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  );

  $creative_spec_fields1 = array(
        AdCreativeFields::NAME => $creativeCreationParams['name'],
        AdCreativeFields::BODY => $creativeCreationParams['body'],
        AdCreativeFields::IMAGE_HASH => $creativeCreationParams['image_hash'],
        AdCreativeFields::OBJECT_STORY_SPEC => $object_spec_fields,
        AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  );

  }else if ($creativeCreationParams['spec_type'] == "post_based"){
  	$creative_spec_fields1 = array(
        AdCreativeFields::NAME => $creativeCreationParams['name'],
        AdCreativeFields::BODY => $creativeCreationParams['body'],
        AdCreativeFields::OBJECT_STORY_ID =>  $creativeCreationParams['object_story_id'],
        AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  );


  }else if ($creativeCreationParams['spec_type'] == "page_likes"){
	$creative_spec_fields1 = array(
        AdCreativeFields::NAME => $creativeCreationParams['name'],
        AdCreativeFields::BODY => $creativeCreationParams['body'],
        AdCreativeFields::IMAGE_HASH => $creativeCreationParams['image_hash'],
        AdCreativeFields::OBJECT_ID =>  $creativeCreationParams['object_id']['page_id'],
        AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  );
  }else if ($creativeCreationParams['spec_type'] == "promotion_app_installs"){
	$creative_spec_fields1 = array(
        AdCreativeFields::NAME => $creativeCreationParams['name'],
        AdCreativeFields::BODY => $creativeCreationParams['body'],
        AdCreativeFields::OBJECT_ID =>  $creativeCreationParams['object_id']['application_id'],
        AdCreativeFields::CALL_TO_ACTION_TYPE=> $creativeCreationParams['call_to_action_type'],
  );
  }
  $creative->setData($creative_spec_fields1);

  $fields = array(
	'creative' => $creative_spec_fields1,
  );

  $params = array(
	'creative' => $creative_spec_fields1,
	'ad_format' => $creativeCreationParams['ad_format'],
  );

//  print_r($creative_spec_fields1);

  $previews = $adaccount->getAdPreviews(array(),$params);
//  echo "---------------------------------------------------------------------------------------------"."\n";
//  print_r($previews->getResponse()->getContent()['data'][0]['body']);

  if($creativeCreationParams['preview'] == "true"){
        print_r($previews->getResponse()->getContent()['data'][0]['body']);
  	die;
  }

  $creative->create();
  //echo $creative->id . "\n";
  return $creative;

 }

 public function uploadVideo($video_name){
  $video = new AdVideo(null, self::$account_id);
  $video_location = ObjectCreation::uploadVideoToLocal();
  $video->{AdVideoFields::SOURCE} = $video_location[0];//ObjectCreation::uploadVideoToLocal();
  $video->{AdVideoFields::NAME} = substr($video_location[1], 0, strlen($video_location[1])-4);
  $video->create();
  sleep(15);
//  $video_thumbnail = $video->read(array('thumbnails'), array());
  while(1){
  $video = new AdVideo($video->id, self::$account_id);
  $video_thumbnail = $video->read(array('thumbnails',), array());
  $video_thumb_array = $video_thumbnail->getData();
  if (count($video_thumb_array['thumbnails']) > 3){
  	print_r(json_encode($video_thumb_array));
	break;
     }
  }
  return $video;
 }

 public function uploadImage($image_location){
  $image = new AdImage(null, self::$account_id);
  $image->{AdImageFields::FILENAME} = $image_location;
  if (!is_readable($image_location)) {
       throw new \RuntimeException("Could not read image");
  }
  $image->create();
  echo $image->hash;
  return $image;
 }

 public function createAdGroup($adgroupCreationParams){
  $adgroup = new AdGroup(null, self::$account_id);
  $adgroupData = array(
    AdGroupFields::CREATIVE => $adgroupCreationParams['creative_array'],
    AdGroupFields::NAME => $adgroupCreationParams['name'],
    AdGroupFields::ADGROUP_STATUS =>  $adgroupCreationParams['adgroup_status'],
    AdGroupFields::CAMPAIGN_ID => $adgroupCreationParams['adcampaign_id'],
    AdGroupFields::OBJECTIVE => $adgroupCreationParams['objective'],
  );

  if (array_key_exists('bid_info', $adgroupCreationParams)){
	$adgroupCreationParams[AdGroupFields::BID_TYPE] = $adgroupCreationParams['bid_type'];
	$adgroupCreationParams[AdGroupFields::BID_INFO] = $adgroupCreationParams['bid_info'];
  }
  $adgroup->setData($adgroupData);
  $adgroup->create();	
  echo $adgroup->id;
 }


 public function updateAdSetAudience($adSetUpdateParams){
  $adset = new AdSet($adSetUpdateParams['id'], self::$account_id);
  $adset_targeting = $adset->read(array('targeting'), array())->getData();

  $custAudIds = $adSetUpdateParams['custAudIds'];
  foreach ($custAudIds as $custAudId){
        array_push($adset_targeting['targeting']['custom_audiences'], array('id' => $custAudId));
  }

  $adsetFields = array(
        AdSetFields::TARGETING => $adset_targeting['targeting'],
  );
  $adset->setData($adsetFields);
  print_r($adset->update()->getData()['id']);
 }

 public function updateAdSet($adSetCreationParams){
  $adset = new AdSet($adSetCreationParams['ad_set_id'], self::$account_id);

  $adsetFieldArray = array();
  if (array_key_exists('daily_budget', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::DAILY_BUDGET] = $adSetCreationParams['daily_budget'];
  }else if (array_key_exists('lifetime_budget', $adSetCreationParams)){
        $adsetFieldArray[AdSetFields::LIFETIME_BUDGET] = $adSetCreationParams['lifetime_budget'];
        if (array_key_exists('pacing_type', $adsetCreationParams)){
                $adsetFieldArray['pacing_type'] = array('day_parting');
                $adsetFieldArray['campaign_schedule'] = array(
                                                array('start_minute' => 540, 'end_minute' => 720, 'days' => array(1,2,3,4,5)),
                                                array('start_minute' => 540, 'end_minute' => 720, 'days' => array(0,6))
                                                );
        }
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

  if (array_key_exists('bid_type', $adSetCreationParams)){
        $adSetFieldsArray[AdSetFields::BID_TYPE] = $adSetCreationParams['bid_type'];
  }

  if (array_key_exists('bid_info', $adSetCreationParams)){
        $adSetFieldsArray[AdSetFields::BID_TYPE] = $adSetCreationParams['bid_info'];
  }

  $adset->setData($adsetFieldArray);
  $adset->update();
  echo 'AdSet  ID: '. $adset->id . "\n";
  return $adset;
 }

 public function parse_arguments($argv){
//  print_r($argv1);
//  die;
  if(count($argv) == 0) {return 0;}
  $argv = $argv['data']['Advertiser'];
//  die;
  if ($argv['type'] == 'adcampaign') {
	$adcampaign_params = array(
 	 'name' => $argv['name'],
	 'objective' => $argv['objectives'],
	 'campaign_group_status' => $argv['status'],
	 'buying_type' => $argv['buying_type'],
	);

   ObjectCreation::createAdCampaign($adcampaign_params); 
  }else if ($argv['type'] == 'adset') {
	$bidtypeArray = explode(",", $argv['bid_type']);
//	echo $bidtypeArray[0]."<br>";
	$adset_params = array(
	 'name' => $argv['adsetname'],
	 'campaign_status' => $argv['status'],
	 'campaign_id' => $argv['campaign_ids'],
	 'daily_budget' => $argv['daily_budget'],
	 'start_time' => $argv['start_time'],
	 'end_time' => $argv['end_time'],
	 'bid_type' => $bidtypeArray[0],
         'promotion_object_id' => $argv['promotion_object_id'],
	 'adset_type' => $argv['adset_type'],
//	 'name' => $argv['bid_value'],
//	 'targeting' => $argv['custom_audience'],
        );

   	if($adset_params['adset_type'] == null){
		$adset_params['adset_type'] = "website_clicks";
	}

	if ($bidtypeArray[0] == BidTypes::BID_TYPE_ABSOLUTE_OCPM){
		if ($bidtypeArray[1] == 'ACTIONS'){
			$adset_params['bid_info'] = array(AdGroupBidInfoFields::ACTIONS => intval($argv['bid_value']));
		}else if ($bidtypeArray[1] == 'REACH'){
                        $adset_params['bid_info'] = array(AdGroupBidInfoFields::REACH => intval($argv['bid_value']));
                }
	}else if ($bidtypeArray[0] == BidTypes::BID_TYPE_CPC){
		$adset_params['bid_info'] = array(AdGroupBidInfoFields::CLICKS => intval($argv['bid_value']));
  	}else if ($bidtypeArray[0] == BidTypes::BID_TYPE_CPM){
                $adset_params['bid_info'] = array(AdGroupBidInfoFields::IMPRESSIONS => intval($argv['bid_value']));
        }
//	$customaud = explode(",", $argv['custom_audience']);
	$adset_params['targeting'] = ObjectCreation::createTargetingAudience(
        				array('countries' => array('IN')), //'regions': [{'key':'region_key'}] // ),
				        array(
				        array(
				               'id' => $argv['audience_ids'])
					     ));
//	print_r($adset_params);
//	die;

   ObjectCreation::createAdSet($adset_params);
  }else if ($argv['type'] == 'adcreative') {
	$creativeparams = array(
	 'name' => $argv['adcreativename'],
	 'body' => $argv['adcreativename'],
//	 'campaign_id' => $argv['adset_id'],
	 'call_to_action_type' => $argv['call_to_action_type'],
	 'image_location' => $argv['image_location'],
	 'call_to_action' => '{\'type\':'.$argv['call_to_action_type'].',\'value\':{\'link\':'.$argv['link'].'}}',
  	 'caption' => $argv['caption'],
	 'ad_description' => $argv['ad_description'],
         'link' => $argv['link'],
	 'message' => $argv['message'],
	 'ad_header' => $argv['ad_header'],
	 'page_id' => $argv['page_id'],
 	 'ad_format' => $argv['ad_format'],
	);
//	print_r($creativeparams);
	ObjectCreation::createAdCreative($creativeparams);
  }else if ($argv['type'] == 'adgroup') {
//	print_r($argv);
//	print_r($_FILES);
	//die;
	$adgroup_etc_params = ObjectCreation::getObjectiveAndObjectID($argv['adset_ids']);
//	print_r($adgroup_etc_params);
        $creativeparams = array(
         'name' => $argv['adcreativename'],
         'body' => $argv['adcreativename'],
	 'spec_type' =>  $argv['spec_type'],
//       'campaign_id' => $argv['adset_id'],
         'call_to_action_type' => $argv['call_to_action_type'],
//         'image_location' => ObjectCreation::uploadImageToLocal(),//$argv['image_location'],
         'call_to_action' => '{\'type\':\''.$argv['call_to_action_type'].'\',\'value\':{\'link\':\''.$argv['link'].'\'}}',
 	 'caption' => $argv['caption'],
         'ad_description' => $argv['ad_description'],
         'link' => $argv['link'],
         'message' => $argv['message'],
         'ad_header' => $argv['ad_header'],
         'page_id' => $argv['page_ids'],
         'ad_format' => $argv['ad_format'],
	 'object_story_id' => $argv['object_story_id'],
	 'object_id' => $adgroup_etc_params['object_id'],
	 'video_location' => $argv['video_location'],
	 'video_id' => $argv['video_ids'],
	 'preview' => $argv['preview'],
//	 'image_url' => $argv['image_url'],
        );
//	print_r($_POST);
//	die;

	if ($argv['image_hash'] != null){
		$creativeparams['image_hash'] = $argv['image_hash'];
	}
	
	if($argv['image_url'] != null){
		$creativeparams['image_url'] = $argv['image_url'];
	}

        //print_r($creativeparams);
	$adgroupparams = array(
	 'name' => $argv['ad_name'],
         'creative_array' => array('creative_id' => ObjectCreation::createAdCreative($creativeparams)->id),
	 'adcampaign_id' => $argv['adset_ids'],
	 'adgroup_status' =>  $argv['status'],
	 'objective' => $adgroup_etc_params['objective'],
	);
	ObjectCreation::createAdGroup($adgroupparams);
  }else if ($argv['type'] == 'adgroup_previews') {
	print_r($argv);
	print_r($_FILES);
	die;
        $adgroup_etc_params = ObjectCreation::getObjectiveAndObjectID($argv['adset_id']);
        $creativeparams = array(
         'name' => $argv['adcreativename'],
         'body' => $argv['adcreativename'],
         'spec_type' =>  $argv['spec_type'],
//       'campaign_id' => $argv['adset_id'],
         'call_to_action_type' => $argv['call_to_action_type'],
//         'image_location' => uploadImageToLocal(),//$argv['image_location'], 
         'call_to_action' => '{\'type\':\''.$argv['call_to_action_type'].'\',\'value\':{\'link\':\''.$argv['link'].'\'}}',

         'caption' => $argv['caption'],
         'ad_description' => $argv['ad_description'],
         'link' => $argv['link'],
         'message' => $argv['message'],
         'ad_header' => $argv['ad_header'],
         'page_id' => $argv['page_id'],
         'ad_format' => $argv['ad_format'],
         'object_story_id' => $argv['object_story_id'],
         'object_id' => $adgroup_etc_params['object_id'],
         'video_location' => $argv['video_location'],
         'video_id' => $argv['video_id'],
        );
	 if ($_FILES['fileToUpload'] != null){
                $creativeparams['image_location'] = ObjectCreation::uploadImageToLocal();
        }

//	ObjectCreation::createAdCreative_previews($creativeparams, $argv);
//      print_r($creativeparams);
//        $adgroupparams = array(
//         'name' => $argv['ad_name'],
//         'creative_array' => array('creative_id' => ObjectCreation::createAdCreative_creative($creativeparams, $argv)->id),
//         'adcampaign_id' => $argv['adset_id'],
//         'adgroup_status' =>  $argv['status'],
//         'objective' => $adgroup_etc_params['objective'],
//        );
//        ObjectCreation::createAdGroup($adgroupparams);
  }else if ($argv['type'] == 'upload_videos') {
//        echo "uploading files";
//	die;
        ObjectCreation::uploadVideo($argv['name']);
//	$video = new AdVideo('879689875415361', self::$account_id);
//  	$video_thumbnail = $video->read(array('thumbnails',), array());
//	print_r(json_encode($video_thumbnail->getData()));
  }else if ($argv['type'] == 'upload_image') {
//        echo "uploading files";
//      die;
//        ObjectCreation::uploadVideo($argv['name']);
	ObjectCreation::uploadImage(ObjectCreation::uploadImageToLocal());
  }else if ($argv['type'] == 'update_audience'){
	ObjectCreation::updateAdSetAudience($argv);
  }

 }
 
 public function getObjectiveAndObjectID($adset_id){
	$adset = new AdSet($adset_id, self::$account_id);
	$fields = array(
		'campaign_group_id',
		'promoted_object',
	);
	$adset_read = $adset->read($fields, array())->getData();
	$adcampaign = new AdCampaign($adset_read['campaign_group_id'], self::$account_id);
	$fields = array(
                'objective',
	);
        if (array_key_exists('object_id', $creativeCreationParams)){
                $fields[AdCreativeFields::OBJECT_STORE_URL] = $creativeCreationParams['object_id']['application_id'];
        }

	$adcampaign_read = $adcampaign->read($fields, array())->getData();
	$returnAr = array();
	if (array_key_exists('promoted_object', $adset_read)){
		$returnAr['object_id'] = $adset_read['promoted_object'];
	}
	$returnAr['objective'] = $adcampaign_read['objective'];

	return $returnAr;
 }

 public function uploadImageToLocal(){
//  print_r($_FILES);
  $target_dir = __DIR__ . "/uploads/";
  $target_file = $target_dir . basename($_FILES["image_id"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["image_id"]["tmp_name"]);
  if($check !== false) {
       	//echo "File is an image - " . $check["mime"] . ".";
       	$uploadOk = 1;
  } else {
       	echo "File is not an image.";
       	$uploadOk = 0;
  }
  }
  // Check if file already exists
  if (file_exists($target_file)) {
//  	  echo "Sorry, file already exists.";
	  $uploadOk = 0;
  }
  // Check file size
  if ($_FILES["image_id"]["size"] > 500000) {
  	//echo "Sorry, your file is too large.";
    	$uploadOk = 0;
  }
  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" ) {
  //  	echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    	$uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    //	echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
    	if (move_uploaded_file($_FILES["image_id"]["tmp_name"], $target_file)) {
      //  	echo "The file ". basename( $_FILES["image_id"]["name"]). " has been uploaded.";
    	} else {
      //  	echo "Sorry, there was an error uploading your file.";
    	}
  }
//  print_r($target_file);
  return $target_file;
 }

 public function uploadVideoToLocal(){
  $target_dir = __DIR__ . "/uploads/";
  $target_file = $target_dir . basename($_FILES["video_id"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if file already exists
  if (file_exists($target_file)) {
  //	  echo "Sorry, file already exists.";
	  $uploadOk = 0;
	  $file_array = array($target_file, $_FILES["video_id"]["name"]);
	  return $file_array;
  }
  // Check file size
/*  if ($_FILES["fileToUpload"]["size"] > 500000000000) {
  	//echo "Sorry, your file is too large.";
    	$uploadOk = 0;
  }
*/
  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" && $imageFileType != "mp4") {
    	//echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    	$uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
  //  	echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
    	if (move_uploaded_file($_FILES["video_id"]["tmp_name"], $target_file)) {
  //      	echo "The file ". basename( $_FILES["video_id"]["name"]). " has been uploaded.";
    	} else {
  //      	echo "Sorry, there was an error uploading your file.";
    	}
  }
//  print_r($target_file);
//  die
  $file_array = array($target_file, $_FILES["video_id"]["name"]);
  return $file_array;
 }

 
}

ObjectCreation::init();
ObjectCreation::api_init();
ObjectCreation::parse_arguments($_POST);
//print_r($_FILES);
//print_r($_POST);
//ObjectCreation::getObjectiveAndObjectID('6028636673120');
/*
ObjectCreation::createAdGroup(array(
  'name' => 'Roy trailer ad',
  'creative_array' => array('creative_id' => '6028413481720'), 
  'adgroup_status' => AdGroup::STATUS_PAUSED,
  'adcampaign_id' => '6028415978720'
));
*/
/*
ObjectCreation::createAdCreative(array(
  'name' => 'Roy creative',
  'body' => 'Roy creative desc',
  'call_to_action_type' => 'NO_BUTTON',
  'image_location' =>  '../../../../Desktop/Roy-Movie-Official-Trailer.jpg',
  'call_to_action' => '{\'type\':\'NO_BUTTON\',\'value\':{\'link\':\'https://www.youtube.com/watch?v=ahz3kTiGo3s/\'}}',
  'caption' => 'Roy trailer',
  'ad_description' => 'Presenting the TRAILER of Bhushan Kumar\'s \'Roy\', a T-Series Film, Directed by Vikramjit Singh, Produced by Divya Khosla Kumar, Bhushan Kumar and Krishan Kumar Co-Produced by Ajay Kapoor, starring Ranbir Kapoor in a Dynamic Role, Arjun Rampal and Jacqueline Fernandez',
  'link' => 'https://www.youtube.com/watch?v=ahz3kTiGo3s/',
  'message' => 'The trailer of Ranbir Kapoor\'s latest film \'ROY\' is here. Watch it now! ',
  'ad_name' => 'Exclusive: \'Roy\' Trailer ',
  'page_id' => '476563889048827',
//  'video_location' => '../test/misc/video.mp4'
));


/*
ObjectCreation::createAdSet(array(
  'name' => 'Roy AdSet Trailer',
  'campaign_id' => '6028411240920',
  'campaign_status' => AdSet::STATUS_PAUSED,
  'daily_budget' => '50000',
  'targeting' => ObjectCreation::createTargetingAudience(
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
ObjectCreation::createAdCampaign(array(
  'name' => 'Test Campaign',
  'objective' => AdObjectives::WEBSITE_CLICKS,
  'campaign_group_status' => AdCampaign::STATUS_PAUSED,
  'buying_type' => AdBuyingTypes::AUCTION,
));
*/

?>
