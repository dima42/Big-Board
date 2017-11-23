<?
require_once 'sitevars.php';
require_once 'htmlcss.php';
require_once 'slack_functions.php';

function showDatabaseError($error_code) {
	if ($error_code != "" || $error_code != NULL) {
		print "<p>I'm sorry, but a database error is puzzling us. $error_code </p>";
	}
}

function displayPuzzles($my_puzzle_list) {
    $statuses = array(
        "featured" => 0,
        "priority" => 0,
        "open" => 0,
        "stuck" => 0,
        "solved" => 0,
    );
    $total_puzzles = 0;
    $results = getStatusProportionsSQL();

    // Count up all the puzzles in each status
    while ($row = $results->fetch_assoc()) {
        $statuses[$row["PUZSTT"]] = $row["PUZSTTSUM"];
        $total_puzzles += $row["PUZSTTSUM"];
    }

	$whos_on_what = getPuzzleAssignments();
    $whos_on_what_array = array();
    while ($row = $whos_on_what->fetch_assoc()) {
    	$whos_on_what_array[$row["SNACK"]] = $row["ANTS"];
	}
	$results = getPuzzles();
    // $bgcolor = "#ffffff";
    $just_starting = TRUE;
    $puzzle_count = 0;

    $result_backup = $results;
    // TODO: Modify the results with the followign logic
	while ($row = $result_backup->fetch_assoc()) {
    	// some specifics for table layout
        // $cell_width = 150;
        $col_width = 6;
        $title_limit = 20;
		// link to spreadsheet first

        // TODO: make up slack channel name and create it. Then add this slug to the array
        // TODO: first post in that slack channel should be all the links.
		$puz_reduced = convertToSlackChannel($row['INDPUZ']);

		// TODO: if there are notes, show them
        $notestext = "<br/>";
        if ($row['PUZNTS'] != "") {
            //"<span class='fake_button' onclick='show_notes(\"".addslashes($row['PUZNTS'])."\")'>note</span>";
            if ($row["STATUS"] != "solved") {
                $notestext = "<p><span class='fake_button'  onclick='show_notes(\"".addslashes($row['PUZNTS'])."\")'>".substr($row['PUZNTS'],0,$title_limit);
                if (strlen($row['PUZNTS']) > $title_limit) {
                    $notestext .= "...";
                }
                $notestext .= "</span>";
            }
        }

		// TODO: if the title of the puzzle is too long, shorten it. Maybe do this in Twig
		$puzzle = $row['INDPUZ'];
        if (strlen($row['INDPUZ']) >$title_limit) {
        	if (substr($puzzle,0,2) == "A " || substr($puzzle,0,4) == "The ") {
            	$puzzle = substr($puzzle,strpos($puzzle," "),strlen($puzzle));
            }
        	$puzzle = substr($puzzle,0,$title_limit-1)."...";
        }

		// TODO: Show just uzzle title if there's no link
		if ($row["PUZURL"] != "") {
			$puzzle_link = "<A HREF='".$row["PUZURL"]."' target='_blank' alt='".$row['INDPUZ']."'>".$puzzle."</A>";
		} else {
			$puzzle_link = $puzzle;
		}

		// TODO: Show who's working on puzzle. Are we still using this?
        if (array_key_exists($row["PUZID"], $whos_on_what_array)) {
            $ants = $whos_on_what_array[$row["PUZID"]];
        } else {
            $ants = "";
        }

        // TODO: If I'm working on puzzle, indicate that. Are we still using this?
        if (array_key_exists($row["PUZID"], $my_puzzle_list)) {
			$on_puzzle = "onit";
		} else {
			$on_puzzle = "noton";
		}

        // $is_puz_out = /* "<img name='puzchk_".$row["PUZID"]."' src='".$on_puzzle.".png' width=14px height=14px onclick='toggle_Puzzle_Checkout(".$row["PUZID"].");'>".
        				// "&nbsp;<span name='puzwrk_".$row["PUZID"]."'>".$ants."</span>" */
                      // "";

        // TODO Show un-meta'd puzzles
        if ($row["META"] == NULL && $just_starting) {
        	print "<table border=0 cellspacing=0 cellpadding=4>\r\n";
			print "<tr><th align=center valign=top class='MetaRound' width=".$cell_width."px>Puzzles<br/>Not in a Meta<br/><a href='?meta=0'>View all</a></th>\r\n";
			$just_starting = FALSE;
        }
	}

    render('all_puzzles.twig', array(
        'statuses' => $statuses,
        'total_puzzles' => $total_puzzles,
        'puzzles' => $results
    ));
}

function displayMeta($meta_id) {
	if ($meta_id == 0) {
        // TODO: redirect to function for showing unattached puzzles.
		// old code: $results = getLoosePuzzlesSQL();
    }

	$results = getMetaSQL($meta_id);

    render('meta.twig', array(
        'meta_id' => $meta_id,
        'puzzles' => $results
    ));
}

function displayFeature($my_puzzle_list, $puzzle_id) {
    $results = getFeaturedPuzzleIDSQL();
    $featureID = "";
    while ($row = $results->fetch_assoc()) {
        $featureID = $row["PUZID"];
    }

    if ($featureID != "") {
        displayPuzzle($my_puzzle_list, $featureID);
    } else {
        displayPuzzles($my_puzzle_list);
    }
}

function displayPuzzle($puzzle_id) {
    $results = getPuzzleSQL($puzzle_id);

    $puzzle_count = $results->num_rows;
    if ($puzzle_count == 0) {
        // TODO: redirect to error template
        print "<P>This puzzle does not exist. It is a ghost puzzle.</p>";
        return;
    }

    $puzzle = $results->fetch_assoc();
    $metas = getAllMetasSQL($puzzle_id);

    render('puzzle.twig', array(
        'puzzle_id' => $puzzle_id,
        'puzzle' => $puzzle,
        'metas' => $metas
    ));
}

function displayUpdates($filter) {
    $results = getUpdatesSQL();

    render('updates.twig', array(
        'filter' => $filter,
        'updates' => $results
    ));
}

function displayAbandonedPuzzles() {
    // first of all let's get the current spreadsheets in the folder
    $from_the_folder = getDrivesFiles();
    $all_puzzles = array();

    $results = getUnsolvedPuzzles();
    foreach ($results as $row) {
        $currentFile = substr($row['PUZSPR'], strpos($row['PUZSPR'], "ccc?key=") + 8, 44);

        $lastmod = "2017-12-31";
        if (array_key_exists($currentFile, $from_the_folder)) {
            $lastmod = $from_the_folder[$currentFile][1];

            $how_old = (time() - strtotime($lastmod))/60;
            if ($how_old > 60*24) {
                $file_age = intval($how_old/(24*60)) . " days";
            } else if ($how_old > 60) {
                $file_age = intval($how_old/60) . " hrs";
            } else {
                $file_age = intval($how_old) . " min";
            }
        }

        $all_puzzles[$currentFile] = array(
            'FILEAGE' => $file_age,
            'INDPUZ' => $row['INDPUZ'],
            'STATUS' => $row['STATUS'],
            'PUZURL' => $row['PUZURL'],
            'PUZSPR' => $row['PUZSPR'],
            'PUZNTS' => $row['PUZNTS'],
        );
    }

    render('abandoned.twig', array(
        'puzzles' => $all_puzzles
    ));
}
?>
