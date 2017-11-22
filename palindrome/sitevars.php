<?php
function connectToDB() {
	$gConnection = mysql_connect('localhost', 'spandext_foggy', 'sm4rg4nA');
	if (!$gConnection) {}
	mysql_select_db('spandext_palin') or writeHeader('Could not select database');

	return "Success";
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
