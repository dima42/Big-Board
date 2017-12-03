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

function postPuzzle($puzzle, $channel = "sandbox") {
	$client = getSlackClient();

	$message = $client->createMessage();
	$message->setText('*'.$puzzle->getTitle().'*');
	$message->setAttachments(getPuzzleAttachments($puzzle));
	$message->setChannel('#'.$channel);
	$message->send();
}

function postSolve($puzzle, $channel = "sandbox") {
	$content = ':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|Big Board> '.
	':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Spreadsheet> '.
	':slack: <#'.$puzzle->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>';

	$client = getSlackClient(":checkered_flag:", "SolveBot");
	$client->to($channel)->attach([
			'text'  => $content,
			'color' => '#000000',
		])->send('*'.$puzzle->getTitle().'* is solved: `'.$puzzle->getSolution().'`');
}
