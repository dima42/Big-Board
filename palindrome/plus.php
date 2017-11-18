<?php

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';

$pal_client = new Google_Client();
$pal_client ->setApplicationName("Palindrome Big Board");
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$pal_client ->setClientId('938479797888.apps.googleusercontent.com');
$pal_client ->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
$pal_client ->setRedirectUri('http://palindrome.spandexters.com');
//$client->setDeveloperKey('insert_your_developer_key');
$pal_plus = new Google_PlusService($pal_client);

?>