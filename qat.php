<?php
require_once "globals.php";

$query   = $argv[1];
$channel = $argv[2];

$encoded_query = rawurlencode($query);

// Build the request URL and get the response from Qat.
$request_url  = "https://www.quinapalus.com/cgi-bin/qat?pat={$encoded_query}";
$qat_response = file_get_contents($request_url);

// The response from Qat holds the results in td tags.
$regex_query = "/<tr><td>&nbsp;(.+)&nbsp;<\/td><\/tr>/";

$patterns         = ['/&nbsp;<\/td><td>&nbsp;/', '/&middot;/'];
$replace          = ['  /  ', ' '];
$cleaned_response = preg_replace($patterns, $replace, $qat_response);

preg_match_all($regex_query, $cleaned_response, $regex_results, PREG_SET_ORDER);

$pretext     = "<{$request_url}|No results> for `{$query}`.";
$attachments = [];

if (count($regex_results) > 0) {
	$pretext = "First 20 <{$request_url}|Qat results> for `{$query}`:\n";

	// Compile the results and add them to the string in a code block.
	$response_text = "```\n";
	foreach (array_slice($regex_results, 0, 20) as $regex_result) {
		$response_text .= "{$regex_result[1]}\n";
	}
	$response_text = substr($response_text, 0, -1);
	$response_text .= "```";

	// Send the response back to the channel!
	$attachments = [
		[
			"text"      => $response_text,
			"mrkdwn_in" => ['text'],
		]
	];
}

$r = postToChannel($pretext, $attachments, ":cat:", "Qat bot", "sandbox");
