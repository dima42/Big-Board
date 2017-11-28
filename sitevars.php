<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';
require_once "sql.php";
use Propel\Runtime\ActiveQuery\Criteria;

// ALERT
$_SESSION['alert'] = "";

// DEBUG
$DEBUG = false;
if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
    $DEBUG = true;
}

// TWIG
Global $twig;
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
));

// TWIG FILTERS
$emojify = new Twig_Filter('emojify', function ($status) {
    switch ($status) {
        case "solved":
            return "🏁";
        case "open":
            return "🤔";
    }
    return "";
});
$twig->addFilter($emojify);

// RENDER
function render($template, $vars = array()) {
    $latestNews = NewsQuery::create()
        ->orderByCreatedAt('desc')
        ->limit(1)
        ->findOne();

    Global $twig;
    $vars['user_id'] = $_SESSION['user_id'];
    $vars['alert'] = $_SESSION['alert_message'];
    $vars['time'] = strftime('%c');
    $vars['news'] = $latestNews->getContent();;
    $vars['news_from'] = $latestNews->getMember()->getFullName();

    $vars['metas'] = PuzzleParentQuery::create()
        ->joinWith('PuzzleParent.Parent')
        ->where('puzzle_id = parent_id')
        ->find();

    if (in_array("error_string", $_SESSION)) {
        $vars['error'] = $_SESSION['error_string'];
    }
    echo $twig->render($template, $vars);

    unset($_SESSION['alert_message']);
}
?>
