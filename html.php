<?
require_once "sql.php";
use Propel\Runtime\ActiveQuery\Criteria;

function show_content() {
	$klein = new \Klein\Klein();

	$klein->respond('GET', '/test', function () {
			return displayTest();
		});

	$klein->respond('GET', '/meta/[:id]', function ($request) {
			return displayMeta($request->id);
		});

	$klein->respond('GET', '/loose', function () {
			return displayLoosePuzzles();
		});

	$klein->dispatch();
}

function show_content_bu() {
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
		return displayPuzzle($_GET['puzzle']);
	}

	// Show main page
	return displayPuzzles();
}

function redirect($location, $message) {
	$_SESSION['alert_message'] = $message;
	header("Location: ".$location);
	exit();
	ob_flush();
}

function displayError($error) {
	render('error.twig', array(
			'error' => $error,
		));
}

function displayTest() {
	$result = create_file_from_template("test-".rand(1000, 9999));
	render('test.twig', array(
			'content' => $result,
		));
}

function displayAdd() {
	render('add.twig', array(
		));
}

function displayRoster() {
	$roster = MemberQuery::create()
		->orderByFullName()
		->find();

	render('roster.twig', array(
			'roster' => $roster,
		));
}

function displayPuzzles() {
	$statuses = PuzzleQuery::create()
		->withColumn('COUNT(Puzzle.Status)', 'StatusCount')
		->groupBy('Puzzle.Status')
		->select(array('Status', 'StatusCount'))
		->find();

	$total_puzzles = 0;
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

	render('all.twig', array(
			'statuses'            => $statuses,
			'total_puzzles'       => $total_puzzles,
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
			'meta'    => $meta,
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
			'puzzles' => $puzzles,
		));
}

function displayFeature($puzzle_id) {
}

function displayPuzzle($puzzle_id) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	// TODO: if not $puzzle, redirect to error template
	// "This puzzle does not exist. It is a ghost puzzle.";

	$puzzles_metas = PuzzleParentQuery::create()
		->joinWith('PuzzleParent.Parent')
		->filterByPuzzleID($puzzle_id)
		->find();

	// FOR USE IN EDITING
	// $available_metas = PuzzleQuery::create()
	//     ->join('Puzzle.PuzzleChild')
	//     ->withColumn('Sum(PuzzleChild.Id = ' . $puzzle_id . ')', 'IsInMeta')
	//     ->groupBy('Puzzle.Id')
	//     ->find();

	render('puzzle.twig', array(
			'puzzle_id'     => $puzzle_id,
			'puzzle'        => $puzzle,
			'puzzles_metas' => $puzzles_metas,
		));
}

function displayNews($filter) {
	$news = NewsQuery::create()
		->orderByCreatedAt()
		->find();

	render('news.twig', array(
			'filter'  => $filter,
			'updates' => $news,
		));
}

function displayUnsolvedPuzzles() {
	$unsolved_puzzles = PuzzleQuery::create()
		->filterByStatus('solved', Criteria::NOT_EQUAL)
		->find();

	$puzzles      = array();
	$driveService = get_new_drive_service();

	foreach ($unsolved_puzzles as $row) {
		$fileID                             = substr($row->getSpreadsheetID(), strpos($row->getSpreadsheetID(), "ccc?key=")+8, 44);
		$puzzles[$fileID]['id']             = $row->getID();
		$puzzles[$fileID]['title']          = $row->getTitle();
		$puzzles[$fileID]['url']            = $row->getURL();
		$puzzles[$fileID]['spreadsheet_id'] = $row->getSpreadsheetID();
		$puzzles[$fileID]['slack_channel']  = $row->getSlackChannel();

		$file                          = $driveService->files->get($fileID);
		$puzzles[$fileID]['lastModBy'] = $file['lastModifyingUserName']??"";

		$how_old  = (time()-strtotime($file['modifiedDate']??"2017-12-31"))/60;
		$file_age = intval($how_old)." min";
		if ($how_old > 60*24) {
			$file_age = intval($how_old/(24*60))." days";
		} else if ($how_old > 60) {
			$file_age = intval($how_old/60)." hrs";
		}

		$puzzles[$fileID]['lastMod'] = $file_age;
	}

	render('unsolved.twig', array(
			'puzzles' => $puzzles,
		));
}
