<?
function pull_back_the_curtain($debugging_text) {
    //print "<P><font color=red>".$debugging_text."</font></p>";
}

function error_debug($link) {
    $error = $link->error;
    if ($error != "" || $error != NULL) {
        pull_back_the_curtain("Error: ".$error);
    }
}

function getData($query) {
    Global $link;
    pull_back_the_curtain($query);
    $query_resource = $link->query($query);
    error_debug($link);
    return $query_resource;
}

function getUserID($google_id, $display_name) {
    Global $link;
    // see if user is in database
	$query = "select pal_id as UID from pal_usr_tbl where pal_ggl_id = ".$google_id;
	$results = getData($query);

	// if results are empty, user does not exist, and we should create a user record for it, returning ID

	if ($results->num_rows == 0) {
		return createUser($google_id, $display_name, $_SESSION['refresh_token']);
	}
	$row = $results->fetch_array(MYSQLI_ASSOC);
	return $row['UID'];
}

function getUserDriveID($root_id, $display_name) {
    Global $link;
    $query = "select pal_id as UID from pal_usr_tbl where pal_ggl_id = '".$root_id."'";
    pull_back_the_curtain($query);
	$results = $link->query($query);
    error_debug($link);

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
	$results = $link->query($query);
    error_debug($link);

	// if results are empty, return 0. there is separate logic to determine if we should create a user
	if ($results->num_rows == 0) {
		return 0;
	}
	// otherwise, return the refresh token.
	$row = $results->fetch_array(MYSQLI_ASSOC);
	return $row['REFRESH_TOKEN'];
}

function setUserRefreshToken($pal_id, $refresh_token) {
    Global $link;
	$query = "update pal_usr_tbl set pal_ggl_rfr = ".$refresh_token." where pal_id = '".$pal_id."'";
	pull_back_the_curtain($query);
	$results = $link->query($query);
    error_debug($link);

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
	$query_resource = $link->query($query);
    error_debug($link);

	return $link->insert_id;
}

function createUser($google_id, $display_name, $refresh) {
    Global $link;
	$query = "insert into pal_usr_tbl (pal_usr_nme, pal_ggl_rfr, pal_ggl_id) ".
			 "values (".
			 "'" . $display_name . "', " .
			 "'" . $refresh . "', " .
			 "'" . $google_id . "' " .
			 ")";
	pull_back_the_curtain($query);
	$query_resource = $link->query($query);
    error_debug($link);
	return $link->insert_id;
}

function signMeUpSQL($pid, $uid) {
    Global $link;
	$query = "insert into puz_chk_out (puz_id, usr_id) values (" . $pid . ", " . $uid . ")";
	$link->query($query);
	if ($link->error == "") {
		return "You are now signed up for this puzzle.";
	} else {
		return $link->error ." (" . $query . ")";
	}
}

function iQuitSQL($pid, $uid) {
    Global $link;
	$query = "update puz_chk_out set chk_in = current_timestamp where puz_id = " . $pid . " and usr_id = " . $uid;
	$link->query($query);
	if ($link->error == "") {
		return "You are no longer working on this puzzle.";
	} else {
		return $link->error ." (".$query.")";
	}
}

function gameChangerSQL($pid, $stt) {
    Global $link;
	// if $stt is featured, then it is the only puzzle that can have that status
	$query = "";
	if ($stt == "featured") {
		$query = "update puz_tbl set puz_stt = 'priority' where puz_stt = 'featured'; ";
		$link->query($query);
	}
	$query = "update puz_tbl set puz_ans = '', puz_stt='" . $stt . "' where puz_id = " . $pid;
	$link->query($query);
	if ($link->error == "") {
		return "I have changed the status of this puzzle to " . $stt;
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

function eurekaSQL($pid, $ans) {
    Global $link;
	$query = "update puz_tbl set puz_ans = '" . $ans . "', puz_stt='solved' where puz_id = " . $pid;
	$link->query($query);
	$query = "update puz_chk_out set chk_in = current_timestamp where puz_id = " . $pid;
	$link->query($query);
	if ($link->error == "") {
		return "Another answer! We are well on our way to winning!";
	} else {
		return $link->error . " (" . $query . ")";
	}
}

function thepuzzleiswhereSQL($pid, $link) {
    Global $link;
	$query = "update puz_tbl set puz_url = '" . $link . "' where puz_id = " . $pid;
	$link->query($query);
	if ($link->error == "") {
		return "Okay! We'll go there instead .  (" . $link . ")";
	} else {
		return $link->error . " (" . $query . ")";
	}
}

function workrelocationSQL($pid, $link) {
    Global $link;
	$query = "update puz_tbl set puz_spr = '" . $link . "' where puz_id = " . $pid;
	$link->query($query);
	if ($link->error == "") {
		return "Okay! We'll work there instead .  (" . $link . ")";
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

function knightswhonolongersayniSQL($pid, $title) {
    Global $link;
	$query = "update puz_tbl set puz_ttl = '" . $title . "' where puz_id = " . $pid;
	$link->query($query);
	if ($link->error == "") {
		return "Okay! This puzzle's title has changed .  (" . $title . ")";
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

function newDaddySQL($pid, $mid) {
    Global $link;
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (" . $pid . ", " . $mid . ")";
	$link->query($query);
	if ($link->error == "") {
		return "This puzzle is now in a new metapuzzle.";
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

function abandonedSQL($pid, $mid) {
    Global $link;
	$query = "delete from puz_rel_tbl where puz_id = " . $pid . " and puz_par_id = " . $mid . "";
	$link->query($query);
	if ($link->error == "") {
		return "This puzzle is now in no longer part of that metapuzzle.";
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

function addUpdateSQL($uid, $cde, $nws) {
    Global $link;
	$query = "insert into pal_upd_tbl (pal_upd_code, pal_upd_txt, usr_id) values ('" . $cde . "', '" . $nws . "', " . $uid . ")";
	$link->query($query);
	if ($link->error == "") {
		return $nws;
	} else {
		return $link->error  . " (" . $query . ")";
	}
}

// Add a puzzle. Each of the next three functions do the same basic thing, but with specific results.
function addNewMetaSQL($ttl, $url, $fid, $slack) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid, $slack);
	addPuzzleRelationSQL($new_puzzle_id, $new_puzzle_id);
	return "M".$new_puzzle_id;
}

function addLoosePuzzleSQL($ttl, $url, $fid, $slack) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid, $slack);
	return "P".$new_puzzle_id;
}

function addPuzzleInMetaSQL($ttl, $url, $par, $fid, $slack) {
    Global $link;
	$new_puzzle_id = addPuzzleSQL($ttl, $url, $fid, $slack);
	addPuzzleRelationSQL($new_puzzle_id, $par);
	return "P".$new_puzzle_id."M".$par;
}

function addPuzzleSQL($ttl, $url, $fid, $slack) {
    Global $link;
    $ttl = $link->real_escape_string($ttl);
	$query = "insert into puz_tbl (puz_ttl, puz_url, puz_spr, slack) " .
        "values ('" . $ttl . "', '" . $url . "', 'https://docs.google.com/spreadsheet/ccc?key=" . $fid."', '" . $slack . "')";
	$link->query($query);
	$new_puz_id = $link->insert_id;
	return $new_puz_id;
}

function addPuzzleRelationSQL($pid, $par) {
    Global $link;
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (".$pid.", ".$par.")";
	$link->query($query);
}

function deletePuzzleSQL($pid) {
    Global $link;
	// there are three places to remove puzzles...the puzzle table, puzzle check out, and puzzle relation table
	$query = "delete from puz_tbl where puz_id = ".$pid;
	$link->query($query);
	$query = "delete from puz_rel_tbl where puz_id = ".$pid." or puz_par_id = ".$pid;
	$link->query($query);
	$query = "delete from puz_chk_out where puz_id = ".$pid;
	$link->query($query);
	if ($link->error == "") {
		return "This puzzle has been erased from the database.";
	} else {
		return $link->error ." (".$query.")";
	}
}

function promotePuzzleSQL($pid) {
    Global $link;
	// there are three places to remove puzzles...the puzzle table, puzzle check out, and puzzle relation table
	$query = "insert into puz_rel_tbl (puz_id, puz_par_id) values (".$pid.", ".$pid.")";
	$link->query($query);
	if ($link->error == "") {
		return "This puzzle is now a metapuzzle.";
	} else {
		return $link->error ." (".$query.")";
	}
}

function checkForExistingPuzzleSQL($title) {
    Global $link;
    $title = $link->real_escape_string($title);
	$query = "select count(*) as TITLE_COUNT from puz_tbl ".
			 "where puz_ttl = '" . $title . "'";
    $results = $link->query($query);
    $row = $results->fetch_array(MYSQLI_ASSOC);
	return $row["TITLE_COUNT"];
}

function updateNotesSQL($pid, $notes) {
    Global $link;
	$query = "update puz_tbl set puz_notes = '".$notes."' where puz_id = ".$pid;
	$link->query($query);
	if ($link->error == "") {
		return "Okay! This puzzle's notes have changed. (".$notes.")";
	} else {
		return $link->error ." (".$query.")";
	}
}
?>
