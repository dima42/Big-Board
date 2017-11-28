<?
require_once "globals.php";
require_once "sql.php";
require_once "new_file_management.php";
require_once "slack_functions.php";

// as commands come in, we need to process them

$f = $_GET{"f"};

if ($f == "CHK") {
	printsignMeUpSQL($_GET{"pid"}, $_GET{"uid"});
}

if ($f == "QWT") {
	printiQuitSQL($_GET{"pid"}, $_GET{"uid"});
}

if ($f == "STT") {
	printgameChangerSQL($_GET{"pid"}, $_GET{"stt"});
	addUpdateSQL($_GET{"uid"}, "PUZ", "I have set the status of puzzle,  ".$_GET{"ttl"} ." to ".$_GET{"stt"} .".");
}

if ($f == "ANS") {
	printeurekaSQL($_GET{"pid"}, $_GET{"ans"});
	addUpdateSQL($_GET{"uid"}, "PUZ", "".$_GET{"ttl"} ." is solved! (The answer was ".$_GET{"ans"} .".");
}

if ($f == "NPL") {
	printthepuzzleiswhereSQL($_GET{"pid"}, $_GET{"link"});
}

if ($f == "NPS") {
	printworkrelocationSQL($_GET{"pid"}, $_GET{"link"});
}

if ($f == "NPN") {
	printknightswhonolongersayniSQL($_GET{"pid"}, $_GET{"ttl"});
}

if ($f == "APM") {
	printnewDaddySQL($_GET{"pid"}, $_GET{"mid"});
}

if ($f == "RPM") {
	printabandonedSQL($_GET{"pid"}, $_GET{"mid"});
}

if ($f == "NWS") {
	printaddUpdateSQL($_GET{"uid"}, $_GET{"code"}, $_GET{"news"});
}

if ($f == "ANM" || $f == "ANP" || $f == "APIM") {
	if (checkForExistingPuzzleSQL($_GET{"ttl"}) == 0) {
		$url = $_GET{"url"};
		if (substr($url, -3, 3) == "URL") {
			$url = substr($url, 0, strlen($url)-3);
		}

		$slack_channel = createNewSlackChannel($_GET{"ttl"});
		$message       = "";
		$file_id       = create_file_from_template($_GET["ttl"]);
		if ($f == "ANM") {
			addNewMetaSQL($_GET{"ttl"}, $url, $file_id, $slack_channel);
			$message = "A new metapuzzle, ".$_GET{"ttl"} .", has been added.";
		}
		if ($f == "ANP") {
			addLoosePuzzleSQL($_GET{"ttl"}, $url, $file_id, $slack_channel);
			$message = "A new puzzle, ".$_GET{"ttl"} .", has been added.";
		}
		if ($f == "APIM") {
			addPuzzleInMetaSQL($_GET{"ttl"}, $url, $_GET{"par"}, $file_id, $slack_channel);
			$message = "A new puzzle, ".$_GET{"ttl"} .", has been added.";
		}
		addUpdateSQL($_GET['uid'], "PUZ", $message);
	} else {
		print"E-42";
	}
}

if ($f == "DPZ") {
	printdeletePuzzleSQL($_GET{"pid"});
}

if ($f == "PRO") {
	printpromotePuzzleSQL($_GET{"pid"});
}

if ($f == "UNS") {
	printupdateNotesSQL($_GET{"pid"}, $_GET{"nts"});
}

?>
