<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';
require_once "sql.php";

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
$emojify = new Twig_Filter('emojify',
	function ($status) {
		switch ($status) {
			case "open":
				return "🤔";
			case "stuck":
				return "🤷🏻‍♀️";
			case "priority":
				return "❗️";
			case "solved":
				return "🏁";
		}
		return "⚪️";
	});
$twig->addFilter($emojify);

$default = new Twig_Filter('default',
	function ($input, $default) {
		if (!$input) {
			return $default;
		}
		return $input;
	});
$twig->addFilter($default);

// RENDER
function render($template, $vars = array()) {
	$latestNews = NewsQuery::create()
		->orderByCreatedAt('desc')
		->limit(1)
		->findOne();

	Global $twig;
	$vars['user']       = $_SESSION['user'];
	$vars['user_id']    = $_SESSION['user_id'];
	$vars['alert']      = $_SESSION['alert_message'];
	$vars['time']       = strftime('%c');
	$vars['latestNews'] = $latestNews;

	$vars['metas'] = PuzzleParentQuery::create()
		->joinWith('PuzzleParent.Parent')
		->where('puzzle_id = parent_id')
		->find();

	if (in_array("error_string", $_SESSION)) {
		$vars['error'] = $_SESSION['error_string'];
	}
	echo $twig->render($template, $vars);

	$_SESSION['alert_message'] = "";
}

// SLACK
function createNewSlackChannel($slug) {
	$drawkwards_token = "xoxp-115681477587-116829918517-116066430608-c8e7080af7cb9da9893453c37a8e7e25";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/channels.create?token=".$drawkwards_token."&name=".$slug);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}
