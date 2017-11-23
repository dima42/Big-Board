<?
require_once 'sitevars.php';
require_once 'htmlcss.php';
require_once 'slack_functions.php';

// function writeHeader($showTeamInformation) {
// 	$palindromeName = getPalindromesName();
// 	print "<html><head><title>".$palindromeName."</title>";
// 	print "<script type='text/javascript' src='pallap.js'></script>";
// 	writeStylesheet();
// 	print "</head>";
// 	print "<body onload='idle_hands();' onmousemove='idle_hands();' onclick='idle_hands();' onkeydown='idle_hands();'>";
// 	print "<table width='1200'>";
// 	print "<tr><td><a href='index.php'><span style='font-size:18px;font-weight:bold;'>$palindromeName Big Board</span></a></td>";

// 	if ($showTeamInformation) {
// 		print "<td align='right'><strong>HQ</strong>: ";
// 	    print getPalindromeRoomNumbers();
// 	    print "&nbsp;&nbsp;&nbsp;<strong>Phone</strong>  ";
// 	    print getPalindromePhoneNumber();
// 	    print "&nbsp;&nbsp;&nbsp;<strong>E-mail</strong>: team-palindrome@mit.edu<br/>";
// 		print "Username: palindrome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Password: aplanacanal</td>";
// 	}

// 	print "</tr></table><P><em><span id='newscrawl'></span></em></p>";
// 	print "<input type='hidden' id='userid' value='".$_SESSION['user_id']."'/>";
// }

function writeIntro() {
	$results = getLatestTeamUpdateSQL();
    if ($results->num_rows > 0) {
	    while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
	    	$latest_news = str_replace("'","&#39;",$row["NEWS"]);
	        $latest_news_from = " (".$row["WHO"].")";
	    }
    } else {
    	$latest_news = "Type over this text to send out a message.";
        $latest_news_from = "";
    }
	print "<p>News/Chat (<span class='pastNews'><a href='?updates&filter=Y'>previous</a></span>): <input id='UrgentMessage' name='UrgentMessage' value='".$latest_news.$latest_news_from."' style='border: none; "
    		."background-color: #EEEEEE;' size=175 onchange='add_update(this, \"URG\", ".$_SESSION["user_id"].")'/><br /></p>";

	print "<p><a href='http://palindrome2017.slack.com/messages/general' target='_new'>Slack (general)</a>";
	print "&nbsp;&nbsp;&nbsp;&nbsp;<a href='https://plus.google.com/hangouts/_/7lw6rz5zwbkiaozrwld6q77ahaa?authuser=0&hl=en' target='_new'>Hangout 1</a>";
	print "&nbsp;&nbsp;&nbsp;&nbsp;<a href='https://plus.google.com/hangouts/_/ha2iwh7ioc4ctfnehufqfvomzya?authuser=0&hl=en' target='_new'>Hangout 2</a>";
}

function writeInstructions() {
	print "<p><strong>Instructions:</strong> <span class='fake_button' id='instruction_status' onclick='toggle_instructions();'>show</span>";
	print "<div id='instructions'>Enter answers below the name of the puzzle.<br/>";
	print "Use the following codes to change the status of a puzzle: <br/>";
	print "<b>.</b> = the puzzle is open<br/>";
	print "<b>?</b> = the puzzle is stuck<br/>";
	print "<b>!</b> = the puzzle is a priority<br/>";
	print "<b>!!!</b> = the puzzle is the feature puzzle. Only one puzzle can be the featured puzzle.<br/>";
	print "<br/>";
	print "Click <span class='fake_button'>info</span> to view and edit puzzle information (include notes).<br/><br/>";
	print "Click <span class='fake_button'>drive</span> to view the spreadsheet for a puzzle.<br/><br/>";
	print "To nest one metapuzzle inside a metapuzzle: Click info for the metapuzzle. Click view as puzzle. Check off the meta to nest it under.";
	print "</div>";
}

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

function displayMeta($my_puzzle_list, $meta_id) {
	if ($meta_id == 0 ) {
		$results = getLoosePuzzlesSQL();
    } else {
		$results = getMetaSQL($meta_id);
    }
    $puzzle_count = $results->num_rows; $which_puzzle = 0;
    if ($puzzle_count == 0) {
    	print "<P>This does not appear to be a metapuzzle. There are no puzzles that are part of it.";
        return;
    }

    $meta_table = "";
    $meta_table = "<table border=0 cellspacing=0 cellpadding=4><tr><th>Puzzle</th><th>Answer</th><!--<th>Who's On It?</th>--><th>Let's Go!</th></tr>";
    $meta_header = "";
    $current_puz_id = "";
    $current_workers = array();

    while ($row = $results->fetch_assoc()) {
    	$which_puzzle += 1;
    	if ($row["META"] == 1) {
        	$meta_header = "<H2><input class='metaTitle' size=100 name='puzttl_".$row["PUZID"]."' value='".$row["PUZNME"]."' style='background: transparent; border: none;'/ onchange='new_name(this, ".$row["PUZID"].")'></H2>";
            if($row["PUZSTT"] == "solved") {
            	$meta_header .= "<H2 style='color:#FF6600'>This puzzle has been solved: ".$row["PUZANS"]."</H2>";
            }

            $meta_header .= "<table><tr><td><a name='puzurllink_".$row["PUZID"]."' href='".$row["PURL"]."'>Meta URL</a></td><td><input size=40 name='puzurl_".$row["PUZID"]."' value='".$row["PURL"]."' onchange='new_link(this, ".$row["PUZID"].")' /></td></tr>";
            $meta_header .= "<tr><td><a name='puzsprlink_".$row["PUZID"]."' href='".$row["PUZSPR"]."'>Google Doc</a></td><td><input size=40 name='puzspr_".$row["PUZID"]."' value='".$row["PUZSPR"]."' onchange='new_sprd(this, ".$row["PUZID"].")' /></td></tr>";
            $meta_header .= "<tr><td>Notes</td><td><input size=40 name='puznts_".$row["PUZID"]."' onchange='upd_notes(this, ".$row["PUZID"].")' value='".$row["PUZNOT"]."'</></td></tr></table>";
            $current_puz_id = $row["PUZID"];
        } else {
          // what we want to do is check to see if this line is the same as the last line. If it isn't, and if it isn't the last row,

          if ($current_puz_id != $meta_id && ($current_puz_id != $row["PUZID"])) {
          	$meta_table .= $current_puzzle_table_front.implode(", ",$current_workers).$current_puzzle_table_back;
            $current_workers = array();
          }
          $current_puz_id = $row["PUZID"];
          $current_puzzle_table_front = "<tr class='".$row["PUZSTT"]."'><td><a href='".$row["PURL"]."' target='_new'>".$row["PUZNME"]."</a></td><td>".$row["PUZANS"]."</td><!--<td>-->";
          $current_puzzle_table_back = "<!--</td>-->";
          $current_workers[] = "";//$row["UNAME"];
          if ($row["PUZSPR"] != "") {
          		$current_puzzle_table_back .= "<th><a href='".$row["PUZSPR"]."' target='_new'><img src='sprd.png' width=20px height=20px /></a></th></tr>";
          	} else {
          		$current_puzzle_table_back .= "<th></th></tr>";
          	}
           if ($which_puzzle == $puzzle_count) {
          		$meta_table .= $current_puzzle_table_front.implode(", ",$current_workers).$current_puzzle_table_back;
           }
         }
	}
    $meta_table .= "</table>";
    print $meta_header;
    print $meta_table;
    print "<p><a href='index.php'>&laquo; Back to the Big Board</a></p>";
	if ($meta_id != 0 ) {
    	print "<p><a href='index.php?puzzle=".$meta_id."'>View this metapuzzle as a regular puzzle</a></p>";
    }
}

function displayPuzzle($my_puzzle_list, $puzzle_id) {
	// this is a little different. For this, we are getting info about the puzzle, but also, we need to edit a lot of its information.
    $results = getPuzzleSQL($puzzle_id);
    $puzzle_count = $results->num_rows;
    if ($puzzle_count == 0) {
    	print "<P>This puzzle does not exist. It is a ghost puzzle.";
        return;
    }

    $which_puzzle = 0;
    $current_workers = array();
    while ($row = $results->fetch_assoc()) {
    	if ($which_puzzle == 0) {
        $puzzle_header = "<H2><input class='metaTitle' size=100 name='puzttl_".$row["PUZID"]."' value='".$row["PUZNME"]."' style='background: transparent; border: none;'/ onchange='new_name(this, ".$row["PUZID"].")'></H2>";
        if($row["PUZSTT"] == "featured") {
            $puzzle_header .= "<H2 style='color:#009900'>This puzzle is the Featured Puzzle</H2>";
         }
        if($row["PUZSTT"] == "solved") {
            $puzzle_header .= "<H2 style='color:#FF6600'>This puzzle has been solved: ".$row["PUZANS"]."</H2>";
        }

        $puzzle_header .= "<table><tr><td><a name='puzurllink_".$row["PUZID"]."' href='".$row["PURL"]."'>Puzzle URL</a></td><td>".
        					"<input size=40 name='puzurl_".$row["PUZID"]."' value='".$row["PURL"]."' onchange='new_link(this, ".$row["PUZID"].")' /></td></tr>";
        $puzzle_header .= "<tr><td><a name='puzsprlink_".$row["PUZID"]."' href='".$row["PUZSPR"]."'>Google Doc</a></td><td>".
        					"<input size=40 name='puzspr_".$row["PUZID"]."' value='".$row["PUZSPR"]."' onchange='new_sprd(this, ".$row["PUZID"].")' /></td></tr>";
        $puzzle_header .= "<tr><td>Notes</td><td><input size=40 name='puznts_".$row["PUZID"]."' onchange='upd_notes(this, ".$row["PUZID"].")' value='".$row["PUZNOT"]."'</></td></tr></table>";
        $which_puzzle += 1;
      }
      $current_workers[] = $row['UNAME'];
	}
    print $puzzle_header;
    //if (count($current_workers) > 0) {
    //	print "<P>Who's working on this: ".implode(", ",$current_workers)."</P>";
    //} else {
    //	print "<P>No one is working on this.</P>";
    //}

   print "<p>&nbsp;</p><p>&nbsp;</p><h2>Metapuzzles</h2>";

   $results = getAllMetasSQL($puzzle_id);
    while ($row = $results->fetch_assoc()) {
    	if ($row["INMETA"] > 0) {
        	print "<p><input type='checkbox' checked name='puzinmeta_".$row['MID']."' onclick='change_parent(this, ".$puzzle_id.", ".$row['MID'].")'>".$row["MTTL"]."</p>";
        } else {
        	print "<p><input type='checkbox' name='puzinmeta_".$row['MID']."' onclick='change_parent(this, ".$puzzle_id.", ".$row['MID'].")'>".$row["MTTL"]."</p>";
        }
    }
   print "<p><a href='index.php'>&laquo; Back to the Big Board</a></p>";
   print "<p>&nbsp;</p><p>&nbsp;</p>";
   print "<table><tr valign=middle><th width=240px align=left>Advanced options.<br/>Do not use unless you are sure.</th>";
   print "<td width=240px><div><a href='#' class='fake_button' onclick='promote_puzzle(".$puzzle_id.");'>Mark this puzzle as a metapuzzle.</a></div></td>";
   print "<td width=240px><div><a href='#' class='fake_button' onclick='delete_puzzle(".$puzzle_id.");'>Delete this puzzle.</a><input type=checkbox id='areyousure' value='yes'/>Are you sure?</div></td></tr></table>";
}

function displayUpdates($filter) {
	$news_table = "";
    $news_table = "<p>"; //Most recent updates are listed first. ";
    if ($filter == "Y") {
    	$news_table .= "<a href='index.php?updates'>All updates</a>";
    } else {
    	$news_table .= "<a href='index.php?updates&filter=Y'>Hide automatic updates</a>";
    }
    $news_table .= "</p><table border=0 cellspacing=0 cellpadding=4>";
    $results = getUpdatesSQL();
    while ($row = $results->fetch_assoc()) {
    	if ($filter != "Y" || $row["TYP"] != "PUZ") {
        	$news_table .= "<tr><td>".$row["WHN"].": ".$row["NEWS"]." (".$row['WHO'].")</td></tr>";
        }
	}
    $news_table .= "</table>";
    print $news_table;
    print "<p><a href='index.php'>&laquo; Back to the Big Board</a></p>";
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

function displayAbandonedPuzzles() {
	// first of all let's get the current spreadsheets in the folder
	$from_the_folder = getDrivesFiles();
	$all_puzzles = array();

    $results = getUnsolvedPuzzles();
    while ($row = $results->fetch_assoc()) {
        // what we want to do is get the last part of the spreadsheet key
        $currentFile = substr($row['PUZSPR'],strpos($row['PUZSPR'],"ccc?key=")+8,44);

        // check to see if the file is in the folder
        if (array_key_exists($currentFile, $from_the_folder)) {
        	$all_puzzles[$currentFile] = $from_the_folder[$currentFile][1]."|"
            								.$row['INDPUZ']."|"
                                            .$row['STATUS']."|"
                                            .$row['PUZURL']."|"
                                            .$row['PUZSPR']."|"
                                            .$row['PUZNTS']."|".$from_the_folder[$currentFile][0];
		} else {
        	$all_puzzles[$currentFile] = "2015-12-31|"
            								.$row['INDPUZ']."|"
                                            .$row['STATUS']."|"
                                            .$row['PUZURL']."|"
                                            .$row['PUZSPR']."|"
                                            .$row['PUZNTS']."|"."Unknown";
		}
	}

	asort($all_puzzles);
	$aband_table = "";
    $aband_table = "<p>The following lists all unsolved puzzles, in order of how long ago their spreadsheet was opened (which may or may not reflect when someone last worked on the puzzle.</p>";
    $aband_table .= "<table border=0 cellspacing=0 cellpadding=4>";
    $aband_table .= "<tr><th>Puzzle</th><th>Last Modified</th><th>Notes</th><th>Status</th><th>Link to Spreadsheet</th></tr>";

    foreach($all_puzzles as $k=>$v) {
    	$this_puzzle = explode("|",$v);
		$how_old = (time()-strtotime($this_puzzle[0]))/60;
        if ($how_old > 60*24) {
        	$how_old_txt = intval($how_old/(24*60))." days";
        } else if ($how_old > 60) {
        	$how_old_txt = intval($how_old/60)." hrs";
        } else {
        	$how_old_txt = intval($how_old)." min";
        }
        $aband_table .= "<tr class='".$this_puzzle[2]."'>".
        				"<td><a href='".$this_puzzle[3]."' target='_new'>".$this_puzzle[1]."</a></td>".
        				"<td>".$how_old_txt."</td>".
        				"<td>".$this_puzzle[5]."</td>".
        				"<td>".$this_puzzle[2]."</td>".
                        "<td><a href='".$this_puzzle[4]."' target='_new'>Drive</a></td>".
                        "</tr>";
        }

    $aband_table .= "</table>";
    print $aband_table;
    print "<p><a href='index.php'>&laquo; Back to the Big Board</a></p>";
}
?>
