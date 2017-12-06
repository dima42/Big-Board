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

// TODO: change default channel big-board
function postToChannel($message, $attachments, $channel = "sandbox", $icon = ":boar:", $bot_name = "Big Board Bot") {
	$client = getSlackClient($icon, $bot_name);
	$client->to($channel)->attach($attachments)->send($message);
}

// TODO: change default channel big-board
function postSolve($puzzle, $channel = "sandbox") {
	$content = ':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|Big Board> '.
	':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Spreadsheet> '.
	':slack: <#'.$puzzle->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>';

	$attachments = [
		'text'  => $content,
		'color' => '#000000',
	];

	postToChannel('*'.$puzzle->getTitle().'* is solved: `'.$puzzle->getSolution().'`', $attachments, $channel, ":checkered_flag:", "SolveBot");
}
