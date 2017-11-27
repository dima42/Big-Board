<?
require_once "sql.php";

function show_content() {
    // Show test
    if (isset($_GET['test'])) {
        return displayTest();
    }

    // Show a meta
    if (isset($_GET['meta'])) {
        return displayMeta($_GET['meta']);
    }

    // Show unattached
    if (isset($_GET['loose'])) {
        return displayLoosePuzzles();
    }

    // Show news
    if (isset($_GET['news'])) {
        return displayNews($_GET['filter']);
    }

    // Show unsolved
    if (isset($_GET['unsolved'])) {
        return displayUnsolvedPuzzles();
    }

    // Show unsolved
    if (isset($_GET['roster'])) {
        return displayRoster();
    }

    // Show form for new puzzle
    if (isset($_GET['add'])) {
        if (isset($_POST['url-list'])) {
            redirect('/?add', 'hi');
        }
        return displayAdd();
    }

    // Show a single puzzle
    if (isset($_GET['puzzle'])) {
        if ($_GET['puzzle'] == 'F') {
            return displayFeature();
        } else {
            return displayPuzzle($_GET['puzzle']);
        }
    }

    // Show main page
    return displayPuzzles();
}

function redirect($location, $message) {
    $_SESSION['alert_message'] = $message;
    header("Location: " . $location);
    exit();
    ob_flush();
}

function displayError($error) {
    render('error.twig', array(
        'error' => $error,
    ));
}

function displayTest() {
    $result = create_file_from_template("test-". rand(1000, 9999));
    render('test.twig', array(
        'content' => $result,
    ));
}

function displayAdd() {
    render('add.twig', array(
    ));
}

function displayRoster() {
    $query = "select pal_id as ID, pal_usr_nme as FULL_NAME, slack_id as SLACK_ID, slack_handle as SLACK_HANDLE from pal_usr_tbl ORDER BY pal_usr_nme";
    $roster = getData($query);

    render('roster.twig', array(
        'roster' => $roster,
    ));
}

function displayPuzzles() {
    // We have removed the check out feature.
    // $query = "select puz_id as PUZID, chk_out as CHECKOUT from puz_chk_out where usr_id = " . $_SESSION["user_id"] . " and chk_in is null";
    // $results = getData($query);
    // $my_puzzle_list = array();
    // while ($row=$results->fetch_array(MYSQLI_ASSOC)) {
    //     $my_puzzle_list[$row['PUZID']] = $row['CHECKOUT'];
    // }
    // return $my_puzzle_list;

    $statuses = array(
        "featured" => 0,
        "priority" => 0,
        "open" => 0,
        "stuck" => 0,
        "solved" => 0,
    );

    $total_puzzles = 0;

    // Count up all the puzzles in each status
    $query = "SELECT puz_stt as STATUS, COUNT(*) STATUS_SUM FROM puz_tbl GROUP BY puz_stt";
    $all_statuses = getData($query);

    // while ($row = $all_statuses->fetch_assoc()) {
    //     $statuses[$row["STATUS"]] = $row["STATUS_SUM"];
    //     $total_puzzles += $row["STATUS_SUM"];
    // }

    $query = "select puz_id as SNACK, count(*) as ANTS ".
             "from puz_chk_out a ".
             "where chk_in is NULL and puz_id in (select puz_id from puz_tbl where puz_stt != 'solved') ".
             "group by puz_id";
    $whos_on_what = getData($query);
 //    $whos_on_what_array = array();
 //    while ($row = $whos_on_what->fetch_assoc()) {
 //    	$whos_on_what_array[$row["SNACK"]] = $row["ANTS"];
	// }

    $query = "select b.puz_id as PUZID, a.puz_ttl as METPUZ, b.puz_ttl PUZZLE_NAME, b.puz_ans PUZANS, b.puz_url PUZURL, b.puz_notes PUZNTS, b.slack SLACK, ".
             "b.puz_spr PUZSPR, b.puz_stt STATUS, c.puz_par_id = c.puz_id as META ".
             "from puz_tbl b left join (puz_rel_tbl c, puz_tbl a) ".
             "on (b.puz_id = c.puz_id and c.puz_par_id = a.puz_id) ".
             "order by (c.puz_par_id is NOT NULL), c.puz_par_id desc, META desc, b.puz_id";
    $all_puzzles = getData($query);

    $all_puzzles_by_meta = array();
    while ($row = $all_puzzles->fetch_assoc()) {
        $all_puzzles_by_meta[$row['METPUZ']][] = $row;
    }

    // while ($row = $results->fetch_assoc()) {
        // TODO: Show who's working on puzzle. Are we still using this?
        // if (array_key_exists($row["PUZID"], $whos_on_what_array)) {
        //     $ants = $whos_on_what_array[$row["PUZID"]];
        // } else {
        //     $ants = "";
        // }

        // TODO: If I'm working on puzzle, indicate that. Are we still using this?
        // if (array_key_exists($row["PUZID"], $my_puzzle_list)) {
        //     $on_puzzle = "onit";
        // } else {
        //     $on_puzzle = "noton";
        // }

        // TODO: Are we still using this?
        // $is_puz_out = /* "<img id='puzchk_".$row["PUZID"]."' src='".$on_puzzle.".png' width=14px height=14px onclick='toggle_Puzzle_Checkout(".$row["PUZID"].");'>".
        // "&nbsp;<span id='puzwrk_".$row["PUZID"]."'>".$ants."</span>" */
        // "";
    // }

    render('all.twig', array(
        'statuses' => $all_statuses,
        'total_puzzles' => $total_puzzles,
        'all_puzzles_by_meta' => $all_puzzles_by_meta,
    ));
}

function displayMeta($meta_id) {
    $query = "select a.puz_id as PUZID, a.puz_ttl as PUZZLE_NAME, a.puz_url as PUZURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as STATUS, a.puz_notes as PUZNTS, ".
                "c.pal_id as UID, c.pal_usr_nme as UNAME, (a.puz_id = " . $meta_id . ") as META ".
                "from puz_tbl a left join (puz_chk_out b, pal_usr_tbl c) ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL ".
                "where a.puz_id in (select puz_id from puz_rel_tbl where puz_par_id = " . $meta_id . ") ".
                "order by META desc, a.puz_id";
    $results = getData($query);

    render('meta.twig', array(
        'meta_id' => $meta_id,
        'puzzles' => $results,
    ));
}

function displayLoosePuzzles() {
    $query = "select a.puz_id as PUZID, a.puz_ttl as PUZZLE_NAME, a.puz_url as PUZURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as STATUS, a.puz_notes as PUZNTS, ".
                "c.pal_id as UID, c.pal_usr_nme as UNAME, FALSE as META ".
                "from puz_tbl a left join (puz_chk_out b, pal_usr_tbl c) ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL ".
                "where a.puz_id not in (select puz_id from puz_rel_tbl) ".
                "order by a.puz_id";
    $puzzles = getData($query);

    render('loose.twig', array(
        'puzzles' => $puzzles
    ));
}

function displayFeature($puzzle_id) {
    $query = "select puz_id as PUZID from puz_tbl b where puz_stt = 'featured'";
    $results = getData($query);

    $featureID = "";
    while ($row = $results->fetch_assoc()) {
        $featureID = $row["PUZID"];
    }

    if ($featureID != "") {
        displayPuzzle($featureID);
    } else {
        displayPuzzles();
    }
}

function displayPuzzle($puzzle_id) {
    $query = "SELECT a.puz_id as PUZID, e.puz_ttl as META, e.puz_id as META_ID, a.puz_ttl as PUZZLE_NAME, a.puz_url as PUZURL, a.puz_spr as PUZSPR, a.puz_ans as PUZANS, a.puz_stt as STATUS, a.puz_notes as PUZNTS, ".
                "c.pal_id as UID, c.pal_usr_nme as UNAME ".
                "FROM puz_tbl a " .
                "LEFT JOIN (puz_chk_out b, pal_usr_tbl c) " .
                "ON a.puz_id = b.puz_id AND b.usr_id = c.pal_id and b.chk_in is NULL " .
                "LEFT JOIN (puz_rel_tbl d, puz_tbl e)" .
                "on (a.puz_id = d.puz_id and d.puz_par_id = e.puz_id)" .
                "WHERE a.puz_id = " . $puzzle_id . "";
    $results = getData($query);

    $puzzle_count = $results->num_rows;
    if ($puzzle_count == 0) {
        // TODO: redirect to error template
        print "<P>This puzzle does not exist. It is a ghost puzzle.</p>";
        return;
    }

    $puzzle = $results->fetch_assoc();

    $query = "select a.puz_id as MID, a.puz_ttl as MTTL, sum(b.puz_id = " . $puzzle_id . ") as INMETA from puz_tbl a, puz_rel_tbl b where a.puz_id = b.puz_par_id group by a.puz_id, a.puz_ttl";
    $puzzle_metas = getData($query);

    render('puzzle.twig', array(
        'puzzle_id' => $puzzle_id,
        'puzzle' => $puzzle,
        'puzzle_metas' => $puzzle_metas,
    ));
}

function displayNews($filter) {
    $query = "select a.pal_upd_txt as NEWS, a.upd_tme as WHN, b.pal_usr_nme as WHO, a.pal_upd_code as TYP ".
             "from pal_upd_tbl a, pal_usr_tbl b ".
             "where a.usr_id = b.pal_id ".
             "order by a.upd_tme desc";
    $news = getData($query);

    render('news.twig', array(
        'filter' => $filter,
        'updates' => $$news,
    ));
}

function displayUnsolvedPuzzles() {
    $query = "select b.puz_id as PUZID, a.puz_ttl as METPUZ, b.puz_ttl PUZZLE_NAME, b.puz_ans PUZANS, b.puz_url PUZURL, a.puz_stt as STATUS, b.puz_notes PUZNTS, ".
             "b.puz_spr PUZSPR, b.puz_stt STATUS, c.puz_par_id = c.puz_id as META ".
             "from puz_tbl b left join (puz_rel_tbl c, puz_tbl a) ".
             "on (b.puz_id = c.puz_id and c.puz_par_id = a.puz_id) ".
             "where b.puz_stt != 'solved' ".
             "order by (c.puz_par_id is NOT NULL), c.puz_par_id desc, META desc, b.puz_id";
    $unsolved_puzzles = getData($query);

    $puzzles = array();
    $driveService = get_new_drive_service();

    foreach ($unsolved_puzzles as $row) {
        $fileID = substr($row['PUZSPR'], strpos($row['PUZSPR'], "ccc?key=") + 8, 44);
        $puzzles[$fileID] = $row;

        $file = $driveService->files->get($fileID);
        $puzzles[$fileID]['lastModBy'] = $file['lastModifyingUserName'] ?? "";

        $how_old = (time() - strtotime($file['modifiedDate'] ?? "2017-12-31")) / 60;
        $file_age = intval($how_old) . " min";
        if ($how_old > 60*24) {
            $file_age = intval($how_old/(24*60)) . " days";
        } else if ($how_old > 60) {
            $file_age = intval($how_old/60) . " hrs";
        }

        $puzzles[$fileID]['lastMod'] = $file_age;
    }

    render('unsolved.twig', array(
        'puzzles' => $puzzles
    ));
}
?>
