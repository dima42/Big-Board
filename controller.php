<?
require_once "sql.php";
require_once "new_file_management.php";
use Cocur\Slugify\Slugify;

use Propel\Runtime\ActiveQuery\Criteria;

function show_content() {
	$klein = new \Klein\Klein();

	$klein->respond('GET', '/', function () {
			return displayAllPuzzles();
		});

	$klein->respond('GET', '/test', function () {
			return displayTest();
		});

	$klein->respond('GET', '/news', function () {
			return displayNews();
		});

	$klein->with('/puzzle/[:id]', function () use ($klein) {

			$klein->respond('GET', '/?', function ($request) {
					return displayPuzzle($request->id);
				});
			$klein->respond('GET', '/edit/?', function ($request) {
					return displayPuzzle($request->id, 'edit');
				});
			$klein->respond('POST', '/edit/?', function ($request) {
					return editPuzzle($request->id, $request);
				});
			$klein->respond('POST', '/solve/?', function ($request) {
					return solvePuzzle($request->id, $request);
				});
			$klein->respond('POST', '/add-note/?', function ($request) {
					return addNote($request->id, $request);
				});
			$klein->respond('POST', '/claim/?', function ($request) {
					return joinPuzzle($request->id);
				});
		});

	$klein->respond('GET', '/me', function () {
			redirect('/member/'.$_SESSION['user_id'].'/edit');
		});

	$klein->with('/member/[:id]', function () use ($klein) {

			$klein->respond('GET', '/?', function ($request) {
					return displayMember($request->id);
				});
			$klein->respond('GET', '/edit/?', function ($request) {
					if ($request->id == $_SESSION['user_id']) {
						return displayMember($request->id, 'edit');
					}
					redirect('/roster');
				});
			$klein->respond('POST', '/edit/?', function ($request) {
					if ($request->id == $_SESSION['user_id']) {
						return saveMember($request->id, $request);
					}
					redirect('/roster');
				});
		});

	$klein->respond('GET', '/meta/[:id]', function ($request) {
			return displayMeta($request->id);
		});

	$klein->respond('GET', '/loose', function () {
			return displayLoosePuzzles();
		});

	$klein->respond('GET', '/unsolved', function () {
			return displayUnsolvedPuzzles();
		});

	$klein->respond('GET', '/roster', function () {
			return displayRoster();
		});

	// ADDING

	$klein->with('/add', function () use ($klein) {

			$klein->respond('GET', '/?', function ($request) {
					return displayAdd();
				});
			$klein->respond('GET', '/[:meta_id]/?', function ($request) {
					return displayAdd($request->meta_id);
				});
			$klein->respond('POST', '/?', function ($request, $response) {
					return addPuzzle($request, $response);
				});
		});

	$klein->respond('GET', '/puzzle_scrape', function ($request, $response) {
			return puzzleScrape($request, $response);
		});

	$klein->dispatch();
}

function redirect($location, $message = "", $alert_type = "info") {
	$_SESSION['alert_message'] = array("message" => $message, "type" => $alert_type);
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
	$puzzle = PuzzleQuery::create()
		->filterByID(203)
		->findOne();

	// postPuzzle($puzzle, $puzzle->getSlackChannel());
	postSolve($puzzle, $puzzle->getSlackChannel());
	// postSolve($puzzle);

	return;

	render('test.twig', array(
			// 'content' => $result,
		));
}

function displayPuzzle($puzzle_id, $method = "get") {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$notes = NoteQuery::create()
		->filterByPuzzle($puzzle)
		->orderByCreatedAt('desc')
		->find();

	$members = PuzzleMemberQuery::create()
		->joinWith('PuzzleMember.Member')
		->filterByPuzzle($puzzle)
		->find();

	// TODO: if not $puzzle, redirect to error template
	// "This puzzle does not exist. It is a ghost puzzle.";

	$metas_to_show = PuzzleParentQuery::create()
		->joinWith('PuzzleParent.Parent')
		->orderByParentId()
		->withColumn('Sum(puzzle_id ='.$puzzle_id.')', 'IsInMeta')
		->filterByParentId($puzzle_id, CRITERIA::NOT_EQUAL)
		->groupBy('Parent.Id')
		->find();

	$me_as_meta = PuzzleParentQuery::create()
		->filterByParent($puzzle)
		->filterByChild($puzzle)
		->count();

	$statuses = array('open', 'stuck', 'priority', 'urgent', 'solved');

	$template = 'puzzle.twig';

	if ($method == "edit") {
		$template = 'puzzle-edit.twig';
	}

	render($template, array(
			'puzzle_id' => $puzzle_id,
			'puzzle'    => $puzzle,
			'notes'     => $notes,
			'members'   => $members,
			'all_metas' => $metas_to_show,
			'statuses'  => $statuses,
			'i_am_meta' => $me_as_meta > 0,
		));
}

function editPuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$puzzle->setTitle($request->title);
	$puzzle->setSolution(strtoupper($request->solution));
	$puzzle->setStatus($request->status);
	$puzzle->setSpreadsheetId($request->spreadsheet_id);
	$puzzle->setSlackChannel($request->slack_channel);
	$puzzle->save();

	// Remove all parents, even myself (if I'm a meta)
	$oldParents = PuzzleParentQuery::create()
		->filterByPuzzleId($puzzle_id)
		->find();
	$oldParents->delete();

	// Assign parents
	foreach ($request->metas as $meta) {
		$puzzleParent = new PuzzleParent();
		$puzzleParent->setPuzzleId($puzzle_id);
		$puzzleParent->setParentId($meta);
		$puzzleParent->save();
	}

	if ($request->i_am_meta == "y") {
		$puzzleParent = new PuzzleParent();
		$puzzleParent->setPuzzleId($puzzle_id);
		$puzzleParent->setParentId($puzzle_id);
		$puzzleParent->save();
	}

	$message = "Saved ".$puzzle->getTitle();

	redirect('/puzzle/'.$puzzle_id.'/edit', $message);
}

function solvePuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$new_solution = strtoupper(trim($request->solution));

	$puzzle->setSolution($new_solution);

	if ($new_solution != '') {
		$puzzle->setStatus('solved');
		postSolve($puzzle, $puzzle->getSlackChannel());
		postSolve($puzzle);
		$alert = $puzzle->getTitle()." is solved! Great work, team! 🎓";
	} else {
		$puzzle->setStatus('open');
		$alert = $puzzle->getTitle()." is open again.";
	}

	$puzzle->save();

	redirect('/puzzle/'.$puzzle_id, $alert);
}

function addNote($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$author = $_SESSION['user'];

	$note = new Note();
	$note->setPuzzleId($puzzle_id);
	$note->setBody($request->body);
	$note->setAuthor($author);
	$note->save();

	$message = "Saved a note to ".$puzzle->getTitle();

	redirect('/puzzle/'.$puzzle_id, $message);
}

function joinPuzzle($puzzle_id) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$member = $_SESSION['user'];

	$newPuzzleMember = new PuzzleMember();

	try {
		$newPuzzleMember->setPuzzleId($puzzle_id);
		$newPuzzleMember->setMember($member);
		$newPuzzleMember->save();
		$message = "You joined ".$puzzle->getTitle().".";
		postJoin($member, $puzzle->getSlackChannel());
	} catch (Exception $e) {
		$message = "You already joined this puzzle.";
	}

	redirect('/puzzle/'.$puzzle_id, $message);
}

// ADDING PUZZLES

function displayAdd($meta_id = '') {
	render('add.twig', array(
			'meta_id' => $meta_id,
		));
}

function puzzleScrape($request, $response) {
	$urls_string = $request->urls;
	$urls        = explode("\n", $urls_string);
	$slugify     = new Slugify();

	$json = array();
	foreach ($urls as $url) {
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$doc    = hQuery::fromUrl($url, ['Accept' => 'text/html']);
			$title  = $doc->find('title')->text();
			$json[] = array(
				"url"   => $url,
				"title" => $title,
				"slack" => substr($slugify->slugify($title), 0, 21)
			);
		}
	}

	return $response->json($json);
}

function addPuzzle($request, $response) {
	$existingURLs   = array();
	$existingTitles = array();
	$existingSlacks = array();
	$newPuzzles     = array();

	foreach ($request->newPuzzles as $puzzleKey => $puzzleContent) {
		$puzzleId = "puzzleGroup".$puzzleKey;

		$puzzleURLExists = PuzzleQuery::create()
			->filterByURL($puzzleContent['url'])
			->findOne();
		$puzzleTitleExists = PuzzleQuery::create()
			->filterByTitle($puzzleContent['title'])
			->findOne();

		if ($puzzleURLExists) {
			$existingURLs[] = $puzzleId;
		}

		if ($puzzleTitleExists) {
			$existingTitles[] = $puzzleId;
		}

		if (!$puzzleURLExists && !$puzzleTitleExists) {
			$slack_response = json_decode(createNewSlackChannel($puzzleContent['slack']), true);

			if (!$slack_response['ok']) {
				$existingSlacks[] = $puzzleId;
				continue;
			}

			$spreadsheet_id = create_file_from_template($puzzleContent['title']);

			$newPuzzle = new Puzzle();
			$newPuzzle->setTitle($puzzleContent['title']);
			$newPuzzle->setUrl($puzzleContent['url']);
			$newPuzzle->setSpreadsheetId($spreadsheet_id);
			$newPuzzle->setSlackChannel($slack_response['channel']['name']);
			$newPuzzle->setSlackChannelId($slack_response['channel']['id']);
			$newPuzzle->setStatus('open');
			$newPuzzle->save();

			$meta = $puzzleContent['meta'];

			if ($meta >= 0) {
				$parentID = $meta;
				if ($meta == 0) {// it's a meta, so set Parent to itself
					$parentID = $newPuzzle->getID();
				}
				$newPuzzleParent = new PuzzleParent();
				$newPuzzleParent->setChild($newPuzzle);
				$newPuzzleParent->setParentId($parentID);
				$newPuzzleParent->save();
			}

			$newPuzzles[] = array(
				'puzzleID' => $puzzleId,
				'title'    => $puzzleContent['title'],
				'pkID'     => $newPuzzle->getID(),
			);

			postPuzzle($newPuzzle, $puzzleContent['slack']);
			postPuzzle($newPuzzle);// big-board channel
		}
	}

	return $response->json(array(
			'existingURLs'   => $existingURLs,
			'existingTitles' => $existingTitles,
			'existingSlacks' => $existingSlacks,
			'newPuzzles'     => $newPuzzles,
		));

	// # send post to slack channel
	// # post news update?
}

// MEMBERS

function displayRoster() {
	$roster = MemberQuery::create()
		->orderByFullName()
		->find();

	render('roster.twig', array(
			'roster' => $roster,
		));
}

function displayMember($member_id, $method = "get") {
	$template = 'member.twig';

	if ($method == "edit") {
		$template = 'member-edit.twig';
	}

	render($template, array(
			'member' => $_SESSION['user'],
		));
}

function saveMember($member_id, $request) {
	$member = $_SESSION['user'];

	$member->setFullName($request->full_name);
	$member->setStrengths($request->strengths);
	$member->setSlackHandle($request->slack_handle);
	$member->setSlackId($request->slack_id);
	$member->save();

	$message = "Saved your profile changes.";

	redirect('/member/'.$member_id.'/edit', $message);
}

// LISTS

function displayAllPuzzles() {
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
			'puzzle'  => $meta,
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

function displayNews() {
	$filter = $_GET['filter'];

	$news = NewsQuery::create()
		->orderByCreatedAt('desc')
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
		$puzzles[$fileID]['status']         = $row->getStatus();
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
