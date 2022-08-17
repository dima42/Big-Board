<?php
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';
require_once "slack.php";
require_once "cache.php";

use Aptoma\Twig\Extension\MarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;
use DebugBar\StandardDebugBar;

session_start();

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

// ERROR HANDLING

$sentry_settings = ['environment' => 'production'];
if ($DEBUG) {
	$sentry_settings['environment'] = 'development';
}
$sentryClient  = new Raven_Client(getenv('SENTRY_CONFIG'), $sentry_settings);
$error_handler = new Raven_ErrorHandler($sentryClient);
$error_handler->registerExceptionHandler();
$error_handler->registerErrorHandler();
$error_handler->registerShutdownFunction();

// TIMEZONES

date_default_timezone_set("UTC");

// ALERT

$_SESSION['alert'] = "";

// STATUSES

Global $STATUSES;
$STATUSES = [
	'open'          => 'circle',
	'stuck'         => 'question',
	'priority'      => 'exclamation',
	'lowpriority'   => 'circle',
	'solved'        => 'flag-checkered',
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
$twig->addExtension(new Twig_Extensions_Extension_Date());

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
	$links = LinkQuery::create()
		->find();

	Global $twig;

	$member = $_SESSION['user']??null;
	if ($member) {
		$vars['user']    = $member;
		$vars['user_id'] = $_SESSION['user_id'];
	}
	$vars['alert']         = $_SESSION['alert_message']??null;
	$vars['statuses']      = ['open', 'stuck', 'priority', 'solved', 'lowpriority'];
	$vars['now']           = strftime('%c');
	$vars['links']         = $links;
	$vars['context']       = $context;
	$vars['slackDomain']   = getenv('SLACK_DOMAIN');
	$vars['googleDriveId'] = getenv('GOOGLE_DRIVE_ID');
	$vars['huntUrl']       = getenv('HUNT_URL');
	$vars['sidebarInfo']   = explode(";", getenv('SIDEBAR_TEAM_INFO'));
	$vars['teamName']      = getenv('TEAM_NAME');
        $vars['teamNameNoSpaces'] = str_replace(' ', '', getenv('TEAM_NAME'));

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
        $pal_client->setApprovalPrompt("force");
	$pal_client->setApplicationName(getenv('GOOGLE_APPLICATION_NAME'));
	$pal_client->setDeveloperKey(getenv('GOOGLE_DEVELOPER_KEY'));
	$pal_client->setClientId(getenv('GOOGLE_CLIENT_ID').".apps.googleusercontent.com");
	$pal_client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
	$pal_client->setScopes('https://www.googleapis.com/auth/drive.file');
	$pal_client->setRedirectUri('http'.($DEBUG?'':'s').'://'.$_SERVER['HTTP_HOST']."/oauth");

	$pal_drive = new Google_Service_Drive($pal_client);
}

$shared_client = new Google_Client();
$login_data = json_decode(getenv('GOOGLE_SERVICE_ACCOUNT_APPLICATION_CREDENTIALS'), true);
$shared_client->setAuthConfig($login_data);
$shared_client->setScopes(array("https://www.googleapis.com/auth/drive"));
Global $shared_drive;
$shared_drive = new Google_Service_Drive($shared_client);
Global $shared_sheets;
$shared_sheets = new Google_Service_Sheets($shared_client);

function create_file_from_template($title) {
        Global $shared_drive;
	    $file = new Google_Service_Drive_DriveFile();
        $file->setName($title);
	    $file->setParents(array(getenv('GOOGLE_DRIVE_PUZZLES_FOLDER_ID')));
        error_log("Starting to copy file");
        $copy = $shared_drive->files->copy(getenv('GOOGLE_DOCS_TEMPLATE_ID'), $file, array('fields' => '*'));
        error_log("starting to set permissions");
        $ownerPermission = new Google_Service_Drive_Permission();
        $ownerPermission->setEmailAddress(getenv('GOOGLE_GROUP_EMAIL'));
        $ownerPermission->setType('group');
        $ownerPermission->setRole('writer');
				$attempts = 0;
				do {
					try {
						error_log("Sharing atttempt " . $attempts);
						$shared_drive->permissions->create($copy['id'], $ownerPermission, array('fields' => '*'));
					} catch (Exception $e) {
						error_log($e->getMessage());
						$attempts++;
						sleep(1);
						continue;
					}
					break;
				}	while ($attempts < 10);
        return $copy['id'];
}

Global $cache;
$cache = new Cache();
