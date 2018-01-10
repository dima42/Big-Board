<?php
require_once "globals.php";

$query   = $argv[1];
$channel = $argv[2];

// Slack uses smart quotes, and that breaks Nutrimatic.  Replace them...
// ...and encode the query for use in the request URL.
$encoded_query = rawurlencode(str_replace(["“", "”"], ["\"", "\""], $query));

// Build the request URL and get the response from Nutrimatic.
$request_url     = "https://nutrimatic.org/?q={$encoded_query}";
$nmatic_response = file_get_contents($request_url);

// The response from Nutrimatic holds the results in span tags.
$regex_query = "/<span style='font-size: .*em'>(.*)<\/span>/";
preg_match_all($regex_query, $nmatic_response, $regex_results, PREG_SET_ORDER);

$pretext     = "<{$request_url}|No results> for `{$query}`.";
$attachments = [];

if (count($regex_results) > 0) {
	$pretext = "<{$request_url}|Nutrimatic results> for `{$query}`:\n";

	// Compile the results and add them to the string in a code block.
	$response_text = "```\n";
	foreach ($regex_results as $regex_result) {
		$response_text .= "{$regex_result[1]}\n";
	}
	$response_text = substr($response_text, 0, -1);
	$response_text .= "```";

	$attachments = [
		[
			"text"      => $response_text,
			"mrkdwn_in" => ['text'],
		]
	];
}

$r = postToChannel($pretext, $attachments, ":tea:", "Nutrimatic bot", $channel);
