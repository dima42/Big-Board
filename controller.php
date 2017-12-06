<?
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

	// OTHER LISTS

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

	// NEWS

	$klein->with('/news', function () use ($klein) {
			$klein->respond('GET', '/?', function ($request) {
					return displayNews("all");
				});
			$klein->respond('GET', '/[:filter]?/?', function ($request) {
					return displayNews($request->filter);
				});
			$klein->respond('POST', '/add/?', function ($request) {
					return addNews($request->body);
				});
			$klein->respond('POST', '/[:update_id]/delete/?', function ($request) {
					return archiveNews($request->update_id);
				});
		});

	// SLACK BOT

	$klein->with('/board', function () use ($klein) {

			$klein->respond('POST', '/?', function ($request, $response) {
					return bigBoardBot($request, $response);
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
	render('error.twig', array(
			'error' => $error,
		));
}

function displayTest() {
	$puzzle = PuzzleQuery::create()
		->filterByID(184)
		->findOne();

	$puzzle->postInfoToSlack();

	// postPuzzle($puzzle, $puzzle->getSlackChannel());
	// postSolve($puzzle, $puzzle->getSlackChannel());
	// postSolve($puzzle);

	return "";

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

	$members = $puzzle->getMembers();

	// TODO: if not $puzzle, redirect to error template
	// "This puzzle does not exist. It is a ghost puzzle.";

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

	if ($me_as_meta > 0) {
		$puzzles = $puzzle->getChildren();
	}

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
		// TODO: post to slack?
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

function archivePuzzleNote($note_id, $puzzle_id) {
	$note = NoteQuery::create()
		->filterByID($note_id)
		->delete();

	$alert = "Note archived.";
	redirect('/puzzle/'.$puzzle_id, $alert);
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

			$puzzle->postInfoToSlack();
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
		->leftJoinPuzzleMember()
		->leftJoin('PuzzleMember.Puzzle')
		->with('Puzzle')
		->orderByFullName()
		->find();

	render('roster.twig', array(
			'roster' => $roster,
		));
}

function displayMember($member_id, $method = "get") {
	$template = 'member.twig';
	$member   = $_SESSION['user'];

	if ($method == "edit") {
		$template = 'member-edit.twig';
	}

	$puzzles = $member->getPuzzles();

	render($template, array(
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

	$all_puzzles = PuzzlePuzzleQuery::create()
		->joinWith('Child')
		->orderByParentId()
		->find();

	$all_puzzles_by_meta = array();
	foreach ($all_puzzles as $puzzle) {
		$all_puzzles_by_meta[$puzzle->getParent()->getTitle()][] = $puzzle->getChild();
	}

	render('all.twig', array(
			'statusCounts'        => $statuses,
			'total_puzzles'       => $total_puzzles,
			'all_puzzles_by_meta' => $all_puzzles_by_meta,
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

	render('loose.twig', array(
			'puzzles' => $puzzles,
		));
}

function displayFeature($puzzle_id) {
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

	render('news.twig', array(
			'filter'  => $filter,
			'updates' => $news,
		));
}

function addNews($text, $type = "important", $puzzle = null) {
	$member = $_SESSION['user'];

	$update = new News();
	$update->setContent($text);
	$update->setNewsType($type);
	$update->setMember($member);
	$update->setPuzzle($puzzle);
	$update->save();

	$message = "Update posted.";
	redirect('/news', $message);
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

	render('unsolved.twig', array(
			'puzzles' => $puzzles,
		));
}

// BOTS

function bigBoardBot($request, $response) {
	if ($request->token != getenv('PALINDROME_SLACKBOT_TOKEN')) {
		return $response->json(['text' => 'Nothing here. Go away.']);
	}

	$parameter        = $request->text;
	$puzzleQuery      = PuzzleQuery::create();
	$channel_response = ['text' => 'I got nothing, sorry. Try again.'];

	if ($parameter == "") {
		$puzzleQuery->filterByStatus('solved', Criteria::NOT_EQUAL);
		$count   = $puzzleQuery->count();
		$pretext = $count." unsolved puzzles:";
	} elseif (in_array($parameter, ['open', 'priority', 'urgent', 'solved'])) {
		$puzzleQuery->filterByStatus($parameter);
		$count   = $puzzleQuery->count();
		$pretext = $count." puzzles marked `".strtoupper($parameter)."`:";
	} else {
		$puzzleQuery->filterByTitle('%'.$parameter.'%', Criteria::LIKE);
		$count   = $puzzleQuery->count();
		$pretext = $count." puzzle titles match a search for `".strtoupper($parameter)."`:";
	}

	$puzzles     = $puzzleQuery->find();
	$attachments = [];
	foreach ($puzzles as $puzzle) {
		$puzzleInfo = [
			emojify($puzzle                                        ->getStatus()),
			'<http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|:boar:> ',
			'<https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|:drive:> ',
			'*'.$puzzle                                            ->getTitle().'*',
			'<#'.$puzzle                                           ->getSlackChannelId().'>',
		];

		$attachments[] = [
			"text"      => join(" ", $puzzleInfo),
			'color'     => 'good',
			"mrkdwn_in" => ['text'],
		];
	}

	$channel_response = [
		'link_names'    => true,
		"response_type" => "in_channel",
		"text"          => $pretext,
		"attachments"   => $attachments,
	];

	// TODO: if the user who sent this isn't in our system yet, ask him/her to click a link that only they see
	// possible to blast this to everyone?
	// $human_response = [];

	return $response->json($channel_response);
}

function infoBot($request, $response) {
	if ($request->token != getenv('PALINDROME_SLACKBOT_TOKEN')) {
		return $response->json(['text' => 'Nothing here. Go away.']);
	}

	$parameter        = $request->text;
	$channel_response = ['text' => "Sorry, I'm stumped."];
	$channel_id       = $request->channel_id;

	$puzzle = PuzzleQuery::create()
		->filterBySlackChannelId($channel_id)
		->findOne();

	if (!$puzzle) {
		$channel_response = [
			"text" => "`".$request->command."` can only be used inside a puzzle channel.",
		];
	} else {
		$channel_response = [
			'link_names'    => true,
			"response_type" => "in_channel",
			"text"          => "*".$puzzle->getTitle()."* is ".emojify($puzzle->getStatus())." ".strtoupper($puzzle->getStatus()),
			"attachments"   => $puzzle->getAttachmentsForSlack(),
		];
	}

	// TODO: if the user who sent this isn't in our system yet, ask him/her to click a link that only they see
	// possible to blast this to everyone?
	// $human_response = [];

	return $response->json($channel_response);
}

function joinBot($request, $response) {
	if ($request->token != getenv('PALINDROME_SLACKBOT_TOKEN')) {
		return $response->json(['text' => 'Nothing here. Go away.']);
	}

	$parameter        = $request->text;
	$channel_response = ['text' => "Hmm, maybe ask a human for help. This computer is confused."];
	$channel_id       = $request->channel_id;
	$slack_user_id    = $request->user_id;

	$puzzle = PuzzleQuery::create()
		->filterBySlackChannelId($channel_id)
		->findOne();

	$member = MemberQuery::create()
		->filterBySlackId($slack_user_id)
		->findOne();

	if (!$puzzle) {
		$channel_response = [
			"text" => "`".$request->command."` can only be used inside a puzzle channel.",
		];
	} elseif (!$member) {
		$channel_response = [
			"text" => "Hi there! Before you can use the `".$request->command."` command, I need to know who you are. Click this link then try the command again.
http://team-palindrome.herokuapp.com/assign_slack_id/".$slack_user_id,
		];
	} else {
		$response         = $member->joinPuzzle($puzzle);
		$channel_response = [
			"text" => $response
		];
	}

	return $response->json($channel_response);
}

function solveBot($request, $response) {
	if ($request->token != getenv('PALINDROME_SLACKBOT_TOKEN')) {
		return $response->json(['text' => 'Nothing here. Go away.']);
	}

	$channel_response = ['text' => 'Not sure what you mean. Seek help.'];
	$channel_id       = $request->channel_id;
	$solution         = strtoupper($request->text);

	$puzzle = PuzzleQuery::create()
		->filterBySlackChannelId($channel_id)
		->findOne();

	if (!$puzzle) {
		$channel_response = [
			"text" => "`".$request->command."` can only be used inside a puzzle channel.",
		];
	} elseif (!$solution) {
		$channel_response = [
			"text" => "Please include a solution, like so: `/solve LOVE`.",
		];
	} else {
		$puzzle->solve($solution);
		$channel_response = [
			'link_names' => true,
			"text"       => "Got it. I posted `".$solution."` as a solution to *".$puzzle->getTitle()."*.",
		];
	}

	// TODO: if the user who sent this isn't in our system yet, ask him/her to click a link that only they see
	// possible to blast this to everyone?
	// $human_response = [];

	return $response->json($channel_response);
}

function noteBot($request, $response) {
	if ($request->token != getenv('PALINDROME_SLACKBOT_TOKEN')) {
		return $response->json(['text' => 'Nothing here. Go away.']);
	}

	$channel_response = ['text' => 'System Error.'];
	$channel_id       = $request->channel_id;
	$body             = trim($request->text);
	$slack_user_id    = $request->user_id;

	debug("Body: ".$body);

	$puzzle = PuzzleQuery::create()
		->filterBySlackChannelId($channel_id)
		->findOne();

	$member = MemberQuery::create()
		->filterBySlackId($slack_user_id)
		->findOne();

	// If there's no body, send back all notes.
	if (!$body) {
		$note_count = $puzzle->countNotes();

		if ($note_count == 0) {
			$channel_response = [
				"text" => "There are no notes attached to *".$puzzle->getTitle()."*.",
			];
		}

		$all_notes = array_map(function ($note) {
				return [
					"pretext" => $note->getAuthor()->getNameForSlack()." wrote:",
					"text"    => $note->getBody(),
				];
			}, iterator_to_array($puzzle->getNotes()));

		$channel_response = [
			'link_names'    => true,
			"response_type" => "in_channel",
			"attachments"   => $all_notes,
		];
	} elseif (!$puzzle) {
		$channel_response = [
			"text" => "`".$request->command."` can only be used inside a puzzle channel.",
		];
	} elseif (!$member) {
		$channel_response = [
			"text" => "Hi there! Before you can use the `".$request->command."` command, I need to know who you are. Click this link then try the command again.
http://team-palindrome.herokuapp.com/assign_slack_id/".$slack_user_id,
		];
	} else {
		$puzzle->note($body, $member);
		$channel_response = [
			"text" => "Got it. I posted your note to *".$puzzle->getTitle()."*.",
		];
	}

	return $response->json($channel_response);
}
