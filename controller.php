<?php
use Cocur\Slugify\Slugify;
use Propel\Runtime\ActiveQuery\Criteria;
require_once("globals.php");

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
	});

$this->respond('GET', '/members', function ($request, $response) {
		return allMembers($response);
	});

$this->respond('GET', '/scrape_avatars', function ($request, $response) {
		return scrapeAvatars();
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
		$this->respond('POST', '/delete/?', function ($request) {
				return deletePuzzle($request->id, $request);
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

// PUZZLE DATA

function allPuzzles($orderBy = 'Title', $orderHow = 'asc', $response) {
	$puzzles = PuzzleQuery::create()
		->orderBy($orderBy, $orderHow)
		->find();

        $properties = [];
        foreach ($puzzles as $puzzle){
            array_push($properties, $puzzle->getProperties());
        }

	return $response->json($properties);
}

function allPuzzlesByMeta($response) {
        error_log("starting by meta retrieval");
	$puzzles = PuzzleQuery::create()
		->leftJoinWithPuzzleParent()
		->orderByStatus('desc')
		->orderByTitle()
		->find();
        error_log("query done");

        $properties = [];
        foreach ($puzzles as $puzzle){
            $props = $puzzle->getProperties($cached_only=true);
            $props["PuzzleParents"] = $puzzle->getPuzzleParents()->toArray();
            array_push($properties, $props);
        }
        error_log("response formed");

	return $response->json($properties);
}

function allMetas($request, $response) {
	$puzzles = PuzzleQuery::create()
		->joinWith('PuzzleChild')
		->where('Puzzle.id = PuzzleChild.parent_id')
		->where('Puzzle.id = PuzzleChild.puzzle_id')
                ->select(['Id'])
		->find()
                ->toArray();

	return $response->json($puzzles);
}

function metaPuzzles($meta_id, $response) {
	$puzzles = PuzzleQuery::create()
		->joinWithPuzzleParent()
		->where('PuzzleParent.ParentId = '.$meta_id)
		->orderByTitle()
		->find();

        $properties = [];
        foreach ($puzzles as $puzzle){
            $props = $puzzle->getProperties();
            $props["PuzzleParents"] = $puzzle->getPuzzleParents()->toArray();
            array_push($properties, $props);
        }

	return $response->json($properties);
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

	$statusCounts   = [[], [], [], []];
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

	$solved_percentage = ($total_puzzle_count == 0)?0:($total_puzzle_count-$unsolved_count)/$total_puzzle_count;
	render('all.twig', 'puzzles', array(
			'statusCounts'       => $statusCounts,
			'unsolved_count'     => $unsolved_count,
			'solved_percentage'  => $solved_percentage,
			'total_puzzle_count' => $total_puzzle_count,
		));
}

function displayAllByMeta() {
	$metas = PuzzleQuery::create()
		->joinWith('PuzzleChild')
		->leftJoinWith('Wrangler')
		->where('Puzzle.id = PuzzleChild.parent_id')
		->where('Puzzle.id = PuzzleChild.puzzle_id')
		->orderByStatus('desc')
		->orderByTitle()
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

	$members = $puzzle->getMembers();

	$puzzles_metas = PuzzleQuery::create()
		->joinPuzzleChild()
		->leftJoinWithWrangler()
		->orderByStatus('desc')
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

    $metas_to_show = PuzzlePuzzleQuery::create()
        ->joinWith('PuzzlePuzzle.Parent')
        ->orderBy('Parent.Title')
        ->withColumn('Sum(puzzle_id ='.$puzzle_id.')', 'IsInMeta')
        ->filterByParentId($puzzle_id, CRITERIA::NOT_EQUAL)
        ->groupBy('Parent.Id')
        ->find();

	render($template, 'puzzles', array(
			'puzzle_id'     => $puzzle_id,
			'puzzle'        => $puzzle,
			'metas_to_show' => $metas_to_show,
			'puzzles_metas' => $puzzles_metas,
			'is_meta'       => $is_meta,
		));
}

function editPuzzle($puzzle_id, $request) {
        Global $shared_drive;
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

	$puzzle->solve($request->solution, $shared_drive);

	$alert = "Saved ".$puzzle->getTitle();
	redirect('/puzzle/'.$puzzle_id.'/edit', $alert);
}

function solvePuzzle($puzzle_id, $request) {
        global $shared_drive;
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

	$alert = $puzzle->solve($request->solution, $shared_drive);

	redirect('/puzzle/'.$puzzle_id, $alert);
}

function deletePuzzle($puzzle_id, $request) {
	$puzzle = PuzzleQuery::create()
		->filterByID($puzzle_id)
		->findOne();

        archiveSlackChannel($puzzle->getSlackChannelID());
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
		postToHuntChannel(
			':priority: *'.$puzzle->getTitle().'* was set to `PRIORITY`.',
			$puzzle->getSlackAttachmentMedium(),
			":bell:",
			"StatusBot"
		);
	}

	$alert = "Changed status.";
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
        global $shared_sheets;
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
		    error_log("spreadsheet make started");
			$spreadsheet_id = create_file_from_template($puzzleContent['title']);
			error_log("spreadsheet make ended");

			$slack_channel_slug = substr($slugify->slugify($puzzleContent['slack']), 0, 19);
			$slack_channel_name = "ρ_".$slack_channel_slug;
			$newChannelID       = createNewSlackChannel($slack_channel_name);

			$newPuzzle = new Puzzle();
			$newPuzzle->setTitle($puzzleContent['title']);
			$newPuzzle->setUrl($puzzle_url);
			$newPuzzle->setSpreadsheetId($spreadsheet_id);
			$newPuzzle->setSlackChannel($slack_channel_name);
			$newPuzzle->setSlackChannelId($newChannelID);
			$newPuzzle->setStatus('open');
			$newPuzzle->save();
			error_log("puzzle saved");

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

			$tobybot      = new Bot();
			$instructions = getTobyBotInstructions();

			// POST TO SLACK CHANNEL
			postToChannel('*'.$newPuzzle->getTitle().'*', $newPuzzle->getSlackAttachmentLarge(), ":hatching_chick:", "NewPuzzleBot", $newPuzzle->getSlackChannel());
			postToChannel('*Puzzle channel commands that I answer to:*', $instructions, ":robot_face:", "HelperBot", $newPuzzle->getSlackChannel());

			// POST TO main big board slack channel
			postToHuntChannel('*'.$newPuzzle->getTitle().'*', $newPuzzle->getSlackAttachmentMedium(), ":hatching_chick:", "NewPuzzleBot");

                        // put metadata in sheet
                        $newPuzzle->postMetadataToSheet($shared_sheets);
		}
	}

	return $response->json(array(
			'existingURLs'   => $existingURLs,
			'existingTitles' => $existingTitles,
			'existingSlacks' => $existingSlacks,
			'newPuzzles'     => $newPuzzles,
		));
}

// MEMBERS

function displayRoster() {
	render('roster.twig', 'roster');
}

function displayMember($member_id) {
	$is_user = false;
	$member  = MemberQuery::create()
		->filterById($member_id)
		->findOne();

	// If it's the logged-in user, take this chance to refresh the session object in case member data has changed
	if ($member_id == $_SESSION['user']->getId()) {
		$is_user          = true;
		$_SESSION['user'] = $member;
	}

	render('member.twig', 'member', array(
			'member'          => $member,
			'is_user'         => $is_user,
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


// ABOUT

function displayAbout() {
	render('about.twig', 'about', array(
		));
}
