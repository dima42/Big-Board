<?php

require_once "sitevars.php";
require_once "slack_functions.php";
connectToDB();
print "<html><body>";
print "<H4>All Puzzles In The Database</h4><ol>";

print "<p><table border=0>";
$query =  	"select puz_id, puz_ttl, puz_stt, puz_ans, puz_url, puz_spr from puz_tbl tbl order by puz_ttl";
$query_resource =  mysql_query($query);
if (mysql_error() != "" || mysql_error() != NULL) { print("getPuzzles error ".mysql_error()); }

while ($row = mysql_fetch_array($query_resource)) {
	print "<tr><td>";
    	print  $row["puz_id"];
	print "</td><td>";
    	print  $row["puz_ttl"];
	print "</td><td>";
    	print  convertToSlackChannel($row["puz_ttl"]);
	print "</td><td>";
    	print  $row["puz_stt"];
	print "</td><td>";
    	print  $row["puz_ans"];
	print "</td><td>";
    	print  $row["puz_url"];
	print "</td><td>";
    	print  $row["puz_spr"];
	print "</td></tr>";
}	

print "</table></p>";

/*print "<p><table>";
$query =  	"select puz_ttl, puz_stt, puz_ans from puz_tbl tbl order by puz_ans";
$query_resource =  mysql_query($query);
if (mysql_error() != "" || mysql_error() != NULL) { print("getPuzzles error ".mysql_error()); }

while ($row = mysql_fetch_array($query_resource)) {
	print "<tr><td>";
    	print  $row["puz_ttl"];
	print "</td><td>";
    	print  $row["puz_stt"];
	print "</td><td>";
    	print  $row["puz_ans"];
	print "</td><tr>";
}	

print "</table>";
*/
print "</body></html>";
?>