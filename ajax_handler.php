<?
require_once "globals.php";
require_once "sql.php";
require_once "new_file_management.php";
require_once "slack_functions.php";

// as commands come in, we need to process them

$f = $_GET{"f"};

if ($f == "CHK") {
	signMeUpSQL($_GET{"pid"}, $_GET{"uid"});
}

if ($f == "QWT") {
	iQuitSQL($_GET{"pid"}, $_GET{"uid"});
}

if ($f == "DPZ") {
	deletePuzzleSQL($_GET{"pid"});
}

if ($f == "PRO") {
	promotePuzzleSQL($_GET{"pid"});
}
