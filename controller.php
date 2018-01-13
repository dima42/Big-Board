<?php
use Cocur\Slugify\Slugify;
use Propel\Runtime\ActiveQuery\Criteria;

$this->respond('GET', '/test',

function ($request, $response) {
		return displayTest($response);
	});

// PUZZLE LISTS

$this->respond('GET', '/', function () {
		return displayAll();
	});

$this->respond('GET', '/bymeta', function () {
		return displayAllByMeta();
	});

// DATA API

$this->with('/puzzles', function () {
		$this->respond('GET', '/all/[:field]/[:order]', function ($request, $response) {
				return allPuzzles($request->field, $request->order, $response);
			});
		$this->respond('GET', '/bymeta', function ($request, $response) {
				return allPuzzlesByMeta($response);
			});
		$this->respond('GET', '/metas', function ($request, $response) {
				return allMetas($request, $response);
			});
		$this->respond('GET', '/meta/[:meta_id]', function ($request, $response) {
				return metaPuzzles($request->meta_id, $response);
			});
		$this->respond('GET', '/member/[:member_id]', function ($request, $response) {
				return memberPuzzles($request->member_id, $response);
			});
		$this->respond('GET', '/withtags', function ($request, $response) {
				return unsolvedWithTags($response);
			});
	});

$this->respond('GET', '/members', function ($request, $response) {
		return allMembers($response);
	});

$this->respond('GET', '/scrape_avatars', function ($request, $response) {
		return scrapeAvatars();
	});

// POLL GOOGLE DRIVE

$this->respond('GET', '/poll_drive', function ($request, $response) {
		return pollDrive();
	});

// PUZZLES

$this->with('/puzzle/[:id]', function () {

		$this->respond('GET', '/?', function ($request) {
				return displayPuzzle($request->id);
			});
		$this->respond('GET', '/edit/?', function ($request) {
				return displayPuzzle($request->id, 'edit');
			});
		$this->respond('POST', '/edit/?', function ($request) {
				return editPuzzle($request->id, $request);
			});
		$this->respond('POST', '/solve/?', function ($request) {
				return solvePuzzle($request->id, $request);
			});
		$this->respond('POST', '/change-status/?', function ($request) {
				return changePuzzleStatus($request->id, $request);
			});
		$this->respond('POST', '/add-note/?', function ($request) {
				return addNote($request->id, $request);
			});
		$this->respond('POST', '/join/?', function ($request) {
				return joinPuzzle($request->id);
			});
		$this->respond('POST', '/leave/?', function ($request) {
				return leavePuzzle($request->id);
			});
		$this->respond('POST', '/delete/?', function ($request) {
				return deletePuzzle($request->id, $request);
			});
		$this->respond('POST', '/delete-note/[:note_id]/?', function ($request) {
				return archivePuzzleNote($request->note_id, $request->id);
			});
	});

// TAGS

$this->with('/tags', function () {
		$this->respond('GET', '/?', function ($request, $response) {
				return displayTags();
			});
		$this->respond('GET', '/admin/?', function ($request, $response) {
				return displayTagAdmin();
			});
		$this->respond('GET', '/edit/?', function ($request, $response) {
				return displayTagAdmin('edit');
			});
		$this->respond('POST', '/add/?', function ($request, $response) {
				return addTag($request);
			});
		$this->respond('POST', '/[:id]/edit/?', function ($request, $response) {
				return editTag($request);
			});
		$this->respond('POST', '/[:id]/move_up/?', function ($request, $response) {
				return moveTag($request, $request->id, 'up');
			});
		$this->respond('POST', '/[:id]/move_dn/?', function ($request, $response) {
				return moveTag($request, $request->id, 'down');
			});
		$this->respond('POST', '/alert/[:id]/?', function ($request, $response) {
				return alertTag($request, $response, $request->id);
			});
		$this->respond('POST', '/invite/?', function ($request, $response) {
				return inviteToTag($request, $response);
			});
	});

// MEMBER

$this->respond('GET', '/me', function () {
		redirect('/member/'.$_SESSION['user']->getId());
	});

$this->respond('GET', '/you', function () {
		redirect('/member/'.$_SESSION['user']->getId());
	});

$this->respond('GET', '/member', function () {
		redirect('/member/'.$_SESSION['user']->getId());
	});

$this->with('/member', function () {
		$this->respond('GET', '/[i:id]/?', function ($request) {
				return displayMember($request->id);
			});
		$this->respond('GET', '/edit/?', function ($request) {
				return displayMemberEdit();
			});
		$this->respond('POST', '/edit/?', function ($request) {
				return saveMember($request);
			});

	});

$this->respond('GET', '/assign_slack_id/[:slack_id]', function ($request) {
		return assignSlackId($request->slack_id);
	});

// ROSTER

$this->respond('GET', '/roster', function () {
		return displayRoster();
	});

// ADDING

$this->with('/add', function () {

		$this->respond('GET', '/?', function ($request) {
				return displayAdd();
			});
		$this->respond('GET', '/[:meta_id]/?', function ($request) {
				return displayAdd($request->meta_id);
			});
		$this->respond('POST', '/?', function ($request, $response) {
				return addPuzzle($request, $response);
			});
	});

$this->respond('GET', '/puzzle_scrape', function ($request, $response) {
		return puzzleScrape($request, $response);
	});

// NEWS

$this->with('/news', function () {
		$this->respond('GET', '/?', function ($request) {
				return displayNews();
			});
		$this->respond('POST', '/add/?', function ($request) {
				return postNews($request->body);
			});
		$this->respond('POST', '/[:update_id]/delete/?', function ($request) {
				return archiveNews($request->update_id);
			});
	});

// ABOUT

$this->respond('GET', '/about', function () {
		return displayAbout();
	});

// LOGOUT

$this->respond('GET', '/logout', function () {
		session_unset();
		setcookie("PAL_ACCESS_TOKEN", "", time()-3600);
		setcookie("refresh_token", "", time()-3600);
		return redirect("/");
	});

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

function scrapeAvatars() {
	$has_avatars = MemberQuery::create()
		->filterBySlackId(null, Criteria::NOT_EQUAL)
		->find();

	foreach ($has_avatars as $member) {
		$s = scrapeAvatar($member);
		preprint($member->getFullName()." ".$s['ok']);
	}

	return;
}

function pollDrive() {
	Global $pal_drive;

	$mostRecentUpdate = PuzzleQuery::create()
		->orderBySheetModDate('desc')
		->select('sheet_mod_date')
		->findOne();

	$mruDateTime = date("c", strtotime($mostRecentUpdate));

	$all_files = $pal_drive->files->listFiles([
			"maxResults" => 200,
			"q"          => "'".getenv('GOOGLE_DRIVE_PUZZLES_FOLDER_ID')."' in parents and trashed != true and modifiedDate > '".$mruDateTime."'",
		]);

	foreach ($all_files["items"] as $k => $sheetData) {
		$sheetData['modifiedDate'];

		$p = PuzzleQuery::create()
			->findOneBySpreadsheetId($sheetData['id']);

		if ($p) {
			debug("UPDATED: ".$p->getTitle());
			$p->setSheetModDate($sheetData['modifiedDate']);
			$p->save();
		}
	}

	return;
}

function displayTest($response) {
	$poll_time = file_get_contents('next_poll_time.txt');

	preprint("Next poll time: ".date("c", $poll_time));
	preprint("Current time: ".date("c", time()));

	if ($poll_time <= time()) {
		pollDrive();
		$next_poll_time = strtotime("+2 minutes", $poll_time);
		file_put_contents('next_poll_time.txt', $next_poll_time);
	}

	return;

	render('test.twig', '', array(
			// 'content' => $result,
		));
}

// PUZZLE DATA

function allPuzzles($orderBy = 'Title', $orderHow = 'asc', $response) {
	$puzzles = PuzzleQuery::create()
		->orderBy($orderBy, $orderHow)
		->orderByTitle($orderHow)
		->select(['Id', 'Title', 'Url', 'SpreadsheetId', 'Solution', 'Status', 'SlackChannelId', 'SolverCount'])
		->find()
		->toArray();

	return $response->json($puzzles);
}

function allPuzzlesByMeta($response) {
	$puzzles = PuzzleQuery::create()
		->leftJoinWithPuzzleParent()
		->orderByTitle()
		->find()
		->toArray();

	return $response->json($puzzles);
}

function allMetas($request, $response) {
	$metas = PuzzleQuery::create()
		->joinWith('PuzzleChild')
		->where('Puzzle.id = PuzzleChild.parent_id')
		->where('Puzzle.id = PuzzleChild.puzzle_id')
		->select(['Id'])
		->find()
		->toArray();

	return $response->json($metas);
}

function metaPuzzles($meta_id, $response) {
	$puzzles = PuzzleQuery::create()
		->joinWithPuzzleParent()
		->where('PuzzleParent.ParentId = '.$meta_id)
		->orderByTitle()
		->find()
		->toArray();

	return $response->json($puzzles);
}

function memberPuzzles($member_id, $response) {
	$member = MemberQuery::create()
		->filterById($member_id)
		->findOne();

	$puzzles = $member->getPuzzles()->toArray();
	return $response->json($puzzles);
}

function allMembers($response) {
	$members = MemberQuery::create()
		->leftJoin('PuzzleMember')
		->withColumn('PuzzleMember.PuzzleId', 'PuzzleId')
		->orderByFullName()
		->select(array('Id', 'FullName', 'Strengths', 'PhoneNumber', 'SlackId', 'PuzzleId', 'Avatar', 'Location'))
		->find()
		->toArray();

	return $response->json($members);
}

function unsolvedWithTags($response) {
	$puzzles = PuzzleQuery::create()
		->filterByStatus('solved', Criteria::NOT_EQUAL)
		->joinWithTagAlert()
		->find()
		->toArray();

	return $response->json($puzzles);
}

// PUZZLE LISTS

function displayAll() {
	Global $STATUSES;

	$statusGroups = PuzzleQuery::create()
		->filterByStatus('solved', Criteria::NOT_EQUAL)
		->withColumn('COUNT(Puzzle.Status)', 'StatusCount')
		->groupBy('Puzzle.Status')
		->select(array('Status', 'StatusCount'))
		->find();

	$total_puzzle_count = PuzzleQuery::create()
		->count();

	$statusCounts   = [[], [], []];
	$unsolved_count = 0;
	foreach ($statusGroups as $status) {
		$unsolved_count          = $unsolved_count+$status['StatusCount'];
		$position                = array_search($status['Status'], array_keys($STATUSES));
		$statusCounts[$position] = [
			"count"      => $status['StatusCount'],
			"percentage" => 100*$status['StatusCount']/$total_puzzle_count,
			"status"     => $status['Status'],
		];
	}

	$member         = $_SESSION['user'];
	$member_puzzles = $member->getPuzzles();

	$solved_percentage = ($total_puzzle_count == 0)?0:($total_puzzle_count-$unsolved_count)/$total_puzzle_count;
	render('all.twig', 'puzzles', array(
			'statusCounts'       => $statusCounts,
			'unsolved_count'     => $unsolved_count,
			'solved_percentage'  => $solved_percentage,
			'total_puzzle_count' => $total_puzzle_count,
			'member_puzzles'     => $member_puzzles,
		));
}

function displayAllByMeta() {
	$metas = PuzzleQuery::create()
		->joinWith('PuzzleChild')
		->leftJoinWith('Wrangler')
		->where('Puzzle.id = PuzzleChild.parent_id')
		->where('Puzzle.id = PuzzleChild.puzzle_id')
		->orderBy('title')
		->select(['Id', 'Title', 'Status'])
		->withColumn('Wrangler.Id', 'WranglerId')
		->withColumn('Wrangler.FullName', 'Wrangler')
		->find()
		->toArray();

	array_unshift($metas, [
			"Id"    => "0",
			"Title" => "(Loose)",
		]);

	render('bymeta.twig', 'bymeta', array(
			'metas' => $metas,
		));
}

// PUZZLE

function displayPuzzle($puzzle_id, $method = "get") {
	$puzzle = PuzzleQuery::create()
		->leftJoinWithWrangler()
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

	$puzzles_metas = PuzzleQuery::create()
		->joinPuzzleChild()
		->leftJoinWithWrangler()
		->orderByTitle()
		->withColumn('PuzzleChild.PuzzleId', 'PuzzleId')
		->where('PuzzleChild.PuzzleId = '.$puzzle->getId())
		->find();

	$is_meta = false;
	foreach ($puzzles_metas as $meta) {
		if ($meta->getId() == $puzzle->getId()) {
			$is_meta = true;
		}
	}

	$template = 'puzzle.twig';

	$metas_to_show = [];
	$full_roster   = [];
	if ($method == "edit") {
		$template = 'puzzle-edit.twig';

		$metas_to_show = PuzzlePuzzleQuery::create()
			->joinWith('PuzzlePuzzle.Parent')
			->orderBy('Parent.Title')
			->withColumn('Sum(puzzle_id ='.$puzzle_id.')', 'IsInMeta')
			->filterByParentId($puzzle_id, CRITERIA::NOT_EQUAL)
			->groupBy('Parent.Id')
			->find();

		$full_roster = MemberQuery::create()
			->orderBy('FullName')
			->find();
	}

	$puzzles = TagQuery::create()
		->findTree(1);

	$topics = TagQuery::create()
		->findTree(2);

	$skills = TagQuery::create()
		->findTree(3);

	$tag_alerts = TagAlertQuery::create()
		->filterByPuzzle($puzzle)
		->select('TagId')
		->find()
		->toArray();

	render($template, 'puzzles', array(
			'puzzle_id'     => $puzzle_id,
			'puzzle'        => $puzzle,
			'notes'         => $notes,
			'members'       => $members,
			'is_member'     => $is_member,
			'metas_to_show' => $metas_to_show,
			'puzzles_metas' => $puzzles_metas,
			'is_meta'       => $is_meta,
			'all_members'   => $full_roster,
			'scopes'        => [
				'Puzzle Types' => $puzzles,
				'Topics'       => $topics,
				'Skills'       => $skills,
			],
			'tag_alerts' => $tag_alerts,
		));
}

function editPuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$wrangler_id = ($request->wrangler != "")?$request->wrangler:null;

	$puzzle->setTitle($request->title);
	$puzzle->setStatus($request->status);
	$puzzle->setSpreadsheetId($request->spreadsheet_id);
	$puzzle->setSlackChannelID($request->slack_channel_id);
	$puzzle->setWranglerId($wrangler_id);
	$puzzle->save();

	// Remove all parents, even myself, if I'm a meta
	$oldParents = PuzzlePuzzleQuery::create()
		->filterByPuzzleId($puzzle_id)
		->find();
	$oldParents->delete();

	// Assign parents
	if ($request->metas) {
		foreach ($request->metas as $meta_id) {
			$meta = PuzzleQuery::create()
				->filterById($meta_id)
				->findOne();
			$puzzle->addParent($meta);
		}
	}

	// Add self as parent if it's a meta
	if ($request->is_meta == "y") {
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

	if ($newStatus == 'priority') {
		$news_text = "status set to `".$newStatus."`.";
		addNews($news_text, $newStatus, $puzzle);

		postToGeneral(
			':priority: *'.$puzzle->getTitle().'* was set to `PRIORITY`.',
			$puzzle->getSlackAttachmentMedium(),
			":bell:",
			"StatusBot"
		);
	} elseif ($newStatus == 'solved') {
		$puzzle->removeMembers();
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

function displayAdd($meta_id = '-1') {
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
				"slack" => substr($slugify->slugify($title), 0, 19)
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

	# Valid Slack channel name characters, taken from https://gist.github.com/gswalden/27ac96e497c3aa1f3230
	# Slack now supports some non-Latin characters, but we should be able to do without.
	$slugify = new Slugify(['regexp' => '/[^a-z0-9._-]+/']);

	foreach ($request->newPuzzles as $puzzleKey => $puzzleContent) {
		$puzzleId   = "puzzleGroup".$puzzleKey;
		$puzzle_url = $puzzleContent['url'];
		if (!preg_match("/\:\/\//", $puzzle_url)) {
			$puzzle_url = "http://".$puzzle_url;
		}

		$puzzleURLExists = PuzzleQuery::create()
			->filterByURL($puzzle_url)
			->findOne();
		$puzzleTitleExists = PuzzleQuery::create()
			->filterByTitle($puzzleContent['title'])
			->findOne();
		$slackNameExists = (getSlackChannelID($puzzleContent['slack']) != 0);

		if ($puzzleURLExists) {
			$existingURLs[] = $puzzleId;
		}

		if ($puzzleTitleExists) {
			$existingTitles[] = $puzzleId;
		}

		if ($slackNameExists) {
			$existingSlacks[] = $puzzleId;
		}

		if (!$puzzleURLExists && !$puzzleTitleExists && !$slackNameExists) {
			$slack_channel_slug = substr($slugify->slugify($puzzleContent['slack']), 0, 19);
			$slack_channel_name = "ρ_".$slack_channel_slug;
			$newChannelID       = createNewSlackChannel($slack_channel_name);

			$spreadsheet_id = create_file_from_template($puzzleContent['title']);

			$newPuzzle = new Puzzle();
			$newPuzzle->setTitle($puzzleContent['title']);
			$newPuzzle->setUrl($puzzle_url);
			$newPuzzle->setSpreadsheetId($spreadsheet_id);
			$newPuzzle->setSlackChannel($slack_channel_name);
			$newPuzzle->setSlackChannelId($newChannelID);
			$newPuzzle->setStatus('open');
			$newPuzzle->save();

			$meta_id = $puzzleContent['meta'];
			$meta    = null;

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

			$tobybot      = new Bot();
			$instructions = getTobyBotInstructions();

			// POST TO SLACK CHANNEL
			postToChannel('*'.$newPuzzle->getTitle().'*', $newPuzzle->getSlackAttachmentLarge(), ":hatching_chick:", "NewPuzzleBot", $newPuzzle->getSlackChannel());
			postToChannel('*Puzzle channel commands that I answer to:*', $instructions, ":robot_face:", "HelperBot", $newPuzzle->getSlackChannel());

			// POST TO #general
			postToGeneral('*'.$newPuzzle->getTitle().'*', $newPuzzle->getSlackAttachmentMedium(), ":hatching_chick:", "NewPuzzleBot");
		}
	}

	return $response->json(array(
			'existingURLs'   => $existingURLs,
			'existingTitles' => $existingTitles,
			'existingSlacks' => $existingSlacks,
			'newPuzzles'     => $newPuzzles,
		));
}

// TAGS

function displayTags() {
	$puzzles = TagQuery::create()
		->findTree(1);

	$topics = TagQuery::create()
		->findTree(2);

	$skills = TagQuery::create()
		->findTree(3);

	render('tags.twig', 'tags', array(
			'scopes'        => [
				'Puzzle Types' => $puzzles,
				'Topics'       => $topics,
				'Skills'       => $skills,
			],
		));
}

function displayTagAdmin($view = 'view') {
	$puzzles = TagQuery::create()
		->findTree(1);

	$topics = TagQuery::create()
		->findTree(2);

	$skills = TagQuery::create()
		->findTree(3);

	$template = 'tags-admin.twig';
	if ($view == 'edit') {
		$template = 'tags-edit.twig';
	}

	render($template, 'tags', array(
			'scopes'        => [
				'Puzzle Types' => $puzzles,
				'Topics'       => $topics,
				'Skills'       => $skills,
			],
		));
}

function addTag($request) {
	Global $DEBUG;

	$parent = TagQuery::create()
		->findPk($request->parent);

	$slugify = new Slugify();
	$slug    = substr($slugify->slugify($request->title), 0, 21);

	if (!$DEBUG) {
		$newChannelID = createNewSlackChannel($slug);
	} else {
		$newChannelID = "fake123";
	}

	if (!$newChannelID) {
		$alert = "Sorry, something went wrong.";
	} else {
		$tag = new Tag();
		$tag->setTitle($request->title);
		$tag->setDescription($request->description);
		$tag->insertAsLastChildOf($parent);
		$tag->setSlackChannel($slug);
		$tag->setSlackChannelId($newChannelID);
		$tag->save();

		$alert = $request->title.' added.';
	}

	redirect('/tags/admin', $alert);
}

function editTag($request) {
	$tag = TagQuery::create()
		->findPk($request->id);

	$tag->setTitle($request->title);
	$tag->setDescription($request->description);
	$tag->save();

	$alert = $request->title.' edited.';
	redirect('/tags/edit', $alert);
}

function moveTag($request, $id, $dir) {
	$tag = TagQuery::create()
		->findPk($id);

	if ($dir == "down") {
		$nextSib = $tag->getNextSibling();
		$tag->moveToNextSiblingOf($nextSib);
	} elseif ($dir == "up") {
		$prevSib = $tag->getPrevSibling();
		$tag->moveToPrevSiblingOf($prevSib);
	}

	redirect('/tags/edit', $tag->getTitle().' moved '.$dir.'.');
}

function alertTag($request, $response, $puzzle_id) {
	$tag_id = $request->tag_id;

	$tag = TagQuery::create()
		->findPk($tag_id);

	$puzzle = PuzzleQuery::create()
		->findPk($puzzle_id);

	// TODO: Don't allow if link has .alerted class

	if ($request->alerted == "true") {
		$ta = TagAlertQuery::create()
			->filterByPuzzleId($puzzle_id)
			->filterByTagId($tag_id)
			->findOne()
			->delete();

		$json = [
			'ok' => 1
		];
	} else {
		$ta = new TagAlert();
		$ta->setPuzzle($puzzle);
		$ta->setTag($tag);
		$ta->save();

		error_log("tagging ".$tag->getTitle());

		postToSlack("*".$puzzle->getTitle()."* is tagged `".strtoupper($tag->getTitle())."`.", $puzzle->getSlackAttachmentMedium(), ":label:", ucfirst($tag->getTitle())." Bot", $tag->getSlackChannelId());

		$json = [
			'ok' => 1
		];
	}

	return $response->json($json);
}

function inviteToTag($request, $response) {
	$channel = $request->channel;
	$member  = $_SESSION['user'];

	$slack_response = inviteToSlackChannel($channel, $member->getSlackId());
	return $response->json($slack_response);
}

// MEMBERS

function displayRoster() {
	$puzzles_with_members = PuzzleQuery::create()
		->joinWith('PuzzleMember')
		->orderBy('Title')
		->groupBy('Title', 'Id')
		->select(['Id', 'Title', 'Status'])
		->find();

	render('roster.twig', 'roster', array(
			'puzzles' => $puzzles_with_members,
		));
}

function displayMember($member_id) {
	$is_user = false;
	$member  = MemberQuery::create()
		->filterById($member_id)
		->findOne();

	$member_channels = [];
	$scopes          = [];

	// If it's the logged-in user, take this chance to refresh the session object in case member data has changed
	if ($member_id == $_SESSION['user']->getId()) {
		$is_user          = true;
		$_SESSION['user'] = $member;

		$slack_id     = $member->getSlackId();
		$all_channels = getAllSlackChannels()['channels'];
		$member_of    = array_filter($all_channels, function ($channel) use ($slack_id) {
				return in_array($slack_id, $channel['members']);
			});
		$member_channels = array_map(function ($channel) {
				return $channel['id'];
			}, $member_of);

		$puzzles = TagQuery::create()
			->findTree(1);

		$topics = TagQuery::create()
			->findTree(2);

		$skills = TagQuery::create()
			->findTree(3);

		$scopes = [
			'Puzzle Types' => $puzzles,
			'Topics'       => $topics,
			'Skills'       => $skills,
		];
	}

	render('member.twig', 'member', array(
			'member'          => $member,
			'is_user'         => $is_user,
			'member_channels' => $member_channels,
			'scopes'          => $scopes,
		));
}

function displayMemberEdit() {
	render('member-edit.twig', 'member', array(
		));
}

function saveMember($request) {
	$member = $_SESSION['user'];

	$member->setFullName($request->full_name);
	$member->setStrengths($request->strengths);
	$member->setPhoneNumber($request->phone_number);
	$member->setLocation($request->location);
	if (isset($request->slack_id)) {
		$member->setSlackId($request->slack_id);
	}
	$member->save();

	$_SESSION['user'] = $member;

	$message = "Saved your profile changes.";
	redirect('/member/edit', $message);
}

function assignSlackId($slack_id) {
	$member = $_SESSION['user'];
	$member->setSlackId($slack_id);
	$member->save();

	$_SESSION['user'] = $member;

	scrapeAvatar($member);

	$message = "Thanks! Saved your Slack ID and grabbed your avatar.";
	redirect('/member/'.$member->getId(), $message);
}

function displayNews() {
	$important = NewsQuery::create()
		->leftJoinWith('News.Member')
		->leftJoinWith('News.Puzzle')
		->orderByCreatedAt('desc')
		->filterByNewsType('important')
		->find();

	$updates = NewsQuery::create()
		->leftJoinWith('News.Member')
		->leftJoinWith('News.Puzzle')
		->orderByCreatedAt('desc')
		->filterByNewsType('important', Criteria::NOT_EQUAL)
		->find();

	render('news.twig', 'news', array(
			'important' => $important,
			'updates'   => $updates,
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
	postToGeneral(
		'*IMPORTANT NEWS* from '.$member->getFullName(), [[
				"text"  => $text,
				"color" => "#ff0000",
			]],
		":mega:",
		"NewsBot"
	);

	$alert = "Update posted.";
	redirect('/news', $alert);
}

function archiveNews($update_id) {
	$note = NewsQuery::create()
		->filterByID($update_id)
		->delete();

	$alert = "News update has been archived.";
	redirect('/news/', $alert);
}

// ABOUT

function displayAbout() {
	render('about.twig', 'about', array(
		));
}
