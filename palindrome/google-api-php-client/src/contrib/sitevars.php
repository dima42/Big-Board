<?php

function connectToDB() {
	$gConnection = mysql_connect('localhost', 'spandext_foggy', 'sm4rg4nA');
	if (!$gConnection) {}
	mysql_select_db('spandext_palin') or writeHeader('Could not select database');

	return "Success";
}
?>