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

$this->respond('GET', '/loose', function () {
		return displayLoosePuzzles();
	});

// DATA API

$this->with('/puzzles', function () {
		$this->respond('GET', '/all', function ($request, $response) {
				return allPuzzles($response);
			});
		$this->respond('GET', '/bymeta', function ($request, $response) {
				return allPuzzlesByMeta($response);
			});
		$this->respond('GET', '/loose', function ($request, $response) {
				return loosePuzzles($response);
			});
		$this->respond('GET', '/meta/[:meta_id]', function ($request, $response) {
				return metaPuzzles($request->meta_id, $response);
			});
		$this->respond('GET', '/member/[:member_id]', function ($request, $response) {
				return memberPuzzles($request->member_id, $response);
			});
	});

$this->respond('GET', '/members', function ($request, $response) {
		return allMembers($response);
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

// TOPICS

$this->with('/topics', function () {
		$this->respond('GET', '/?', function ($request, $response) {
				return displayTopics();
			});
		$this->respond('POST', '/add/?', function ($request, $response) {
				return addTopic($request);
			});
		$this->respond('POST', '/[:id]/move_up/?', function ($request, $response) {
				return moveTopic($request, $request->id, 'up');
			});
		$this->respond('POST', '/[:id]/move_dn/?', function ($request, $response) {
				return moveTopic($request, $request->id, 'down');
			});
		$this->respond('POST', '/alert/[:id]/?', function ($request, $response) {
				return alertTopic($request, $response, $request->id);
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
				return displayNews("all");
			});
		$this->respond('GET', '/[:filter]/?', function ($request) {
				return displayNews($request->filter);
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

function displayTest($response) {
	$member = $_SESSION['user'];

	$commander = getSlackCommander();

	$answer = $commander->execute('users.info', [
			'user' => $member->getSlackId()
		]);

	// Avatar options: image_24, 32, 48, 72, 192, 512, 1024
	$avatar = $answer->getBody()['user']['profile']['image_192'];
	$member->setAvatar($avatar);
	$member->save();

	preprint($avatar);
	return;

	render('test.twig', '', array(
			// 'content' => $result,
		));
}

// PUZZLE DATA

function allPuzzles($response) {
	$puzzles = PuzzleQuery::create()
		->orderByTitle()
		->find()
		->toArray();

	return $response->json($puzzles);
}

function allPuzzlesByMeta($response) {
	$puzzles = PuzzleQuery::create()
		->joinWithPuzzleParent()
		->orderByTitle()
		->find()
		->toArray();

	return $response->json($puzzles);
}

function loosePuzzles($response) {
	$all_puzzles = PuzzleQuery::create()
		->leftJoinWithPuzzleParent()
		->orderByTitle()
		->find()
		->toArray();

	$puzzles = array_filter($all_puzzles, function ($puzzle) {
			return ($puzzle['PuzzleParents'] == []);
		});

	return $response->json($puzzles);
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
		->select(array('Id', 'FullName', 'Strengths', 'PhoneNumber', 'SlackId', 'PuzzleId'))
		->find()
		->toArray();

	return $response->json($members);
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

	$solved_percentage = ($total_puzzle_count == 0)?0:($total_puzzle_count-$unsolved_count)/$total_puzzle_count;
	render('all.twig', 'puzzles', array(
			'statusCounts'       => $statusCounts,
			'unsolved_count'     => $unsolved_count,
			'solved_percentage'  => $solved_percentage,
			'total_puzzle_count' => $total_puzzle_count,
		));
}

function displayAllByMeta() {
	$metas = PuzzlePuzzleQuery::create()
		->joinWith('PuzzlePuzzle.Parent')
		->leftJoinWith('PuzzlePuzzle.Parent.Wrangler')
		->where('puzzle_id = parent_id')
		->orderBy('Parent.title')
		->find();

	render('bymeta.twig', 'bymeta', array(
			'metas' => $metas,
		));
}

function displayLoosePuzzles() {
	render('loose.twig', 'loose', array(
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

	$categories = TopicQuery::create()
		->findRoot(1)
		->getBranch();

	$skills = TopicQuery::create()
		->findRoot(2)
		->getBranch();

	$topic_alerts = TopicAlertQuery::create()
		->filterByPuzzle($puzzle)
		->select('TopicId')
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
			'categories'    => $categories,
			'skills'        => $skills,
			'topic_alerts'  => $topic_alerts,
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
	$puzzle->setWranglerId($request->wrangler);
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
			':priority: *'.$puzzle->getTitle().'* was set to PRIORITY.',
			$puzzle->getSlackAttachmentMedium(),
			":bell:",
			"StatusBot"
		);
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
		$slackNameExists = (getSlackChannelID($puzzleContent['slack']) != 0);

		if ($puzzleURLExists) {
			$existingURLs[] = $puzzleId;
		}

		if ($puzzleTitleExists) {
			$existingTitles[] = $puzzleId;
		}

		if ($puzzleTitleExists) {
			$existingSlacks[] = $puzzleId;
		}

		if (!$puzzleURLExists && !$puzzleTitleExists) {
			$newChannelID = createNewSlackChannel($puzzleContent['slack']);

			$spreadsheet_id = create_file_from_template($puzzleContent['title']);

			$newPuzzle = new Puzzle();
			$newPuzzle->setTitle($puzzleContent['title']);
			$newPuzzle->setUrl($puzzleContent['url']);
			$newPuzzle->setSpreadsheetId($spreadsheet_id);
			$newPuzzle->setSlackChannel($puzzleContent['slack']);
			$newPuzzle->setSlackChannelId($newChannelID);
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
			postToChannel('*'.$newPuzzle->getTitle().'*', $newPuzzle->getSlackAttachmentLarge(), ":hatching_chick:", "NewPuzzleBot", $newPuzzle->getSlackChannel());
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

// TOPICS

function displayTopics() {
	$category_root = TopicQuery::create()
		->findRoot(1);

	$skill_root = TopicQuery::create()
		->findRoot(2);

	$categories = $category_root
		->getBranch();

	$skills = $skill_root
		->getBranch();

	render('topics.twig', 'topics', array(
			'scopes' => [
				$categories, $skills
			],
		));
}

function addTopic($request) {
	// TODO: Special case adding top-level category
	$parent = TopicQuery::create()
		->findPk($request->parent);

	$topic = new Topic();
	$topic->setTitle($request->title);
	$topic->insertAsLastChildOf($parent);
	$topic->save();

	redirect('/topics', $request->title.' added.');
}

function moveTopic($request, $id, $dir) {
	$topic = TopicQuery::create()
		->findPk($id);

	if ($dir == "down") {
		$nextSib = $topic->getNextSibling();
		$topic->moveToNextSiblingOf($nextSib);
	} elseif ($dir == "up") {
		$prevSib = $topic->getPrevSibling();
		$topic->moveToPrevSiblingOf($prevSib);
	}

	redirect('/topics', $topic->getTitle().' moved '.$dir.'.');
}

function alertTopic($request, $response, $puzzle_id) {
	$topic = TopicQuery::create()
		->findPk($request->topic_id);

	$puzzle = PuzzleQuery::create()
		->findPk($puzzle_id);

	// TODO: Send alert to Slack.
	// TODO: Don't allow if link has .alerted class

	$ta = new TopicAlert();
	$ta->setPuzzle($puzzle);
	$ta->setTopic($topic);
	$ta->save();

	postToSlack("*".$puzzle->getTitle()."* has been tagged ".$topic->getTitle(), $puzzle->getSlackAttachmentMedium(), ":label:", "TagBot", $topic->getSlackChannelId());

	$json = [
		'ok'     => 1,
		'puzzle' => $puzzle_id,
		'topic'  => $topic->getId(),
	];

	return $response->json($json);
}
// MEMBERS

function displayRoster() {
	$puzzles_with_members = PuzzleQuery::create()
		->joinWith('PuzzleMember')
		->orderBy('Title')
		->groupBy('Title', 'Id')
		->select(['Id', 'Title'])
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

	// If it's the logged in use, take this chance to refresh the session object in case member data has changed
	if ($member_id == $_SESSION['user']->getId()) {
		$is_user          = true;
		$_SESSION['user'] = $member;
	}

	render('member.twig', 'member', array(
			'member'  => $member,
			'is_user' => $is_user,
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

	$alert = "Update archived.";
	redirect('/news/', $alert);
}

// ABOUT

function displayAbout() {
	render('about.twig', 'about', array(
		));
}
