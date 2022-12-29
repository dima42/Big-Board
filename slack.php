<?php
use Propel\Runtime\ActiveQuery\Criteria;
require_once("globals.php");

function getSlackClient($slack_key_env_var) {
	$slack_key = getenv($slack_key_env_var);
	return new wrapi\slack\slack($slack_key);
}

function getTobyBotClient() {
	return getSlackClient('TOBYBOT_SLACK_KEY');
}

function getSlackChannelID($slug) {
	$client = getTobyBotClient();

        $next_cursor = NULL;

        while (true) {

    	    $response_body = $client->conversations->list([
                "cursor" => $next_cursor
            ]);


	    foreach ($response_body['channels'] as $key => $channel) {
		if ($channel['name'] == $slug) {
			return $channel['id'];
		}
	    }

            $next_cursor = $response_body["response_metadata"]["next_cursor"];
	    if ($next_cursor == "") {
                return 0;
            }
        }
}

function createNewSlackChannel($slug) {
	$client = getTobyBotClient();

	$slack_response = $client->conversations->create([
			'name' => $slug
		]);

    error_log(serialize($slack_response));

    $id = $slack_response["channel"]["id"];

    $client->conversations->leave([
                    'channel' => $id
            ]);

	return $id;
}

function archiveSlackChannel($channel_id) {
	$client = getTobyBotClient();
        $client->conversations->archive([
                   'channel' => $channel_id
        ]);
}

function inviteToSlackChannel($channel_id, $member_id) {
	$client = getTobyBotClient();

	$response = $client->conversations->invite([
			'channel' => $channel_id,
			'user'    => $member_id,
		]);

	return $response->getBody();
}

function postToHuntChannel($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot") {
	$channel = getenv('SLACK_HUNT_CHANNEL');
	return postToChannel($message, $attachments, $icon, $bot_name, $channel);
}

function postToBigBoard($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot") {
	$channel = "big-board";
	return postToChannel($message, $attachments, $icon, $bot_name, $channel);
}

function postToSlack($message, $attachments = [], $icon = ":boar:", $bot_name = "Big Board Bot", $channel = "big-board") {
	$client = getTobyBotClient();

	$response = $client->chat->postMessage([
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
	$client      = getTobyBotClient();
    $response_body = $client->users->info([
			'user' => $member->getSlackId()
		]);

	if ($response_body['ok'] == 1) {
		// Avatar options: image_24, 32, 48, 72, 192, 512, 1024
		$avatar = $response_body['user']['profile']['image_192'];
		$member->setAvatar($avatar);
		$member->save();
	}
	return $response_body;
}

// SLACK BOT

function getTobyBotInstructions() {
	$instructions = [
		"`/info` returns all links on this puzzle.",
		"`/solve [text]` sets [text] as the solution to the puzzle.",
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
		} elseif (in_array($parameter, ['open', 'stuck', 'priority', 'solved', 'lowpriority'])) {
			$puzzleQuery->filterByStatus($parameter);
			$count   = $puzzleQuery->count();
			$pretext = $count." puzzles marked `".strtoupper($parameter)."`:";
		} elseif ($parameter == "ange") {
                        $puzzleQuery->filterByStatus(['open', 'stuck', 'priority'], Criteria::IN);
                        $count   = $puzzleQuery->count();
                        $pretext = $count." puzzles marked open/stuck/priority: ";
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

	private function solve($request, $response) {
                global $shared_drive;
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
			$puzzle->solve($solution, $shared_drive);
			$channel_response = [
				'link_names' => true,
				"text"       => "Got it. I posted `".$solution."` as a solution to *".$puzzle->getTitle()."*.",
			];
		}

		return $channel_response;
	}

}
