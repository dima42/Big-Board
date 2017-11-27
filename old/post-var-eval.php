<?php?>
	<html><body>
	<p>Evaluating all of the post variables</p>
<?
foreach ( $_POST as $key => $value )
{
	print "<P>".$key." = ".$value;
}

foreach ( $_GET as $key => $value )
{
	print "<P>".$key." = ".$value;
}
?><P>Okay, what next</body></html>