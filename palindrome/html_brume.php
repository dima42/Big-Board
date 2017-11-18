<?
function writeHeader($showTeamInformation) {
	print <<<_HTML_
<html>
<head>
	<title>Palindrome</title>
	<script type="text/javascript" src="pallap.js"></script>
<style type="text/css">
#new_puzzle_input {
	display:none;
	text-align: center;
	width: 180px; height: 80px;
	border: solid 1px #000000;
	background: #CCFFCC;	
}

#UrgentMessage {
	color: #FF6600;
	background: #FFD1B2;
}

.metaTitle {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
.MetaRound, H1, A, P, H2, DIV,SPAN, INPUT, TD, TH {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 11px
}
.puzzle {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 11px;
	border-color: #666666;
	border-width:thin;
}
.solved { background-color: #F6F6F6; }
.open { background-color: #CCFFCC; }
.stuck { background-color: #33FF33; }
.priority { background-color: #00CC00; }

.fake_button {
	color: black; 
	border: 1px solid #666666;
	padding: 2px 2px 2px 2px;
	text-decoration: none;
}
</style>
</head>

<body onload="idle_hands();" onmousemove="idle_hands();" onclick="idle_hands();" onkeydown="idle_hands();">
<table width='1200'>
<tr><td><strong>Palindrome Big Board</strong></td>
_HTML_;
if ($showTeamInformation) {
	print <<<_HTML_
<td align='right'><strong>HQ</strong>: 4-149 (main), 4-146, 4-256&nbsp;&nbsp;&nbsp;<strong>Phone</strong> 617-555-1111&nbsp;&nbsp;&nbsp;<strong>E-mail</strong>: team-palindrome@mit.edu</td>
_HTML_;
}

print <<<_HTML_
</tr>
</table>
<P><em><span id='newscrawl'></span></em></p>
_HTML_;

print "<input type='hidden' id='userid' value='".$_SESSION['user_id']."'/>";
}

function writeIntro() {
	$results = getLatestTeamUpdateSQL();
    if (mysql_num_rows($results) > 0) {
    while ($row = mysql_fetch_array($results)) {
    	$latest_news = $row["NEWS"];
        $latest_news_from  = $row["WHO"];
    }
	print "<p>News <a href='?updates'>past news</a>: <input id='UrgentMessage' name='UrgentMessage' value='".$latest_news."' style='border: none; "
    		."background: transparent;' size=200 onchange='add_update(this, \"URG\", ".$_SESSION["user_id"].")'/></p>";
    } else {
    print "<p>News <a href='?updates'>past news</a>: <input id='UrgentMessage' name='UrgentMessage' value='Type over this text to send out an urgent message.' style='border: none; "
    		."background: transparent;' size=200 onchange='add_update(this, \"URG\", ".$_SESSION["user_id"].")'/></p>";
    }
}

function writeKey() {
	$statuses = array(); $total_puzzles = 0;
	$results = getStatusProportionsSQL();
	while ($row = mysql_fetch_assoc($results)) {
		// link to spreadsheet first
		$statuses[$row["PUZSTT"]] = $row["PUZSTTSUM"];
        $total_puzzles += $row["PUZSTTSUM"];
    }
    print "<p><table border=0 cellspacing=0 cellpadding=4><tr>";
	print "<th align=center valign=middle width=120px onclick='show_puzzle_input(\"M\",0, ".$_SESSION['user_id'].");'>";
    print "<img src='meta.png' height=25px width=25px alt=\"New Meta\" />&nbsp;<span>New Meta</span></th>";
 	print "<th align=center valign=middle width=120px onclick='show_puzzle_input(\"P\",0, ".$_SESSION['user_id'].");'/>";
    print "<img src='puzzle.png' height=25px width=25px alt=\"New Puzzle\" >&nbsp;<span>New Puzzle</span></th>";
	print "<th align=center valign=middle class=\"\" width=120px>&nbsp;</th>";

	if (array_key_exists("open",$statuses)) { print "<th align=center valign=middle class='puzzle open' width=".(480*$statuses["open"]/$total_puzzles)."px>Open<br/>(".$statuses["open"].")</th>"; }
	if (array_key_exists("stuck",$statuses)) { print "<th align=center valign=middle class='puzzle stuck' width=".(480*$statuses["stuck"]/$total_puzzles)."px>Stuck<br/>(".$statuses["stuck"].")</th>"; }
	if (array_key_exists("priority",$statuses)) { print "<th align=center valign=middle class='puzzle priority' width=".(480*$statuses["priority"]/$total_puzzles)."px>Priority<br/>(".$statuses["priority"].")</th>"; }
	if (array_key_exists("solved",$statuses)) { print "<th align=center valign=middle class='puzzle solved' width=".(480*$statuses["solved"]/$total_puzzles)."px>Solved<br/>(".$statuses["solved"].")</th>"; }
    print <<<_HTML_
</tr>
</table></p>
_HTML_;
}

function writeInstructions() {
	print <<<_HTML_
<p><strong>Instructions:</strong> Click below a puzzle name to enter an answer.<br/>
Enter . to mark a puzzle as open, ? to mark a puzzle as stuck, ! to mark a puzzle as a priority.<br/>
Click info view/edit puzzle information.<br/>
Click the Drive icon to view the spreadsheet for a puzzle.<br/>
Click the checkmark to check out a puzzle or work on a puzzle.</p>

_HTML_;
}

function writeHiddenNewMetabox() {
	print <<<_HTML_
<div id='new_puzzle_input'>New <span id='new_puzzle_type'></span><br/>
<input id='new_puzzle_title' name='new_puzzle_title' value='Name' size=25 /><br/>
<input id='new_puzzle_url' name='new_puzzle_url' value='URL' size=25/><br/>
<div id='new_puzzle_save' style='text-align: center; padding: 2px 2px 2px 2px;'>
<span class="fake_button" onclick='save_new_puzzle();'>Save</span> | 
<span class="fake_button" onclick='abort_addition();'>Cancel</span>
</div>
<input id='new_puzzle_hidden_type' name='new_puzzle_hidden_type'type='hidden' value=''><input id='new_puzzle_hidden_uid' name='new_puzzle_hidden_uid'type='hidden' value=''><input id='new_puzzle_hidden_parent' id='new_puzzle_hidden_parent' type='hidden' value=''></div>
_HTML_;

}

function writeFooter() {
	writeHiddenNewMetabox();
	print <<<_HTML_
<p>Page will reload every 120 seconds automatically.</p></body></html>
_HTML_;
}

function showDatabaseError($error_code) {
	if ($error_code != "" || $error_code != NULL) {
		print "<p>I'm sorry, but a database error is puzzling us. $error_code </p>";
	}
}

function displayPuzzles($my_puzzle_list) {
	$whos_on_what = getPuzzleAssignments(); $whos_on_what_array = array();
    while ($row = mysql_fetch_assoc($whos_on_what)) {
    	$whos_on_what_array[$row["SNACK"]] = $row["ANTS"];
	}
	$result = getPuzzles();
	$bgcolor = "#ffffff";
	$just_starting = TRUE; $puzzle_count = 0;
	
	while ($row = mysql_fetch_assoc($result)) {
    	// some specifics for table layout
        $cell_width = 150; $col_width = 6; $title_limit = 20;
		// link to spreadsheet first
		$sprdlink = "&nbsp;&nbsp;&nbsp;&nbsp;<a href='".$row['PUZSPR']."' target='_blank'><span ";
        if (!array_key_exists($row["PUZID"],$my_puzzle_list)) {
        	//$sprdlink .= " onclick=('toggle_Puzzle_Checkout(".$row["PUZID"].");')";
        }
        $sprdlink .= "><img src='sprd.png' height=15/></span></a>&nbsp;&nbsp;&nbsp;&nbsp;";
		
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
        $is_puz_out = "<img name='puzchk_".$row["PUZID"]."' src='".$on_puzzle.".png' width=14px height=14px onclick='toggle_Puzzle_Checkout(".$row["PUZID"].");'>".
        				"&nbsp;<span name='puzwrk_".$row["PUZID"]."'>".$ants."</span>";
		
        $answer_field = "<input name='puzans_".$row["PUZID"]."' value='".$row["PUZANS"]."' size=".($cell_width/7.5)." class='".$row["PUZSTT"]."' style='border-width:0px; text-align: center;'".
        					" onchange='editAnswer(this, ".$row["PUZID"].", \"".$row["PUZANS"]."\")'><br/>\r\n";
        			
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
			print "<table border=0 cellspacing=0 cellpadding=4>\r\n";
			print "<tr><th align=center valign=top name='puzzle_".$row['PUZID']."' class='MetaRound ".$row['STATUS']."' width=".$cell_width."px>".$puzzle_link."<br/>\r\n".
					$answer_field.
					"<a href='?meta=".$row['PUZID']."' class='fake_button'>info</a>&nbsp;".$sprdlink."&nbsp;".$is_puz_out."&nbsp;<img src='puzzle.png' border=1px height=14px width=14px/ onclick='show_puzzle_input(\"P\",".$row['PUZID'].");'></th>\r\n";
		} else {
			if ($puzzle_count == $col_width) {
				print "</tr><tr><td>&nbsp;</td>";	
				$puzzle_count = 0;
			}
            
			print "<td align=center valign=top name='puzzle_".$row['PUZID']."'  width=".$cell_width."px class='puzzle ".$row['STATUS']."'>".$puzzle_link."<br/>\r\n".
				 $answer_field.
				  "<a href='?puzzle=".$row['PUZID']."' class='fake_button'>info</a>&nbsp;".$sprdlink."&nbsp;".$is_puz_out."</td>\r\n";
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
    $puzzle_count = mysql_num_rows($results); $which_puzzle = 0;
    if ($puzzle_count == 0) {
    	print "<P>This does not appear to be a metapuzzle. There are no puzzles that are part of it.";
        return;
    }
    
    $meta_table = "";
    $meta_table = "<table border=0 cellspacing=0 cellpadding=4><tr><th>Puzzle</th><th>Answer</th><th>Who's On It?</th><th>Let's Go!</th></tr>";
    $meta_header = "";
    $current_puz_id = "";
    $current_workers = array();

    while ($row = mysql_fetch_assoc($results)) {
    	$which_puzzle += 1;
    	if ($row["META"] == 1) {
        	$meta_header = "<H2><input class='metaTitle' size=100 name='puzttl_".$row["PUZID"]."' value='".$row["PUZNME"]."' style='background: transparent; border: none;'/ onchange='new_name(this, ".$row["PUZID"].")'></H2>";
            if($row["PUZSTT"] == "solved") {
            	$meta_header .= "<H2 style='color:red'>This puzzle has been solved: ".$row["PUZANS"]."</H2>";
            }
            
            $meta_header .= "<P><a name='puzurllink_".$row["PUZID"]."' href='".$row["PURL"]."'>Meta URL</a> <input size=40 name='puzurl_".$row["PUZID"]."' value='".$row["PURL"]."' onchange='new_link(this, ".$row["PUZID"].")' /></P>";
            $meta_header .= "<P><a name='puzsprlink_".$row["PUZID"]."' href='".$row["PUZSPR"]."'>Google Doc</a> <input size=40 name='puzspr_".$row["PUZID"]."' value='".$row["PUZSPR"]."' onchange='new_sprd(this, ".$row["PUZID"].")' /></P>";
            $current_puz_id = $row["PUZID"];
        } else {
          // what we want to do is check to see if this line is the same as the last line. If it isn't, and if it isn't the last row, 
          	
          if ($current_puz_id != $meta_id && ($current_puz_id != $row["PUZID"])) {
          	$meta_table .= $current_puzzle_table_front.implode(", ",$current_workers).$current_puzzle_table_back;
            $current_workers = array();
          }
          $current_puz_id = $row["PUZID"];
          $current_puzzle_table_front = "<tr class='".$row["PUZSTT"]."'><td><a href='".$row["PURL"]."' target='_new'>".$row["PUZNME"]."</a></td><td>".$row["PUZANS"]."</td><td>";
          $current_puzzle_table_back = "</td>";
          $current_workers[] = $row["UNAME"];
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
    print "<p><a href='index.php'>Back to the Big Board</a></p>";
    print "<p><a href='index.php?puzzle=".$meta_id."'>View this metapuzzle as a regular puzzle</a></p>";
}

function displayPuzzle($my_puzzle_list, $puzzle_id) {
	// this is a little different. For this, we are getting info about the puzzle, but also, we need to edit a lot of its information.
    $results = getPuzzleSQL($puzzle_id);
    $puzzle_count = mysql_num_rows($results);
    if ($puzzle_count == 0) {
    	print "<P>This puzzle does not exist. It is a ghost puzzle.";
        return;
    }
    
    $which_puzzle = 0;
    $current_workers = array();
    while ($row = mysql_fetch_assoc($results)) {
    	if ($which_puzzle == 0) {
        $puzzle_header = "<H2><input class='metaTitle' size=100 name='puzttl_".$row["PUZID"]."' value='".$row["PUZNME"]."' style='background: transparent; border: none;'/ onchange='new_name(this, ".$row["PUZID"].")'></H2>";
        if($row["PUZSTT"] == "solved") {
            $puzzle_header .= "<H2 style='color:red'>This puzzle has been solved: ".$row["PUZANS"]."</H2>";
        }
            
        $puzzle_header .= "<P><a name='puzurllink_".$row["PUZID"]."' href='".$row["PURL"]."'>Puzzle URL</a> ".
        					"<input size=40 name='puzurl_".$row["PUZID"]."' value='".$row["PURL"]."' onchange='new_link(this, ".$row["PUZID"].")' /></P>";
        $puzzle_header .= "<P><a name='puzsprlink_".$row["PUZID"]."' href='".$row["PUZSPR"]."'>Google Doc</a> ".
        					"<input size=40 name='puzspr_".$row["PUZID"]."' value='".$row["PUZSPR"]."' onchange='new_sprd(this, ".$row["PUZID"].")' /></P>";
        $which_puzzle += 1;
      }
      $current_workers[] = $row['UNAME'];
	}
    print $puzzle_header;
    if (count($current_workers) > 0) {
    	print "<P>Who's working on this: ".implode(", ",$current_workers)."</P>";
    } else {
    	print "<P>No one is working on this.</P>";
    }

   print "<p>&nbsp;</p><p>&nbsp;</p><h2>Metapuzzles</h2>";
   
   $results = getAllMetasSQL($puzzle_id);
    while ($row = mysql_fetch_assoc($results)) {
    	if ($row["INMETA"] > 0) {
        	print "<p><input type='checkbox' checked name='puzinmeta_".$row['MID']."' onclick='change_parent(this, ".$puzzle_id.", ".$row['MID'].")'>".$row["MTTL"]."</p>";
        } else {
        	print "<p><input type='checkbox' name='puzinmeta_".$row['MID']."' onclick='change_parent(this, ".$puzzle_id.", ".$row['MID'].")'>".$row["MTTL"]."</p>";
        }
    }
   print "<p><a href='index.php' style='border: 1px solid #666666; padding: 2px 2px 2px 2px; text-decoration: none;'>Back to the Big Board</a></p>";
   print "<p>&nbsp;</p><p>&nbsp;</p>";
   print "<table><tr valign=middle><th width=240px>Advanced Options.<br/>Do not use unless you are sure.</th>";
   print "<td width=240px><div><a href='#' style='color: black; border: 1px solid #666666; padding: 2px 2px 2px 2px; text-decoration: none;' onclick='promote_puzzle(".$puzzle_id.");'>This puzzle is a metapuzzle.</a></div></td>";
   print "<td width=240px><div><a href='#' style='color: black; border: 1px solid #666666; padding: 2px 2px 2px 2px; text-decoration: none;' onclick='delete_puzzle(".$puzzle_id.");'>Delete this puzzle.</a><input type=checkbox id='areyousure' value='yes'/>&nbsp;Are you sure</div></td></tr></table>"; 
}

function displayUpdates() {
	$news_table = "";
    $news_table = "<p>Most recent updates are listed first.</p><table border=0 cellspacing=0 cellpadding=4>";
    $results = getUpdatesSQL();
    while ($row = mysql_fetch_assoc($results)) {
    	$news_table .= "<tr><td>".$row["WHN"].": ".$row["NEWS"]." (".$row['WHO'].")</td></tr>";
	}
    $news_table .= "</table>";
    print $news_table;
    print "<p><a href='index.php'>Back to the Big Board</a></p>";
}

function pull_back_the_curtain($debugging_text) {
	//print "<P><font color=red>".$debugging_text."</font></p>";
}

function displayWarning() {
	print "<P><font color=red>Hi there. Sorry, but you are not allowed to view this information.</font></p>";
}
?>