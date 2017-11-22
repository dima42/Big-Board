<?php
$DEBUG = false;

if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
    $DEBUG = true;
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

function getPalindromesName() {
	return "Too Long No Loot";
}

function getPalindromeRoomNumbers() {
    return "4-159, 4-146, 4-145";
}

function getPalindromePhoneNumber() {
    return "617 715-4577";
}
?>
