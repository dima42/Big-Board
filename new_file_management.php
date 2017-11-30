<?php
require_once 'globals.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

function get_new_drive_service() {
	$pal_client = new Google_Client();
	$pal_client->setAccessType("offline");
	$pal_client->setApplicationName("Palindrome Big Board");
	$pal_client->setClientId('938479797888.apps.googleusercontent.com');
	$pal_client->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
	$pal_client->setRedirectUri('http://team-palindrome.herokuapp.com');
	$access_token = $_COOKIE['PAL_ACCESS_TOKEN'];
	if ($access_token == "") {
		$_SESSION['error_string'] .= "You must have cookies active.";
		return;
	} else {
		$pal_client->setAccessToken(stripslashes($access_token));
		if ($pal_client->isAccessTokenExpired()) {
			$_SESSION['error_string'] .= "This token is no good";
		}
		return new Google_DriveService($pal_client);
	}
}

function create_file_from_template($title) {
	$service = get_new_drive_service();
	$file    = new Google_DriveFile();
	$file->setTitle($title);
	// 1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4 is ID of our Template file, which is inside Mystery Hunt 2018/All Puzzles.
	$copy = $service->files->copy('1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4', $file);

	return $copy['id'];
}
