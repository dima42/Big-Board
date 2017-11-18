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
	return  "0BwQVTWNxkZQaeWF6YkJzOWV1Y0k"; // current Mystery Hunt 2014//All Puzzles folder	
}

function getPalindromesName() {
	return "Dammmit I'm Mad";
}

function getPalindromeRoomNumbers() {
    return "4-159 (main), 4-153, 4-163 (after 8pm Friday)";
}

function getPalindromePhoneNumber() {
    return "617-715-4577";
}
?>