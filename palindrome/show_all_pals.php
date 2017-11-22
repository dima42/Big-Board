<?php
require_once "sitevars.php";
Global $link;
$link = connectToDB();

print "<html><body>";
print "<H4>All Palindrome members on the Big Board</h4><ol>";

$query = "select pal_usr_nme as WHO from pal_usr_tbl order by 1";
$query_resource =  $link->query($query);
if ($link->error != "" || $link->error != NULL) {
    print("getPuzzles error ".$link->error);
}

while ($row = $query_resource->fetch_array(MYSQLI_ASSOC)) {
    print "<li>" . $row["WHO"];
    print "</li>";
}

print "</ol></body></html>";
?>
