<?
use Cocur\Slugify\Slugify;
use Propel\Runtime\ActiveQuery\Criteria;

function show_content() {
	$klein = new \Klein\Klein();

	$klein->respond('GET', '/test', function () {
			return displayTest();
		});

	// PUZZLE LISTS

	$klein->respond('GET', '/', function () {
			return displayAll();
		});

	$klein->respond('GET', '/bymeta', function () {
			return displayAllByMeta();
		});

	$klein->respond('GET', '/loose', function () {
			return displayLoosePuzzles();
		});

	$klein->respond('GET', '/unsolved', function () {
			return displayUnsolvedPuzzles();
		});

	// PUZZLES

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
			$klein->respond('POST', '/change-status/?', function ($request) {
					return changePuzzleStatus($request->id, $request);
				});
			$klein->respond('POST', '/add-note/?', function ($request) {
					return addNote($request->id, $request);
				});
			$klein->respond('POST', '/join/?', function ($request) {
					return joinPuzzle($request->id);
				});
			$klein->respond('POST', '/leave/?', function ($request) {
					return leavePuzzle($request->id);
				});
			$klein->respond('POST', '/delete/?', function ($request) {
					return deletePuzzle($request->id, $request);
				});
			$klein->respond('POST', '/delete-note/[:note_id]/?', function ($request) {
					return archivePuzzleNote($request->note_id, $request->id);
				});
		});

	// MEMBER

	$klein->respond('GET', '/me', function () {
			redirect('/member/'.$_SESSION['user_id']);
		});

	$klein->respond('GET', '/you', function () {
			redirect('/member/'.$_SESSION['user_id']);
		});

	$klein->respond('GET', '/member', function () {
			redirect('/member/'.$_SESSION['user_id']);
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

	$klein->respond('GET', '/assign_slack_id/[:slack_id]', function ($request) {
			return assignSlackId($request->slack_id);
		});

	// ROSTER

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

	// NEWS

	$klein->with('/news', function () use ($klein) {
			$klein->respond('GET', '/?', function ($request) {
					return displayNews("all");
				});
			$klein->respond('GET', '/[:filter]/?', function ($request) {
					return displayNews($request->filter);
				});
			$klein->respond('POST', '/add/?', function ($request) {
					return postNews($request->body);
				});
			$klein->respond('POST', '/[:update_id]/delete/?', function ($request) {
					return archiveNews($request->update_id);
				});
		});

	// LOGOUT

	$klein->respond('GET', '/logout', function () {
			session_unset();
			setcookie("PAL_ACCESS_TOKEN", "", time()-3600);
			setcookie("refresh_token", "", time()-3600);
			return redirect("/");
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
	render('error.twig', 'error', array(
			'error' => $error,
		));
}

function displayTest() {
	$puzzle = PuzzleQuery::create()
		->filterByID(184)
		->findOne();

	return "";

	render('test.twig', '', array(
			// 'content' => $result,
		));
}

// PUZZLE LISTS

function displayAll() {
	$statuses = PuzzleQuery::create()
		->filterByStatus('solved', Criteria::NOT_EQUAL)
		->withColumn('COUNT(Puzzle.Status)', 'StatusCount')
		->groupBy('Puzzle.Status')
		->select(array('Status', 'StatusCount'))
		->find();

	$total_puzzle_count = PuzzleQuery::create()
		->count();

	$statusCounts   = [];
	$unsolved_count = 0;
	foreach ($statuses as $status) {
		$unsolved_count                  = $unsolved_count+$status['StatusCount'];
		$statusCounts[$status['Status']] = [
			"count"      => $status['StatusCount'],
			"percentage" => 100*$status['StatusCount']/$total_puzzle_count,
		];
	}

	$puzzles = PuzzleQuery::create()
		->orderByTitle()
		->find();

	render('all.twig', 'all', array(
			'statusCounts'       => $statusCounts,
			'unsolved_count'     => $unsolved_count,
			'total_puzzle_count' => $total_puzzle_count,
			'puzzles'            => $puzzles,
		));
}

function displayAllByMeta() {
	$all_puzzles = PuzzlePuzzleQuery::create()
		->joinWith('Child')
		->orderByParentId()
		->find();

	$metas = PuzzlePuzzleQuery::create()
		->joinWith('PuzzlePuzzle.Parent')
		->where('puzzle_id = parent_id')
		->orderBy('Parent.title')
		->find();

	$all_puzzles_by_meta = array();
	foreach ($all_puzzles as $puzzle) {
		$all_puzzles_by_meta[$puzzle->getParent()->getTitle()][] = $puzzle->getChild();
	}
	ksort($all_puzzles_by_meta);

	render('bymeta.twig', 'bymeta', array(
			'all_puzzles_by_meta' => $all_puzzles_by_meta,
			'metas'               => $metas,
		));
}

function displayLoosePuzzles() {
	// TODO: refactor this to use COUNT() mechanism
	$all_puzzles = PuzzleQuery::create()
		->leftJoinWith('Puzzle.PuzzleParent')
		->find();

	$puzzles = array();
	foreach ($all_puzzles as $puzzle) {
		if ($puzzle->countPuzzleParents() == 0) {
			$puzzles[] = $puzzle;
		}
	}

	render('loose.twig', 'loose', array(
			'puzzles' => $puzzles,
		));
}

// PUZZLE

function displayPuzzle($puzzle_id, $method = "get") {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	if (!$puzzle) {
		redirect('/', "Puzzle $puzzle_id does not exist.");
	}

	$notes = NoteQuery::create()
		->filterByPuzzle($puzzle)
		->orderByCreatedAt('desc')
		->find();

	$members = $puzzle->getMembers();

	$is_member      = false;
	$current_member = $_SESSION['user'];
	foreach ($members as $member) {
		if ($member->getId() == $current_member->getId()) {
			$is_member = true;
		}
	}

	// TODO: Can we use $puzzle->getParents() for this?
	$metas_to_show = PuzzlePuzzleQuery::create()
		->joinWith('PuzzlePuzzle.Parent')
		->orderByParentId()
		->withColumn('Sum(puzzle_id ='.$puzzle_id.')', 'IsInMeta')
		->filterByParentId($puzzle_id, CRITERIA::NOT_EQUAL)
		->groupBy('Parent.Id')
		->find();

	// TODO: Can we use $puzzle->getParents() for this?
	$me_as_meta = PuzzlePuzzleQuery::create()
		->filterByParent($puzzle)
		->filterByChild($puzzle)
		->count();

	$puzzles = null;
	if ($me_as_meta > 0) {
		$puzzles = $puzzle->getChildren();
	}

	$template = 'puzzle.twig';

	if ($method == "edit") {
		$template = 'puzzle-edit.twig';
	}

	render($template, 'puzzle', array(
			'puzzle_id' => $puzzle_id,
			'puzzle'    => $puzzle,
			'notes'     => $notes,
			'members'   => $members,
			'is_member' => $is_member,
			'all_metas' => $metas_to_show,
			'i_am_meta' => $me_as_meta > 0,
			'puzzles'   => $puzzles,
		));
}

function editPuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$puzzle->setTitle($request->title);
	$puzzle->setStatus($request->status);
	$puzzle->setSpreadsheetId($request->spreadsheet_id);
	$puzzle->setSlackChannel($request->slack_channel);
	$puzzle->save();

	// Remove all parents, even myself if I'm a meta
	$oldParents = PuzzlePuzzleQuery::create()
		->filterByPuzzleId($puzzle_id)
		->find();
	$oldParents->delete();

	// Assign parents
	foreach ($request->metas as $meta_id) {
		$meta = PuzzleQuery::create()
			->filterById($meta_id)
			->findOne();
		$puzzle->addParent($meta);
	}

	// Add self as parent if it's a meta
	if ($request->i_am_meta == "y") {
		$puzzle->addParent($puzzle);
	}

	$puzzle->save();

	$puzzle->solve($request->solution);

	$alert = "Saved ".$puzzle->getTitle();
	redirect('/puzzle/'.$puzzle_id.'/edit', $alert);
}

function solvePuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$alert = $puzzle->solve($request->solution);

	redirect('/puzzle/'.$puzzle_id, $alert);
}

function deletePuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$puzzle_title = $puzzle->getTitle();
	$puzzle->delete();

	$alert = "Archived ".$puzzle_title;
	redirect('/', $alert);
}

function changePuzzleStatus($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$newStatus = $request->status;
	$puzzle->setStatus($newStatus);
	$puzzle->save();

	if (in_array($newStatus, ['priority', 'urgent'])) {
		$news_text = "status set to `".$newStatus."`.";
		addNews($news_text, $newStatus, $puzzle);
		postToChannel(emojify($puzzle->getStatus()).' URGENT help is needed on *'.$puzzle->getTitle().'*!', $puzzle->getSlackAttachmentMedium(), null, ":bell:", "StatusBot");
		// TODO: change channel to #general
	}

	$alert = "Changed status.";
	redirect('/puzzle/'.$puzzle_id, $alert);
}

function addNote($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$noteText = $request->body;

	if (trim($noteText) != "") {
		$alert = $puzzle->note($noteText, $_SESSION['user']);
	}

	redirect('/puzzle/'.$puzzle_id, $alert);
}

function joinPuzzle($puzzle_id) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$member = $_SESSION['user'];

	$alert = $member->joinPuzzle($puzzle);
	redirect('/puzzle/'.$puzzle_id, $alert);
}

function leavePuzzle($puzzle_id) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$member = $_SESSION['user'];

	$alert = $member->leavePuzzle($puzzle);
	redirect('/puzzle/'.$puzzle_id, $alert);
}

function archivePuzzleNote($note_id, $puzzle_id) {
	$note = NoteQuery::create()
		->filterByID($note_id)
		->delete();

	$alert = "Note archived.";
	redirect('/puzzle/'.$puzzle_id, $alert);
}

// ADDING PUZZLES

function displayAdd($meta_id = '') {
	// TODO: We use this on /bymeta too. Abstract to generated-class?
	$metas = PuzzlePuzzleQuery::create()
		->joinWith('PuzzlePuzzle.Parent')
		->where('puzzle_id = parent_id')
		->orderBy('Parent.title')
		->find();

	render('add.twig', 'add', array(
			'meta_id' => $meta_id,
			'metas'   => $metas,
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

			$meta_id = $puzzleContent['meta'];

			if ($meta_id == 0) {
				// it's a meta, so set Parent to itself
				$meta = $newPuzzle;
			} elseif ($meta_id > 0) {
				$meta = PuzzleQuery::create()
					->filterByID($meta_id)
					->findOne();
			}

			if ($meta) {
				$newPuzzle->addParent($meta);
				$newPuzzle->save();
			}

			$newPuzzles[] = array(
				'puzzleID' => $puzzleId,
				'title'    => $puzzleContent['title'],
				'pkID'     => $newPuzzle->getID(),
			);

			$news_text = "was added.";
			addNews($news_text, 'open', $newPuzzle);

			// POST TO SLACK CHANNEL
			postToChannel('*'.$puzzle->getTitle().'*', $puzzle->getSlackAttachmentLarge(), $channel);
			postToChannel('*'.$puzzle->getTitle().'*', $puzzle->getSlackAttachmentLarge());
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

// ROSTER

function displayRoster() {
	$members = MemberQuery::create()
		->leftJoinWith('PuzzleMember')
		->leftJoinWith('PuzzleMember.Puzzle')
		->orderByFullName()
		->find();

	render('roster.twig', 'roster', array(
			'roster' => $members,
		));
}

function displayMember($member_id, $method = "get") {
	$template = 'member.twig';
	$member   = $_SESSION['user'];

	if ($method == "edit") {
		$template = 'member-edit.twig';
	}

	$puzzles = $member->getPuzzles();

	render($template, 'member', array(
			'member'  => $member,
			'puzzles' => $puzzles,
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

function assignSlackId($slack_id) {
	$member = $_SESSION['user'];
	$member->setSlackId($slack_id);
	$member->save();

	$message = "Thanks! Saved your Slack ID.";
	redirect('/member/'.$member->getId(), $message);
}

function displayNews($filter = "all") {
	$news = NewsQuery::create()
		->leftJoinWith('News.Member')
		->leftJoinWith('News.Puzzle')
		->orderByCreatedAt('desc');

	if ($filter == "important") {
		$news->filterByNewsType('important')
		     ->find();
	} elseif ($filter == "puzzles") {
		$news->where('puzzle_id is not null')
		     ->find();
	} else {
		$news->find();
	}

	render('news.twig', 'news', array(
			'filter'  => $filter,
			'updates' => $news,
		));
}

function addNews($text, $type = "important", $puzzle = null, $member = null) {
	$update = new News();
	$update->setContent($text);
	$update->setNewsType($type);
	$update->setMember($member);
	$update->setPuzzle($puzzle);
	$update->save();
}

function postNews($text) {
	if (trim($text) == "") {
		redirect('/news/');
	}

	$member = $_SESSION['user'];
	addNews($text, "important", null, $member);
	postToChannel('*IMPORTANT NEWS* from '.$member->getFullName(), [
			"text"  => $text,
			"color" => "#ff0000",
		], null, ":mega:", "NewsBot");

	$alert = "Update posted.";
	redirect('/news', $alert);
}

function archiveNews($update_id) {
	$note = NewsQuery::create()
		->filterByID($update_id)
		->delete();

	$alert = "Update archived.";
	redirect('/news/', $alert);
}

function displayUnsolvedPuzzles() {
	$puzzles = PuzzleQuery::create()
		->filterByStatus('solved', Criteria::NOT_EQUAL)
		->find();

	render('unsolved.twig', 'unsolved', array(
			'puzzles' => $puzzles,
		));
}
