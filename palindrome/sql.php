<?
function error_debug($link) {
    $error = $link->error;
    if ($error != "" || $error != NULL) {
        pull_back_the_curtain("getPuzzles error ".$error);
    }
}

function getPuzzles() {
    Global $link;
	$query =  	"select b.puz_id as PUZID, a.puz_ttl as METPUZ, b.puz_ttl INDPUZ, b.puz_ans PUZANS, b.puz_url PUZURL, b.puz_notes PUZNTS, ".
				"b.puz_spr PUZSPR, b.puz_stt STATUS, c.puz_par_id = c.puz_id as META ".
				"from puz_tbl b left join (puz_rel_tbl c, puz_tbl a) ".
				"on (b.puz_id = c.puz_id and c.puz_par_id = a.puz_id) ".
				"order by (c.puz_par_id is NOT NULL), c.puz_par_id desc, META desc, b.puz_id";
	pull_back_the_curtain($query);
	$query_resource =  $link->query($query);
    error_debug($link);
	return $query_resource;
}

function getUnsolvedPuzzles() {
    Global $link;
	$query =  	"select b.puz_id as PUZID, a.puz_ttl as METPUZ, b.puz_ttl INDPUZ, b.puz_ans PUZANS, b.puz_url PUZURL, b.puz_notes PUZNTS, ".
				"b.puz_spr PUZSPR, b.puz_stt STATUS, c.puz_par_id = c.puz_id as META ".
				"from puz_tbl b left join (puz_rel_tbl c, puz_tbl a) ".
				"on (b.puz_id = c.puz_id and c.puz_par_id = a.puz_id) ".
				"where b.puz_stt != 'solved' ".
				"order by (c.puz_par_id is NOT NULL), c.puz_par_id desc, META desc, b.puz_id";
	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("getPuzzles error ".mysql_error()); }
	return $query_resource;
}

function getFeaturedPuzzleIDSQL() {
    Global $link;
	$query = "select puz_id as PUZID from puz_tbl b where puz_stt = 'featured'";
	pull_back_the_curtain($query);
	$query_resource =  $link->query($query);
    error_debug($link);
	return $query_resource;
}

function getPuzzleAssignments() {
    Global $link;
	$query =  	"select puz_id as SNACK, count(*) as ANTS ".
				"from puz_chk_out a ".
				"where chk_in is NULL and puz_id in (select puz_id from puz_tbl where puz_stt != 'solved') ".
				"group by puz_id";
	pull_back_the_curtain($query);
	$query_resource =  $link->query($query);
    error_debug($link);
	return $query_resource;
}

function getUpdatesSQL() {
    Global $link;
	$query =  	"select a.pal_upd_txt as NEWS, a.upd_tme as WHN, b.pal_usr_nme as WHO, a.pal_upd_code as TYP ".
				"from pal_upd_tbl a, pal_usr_tbl b ".
				"where a.usr_id = b.pal_id ".
				"order by a.upd_tme desc";
	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("getPuzzles error ".mysql_error()); }
	return $query_resource;
}
function findUser($google_id) {
    Global $link;
	$query = "select pal_id as UID from pal_usr_tbl where pal_ggl_id = ".$google_id;

	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("findUser error ".mysql_error()); }
	return $query_resource;
}

function getUserID($google_id, $display_name) {
    Global $link;
	// see if user is in database
	$results = findUser($google_id);

	// if results are empty, user does not exist, and we should create a user record for it, returning ID

	if (mysql_num_rows($results) == 0) {
		return createUser($google_id, $display_name, $_SESSION['refresh_token']);
	}
	$row=mysql_fetch_array($results);
	return $row['UID'];
}

function getUserDriveID($root_id, $display_name) {
    Global $link;
    $query = "select pal_id as UID from pal_usr_tbl where pal_ggl_id = '".$root_id."'";
    pull_back_the_curtain($query);
	$results =  $link->query($query);
	if ($link->error != "" || $link->error != NULL) { pull_back_the_curtain("findUser error ".mysqli_error()); }

	// if results are empty, return 0. there is separate logic to determine if we should create a user
	if ($results->num_rows == 0) {
		return 0;
	}

	// otherwise, return the ID that we found.
	$row = $results->fetch_array(MYSQLI_ASSOC);
	return $row['UID'];
}

function getUserRefreshToken($pal_id) {
    Global $link;
	$query = "select pal_ggl_rfr as REFRESH_TOKEN from pal_usr_tbl where pal_id = '".$pal_id."'";
	pull_back_the_curtain($query);
	$results =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("findUser error ".mysql_error()); }

	// if results are empty, return 0. there is separate logic to determine if we should create a user
	if (mysql_num_rows($results) == 0) {
		return 0;
	}
	// otherwise, return the refresh token.
	$row=mysql_fetch_array($results);
	return $row['REFRESH_TOKEN'];
}

function setUserRefreshToken($pal_id, $refresh_token) {
    Global $link;
	$query = "update pal_usr_tbl set pal_ggl_rfr = ".$refresh_token." where pal_id = '".$pal_id."'";
	pull_back_the_curtain($query);
	$results =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("findUser error ".mysql_error()); }

	return 1;
}

function createUserDriveID($google_id, $display_name) {
    Global $link;
	$query = "insert into pal_usr_tbl (pal_usr_nme, pal_ggl_rfr, pal_ggl_id) ".
			 "values (".
			 "'".$display_name."', ".
			 "'".$_SESSION['refresh_token']."', ".
			 "'".$google_id."' ".
			 ")";
	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("findUser error ".mysql_error()); }
	return mysql_insert_id();
}

function createUser($google_id, $display_name, $refresh) {
    Global $link;
	$query = "insert into pal_usr_tbl (pal_usr_nme, pal_ggl_rfr, pal_ggl_id) ".
			 "values (".
			 "'".$display_name."', ".
			 "'".$refresh."', ".
			 "'".$google_id."' ".
			 ")";
	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("findUser error ".mysql_error()); }
	return mysql_insert_id();
}

function getCurrentPuzzleSQL($user_id) {
    Global $link;
	$query = "select puz_id as PUZID, chk_out as CHECKOUT from puz_chk_out where usr_id = ".$user_id." and chk_in is null";
	pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("get current puzzles error ".mysql_error()); }
	return $query_resource;
}

function signMeUpSQL($pid, $uid) {
    Global $link;
	$query = "insert into puz_chk_out (puz_id, usr_id) values (".$pid.", ".$uid.")";
	mysql_query($query);
	if (mysql_error() == "") {
		return "You are now signed up for this puzzle.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function iQuitSQL($pid, $uid) {
    Global $link;
	$query = "update puz_chk_out set chk_in = current_timestamp where puz_id = ".$pid." and usr_id = ".$uid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "You are no longer working on this puzzle.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function gameChangerSQL($pid, $stt) {
    Global $link;
	// if $stt is featured, then it is the only puzzle that can have that status
	$query = "";
	if ($stt == "featured") {
		$query = "update puz_tbl set puz_stt = 'priority' where puz_stt = 'featured'; ";
		mysql_query($query);
	}
	$query = "update puz_tbl set puz_ans = '', puz_stt='".$stt."' where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "I have changed the status of this puzzle to ".$stt;
	} else {
		return mysql_error()." (".$query.")";
	}
}

function eurekaSQL($pid, $ans) {
    Global $link;
	$query = "update puz_tbl set puz_ans = '".$ans."', puz_stt='solved' where puz_id = ".$pid;
	mysql_query($query);
	$query = "update puz_chk_out set chk_in = current_timestamp where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "Another answer! We are well on our way to winning!";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function thepuzzleiswhereSQL($pid, $link) {
    Global $link;
	$query = "update puz_tbl set puz_url = '".$link."' where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "Okay! We'll go there instead. (".$link.")";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function workrelocationSQL($pid, $link) {
    Global $link;
	$query = "update puz_tbl set puz_spr = '".$link."' where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "Okay! We'll work there instead. (".$link.")";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function knightswhonolongersayniSQL($pid, $title) {
    Global $link;
	$query = "update puz_tbl set puz_ttl = '".$title."' where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "Okay! This puzzle's title has changed. (".$title.")";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function newDaddySQL($pid, $mid) {
    Global $link;
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (".$pid.", ".$mid.")";
	mysql_query($query);
	if (mysql_error() == "") {
		return "This puzzle is now in a new metapuzzle.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function abandonedSQL($pid, $mid) {
    Global $link;
	$query = "delete from puz_rel_tbl where puz_id = ".$pid." and puz_par_id = ".$mid."";
	mysql_query($query);
	if (mysql_error() == "") {
		return "This puzzle is now in no longer part of that metapuzzle.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function addUpdateSQL($uid, $cde, $nws) {
    Global $link;
	$query = "insert into pal_upd_tbl (pal_upd_code, pal_upd_txt, usr_id) values ('".$cde."', '".$nws."', ".$uid.")";
	mysql_query($query);
	if (mysql_error() == "") {
		return $nws;
	} else {
		//print "<!--".mysql_error()." (".$query.")-->";
		return mysql_error()." (".$query.")";
	}
}

function getMetaSQL($pid) {
    Global $link;
	$query = "select a.puz_id as PUZID, a.puz_ttl as PUZNME, a.puz_url as PURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as PUZSTT, a.puz_notes as PUZNOT, ".
				"c.pal_id as UID, c.pal_usr_nme as UNAME, (a.puz_id = ".$pid.") as META ".
				"from puz_tbl a left join (puz_chk_out b, pal_usr_tbl c) ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL ".
				"where a.puz_id in (select puz_id from puz_rel_tbl where puz_par_id = ".$pid.") ".
				"order by META desc, a.puz_id";
	mysql_query($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("mymeta error ".mysql_error()); }
	return $query_resource;
}

function getLoosePuzzlesSQL() {
    Global $link;
	$query = "select a.puz_id as PUZID, a.puz_ttl as PUZNME, a.puz_url as PURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as PUZSTT, a.puz_notes as PUZNOT, ".
				"c.pal_id as UID, c.pal_usr_nme as UNAME, FALSE as META ".
				"from puz_tbl a left join (puz_chk_out b, pal_usr_tbl c) ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL ".
				"where a.puz_id not in (select puz_id from puz_rel_tbl) ".
				"order by a.puz_id";
	mysql_query($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("mymeta error ".mysql_error()); }
	return $query_resource;
}

function getPuzzleSQL($pid) {
    Global $link;
	$query = "select a.puz_id as PUZID, a.puz_ttl as PUZNME, a.puz_url as PURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as PUZSTT, a.puz_notes as PUZNOT, ".
				"c.pal_id as UID, c.pal_usr_nme as UNAME ".
				"from puz_tbl a left join (puz_chk_out b, pal_usr_tbl c) ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL ".
				"where a.puz_id = ".$pid."";
	mysql_query($query); pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("myPuzzle error ".mysql_error()); }
	return $query_resource;
}


function getAllMetasSQL($pid) {
    Global $link;
	$query = "select a.puz_id as MID, a.puz_ttl as MTTL, sum(b.puz_id = ".$pid.") as INMETA from puz_tbl a, puz_rel_tbl b where a.puz_id = b.puz_par_id group by a.puz_id, a.puz_ttl";
	mysql_query($query); pull_back_the_curtain($query);
	$query_resource =  mysql_query($query);
	if (mysql_error() != "" || mysql_error() != NULL) { pull_back_the_curtain("myPuzzle error ".mysql_error()); }
	return $query_resource;
}

function getLatestTeamUpdateSQL() {
    Global $link;
	$query = "select a.pal_upd_txt as NEWS, b.pal_usr_nme as WHO from pal_upd_tbl a, pal_usr_tbl b where a.pal_upd_code = 'URG' and a.usr_id = b.pal_id ".
				"order by a.row_id";
    // $link->query($query);
    // pull_back_the_curtain($query);
	$query_resource =  $link->query($query);
	if ($link->error != "" || $link->error != NULL) {
        pull_back_the_curtain("myPuzzle error ".$link->error);
    }
	return $query_resource;
}

// each of the next three functions do the same basic thing, but with specific results.
function addNewMetaSQL($ttl, $url, $fid) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid);
	addPuzzleRelationSQL($new_puzzle_id, $new_puzzle_id);
	return "M".$new_puzzle_id;
}
function addLoosePuzzleSQL($ttl, $url, $fid) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid);
	return "P".$new_puzzle_id;
}
function addPuzzleInMetaSQL($ttl, $url, $par, $fid) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid);
	addPuzzleRelationSQL($new_puzzle_id, $par);
	return "P".$new_puzzle_id."M".$par;
}

function addPuzzleSQL($ttl, $url, $fid) {
    Global $link;
	$query = "insert into puz_tbl (puz_ttl, puz_url, puz_spr) values ('".$ttl."', '".$url."', 'https://docs.google.com/spreadsheet/ccc?key=".$fid."')";
	mysql_query($query);
	$new_puz_id = mysql_insert_id();
	return $new_puz_id;
}

function addPuzzleRelationSQL($pid, $par) {
    Global $link;
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (".$pid.", ".$par.")";
	mysql_query($query);
}

function deletePuzzleSQL($pid) {
    Global $link;
	// there are three places to remove puzzles...the puzzle table, puzzle check out, and puzzle relation table
	$query = "delete from puz_tbl where puz_id = ".$pid;
	mysql_query($query);
	$query = "delete from puz_rel_tbl where puz_id = ".$pid." or puz_par_id = ".$pid;
	mysql_query($query);
	$query = "delete from puz_chk_out where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "This puzzle has been erased from the database.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function promotePuzzleSQL($pid) {
    Global $link;
	// there are three places to remove puzzles...the puzzle table, puzzle check out, and puzzle relation table
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (".$pid.", ".$pid.")";
	mysql_query($query);
	if (mysql_error() == "") {
		return "This puzzle is now a metapuzzle.";
	} else {
		return mysql_error()." (".$query.")";
	}
}

function getStatusProportionsSQL() {
    Global $link;
	// let's get the specific proportion of statuses
	$query = "SELECT puz_stt as PUZSTT, COUNT(*) PUZSTTSUM FROM puz_tbl GROUP BY puz_stt";
    // $link->query($query);
    // pull_back_the_curtain($query);
	$query_resource =  $link->query($query);
    error_debug($link);
	return $query_resource;
}

function checkForExistingPuzzleSQL($title) {
    Global $link;
	$query = "select count(*) as TITLE_COUNT from puz_tbl ".
			 "where puz_ttl = '".$title."'";
	$row = mysql_fetch_array(mysql_query($query));
	return ($row["TITLE_COUNT"]);
}

function updateNotesSQL($pid, $notes) {
    Global $link;
	$query = "update puz_tbl set puz_notes = '".$notes."' where puz_id = ".$pid;
	mysql_query($query);
	if (mysql_error() == "") {
		return "Okay! This puzzle's notes have changed. (".$notes.")";
	} else {
		return mysql_error()." (".$query.")";
	}
}
?>
