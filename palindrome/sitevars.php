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
    Global $twig;
    $vars['user_id'] = $_SESSION['user_id'];
    $vars['time'] = strftime('%c');
    if (in_array("error_string", $_SESSION)) {
        $vars['error'] = $_SESSION['error_string'];
    }
    echo $twig->render($template, $vars);
}

function connectToDB() {
    $user = 'spandext_foggy';
    $password = 'sm4rg4nA';
    $db = 'spandext_palin';
    $host = 'localhost';
    $port = 8889;

    $link = mysqli_connect(
       $host,
       $user,
       $password,
       $db,
       $port
    );
	if (!$link) {
        writeHeader('Could not select database');
    }
	return $link;
}

// Call this to find the folder we are using to store puzzle spreadsheets.
// When setting up an upcoming Mystery Hunt folder, create an All Puzzle folders.
function getCurrentParentFolder() {
	return  "0BwQVTWNxkZQaNmI0QkNlWGVSQmM"; // current Mystery Hunt 2016/All Puzzles folder
}
?>
