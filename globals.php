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

// ERROR HANDLING

$client        = new Raven_Client(getenv('RAVEN_CONFIG'));
$error_handler = new Raven_ErrorHandler($client);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();

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

// STATUSES

Global $STATUSES;
$STATUSES = [
	'open'     => 'circle',
	'stuck'    => 'question',
	'priority' => 'exclamation',
	'solved'   => 'flag-checkered',
];

function getFAicon($status) {
	return "fas fa-".$STATUSES[$status]??$STATUSES['open'];
}

// TWIG
Global $twig;
$engine = new MarkdownEngine\MichelfMarkdownEngine();
$loader = new Twig_Loader_Filesystem('templates');
$twig   = new Twig_Environment($loader, array(
	));
$twig->addExtension(new MarkdownExtension($engine));

// TWIG FILTERS
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
function render($template, $context = "", $vars = array()) {
	$latestNews = NewsQuery::create()
		->orderByCreatedAt('desc')
		->filterByNewsType('important')
		->limit(1)
		->findOne();

	$links = LinkQuery::create()
		->find();

	Global $twig;

	$member = $_SESSION['user']??null;
	if ($member) {
		$vars['user']    = $member;
		$vars['user_id'] = $_SESSION['user_id'];
	}
	$vars['alert']         = $_SESSION['alert_message']??null;
	$vars['statuses']      = ['open', 'stuck', 'priority', 'solved'];
	$vars['now']           = strftime('%c');
	$vars['latestNews']    = $latestNews;
	$vars['links']         = $links;
	$vars['context']       = $context;
	$vars['slackDomain']   = getenv('SLACK_DOMAIN');
	$vars['googleDriveId'] = getenv('GOOGLE_DRIVE_ID');
	$vars['huntUrl']       = getenv('HUNT_URL');
	$vars['sidebarInfo']   = explode(";", getenv('SIDEBAR_TEAM_INFO'));
	$vars['teamName']      = getenv('TEAM_NAME');

	Global $DEBUG;
	if ($DEBUG) {
		$debugbar         = new StandardDebugBar();
		$debugbarRenderer = $debugbar->getJavascriptRenderer();

		$debugbar["messages"]->addMessage("hello world!");

		$vars['debugHead'] = $debugbarRenderer->renderHead();
		$vars['debugBar']  = $debugbarRenderer->render();
	}

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
	$pal_client->setApplicationName(getenv('GOOGLE_APPLICATION_NAME'));
	$pal_client->setClientId(getenv('GOOGLE_CLIENT_ID').".apps.googleusercontent.com");
	$pal_client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
	$pal_client->setRedirectUri('http'.($DEBUG?'':'s').'://'.$_SERVER['HTTP_HOST']."/oauth");

	$pal_drive = new Google_DriveService($pal_client);
}

function create_file_from_template($title) {
	Global $pal_drive;
	$file = new Google_DriveFile();
	$file->setTitle($title);
	$copy = $pal_drive->files->copy(getenv('GOOGLE_DOCS_TEMPLATE_ID'), $file);

	return $copy['id'];
}
