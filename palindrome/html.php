<?
require_once 'sitevars.php';
require_once 'htmlcss.php';
require_once 'slack_functions.php';

function writeKey() {
    $statuses = array();
    $total_puzzles = 0;
	$results = getStatusProportionsSQL();
	while ($row = $results->fetch_assoc()) {
		// link to spreadsheet first
		$statuses[$row["PUZSTT"]] = $row["PUZSTTSUM"];
        $total_puzzles += $row["PUZSTTSUM"];
    }
    print "<p><table border=0 cellspacing=0 cellpadding=4><tr>";
	print "<th align=center valign=middle width=120px onclick='show_puzzle_input(\"M\",0, ".$_SESSION['user_id'].");'>";
	print "<span class='fake_button'>+New Meta</span></th>";
    //print "<span class='clickable'><img src='meta.png' height=25px width=25px alt=\"New Meta\" />&nbsp;<span>New Meta</span></span></th>";
 	print "<th align=center valign=middle width=120px onclick='show_puzzle_input(\"P\",0, ".$_SESSION['user_id'].");'/>";
    print "<span class='fake_button'>+New Puzzle</span></th>";
    //print "<span class='clickable'><img src='puzzle.png' height=25px width=25px alt=\"New Puzzle\" >&nbsp;<span>New Puzzle</span></span></th>";
	print "<th align=center valign=middle class=\"\" width=120px>&nbsp;</th>";

	if (array_key_exists("featured",$statuses)) {
    	print "<th align=center valign=middle class='puzzle featured' width=".(480*$statuses["featured"]/$total_puzzles)."px>Featured<br/>(<a href='index.php?puzzle=F'>View</a>)</th>"; }
	if (array_key_exists("priority",$statuses)) {
    	print "<th align=center valign=middle class='puzzle priority' width=".(480*$statuses["priority"]/$total_puzzles)."px>Priority<br/>(".$statuses["priority"].")</th>"; }
	if (array_key_exists("open",$statuses)) {
    	print "<th align=center valign=middle class='puzzle open' width=".(480*$statuses["open"]/$total_puzzles)."px>Open<br/>(".$statuses["open"].")</th>"; }
	if (array_key_exists("stuck",$statuses)) {
    	print "<th align=center valign=middle class='puzzle stuck' width=".(480*$statuses["stuck"]/$total_puzzles)."px>Stuck<br/>(".$statuses["stuck"].")</th>"; }
	if (array_key_exists("solved",$statuses)) { print "<th align=center valign=middle class='puzzle solved' width=".(480*$statuses["solved"]/$total_puzzles)."px>Solved<br/>(".$statuses["solved"].")</th>"; }
    print "<th><a href='index.php?bylastmod'>All Unsolved<br/>Puzzles</a></th></tr></table></p>";
}

function showDatabaseError($error_code) {
	if ($error_code != "" || $error_code != NULL) {
		print "<p>I'm sorry, but a database error is puzzling us. $error_code </p>";
	}
}

function displayPuzzles($my_puzzle_list) {
	$whos_on_what = getPuzzleAssignments();
    $whos_on_what_array = array();
    while ($row = $whos_on_what->fetch_assoc()) {
    	$whos_on_what_array[$row["SNACK"]] = $row["ANTS"];
	}
	$result = getPuzzles();
	$bgcolor = "#ffffff";
	$just_starting = TRUE; $puzzle_count = 0;

	while ($row = $result->fetch_assoc()) {
    	// some specifics for table layout
        $cell_width = 150; $col_width = 6; $title_limit = 20;
		// link to spreadsheet first
		$sprdlink = "<a href='".$row['PUZSPR']."' target='_blank' style='text-decoration: none;'><span class='fake_button' ";
        if (!array_key_exists($row["PUZID"],$my_puzzle_list)) {
        	//$sprdlink .= " onclick=('toggle_Puzzle_Checkout(".$row["PUZID"].");')";
        }
        $sprdlink .= ">drive</span></a>";
		$puz_reduced = convertToSlackChannel($row['INDPUZ']);
		$slacklink = "<a href='http://palindrome2017.slack.com/messages/".$puz_reduced."' target='_blank' style='text-decoration: none;'><span class='fake_button' ";
        $slacklink .= ">slack</span></a>";

		// display notes?
        $noteslink = "";
        $notestext = "<br/>";
        if ($row['PUZNTS'] != "") {
            $noteslink = "";//"<span class='fake_button' onclick='show_notes(\"".addslashes($row['PUZNTS'])."\")'>note</span>";
            if ($row["STATUS"] != "solved") {
                $notestext = "<p><span class='fake_button'  onclick='show_notes(\"".addslashes($row['PUZNTS'])."\")'>".substr($row['PUZNTS'],0,$title_limit);
                if (strlen($row['PUZNTS']) > $title_limit) {
                    $notestext .= "...";
                }
                $notestext .= "</span>";
            }
        }

		//if the title of the puzzle is too long, shorten it
		$puzzle = $row['INDPUZ'];
        if (strlen($row['INDPUZ']) >$title_limit) {
        	if (substr($puzzle,0,2) == "A " || substr($puzzle,0,4) == "The ") {
            	$puzzle = substr($puzzle,strpos($puzzle," "),strlen($puzzle));
            }
        	$puzzle = substr($puzzle,0,$title_limit-1)."...";
        } else {}
		// if there is a puzzle URL, use it
		if ($row["PUZURL"] != "") {
				$puzzle_link = "<A HREF='".$row["PUZURL"]."' target='_blank' alt='".$row['INDPUZ']."'>".$puzzle."</A>";
		} else {
				$puzzle_link = $puzzle;
		}

        if (array_key_exists($row["PUZID"],$whos_on_what_array)) {
            	$ants = $whos_on_what_array[$row["PUZID"]];
            } else {
            	$ants = "";
            }
		if (array_key_exists($row["PUZID"],$my_puzzle_list)) {
			//oooh something
			$on_puzzle = "onit";
		} else {
			$on_puzzle = "noton";
		}
        $is_puz_out = /* "<img name='puzchk_".$row["PUZID"]."' src='".$on_puzzle.".png' width=14px height=14px onclick='toggle_Puzzle_Checkout(".$row["PUZID"].");'>".
        				"&nbsp;<span name='puzwrk_".$row["PUZID"]."'>".$ants."</span>" */
                      "";

        $answer_field =
            "<input name='puzans_" .
            $row["PUZID"] .
            "' value='".$row["PUZANS"] .
            "' size=" . ($cell_width/7.5) .
            " class='" .
            $row["STATUS"] .
            "' style='border-width:0px; text-align: center;'" .
			" onchange='editAnswer(this, " .
            $row["PUZID"] .
            ", \"" .
            $row["PUZANS"] .
            "\", " .
            $_SESSION['user_id'] .
            ", \"" .
            $row['INDPUZ'] .
            "\")'><br/>\r\n";

        if ($row["META"] == NULL && $just_starting) {
        	print "<table border=0 cellspacing=0 cellpadding=4>\r\n";
			print "<tr><th align=center valign=top class='MetaRound' width=".$cell_width."px>Puzzles<br/>Not in a Meta<br/><a href='?meta=0'>View all</a></th>\r\n";
			$just_starting = FALSE;
        }

		if ($row["META"] == 1) {
			// this is the start of a new table
			if (!$just_starting) {
					print "<td colspan='".($col_width-$puzzle_count)."' width='".($cell_width*($col_width-$puzzle_count))."px'>&nbsp;</td></tr></table><br/>";
					$puzzle_count = 0;
			} else {
				$just_starting = FALSE;
			}
			print "<table border=0 cellspacing=4 cellpadding=4>\r\n";
			print "<tr><th align=left valign=top name='puzzle_".$row['PUZID']."' style='border:1px solid #666666' class='MetaRound ".$row['STATUS']."' width=".$cell_width."px>";
            print $puzzle_link."<br/>\r\n";
			print $answer_field."<br/>\r\n";
			print "<a href='?meta=".$row['PUZID']."' class='fake_button'>info</a>";
            print $sprdlink;
            print $slacklink;
            print "<span class='fake_button' onclick='show_puzzle_input(\"P\",".$row['PUZID'].", ".$_SESSION['user_id'].");'>+puzzle</span>";
            print $noteslink;
            print $notestext;
            print "</th>\r\n";
		} else {
			if ($puzzle_count == $col_width) {
				print "</tr><tr><td>&nbsp;</td>";
				$puzzle_count = 0;
			}

			print "<td align=left valign=top name='puzzle_".$row['PUZID']."'  width=".$cell_width."px class='puzzle ".$row['STATUS']."'>";
            print $puzzle_link."<br/>\r\n";
			print $answer_field."<br/>\r\n";
			print "<a href='?puzzle=".$row['PUZID']."' class='fake_button'>info</a>";
            print $sprdlink;
            print $slacklink;
            print $noteslink;
            print $notestext;
            print "</td>\r\n";
			$puzzle_count += 1;
		}
	}

	print "<td colspan='".($col_width-$puzzle_count)."' width='".($cell_width*($col_width-$puzzle_count))."px'>&nbsp;</td></tr></table><br/>\r\n";
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
