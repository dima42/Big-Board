<?php
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Propel\Runtime\ActiveQuery\Criteria;

function getSlackCommander() {
	$slack_key = getenv('TOBYBOT_SLACK_KEY');

	$interactor = new CurlInteractor;
	$interactor->setResponseFactory(new SlackResponseFactory);

	return new Commander($slack_key, $interactor);
}

function getAllSlackChannels() {
	$commander = getSlackCommander();

	return $commander->execute('channels.list', [
			'exclude_archived' => true
		])->getBody();
}

function getSlackChannelID($slug) {
	$commander = getSlackCommander();

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
	$commander = getSlackCommander();

	$slack_response = $commander->execute('channels.create', [
			'name' => $slug
		]);

	return getSlackChannelID($slug);
}

function inviteToSlackChannel($channel_id, $member_id) {
	$commander = getSlackCommander();

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
	$slack_key = getenv('BIGBOARDBOT_SLACK_KEY');

	$interactor = new CurlInteractor;
	$interactor->setResponseFactory(new SlackResponseFactory);
	$commander = new Commander($slack_key, $interactor);

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
	postToSlack($message, $attachments, $icon, $bot_name, '#'.$channel);
}

function scrapeAvatar($member) {
	$commander      = getSlackCommander();
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
}

// SLACK BOT

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
			$text   = "*".$puzzle->getTitle()."* is :".$puzzle->getStatus().": ".strtoupper($status);
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

	private function join($request, $response) {
		$parameter        = $request->text;
		$channel_response = ['text' => "Hmm, maybe ask a human for help. This computer is confused."];
		$channel_id       = $request->channel_id;
		$slack_user_id    = $request->user_id;

		if (!$channel_id) {
			return ['text' => "No channel specified."];
		}

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
		} else {
			$response         = $member->joinPuzzle($puzzle);
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
		} else {
			$puzzle->note($body, $member);
			$channel_response = [
				"text" => "Got it. I posted your note to *".$puzzle->getTitle()."*.",
			];
		}

		return $channel_response;
	}

	private function nmatic($request, $response) {
		$query = $request->text;

		// Slack uses smart quotes, and that breaks Nutrimatic.  Replace them...
		// ...and encode the query for use in the request URL.
		$encoded_query = rawurlencode(str_replace(["“", "”"], ["\"", "\""], $query));

		// Build the request URL and get the response from Nutrimatic.
		$request_url = "https://nutrimatic.org/?q={$encoded_query}";
		$response = file_get_contents($request_url);

		// The response from Nutrimatic holds the results in span tags.
		$regex_query = "/<span style='font-size: .*em'>(.*)<\/span>/";
		preg_match_all($regex_query, $response, $regex_results, PREG_SET_ORDER);

		// Handle the case where the query yields no results.
		if (count($regex_results) == 0) {
			return [
				"text" => "No results for `{$query}`.",
				"response_type" => "in_channel",
			];
		}

		// Compile the results into a string.
		$response_text = "Nutrimatic results for `{$query}`: ";
		foreach ($regex_results as $regex_result) {
			$response_text .= "{$regex_result[1]}, ";
		}
		$response_text = substr($response_text, 0, -2);

		// Send the response back to the channel!
		$channel_response = [
			"text" => $response_text,
			"response_type" => "in_channel",
		];

		return $channel_response;
	}
}
