<?
use Maknz\Slack\Attachment;

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

function postPuzzle($puzzle, $channel = "big-board") {
	$client = getSlackClient();

	$messages = array(
		':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|Big Board>',
		':page_facing_up: <'.$puzzle->getUrl().'|MIT puzzle page>',
		':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Google Spreadsheet>',
		':slack: <#'.$puzzle->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>',
	);

	$message     = $client->createMessage();
	$attachments = array_map(function ($msg) use (&$message) {
			$message->attach(new Attachment([
						'text'  => $msg,
						'color' => 'good',
					]));
		}, $messages);
	$message->setText('*'.$puzzle->getTitle().'*');
	$message->setChannel('#'.$channel);
	$message->send();
}

function postSolve($puzzle, $channel = "big-board") {
	$content = ':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$puzzle->getId().'|Big Board> '.
	':drive: <https://docs.google.com/spreadsheet/ccc?key='.$puzzle->getSpreadsheetId().'|Spreadsheet> '.
	':slack: <#'.$puzzle->getSlackChannelId().'|'.$puzzle->getSlackChannel().'>';

	$client = getSlackClient(":checkered_flag:", "Solve Bot");
	$client->to($channel)->attach([
			'text'  => $content,
			'color' => '#000000',
		])->send('*'.$puzzle->getTitle().'* is solved: `'.$puzzle->getSolution().'`');
}
