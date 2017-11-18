<?php

require_once "sitevars.php";
connectToDB();
print "<html><body>";
print "<H4>All Palindrome members on the Big Board</h4><ol>";

$query =  	"select pal_usr_nme as WHO from pal_usr_tbl order by 1";
$query_resource =  mysql_query($query);
if (mysql_error() != "" || mysql_error() != NULL) { print("getPuzzles error ".mysql_error()); }

while ($row = mysql_fetch_array($query_resource)) {
    	print "<LI>".$row["WHO"];
}	

print "</ol></body></html>";
?>