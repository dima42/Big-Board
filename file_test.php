<?php
require_once "sitevars.php";
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

session_start();

// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$pal_client = new Google_Client();
$pal_client ->setAccessType("offline");
$pal_client ->setApplicationName("Palindrome Big Board");
$pal_client ->setClientId('938479797888.apps.googleusercontent.com');
$pal_client ->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
$pal_client ->setRedirectUri('http://palindrome.spandexters.com');
//$client->setDeveloperKey('insert_your_developer_key');
$pal_plus = new Google_PlusService($pal_client);
$pal_drive = new Google_DriveService($pal_client);

$hunt_folder = new Google_DriveFile();
$paramters = array();
try {
	$hunt_folder = $pal_drive->files->get("0BwQVTWNxkZQaam9JbE8yMHR3ak0");
} catch (Exception $e) {
	print "Oops!<br/>".$e->getMessage();
}

$result = $hunt_folder->createdDate;
//$result =  $hunt_folder->getItems();

print "Who used the folder last".$result;

?>
