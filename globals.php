<?php
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';
require_once "slack.php";
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

use Aptoma\Twig\Extension\MarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;
use DebugBar\StandardDebugBar;

session_start();

// ALERT
$_SESSION['alert'] = "";

// DEBUG
Global $DEBUG;
$DEBUG = false;
if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
	$DEBUG = true;
}

function debug($text) {
	Global $DEBUG;
	if ($DEBUG) {
		error_log($text);
	}
}

function preprint($arr) {
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

// TWIG
Global $twig;
$engine = new MarkdownEngine\MichelfMarkdownEngine();
$loader = new Twig_Loader_Filesystem('templates');
$twig   = new Twig_Environment($loader, array(
	));
$twig->addExtension(new MarkdownExtension($engine));

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
		case "important":
			return "📣";
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
		->filterByNewsType('important')
		->limit(1)
		->findOne();

	Global $twig;

	$member = $_SESSION['user']??null;
	if ($member) {
		$vars['user']         = $member;
		$vars['user_id']      = $_SESSION['user_id'];
		$vars['user_puzzles'] = $member->getPuzzles();
	}
	$vars['alert']      = $_SESSION['alert_message']??null;
	$vars['statuses']   = ['open', 'stuck', 'priority', 'urgent', 'solved'];
	$vars['now']        = strftime('%c');
	$vars['latestNews'] = $latestNews;

	Global $DEBUG;
	if ($DEBUG) {
		$debugbar         = new StandardDebugBar();
		$debugbarRenderer = $debugbar->getJavascriptRenderer();

		$debugbar["messages"]->addMessage("hello world!");

		$vars['debugHead'] = $debugbarRenderer->renderHead();
		$vars['debugBar']  = $debugbarRenderer->render();
	}

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

Global $pal_client;
Global $pal_drive;

if (!$pal_client) {
	// SET UP GOOGLE_CLIENT OBJECT
	$pal_client = new Google_Client();
	$pal_client->setAccessType("offline");
	$pal_client->setApplicationName("Palindrome Big Board");
	$pal_client->setClientId('938479797888.apps.googleusercontent.com');
	// TODO put the following in a environment variable
	$pal_client->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
	$pal_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST']."/oauth");

	$pal_drive = new Google_DriveService($pal_client);
}

function create_file_from_template($title) {
	Global $pal_drive;
	$file = new Google_DriveFile();
	$file->setTitle($title);
	// 1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4 is ID of our Template file, which is inside Mystery Hunt 2018/All Puzzles.
	$copy = $pal_drive->files->copy('1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4', $file);

	return $copy['id'];
}
