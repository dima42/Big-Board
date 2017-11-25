<?php
session_start();
require_once 'vendor/autoload.php';

$DEBUG = false;

if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
    $DEBUG = true;
}

$loader = new Twig_Loader_Filesystem('templates');
Global $twig;
$twig = new Twig_Environment($loader, array(
));

function render($template, $vars = array()) {
    $news = "Type over this text to send out a message.";
    $news_from = "";

    $latest_updates = getLatestTeamUpdateSQL();
    $row = $latest_updates->fetch_assoc();
    $news = $row["NEWS"];
    $news_from = $row["WHO"];
    $metas = getAllMetas();

    Global $twig;
    $vars['user_id'] = $_SESSION['user_id'];
    $vars['time'] = strftime('%c');
    $vars['news'] = $news;
    $vars['news_from'] = $news_from;
    $vars['metas'] = $metas;

    if (in_array("error_string", $_SESSION)) {
        $vars['error'] = $_SESSION['error_string'];
    }
    echo $twig->render($template, $vars);
}

function connectToDB() {
    $url = parse_url(getenv("PALINDROME_DATABASE_URL"));
    $server = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $db = substr($url["path"], 1);

    $link = mysqli_connect(
       $server,
       $username,
       $password,
       $db
    );
    if (!$link) {
        writeHeader('Could not select database');
    }
    return $link;
}
?>
