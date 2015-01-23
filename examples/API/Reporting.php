<?php

namespace FacebookAds\examples;

use FacebookAds\Api;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Values\CustomAudienceTypes;

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Fields\AdPreviewFields;

use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\Fields\AdCampaignFields;
use FacebookAds\Object\Values\AdObjectives;

use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\AdGroupBidInfoFields;
use FacebookAds\Object\Values\BidTypes;

use FacebookAds\Object\AdGroup;
use FacebookAds\Object\Fields\AdGroupFields;
 
class Reporting{

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
  $config_path =  __DIR__ .'/../config1.php';

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
  $reportingArguments = array();
  $k = array_search("-t", $argv);
  $reportingArguments['type'] = $argv[$k+1];
  $k = array_search("-id", $argv);
  $reportingArguments['id'] = $argv[$k+1];
  $k = array_search("-et", $argv);
  if ($k == null){
  	$reportingArguments['end_time'] = 0;
  }else{
  	$reportingArguments['end_time'] = $argv[$k+1];
  }
  $k = array_search("-st", $argv);
  if ($k == null){
  	$reportingArguments['start_time'] = 0;
  }else{
  	$reportingArguments['start_time'] = $argv[$k+1];
  }

  if($reportingArguments['type'] == 'adacctrep') {
	self::reportAdAccount($reportingArguments);
  }else if($reportingArguments['type'] == 'adcampaign') {
	self::reportAdCampaign($reportingArguments);
  }else if($reportingArguments['type'] == 'adset') {
        self::reportAdSet($reportingArguments);
  }else if($reportingArguments['type'] == 'adgroup') {
        self::reportAdGroup($reportingArguments);
  }

 }

 public function reportAdGroup($adGroupParams){
  $adGroup = new AdGroup($adGroupParams['id'], self::$account_id);
  $params = array();
  if($adGroupParams['start_time'] != 0){
        $params['start_time'] = $adGroupParams['start_time'];
  }
  if($adGroupParams['end_time'] != 0){
        $params['end_time'] = $adGroupParams['end_time'];
  }       
  $stats = $adGroup->getStats(array(),$params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($stats->getData());
 }


 public function reportAdSet($adSetParams){
  $adSet = new AdSet($adSetParams['id'], self::$account_id);
  $params = array();
  if($adSetParams['start_time'] != 0){
        $params['start_time'] = $adSetParams['start_time'];
  }
  if($adSetParams['end_time'] != 0){
        $params['end_time'] = $adSetParams['end_time'];
  }
  $stats = $adSet->getStats(array(),$params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($stats->getData());
 }

 public function reportAdCampaign($adCampaignParams){
  $adCampaign = new AdCampaign($adCampaignParams['id'], self::$account_id);
  $fields = array(
  'impressions',
  'clicks',
  'spent',
  'campaign_group_id',
  'end_time',
  'actions',
  'social_clicks',
  'social_impressions',
  'social_spent',
  'social_unique_clicks',
  'social_unique_impressions',
  'start_time',
  'unique_clicks',
  'unique_impressions',
  );

  $params = array();
  if($adCampaignParams['start_time'] != 0){
  	$params['start_time'] = $adCampaignParams['start_time'];
  }
  if($adCampaignParams['end_time'] != 0){
  	$params['end_time'] = $adCampaignParams['end_time'];
  }
  
  $stats = $adCampaign->getStats($fields, $params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($stats->getResponse()->getContent());
 }

 public function reportAdAccount($adAccountParams){
//  print_r($adAccountParams['filter']['start_time']);
//  die;
  $adaccount = new AdAccount(self::$account_id);
  $params = array(
//    'date_preset'=>'last_28_days',
    'data_columns'=> array(
	'campaign_name',
	'adgroup_name',
	'reach',
	'frequency',
	'impressions',
	'clicks',
	'unique_clicks',
	'ctr',
	'unique_ctr',
	'spend',
	'cpm',
	'cpp',
	'cpc',
	'cost_per_unique_click',
	'actions',
	'unique_actions',
//	'placement',
//	'impression_device',
/*
	'actions',
	'spend',
//	'action_device',
//	'placement',
//	'impression_device',
// Impression and spend columns
	'reach',
	'frequency',
	'social_reach',
	'social_impressions',
	'unique_impressions',
	'cpp',
	'cpm',
	'impressions', 
// Clicks 
	'clicks',
	'unique_clicks',
	'social_clicks',	
	'unique_social_clicks',
	'ctr',
	'unique_ctr',
	'cpc',
	'cost_per_unique_click',
*/
),
    'async' => 'true',
  );

  if(isset($adAccountParams['filter']['start_time']) && isset($adAccountParams['filter']['end_time'])){

      if ($adAccountParams['filter']['start_time'] != '' && $adAccountParams['filter']['end_time'] != ''){
  	$start_time = strtotime($adAccountParams['filter']['start_time']);
	$end_time = strtotime($adAccountParams['filter']['end_time']);

//print_r(date('Y m d H:i:s',$start_time));
//print_r(date('Y m d H:i:s',$end_time));

	$start_time = $start_time - (5*3600) - 1800;
        $end_time = $end_time - (5*3600) - 1800;


	$tm = $end_time - $start_time;
	$tm = $tm/(3600*24);


//	echo "\n".$tm;

        $params['time_interval'] = "{ 'time_start' : ".$start_time.", 'time_stop' : ".$end_time."}";
        $params['time_increment'] = $tm;
      }else{
	$params['date_preset'] = 'last_14_days';
        $params['time_increment'] = 14;

      }
  }else{
	$params['date_preset'] = 'last_14_days';
	$params['time_increment'] = 14;
  }


  $filter_fields = [['field' => 'campaign_group_id', 'type' => 'in']];

  $filter_fields[0]['value'] = $adAccountParams['campaign_ids'];

  $params['filters'] = $filter_fields;

//  print_r($adAccountParams['filter']);

  if ($adAccountParams['filter']['filter'] == 'placement'){
//	echo "Placement filter";
	array_push($params['data_columns'], 'placement');
  }else if ($adAccountParams['filter']['filter'] == 'gender'){
   //   echo "gender filter";
        array_push($params['data_columns'], 'gender');
  }else if ($adAccountParams['filter']['filter'] == 'age'){
   //   echo "age filter";
        array_push($params['data_columns'], 'age');
  }else  if ($adAccountParams['filter']['filter'] == 'placement_and_device'){
 //     echo "Placement_device filter";
        array_push($params['data_columns'], 'placement');
        array_push($params['data_columns'], 'impression_device');

  }

	//die;
  $reportStats = $adaccount->getReportsStats(array('limit' => 100),$params);
//  echo "---------------------------------------------------------------------------------------------"."\n";
  $reportArray = $reportStats->getResponse()->getContent();

  print_r(json_encode($reportStats->getResponse()->getContent()['data']));


//  echo "\n";
//  echo "---------------------------------------------------------------------------------------------"."\n";
//  $stats = $adaccount->getStats();
//  echo "---------------------------------------------------------------------------------------------"."\n"; 
//  print_r($stats->getData());
//  echo "\n";
/*  $fields = array(
    AdPreviewFields::CREATIVE,
    AdPreviewFields::POST,
    AdPreviewFields::AD_FORMAT,
    AdPreviewFields::BODY,
  );
  $creative = new AdCreative('6028369968320', self::$account_id);
  $params = array(
    'ad_format' => 'RIGHT_COLUMN_STANDARD',
    'creative' => $creative,
  );

  $previews = $adaccount->getAdPreviews($fields, $params);
  echo "---------------------------------------------------------------------------------------------"."\n";
  print_r($previews);
  echo "\n";
*/

 //Reporting::createHtmlReport($reportArray['data']);
 }

 public function createHtmlReport($reportArray){
  $i=0;
  echo "<html><body>"."\n";
  $s = "<table style=\"width:100%\">"."\n";
  foreach( $reportArray as $report){
        if ($i == 0){
                $tableHeaderArray = array_keys($report);
                $s = $s."<tr>"."\n";
                foreach ($tableHeaderArray as $tableHeader){
                        $s = $s."<th>".$tableHeader."</th>\n";
                }
                $s = $s."</tr>"."\n";
                $i = 1;
        }

        $s = $s."<tr>"."\n";
        foreach ($report as $tableRow){
                $s = $s."<td>".$tableRow."</td>\n";
        }
        $s = $s."</tr>"."\n";
  }
  $s = $s."</table>";
  echo $s;
  echo "</html></body>";
 }



}
Reporting::init();
Reporting::api_init();

$p = array('campaign_ids' => array('6029154125520'), 'filter' => array('filter'=>'placement'));

//Reporting::parseArguments($argv);
//print_r($_POST);
Reporting::reportAdAccount($_POST);

?>
