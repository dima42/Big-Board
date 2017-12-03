<?

function createNewSlackChannel($slug) {
	$slack_key = getenv('PALINDROME_SLACK_KEY');

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/channels.create?token=".$slack_key."&name=".$slug);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}

function getSlackClient($icon = ":boar:", $username = "Big Board Bot") {
	$settings = [
		'username'                => $username,
		'icon'                    => $icon,
		'link_names'              => true,
		'markdown_in_attachments' => array('text'),
	];

	$client = new Maknz\Slack\Client('https://hooks.slack.com/services/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', $settings);
	return $client;
}

function postToChannel($message, $channel = "sandbox") {
	$client = getSlackClient();
	$client->to('#'.$channel)->send($message);
}

function getPuzzleAttachments($puzzle) {
	$puzzle_info = [
		':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle ->getId().'|Big Board>',
		':page_facing_up: <'.$puzzle                                   ->getUrl().'|MIT puzzle page>',
		':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Google Spreadsheet>',
		':slack: <#'.$puzzle                                           ->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>'
	];

	return array_map(function ($msg) {
			return [
				'text'  => $msg,
				'color' => 'good',
			];
		}, $puzzle_info);
}

function postPuzzle($puzzle, $channel = "big-board") {
	$client = getSlackClient();

	$message = $client->createMessage();
	$message->setText('*'.$puzzle->getTitle().'*');
	$message->setAttachments(getPuzzleAttachments($puzzle));
	$message->setChannel('#'.$channel);
	$message->send();
}

function postJoin($member, $channel = "sandbox") {
	$client = getSlackClient(":wave:", "JoinBot");
	$client->to($channel)->attach([
			'text'  => $member->getFullName(),
			'color' => 'good',
		])->send('New member!');
}

function postSolve($puzzle, $channel = "big-board") {
	$content = ':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|Big Board> '.
	':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Spreadsheet> '.
	':slack: <#'.$puzzle->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>';

	$client = getSlackClient(":checkered_flag:", "SolveBot");
	$client->to($channel)->attach([
			'text'  => $content,
			'color' => '#000000',
		])->send('*'.$puzzle->getTitle().'* is solved: `'.$puzzle->getSolution().'`');
}
