<?
require_once "sql.php";
require_once "sitevars.php";
require_once "new_file_management.php";
require_once "slack_functions.php";

connectToDB();

// as commands come in, we need to process them

$f = $_GET{"f"};

if ($f == "CHK") { print signMeUpSQL($_GET{"pid"},$_GET{"uid"});}
if ($f == "QWT") { print iQuitSQL($_GET{"pid"},$_GET{"uid"}); }
if ($f == "STT") { 
	print gameChangerSQL($_GET{"pid"},$_GET{"stt"});
	addUpdateSQL($_GET{"uid"},"PUZ","I have set the status of puzzle, ".$_GET{"ttl"}." to ".$_GET{"stt"}.".");	
}
if ($f == "ANS") { 
	print eurekaSQL($_GET{"pid"},$_GET{"ans"});
	addUpdateSQL($_GET{"uid"},"PUZ","".$_GET{"ttl"}." is solved! (The answer was ".$_GET{"ans"}.".");	
}
if ($f == "NPL") { print thepuzzleiswhereSQL($_GET{"pid"},$_GET{"link"});}
if ($f == "NPS") { print workrelocationSQL($_GET{"pid"},$_GET{"link"});}
if ($f == "NPN") { print knightswhonolongersayniSQL($_GET{"pid"},$_GET{"ttl"});}
if ($f == "APM") { print newDaddySQL($_GET{"pid"},$_GET{"mid"});}
if ($f == "RPM") { print abandonedSQL($_GET{"pid"},$_GET{"mid"});}
if ($f == "NWS") { print addUpdateSQL($_GET{"uid"},$_GET{"code"},$_GET{"news"});}
if ($f == "ANM" || $f == "ANP" || $f == "APIM") {
	if (checkForExistingPuzzleSQL($_GET{"ttl"})==0) {
	  $url = $_GET{"url"}; if (substr($url,-3,3) == "URL") { $url = substr($url,0,strlen($url)-3); }
	  if ($f == "ANM") {$file_id = create_new_file($_GET["ttl"]); 
	  					addUpdateSQL($_GET['uid'],"PUZ","A new metapuzzle, ".$_GET{"ttl"}.", has been added.");
						createNewSlackChannel($_GET{"ttl"});
						print addNewMetaSQL($_GET{"ttl"},$url, $file_id);}
	  if ($f == "ANP") { $file_id = create_new_file($_GET["ttl"]);
	  					addUpdateSQL($_GET['uid'],"PUZ","A new puzzle, ".$_GET{"ttl"}.", has been added.");
						createNewSlackChannel($_GET{"ttl"});
						print addLoosePuzzleSQL($_GET{"ttl"},$url, $file_id); $type="puzzle";}
	  if ($f == "APIM") { $file_id = create_new_file($_GET["ttl"]);
	  					addUpdateSQL($_GET['uid'],"PUZ","A new puzzle, ".$_GET{"ttl"}.", has been added.");
						createNewSlackChannel($_GET{"ttl"});
						print addPuzzleInMetaSQL($_GET{"ttl"},$url,$_GET{"par"}, $file_id);
						}
	  
	} else {
		print "E-42";	
	}
}
if ($f == "DPZ") { print deletePuzzleSQL($_GET{"pid"});}
if ($f == "PRO") { print promotePuzzleSQL($_GET{"pid"});}
if ($f == "UNS") { print updateNotesSQL($_GET{"pid"},$_GET{"nts"});}

?>