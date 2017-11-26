<?
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

    // Show form for new puzzle
    if (isset($_GET['new'])) {
        return displayNew();
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

function getCurrentPuzzle($user_id) {
    $results = getCurrentPuzzleSQL($user_id);
    $my_puzzles = array();
    while ($row=$results->fetch_array(MYSQLI_ASSOC)) {
        $my_puzzles[$row['PUZID']] = $row['CHECKOUT'];
    }
    return $my_puzzles;
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

function displayNew() {
    render('new.twig', array(
    ));
}

function displayPuzzles() {
    // We have removed the check out feature.
    // $my_puzzle_list = getCurrentPuzzle($_SESSION["user_id"]);

    $statuses = array(
        "featured" => 0,
        "priority" => 0,
        "open" => 0,
        "stuck" => 0,
        "solved" => 0,
    );

    $total_puzzles = 0;

    // Count up all the puzzles in each status
    $all_statuses = getStatusProportionsSQL();
    while ($row = $all_statuses->fetch_assoc()) {
        $statuses[$row["STATUS"]] = $row["PUZSTTSUM"];
        $total_puzzles += $row["PUZSTTSUM"];
    }

	$whos_on_what = getPuzzleAssignments();
    $whos_on_what_array = array();
    while ($row = $whos_on_what->fetch_assoc()) {
    	$whos_on_what_array[$row["SNACK"]] = $row["ANTS"];
	}

	$all_puzzles = getPuzzles();
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
        // $is_puz_out = /* "<img name='puzchk_".$row["PUZID"]."' src='".$on_puzzle.".png' width=14px height=14px onclick='toggle_Puzzle_Checkout(".$row["PUZID"].");'>".
        // "&nbsp;<span name='puzwrk_".$row["PUZID"]."'>".$ants."</span>" */
        // "";
    // }

    render('all.twig', array(
        'statuses' => $statuses,
        'total_puzzles' => $total_puzzles,
        'all_puzzles_by_meta' => $all_puzzles_by_meta,
    ));
}

function displayMeta($meta_id) {
    $results = getMeta($meta_id);

    render('meta.twig', array(
        'meta_id' => $meta_id,
        'puzzles' => $results
    ));
}

function displayLoosePuzzles() {
	$results = getLoosePuzzles();

    render('loose.twig', array(
        'puzzles' => $results
    ));
}

function displayFeature($puzzle_id) {
    $results = getFeaturedPuzzleIDSQL();
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
    $results = getPuzzle($puzzle_id);

    $puzzle_count = $results->num_rows;
    if ($puzzle_count == 0) {
        // TODO: redirect to error template
        print "<P>This puzzle does not exist. It is a ghost puzzle.</p>";
        return;
    }

    $puzzle = $results->fetch_assoc();
    $puzzle_metas = getPuzzleMetas($puzzle_id);

    render('puzzle.twig', array(
        'puzzle_id' => $puzzle_id,
        'puzzle' => $puzzle,
        'puzzle_metas' => $puzzle_metas,
    ));
}

function displayNews($filter) {
    $results = getUpdatesSQL();

    render('news.twig', array(
        'filter' => $filter,
        'updates' => $results
    ));
}

function displayUnsolvedPuzzles() {
    $unsolved_puzzles = getUnsolvedPuzzles();
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
