<?
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Propel\Runtime\ActiveQuery\Criteria;

function createNewSlackChannel($slug) {
	$slack_key = getenv('PALINDROME_SLACK_KEY');

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/channels.create?token=".$slack_key."&name=".$slug);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}

// TODO: change default channel big-board
function postToChannel($message, $attachments = [], $channel = "sandbox", $icon = ":boar:", $bot_name = "Big Board Bot") {
	$slack_key = getenv('PALINDROME_SLACK_KEY');

	$interactor = new CurlInteractor;
	$interactor->setResponseFactory(new SlackResponseFactory);
	$commander = new Commander($slack_key, $interactor);

	preprint("a");
	preprint(json_encode($attachments));
	preprint("b");

	$response = $commander->execute('chat.postMessage', [
			'no_format'   => true,
			'channel'     => '#'.$channel,
			'icon_emoji'  => $icon,
			'username'    => $bot_name,
			'text'        => $message,
			'attachments' => json_encode($attachments),
			'link_names'  => 1,
		]);

	return $response;
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

		if ($request->token == getenv('PALINDROME_SLACKBOT_TOKEN') && $user_id) {
			if (!$member) {
				$payload = ["text" => "Hi there! Before you can use `".$request->command."`, I need to know who you are. Click this link then try the command again:
http://team-palindrome.herokuapp.com/assign_slack_id/".$user_id];
			} else {
				$payload = call_user_func_array(array($this, $name), $args);
			}
		}

		return $response->json($payload);
	}

	private function board($request, $response) {
		$parameter        = $request->text;
		$puzzleQuery      = PuzzleQuery::create();
		$channel_response = ['text' => 'I got nothing, sorry. Try again.'];

		if ($parameter == "") {
			$puzzleQuery->filterByStatus('solved', Criteria::NOT_EQUAL);
			$count   = $puzzleQuery->count();
			$pretext = $count." puzzles are unsolved:";
		} elseif (in_array($parameter, ['open', 'priority', 'urgent', 'solved'])) {
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
			$text   = "*".$puzzle->getTitle()."* is ".emojify($puzzle->getStatus())." ".strtoupper($status);
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
}
