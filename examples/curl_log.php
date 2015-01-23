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

if (is_null($access_token) || is_null($app_id) || is_null($app_secret)) {
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
use FacebookAds\Logger\CurlLogger;

Api::init($app_id, $app_secret, $access_token);

// Create the CurlLogger
$logger = new CurlLogger();

// To write to a file pass in a file handler
$logger = new CurlLogger(fopen('test','w'));

// If you need to escape double quotes, use the following - useful for docs
$logger->setEscapeQuotes(true);

// Hide target ids and tokens
$logger->setShowSensitiveData(false);

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;

$account = (new AdAccount($account_id))->read(array(
  AdAccountFields::ID,
  AdAccountFields::NAME,
  AdAccountFields::ACCOUNT_STATUS
));
