<?php
require_once "sitevars.php";

Global $link;
$link = connectToDB();

print "<html><body>";
print "<h4>All Puzzles In The Database</h4><ol>";

print "<p><table border=0>";
$query = "select puz_id, puz_ttl, puz_stt, puz_ans, puz_url, puz_spr from puz_tbl tbl order by puz_ttl";
$query_resource =  $link->query($query);
if ($link->error != "" || $link->error != NULL) {
    print("getPuzzles error ".$link->error);
}

while ($row = $query_resource->fetch_array(MYSQLI_ASSOC)) {
	print "<tr><td>";
    	print  $row["puz_id"];
	print "</td><td>";
    	print  $row["puz_ttl"];
	print "</td><td>";
    	print  $row["slack"];
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
$query_resource =  $link->query($query);
if ($link->error != "" || $link->error != NULL) {
    print("getPuzzles error ".$link->error);
}

while ($row = $query_resource->fetch_array(MYSQLI_ASSOC)) {
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
