<?php
print <<<_HTML_
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Spandexter Words Display</title>
</head>

<body>
_HTML_;

connectToDB();



$query = "select * from words_tbl";
$result = mysql_query($query);
print "<table>";
while ($row = mysql_fetch_assoc($result)) {
	print "<tr><td>Word</td></tr>";
}
print "</table>";

print <<<_HTML_
</body>
</html>
_HTML_;


function connectToDB() {
	$gConnection = mysql_connect('localhost', 'spandext_foggy', 'sm4rg4nA');
	if (!$gConnection) {
	}
	mysql_select_db('spandext_prd_words') or writeHeader('Could not select database');

	return "Success";
}


?>