<?php
/**
 * Copyright 2014 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */


// Set your access token here:
$access_token = 'CAAC4XoGMYroBAOn5BpQ9wLQYljsca7LUQHqplDp8mp1ZCx0XWQvhFz4ILxXgwVWKw38RqUaqf00nhjtmhrPtcMp8Ivv3MJ7QomzbKQ63bPWYg3wCqCFb8StqEwZBhtvMJtCAdNdCZAsb6VipzBH5nOh2Rx4XlwZB2tVoIIsz9e7IxTn5nws9ZCcdp6ZC479gBzIB0ezWUXa83HxEKiQyst';
$app_id = '202716039897786';
$app_secret = '641336fcda0e8c5a5f9919392ffb7dd9';
$account_id = 'act_323989862';

if(is_null($access_token) || is_null($app_id) || is_null($app_secret)) {
  throw new \Exception(
    'You must set your access token, app id and app secret before executing'
  );
}

if (is_null($account_id)) {
  throw new \Exception(
    'You must set your account id before executing');
}

define('SDK_DIR', __DIR__ . '/..'); // Path to the SDK directory
$loader = include SDK_DIR.'/vendor/autoload.php';

use FacebookAds\Api;

Api::init($app_id, $app_secret, $access_token);


/**
 * Step 1 Read the AdAccount (optional)
 */
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

use FacebookAds\Object\Page;
$fields = array(
        'id',
        'name',
  );

$page = new Page('476563889048827');

$posts = $page->getPages($fields,array());
print_r($posts->getResponse()->getContent());
die;


$account = (new AdAccount($account_id))->read(array(
  AdAccountFields::ID,
  AdAccountFields::NAME,
  AdAccountFields::ACCOUNT_STATUS
));

echo "\nUsing this account: ";
echo $account->id."\t".$account->name."\n";

$params = array(
    'date_preset'=>'last_28_days',
    'data_columns'=>"['account_id','actions','spend', 'impressions', 'clicks']",
    'async' => 'true',
#    'actions_group_type' => 
);

$account->getPages(array('name',), array());


$report = $account->getReportsStats(array(), $params);
print_r($report);

#foreach($report as $stat1) {
#    print_r($stat1);
#}

// Check the account is active
if($account->{AdAccountFields::ACCOUNT_STATUS} !== 1) {
  throw new \Exception(
    'This account is not active');
}

/**
 * Step 2 Create the AdCampaign
 */
use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\AdCampaignFields;
use FacebookAds\Object\Values\AdObjectives;

$adcampaignid = '6028317881120';

function createAdCampaign($adcampaignid, $account){
$campaign  = new AdCampaign($adcampaignid, $account->id);
$campaign->setData(array(
  AdCampaignFields::NAME => 'My First Campaign',
  AdCampaignFields::OBJECTIVE => AdObjectives::WEBSITE_CLICKS,
  AdCampaignFields::STATUS => AdCampaign::STATUS_PAUSED,
));

if(is_null($adcampaignid)){
	$campaign->create();
}
echo "Campaign ID:" . $campaign->id . "\n";
return $campaign;
}

function updateAdCampaign($adcampaignid, $account, $campaign_name, $objective, $adcampaign_status){
$campaign  = new AdCampaign($adcampaignid, $account->id);
$campaign->setData(array(
  AdCampaignFields::NAME => 'My First Campaign',
  AdCampaignFields::OBJECTIVE => AdObjectives::WEBSITE_CLICKS,
  AdCampaignFields::STATUS => AdCampaign::STATUS_PAUSED,
));

$campaign->update();
echo "Campaign ID:" . $campaign->id . "\n";
return $campaign;
}

$campaign = createAdCampaign($adcampaignid, $account);

$campaign_fields = array(
	"id","impressions","clicks","spent","social_impressions",
);

#$reads = $campaign->read(array(
#    AdCampaignFields::ID,
#    AdCampaignFields::ACCOUNT_ID,
#    AdCampaignFields::OBJECTIVE,
#    AdCampaignFields::NAME,
#    AdCampaignFields::STATUS,
#    AdCampaignFields::BUYING_TYPE,
#)
#);

#$campaign = updateAdCampaing($adcampaignid, $account, $campaigan_name, $objective, $adcampaign_status);

/**
 * Step 3 Search Targeting
*/ 
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\Search\TargetingSearchTypes;

function targetingSearch(){

$results = TargetingSearch::search(
  $type = TargetingSearchTypes::INTEREST,
  $class = null,
  $query = 'facebook');

// we'll take the top result for now
$target = (count($results)) ? $results->current() : null;

echo "Using target: ".$target->name."\n";
}

function targetingSpecs($locationSpec,$custom_aud_spec){
$targeting = new TargetingSpecs();
$targeting->{TargetingSpecsFields::GEO_LOCATIONS}
	= $locationSpec;
$targeting->{TargetingSpecsFields::CUSTOM_AUDIENCES} 
	= $custom_aud_spec;
return $targeting;
}
$locationSpec = array('countries' => array('IN'));
$custom_aud_spec = array(
      array(
        'id' => 6028240949520,
        'name' => 'My Custom Audiece'),
      array(
        'id' => 6028233327320,
        'name' => 'Custom'),
    );

$targeting = targetingSpecs($locationSpec, $custom_aud_spec);

/**
 * Step 4 Create the AdSet
 */
use FacebookAds\Object\AdSet;
#use FacebookAds\Object\TargetingSpecs;
#use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\AdGroupBidInfoFields;
use FacebookAds\Object\Values\BidTypes;

#$adsetid = '6028317965920';

$adsetid = null;

function createAdset($adsetid, $account, $campaign, $targeting){
$adset = new AdSet($adsetid, $account->id);
$adset->setData(array(
  AdSetFields::NAME => 'AdSet1',
  AdSetFields::CAMPAIGN_GROUP_ID => $campaign->id,
  AdSetFields::CAMPAIGN_STATUS => AdSet::STATUS_ACTIVE,
  AdSetFields::DAILY_BUDGET => '40000',
  AdSetFields::TARGETING => $targeting,
  AdSetFields::BID_TYPE => BidTypes::BID_TYPE_CPM,
  AdSetFields::BID_INFO =>
    array(AdGroupBidInfoFields::IMPRESSIONS => 200),
  AdSetFields::START_TIME =>
    (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
  AdSetFields::END_TIME =>
    (new \DateTime("+2 week"))->format(\DateTime::ISO8601),
));

if(is_null($adsetid)){
	$adset->create();
}
echo 'AdSet  ID: '. $adset->id . "\n";
return $adset;
}

$adset = createAdSet($adsetid, $account, $campaign, $targeting);
$query_fields = array(
	'start_time' => (new \DateTime("+1 week"))->format(\DateTime::ISO8601),
	'end_time' => (new \DateTime("+2 week"))->format(\DateTime::ISO8601),
);


#$stats = $adset->getStats($campaign_fields, $query_fields);
#print_r($stats);
#print_r($stats->impressions);

/**
 * Step 5 Create an AdImage
 */
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;

function createImage($account,$file_locn){
$image = new AdImage(null, $account->id);
$image->{AdImageFields::FILENAME} = $file_locn;
$image->create();
echo 'Image Hash: '.$image->hash . "\n";
return $image;
}

$file_locn = SDK_DIR.'/test/misc/image.png';
$image = createImage($account,$file_locn);

$page_id='761120863969761';

use FacebookAds\Object\ObjectStorySpec;
use FacebookAds\Object\Fields\ObjectStorySpecFields;
use FacebookAds\Object\Traits\FieldValidation;
use FacebookAds\Object\ObjectStory\LinkData;
use FacebookAds\Object\Fields\ObjectStory\LinkDataFields;


function createLinkData($account,$image,$page_id){

 $linkdataid = null;

 $linkdata = new LinkData($linkdataid, $account->id);
 $linkdata->setData(array(
   LinkDataFields::CALL_TO_ACTION=>'{\'type\':\'LEARN_MORE\',\'value\':{\'link\':\'https://nestle.in/\'}}',
   LinkDataFields::CAPTION=>'Learn More',
 #   LinkDataFields::CHILD_ATTACHMENTS,
   LinkDataFields::DESCRIPTION=>'Nestle ads description',
   LinkDataFields::IMAGE_HASH=>$image->hash,
 #   LinkDataFields::IMAGE_CROPS,
   LinkDataFields::LINK=>'https://nestle.in/',
   LinkDataFields::MESSAGE=>'Nestle message',
   LinkDataFields::NAME=>'Nestle names',
 #   LinkDataFields::PICTURE,

 ));

 $objectspecid = null;



 $objectstoryspec = new ObjectStorySpec($objectspecid, $account->id);
 $objectstoryspec->setData(array(
   ObjectStorySpecFields::LINK_DATA=>$linkdata,
   ObjectStorySpecFields::PAGE_ID=>$page_id,
 ));

 return $objectstoryspec;
}

/**
 * Step 5 Create an AdCreative
 */
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;

function createCreative($creativeid,$account,$image,$page_id,$call_to_action_type){

$creative = new AdCreative($creativeid, $account->id);
$creative->setData(array(
  AdCreativeFields::NAME => 'Sample Creative',
#  AdCreativeFields::TITLE => 'Welcome to the Jungle',
  AdCreativeFields::BODY => 'We\'ve got fun \'n\' games',
  AdCreativeFields::IMAGE_HASH => $image->hash,
#  AdCreativeFields::OBJECT_URL => 'http://www.example.com/',
  AdCreativeFields::OBJECT_STORY_SPEC=>createLinkData($account,$image,$page_id),
  AdCreativeFields::CALL_TO_ACTION_TYPE=>$call_to_action_type,
));

if(is_null($creativeid)){
	$creative->create();
}
echo 'Creative ID: '.$creative->id . "\n";
return $creative;
}

#$creative_id = '6028317966720';
$creative_id = null;
$call_to_action_type = 'LEARN_MORE';
$creative = createCreative($creative_id,$account,$image,$page_id,$call_to_action_type);

/**
 * Step 7 Create an AdGroup
 */
use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
function createAd($adgroupid,$account, $creative, $adset){
$adgroup = new AdGroup($adgroupid, $account->id);
$adgroup->setData(array(
  AdGroupFields::CREATIVE =>
    array('creative_id' => $creative->id),
  AdGroupFields::NAME => 'My First AdGroup',
  AdGroupFields::ADGROUP_STATUS =>  AdGroup::STATUS_PAUSED,
  AdGroupFields::CAMPAIGN_ID => $adset->id,
));

if(is_null($adgroupid)){
	#$adgroup->create();
	$i=0;
}
echo 'AdGroup ID:' . $adgroup->id . "\n";
return $adgroup;
}

$adgroupid = null;
createAd($adgroupid,$account,$creative,$adset);

