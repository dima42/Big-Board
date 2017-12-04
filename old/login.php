<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';

session_start();

$client = new Google_Client();
$client->setApplicationName("Palindrome Big Board");
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$client->setClientId('938479797888.apps.googleusercontent.com');
$client->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
$client->setRedirectUri('http://palindrome.spandexters.com/login.php');
//$client->setDeveloperKey('insert_your_developer_key');
$plus = new Google_PlusService($client);

if (isset($_REQUEST['logout'])) {
	unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token'])) {
	$client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
	$me = $plus->people->get('me');

	// These fields are currently filtered through the PHP sanitize filters.
	// See http://www.php.net/manual/en/filter.filters.sanitize.php
	$url          = filter_var($me['url'], FILTER_VALIDATE_URL);
	$img          = filter_var($me['image']['url'], FILTER_VALIDATE_URL);
	$name         = filter_var($me['displayName'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	$personMarkup = "<a rel='me' href='$url'>$name</a><div><img src='$img'></div>";

	$optParams      = array('maxResults' => 100);
	$activities     = $plus->activities->listActivities('me', 'public', $optParams);
	$activityMarkup = '';
	foreach ($activities['items'] as $activity) {
		// These fields are currently filtered through the PHP sanitize filters.
		// See http://www.php.net/manual/en/filter.filters.sanitize.php
		$url     = filter_var($activity['url'], FILTER_VALIDATE_URL);
		$title   = filter_var($activity['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$content = filter_var($activity['object']['content'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$activityMarkup .= "<div class='activity'><a href='$url'>$title</a><div>$content</div></div>";
	}

	// The access token may have been updated lazily.
	$_SESSION['access_token'] = $client->getAccessToken();
} else {
	$authUrl = $client->createAuthUrl();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel='stylesheet' href='style.css' />
  <title>Palindrome Big Board Log In</title>
<style type="text/css">
#UrgentMessage {
	color: #F00;
}
#UrgentMessage {
	color: #F00;
}
.MetaRound, H1, P, A {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 11px
}
.puzzle {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 11px;
	border-color: #666666;
	border-width:thin;
}
.solved { background-color: #F0F0F0; }
.open { background-color: #90F790; }
.stuck { background-color: #D0A020; }
.priority { background-color: #FF0000; }

</style>
</head>
<body>
<header><h1>Palindrome Big Board Log In</h1></header>
<p>Hi there. If you are seeing this page, click Connect Me! to use Google+ services to get hooked up.</p>
<div class="box">

<?php if (isset($personMarkup)):?>
<div class="me"><?php print$personMarkup?></div>
<?php endif?>

<?php if (isset($activityMarkup)):?>
<div class="activities">Your Activities: <?php print$activityMarkup?></div>
<?php endif?>

<?php
if (isset($authUrl)) {
	print"<a class='login' href='$authUrl'>Connect Me!</a>";
} else {
	print"<a class='logout' href='?logout'>Logout</a>";
}
?>
</div>
</body>
</html>
