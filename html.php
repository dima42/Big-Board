<?
require_once "sql.php";
use Propel\Runtime\ActiveQuery\Criteria;

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

    $roster = MemberQuery::create()
        ->orderByFullName()
        ->find();

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

    $total_puzzles = 0;

    $statuses = PuzzleQuery::create()
        ->withColumn('COUNT(Puzzle.Status)', 'StatusCount')
        ->groupBy('Puzzle.Status')
        ->select(array('Status', 'StatusCount'))
        ->find();

    foreach ($statuses as $status) {
        $total_puzzles += $status['StatusCount'];
    }

    $all_puzzles = PuzzleParentQuery::create()
        ->orderByParentId()
        ->find();

    $all_puzzles_by_meta = array();
    foreach ($all_puzzles as $puzzle) {
        $all_puzzles_by_meta[$puzzle->getParent()->getTitle()][] = $puzzle->getChild();
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
        'statuses' => $statuses,
        'total_puzzles' => $total_puzzles,
        'all_puzzles_by_meta' => $all_puzzles_by_meta,
    ));
}

function displayMeta($meta_id) {
    $meta = PuzzleQuery::create()
        ->filterByID($meta_id)
        ->findOne();

    $puzzles = PuzzleQuery::create()
        ->usePuzzleParentQuery()
            ->filterByParent($meta)
        ->endUse()
        ->find();

    // TODO: if not $meta, redirect to error page
        // "This does not appear to be a metapuzzle. There are no puzzles that are part of it."

    render('meta.twig', array(
        'meta' => $meta,
        'puzzles' => $puzzles,
    ));
}

function displayLoosePuzzles() {
    $all_puzzles = PuzzleQuery::create()
        ->leftJoinWith('Puzzle.PuzzleParent')
        ->find();

    $puzzles = array();
    foreach ($all_puzzles as $puzzle) {
        if ($puzzle->countPuzzleParents() == 0) {
            $puzzles[] = $puzzle;
        }
    }

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
    $puzzle = PuzzleQuery::create()
        ->filterByID($puzzle_id)
        ->findOne();

    // TODO: if not $puzzle, redirect to error template
        // "This puzzle does not exist. It is a ghost puzzle.";

    $puzzle_metas = PuzzleQuery::create()
        ->join('Puzzle.PuzzleChild')
        ->withColumn('Sum(PuzzleChild.Id = ' . $puzzle_id . ')', 'IsInMeta')
        ->groupBy('Puzzle.Id')
        ->find();

    render('puzzle.twig', array(
        'puzzle_id' => $puzzle_id,
        'puzzle' => $puzzle,
        'puzzle_metas' => $puzzle_metas,
    ));
}

function displayNews($filter) {
    $news = NewsQuery::create()
        ->orderByCreatedAt()
        ->find();

    render('news.twig', array(
        'filter' => $filter,
        'updates' => $news,
    ));
}

function displayUnsolvedPuzzles() {
    $unsolved_puzzles = PuzzleQuery::create()
        ->filterByStatus('solved', Criteria::NOT_EQUAL)
        ->find();

    $puzzles = array();
    $driveService = get_new_drive_service();

    foreach ($unsolved_puzzles as $row) {
        $fileID = substr($row->getSpreadsheetID(), strpos($row->getSpreadsheetID(), "ccc?key=") + 8, 44);
        $puzzles[$fileID]['id'] = $row->getID();
        $puzzles[$fileID]['title'] = $row->getTitle();
        $puzzles[$fileID]['url'] = $row->getURL();
        $puzzles[$fileID]['spreadsheet_id'] = $row->getSpreadsheetID();
        $puzzles[$fileID]['slack_channel'] = $row->getSlackChannel();

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
