<?php
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Propel\Runtime\ActiveQuery\Criteria;

function getSlackCommander($slack_key_env_var) {
	$slack_key = getenv($slack_key_env_var);

	$interactor = new CurlInteractor;
	$interactor->setResponseFactory(new SlackResponseFactory);

	return new Commander($slack_key, $interactor);
}

function getBigBoardBotCommander() {
	return getSlackCommander('BIGBOARDBOT_SLACK_KEY');
}

function getTobyBotCommander() {
	return getSlackCommander('TOBYBOT_SLACK_KEY');
}

function getAllSlackChannels() {
	$commander = getTobyBotCommander();

	return $commander->execute('channels.list', [
			'exclude_archived' => true
		])->getBody();
}

function getSlackChannelID($slug) {
	$commander = getTobyBotCommander();

	$response = $commander->execute('channels.list', [
			'channel' => $slug
		]);

	$response_body = $response->getBody();

	if ($response_body['ok'] != 1) {
		return false;
	}

	foreach ($response_body['channels'] as $key => $channel) {
		if ($channel['name'] == $slug) {
			return $channel['id'];
		}
	}

	return 0;
}

function createNewSlackChannel($slug) {
	$commander = getTobyBotCommander();

	$slack_response = $commander->execute('channels.create', [
			'name' => $slug
		]);

	return getSlackChannelID($slug);
}

function inviteToSlackChannel($channel_id, $member_id) {
	$commander = getTobyBotCommander();

	$response = $commander->execute('channels.invite', [
			'channel' => $channel_id,
			'user'    => $member_id,
		]);

	return $response->getBody();
}

function postToGeneral($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot") {
	$channel = "general";
	return postToChannel($message, $attachments, $icon, $bot_name, $channel);
}

function postToBigBoard($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot") {
	$channel = "big-board";
	return postToChannel($message, $attachments, $icon, $bot_name, $channel);
}

function postToSlack($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot", $channel = "big-board") {
	$commander = getBigBoardBotCommander();

	$response = $commander->execute('chat.postMessage', [
			'no_format'   => true,
			'channel'     => $channel,
			'icon_emoji'  => $icon,
			'username'    => $bot_name,
			'text'        => $message,
			'attachments' => json_encode($attachments),
			'link_names'  => 1,
		]);

	return $response;
}

function postToChannel($message, $attachments, $icon, $bot_name, $channel = "big-board") {
	return postToSlack($message, $attachments, $icon, $bot_name, '#'.$channel);
}

function scrapeAvatar($member) {
	$commander      = getTobyBotCommander();
	$slack_response = $commander->execute('users.info', [
			'user' => $member->getSlackId()
		]);

	$response_body = $slack_response->getBody();
	if ($response_body['ok'] == 1) {
		// Avatar options: image_24, 32, 48, 72, 192, 512, 1024
		$avatar = $slack_response->getBody()['user']['profile']['image_192'];
		$member->setAvatar($avatar);
		$member->save();
	}
	return $response_body;
}

// SLACK BOT

function getTobyBotInstructions() {
	$instructions = [
		"`/info` returns all links and solvers on this puzzle.",
		"`/note` returns all of this puzzle's notes.",
		"`/note [text]` adds [text] as a note on this puzzle.",
		"`/solve [text]` sets [text] as the solution to the puzzle.",
		"`/workon` attaches you to this puzzle. (If you are working on a different puzzle, this will forcefully detach you from it.)",
		"`!help` gets you a random puzzle-solving tip.",
		"`/tobybot` returns this list (in a private message to you).",
	];

	return array_map(function ($note) {
			return [
				"text"      => $note,
				"mrkdwn_in" => ['text'],
				"color"     => "#225DAA",
			];
		}, $instructions);
}

class Bot {
	public function __call($name, $args) {
		$request  = $args[0];
		$response = $args[1];
		$user_id  = $request->user_id;

		$member = MemberQuery::create()
			->filterBySlackId($user_id)
			->findOne();

		$this->member = $member;
		$payload      = ['text' => 'Nothing here. Go away.'];

		$command = substr($request->command, 1);

		if ($request->token == getenv('TOBYBOT_VERIFICATION_TOKEN') && $user_id) {
			if (!$member) {
				$payload = ["text" => "Hi there! Before you can use `".$request->command."`, I need to know who you are. Click this link then try the command again: http://".getenv('APP_DOMAIN')."/assign_slack_id/".$user_id];
			} else {
				$payload = call_user_func_array(array($this, $command), $args);
				// If this user doesn't have an avatar, grab it
				if (!$member->getAvatar()) {
					scrapeAvatar($member);
				}
			}
		}

		return $response->json($payload);
	}

	private function tobybot($request, $response) {
		$attachments = getTobyBotInstructions();

		$channel_response = [
			'link_names'  => true,
			'text'        => "*Tobybot commands that work within a puzzle channel:*",
			"attachments" => $attachments,
		];

		return $channel_response;
	}

	private function avatar($request, $response) {
		scrapeAvatar($this->member);
		$channel_response = [
			"text" => "Avatar scraped"
		];
		return $channel_response;
	}

	private function connect($request, $response) {
		$channel_response = [
			"text" => "You're already connected. Thanks!",
		];

		return $channel_response;
	}

	private function board($request, $response) {
		$parameter        = $request->text;
		$puzzleQuery      = PuzzleQuery::create();
		$channel_response = ['text' => 'I got nothing, sorry. Try again.'];

		if ($parameter == "") {
			$puzzleQuery->filterByStatus('solved', Criteria::NOT_EQUAL);
			$count   = $puzzleQuery->count();
			$pretext = $count." puzzles are unsolved:";
		} elseif (in_array($parameter, ['open', 'stuck', 'priority', 'solved'])) {
			$puzzleQuery->filterByStatus($parameter);
			$count   = $puzzleQuery->count();
			$pretext = $count." puzzles marked `".strtoupper($parameter)."`:";
		} else {
			$puzzleQuery->filterByTitle('%'.$parameter.'%', Criteria::LIKE);
			$count   = $puzzleQuery->count();
			$pretext = $count." puzzle titles match a search for `".strtoupper($parameter)."`:";
		}

		$puzzles     = $puzzleQuery->orderByTitle()->find();
		$attachments = array_map(function ($puzzle) {
				return $puzzle->getSlackAttachmentSmall();
			}, iterator_to_array($puzzles));

		return [
			'link_names'    => true,
			"response_type" => "in_channel",
			"text"          => $pretext,
			"attachments"   => $attachments,
		];
	}

	private function info($request, $response) {
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
			$status = $puzzle->getStatus();
			$text   = "*".$puzzle->getTitle()."* is :".$puzzle->getStatus().": `".strtoupper($status)."`";
			if ($status == "solved") {
				$text .= ": `".$puzzle->getSolution()."`";
			}
			$channel_response = [
				'link_names'    => true,
				"response_type" => "in_channel",
				"text"          => $text,
				"attachments"   => $puzzle->getSlackAttachmentLarge(),
			];
		}

		return $channel_response;
	}

	private function workon($request, $response) {
		$parameter        = $request->text;
		$channel_response = ['text' => "Hmm, maybe ask a human for help. This computer is confused."];
		$channel_id       = $request->channel_id;
		$slack_user_handle    = $request->user_name;

		if ($parameter) {
			$slack_user_handle = $parameter;
		}

		if (!$channel_id) {
			return ['text' => "No channel specified."];
		}

		$puzzle = PuzzleQuery::create()
			->filterBySlackChannelId($channel_id)
			->findOne();

		$member = MemberQuery::create()
			->filterBySlackHandle($slack_user_handle)
			->findOne();

		if (!$puzzle) {
			$channel_response = [
				"text" => "`".$request->command."` can only be used inside a puzzle channel.",
			];
		} else {
			$isMember = $puzzle->getMembers()->contains($member);
			if (!$isMember) {
				$response = $member->joinPuzzle($puzzle);
			} else {
				$response = $member->leavePuzzle($puzzle);
			}
			$channel_response = [
				"text" => $response
			];
		}

		return $channel_response;
	}

	private function solve($request, $response) {
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

		return $channel_response;
	}

	private function note($request, $response) {
		$channel_response = ['text' => 'System Error.'];
		$channel_id       = $request->channel_id;
		$body             = trim($request->text);
		$slack_user_id    = $request->user_id;

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
					"text"          => "There are no notes attached to *".$puzzle->getTitle()."*.",
					"response_type" => "in_channel",
				];
			} else {
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
			}

		} elseif (!$puzzle) {
			$channel_response = [
				"text" => "`".$request->command."` can only be used inside a puzzle channel.",
			];
		} else {
			$puzzle->note($body, $member);
			$channel_response = [
				"text"          => "Got it. I posted your note to *".$puzzle->getTitle()."*.",
				"response_type" => "in_channel",
			];
		}

		return $channel_response;
	}

	private function tagged($request, $response) {
		$channel_response = ['text' => 'Sorry, there is a problem.'];
		$channel_id       = $request->channel_id;

		$tag = TagQuery::create()
			->filterBySlackChannelId($channel_id)
			->findOne();

		// If there's no body, send back all puzzles with this tag.
		if ($tag) {
			$puzzleQuery = PuzzleQuery::create()
				->filterByStatus('solved', Criteria::NOT_EQUAL)
				->joinWithTagAlert()
				->where('TagAlert.tag_id = '.$tag->getId());

			$puzzleCount = $puzzleQuery->count();

			if ($puzzleCount == 0) {
				$channel_response = [
					"text"          => "There are no unsolved puzzles tagged with *".$tag->getTitle()."*.",
					"response_type" => "in_channel",
				];
			} else {
				$all_puzzles = array_map(function ($puzzle) {
						return $puzzle->getSlackAttachmentSmall();
					}, iterator_to_array($puzzleQuery->find()));

				$channel_response = [
					'link_names'    => true,
					'text'          => $puzzleCount." puzzles tagged `".strtoupper($tag->getTitle())."`",
					"response_type" => "in_channel",
					"attachments"   => $all_puzzles,
				];
			}
		} else {
			$channel_response = [
				"text" => "`".$request->command."` can only be used inside a tag channel.",
			];
		}

		return $channel_response;
	}
}
