<?php

function createNewSlackChannel($slug) {
	$drawkwards_token = "xoxp-115681477587-116829918517-116066430608-c8e7080af7cb9da9893453c37a8e7e25";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://slack.com/api/channels.create?token=".$drawkwards_token."&name=".$slug);
	$result = curl_exec($curl);
	curl_close($curl);

	return $slug;
}
