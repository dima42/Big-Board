<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';
require_once "sql.php";
require_once "slack.php";

// ALERT
$_SESSION['alert'] = "";

// DEBUG
$DEBUG = false;
if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
	$DEBUG = true;
}

function preprint($arr) {
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

// TWIG
Global $twig;
$loader = new Twig_Loader_Filesystem('templates');
$twig   = new Twig_Environment($loader, array(
	));

// TWIG FILTERS
function emojify($status) {
	switch ($status) {
		case "open":
			return "🤔";
		case "stuck":
			return "🤷🏻‍♀️";
		case "priority":
			return "❗️";
		case "urgent":
			return "🚨";
		case "solved":
			return "🏁";
	}

	return "⚪️";
}

$emojify = new Twig_Filter('emojify', 'emojify');
$twig->addFilter($emojify);

$default = new Twig_Filter('default',
	function ($input, $default) {
		if (!$input) {
			return $default;
		}
		return $input;
	});
$twig->addFilter($default);

$yesno = new Twig_Filter('yesno',
	function ($input, $ifyes, $ifno = "") {
		if ($input) {
			return $ifyes;
		}
		return $ifno;
	});
$twig->addFilter($yesno);

// RENDER
function render($template, $vars = array()) {
	$latestNews = NewsQuery::create()
		->orderByCreatedAt('desc')
		->limit(1)
		->findOne();

	Global $twig;
	$vars['user']       = $_SESSION['user']??null;
	$vars['user_id']    = $_SESSION['user_id']??0;
	$vars['alert']      = $_SESSION['alert_message']??null;
	$vars['time']       = strftime('%c');
	$vars['latestNews'] = $latestNews;

	$vars['metas'] = PuzzlePuzzleQuery::create()
		->joinWith('PuzzlePuzzle.Parent')
		->where('puzzle_id = parent_id')
		->find();

	if (in_array("error_string", $_SESSION)) {
		$vars['error'] = $_SESSION['error_string'];
	}
	echo $twig->render($template, $vars);

	$_SESSION['alert_message'] = "";
}

// GOOGLE
require_once 'globals.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

function get_new_drive_service() {
	$_SESSION['error_string'] = "";
	$pal_client               = new Google_Client();
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
