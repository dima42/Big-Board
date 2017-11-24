<?php
use Cocur\Slugify\Slugify;

function convertToSlackChannel($fodder) {
    $slugify = new Slugify();
    $slug = $slugify->slugify($fodder);
	return substr($slug, 0, 21);
    // strtolower(substr(preg_replace("/[^a-zA-Z0-9]+/", "", $fodder),0,21));
};

function createNewSlackChannel($title) {
    $drawkwards_token = "xoxp-115681477587-116829918517-116066430608-c8e7080af7cb9da9893453c37a8e7e25";
	$slack_channel_title = convertToSlackChannel($title);
	$curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, "https://slack.com/api/channels.create?token=" . $drawkwards_token . "&name=" . $slack_channel_title);
    $result = curl_exec ($curl);
	curl_close ($curl);

	print $drawkwards_token;
	print $result;

    return $slack_channel_title;
}


?>
