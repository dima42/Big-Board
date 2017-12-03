<?php
require_once "globals.php";
require_once "controller.php";
require_once "sql.php";
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

$klein = new \Klein\Klein();

$klein->with('/api', function () use ($klein) {
		$klein->respond('POST', '/board', function ($request, $response) {
				return bigBoardBot($request, $response);
			});

		$klein->respond('POST', '/solve', function ($request, $response) {
				return solveBot($request, $response);
			});

		$klein->respond('POST', '/info', function ($request, $response) {
				return infoBot($request, $response);
			});

		$klein->respond('POST', '/join', function ($request, $response) {
				return joinBot($request, $response);
			});

	});

$klein->respond(function ($request, $response) {
    // SET UP GOOGLE_CLIENT OBJECT
    $pal_client = new Google_Client();
    $pal_client->setAccessType("offline");
    $pal_client->setApplicationName("Palindrome Big Board");
    $pal_client->setClientId('938479797888.apps.googleusercontent.com');
    $pal_client->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
    $pal_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST']);

    // SET UP DRIVE SERVICE OBJECT

	if (!is_authorized($pal_client)) {
		$authUrl = $pal_client->createAuthUrl();
		render('loggedout.twig', array(
				'auth_url' => $authUrl,
			));
	} else {
		show_page($pal_client);
	}
});

$klein->dispatch();

function is_authorized($pal_client) {
	// let's get the persons access token for future use. This is where the login takes place.
	if (isset($_GET['code'])) {
		$pal_client->authenticate($_GET['code']);
		$_SESSION['access_token'] = $pal_client->getAccessToken();
		setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
		header('Location: /');

		$token_dump                = json_decode($_SESSION['access_token']);
		$_SESSION['refresh_token'] = $token_dump->{'refresh_token'};
		setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());

		return true;
	}

	// If no access_token in session, check the cookies
	if (!isset($_SESSION['access_token']) && isset($_COOKIE['PAL_ACCESS_TOKEN'])) {
		$_SESSION['access_token'] = stripslashes($_COOKIE['PAL_ACCESS_TOKEN']);
	}

    // Now check for access_token in the SESSION
	if (isset($_SESSION['access_token'])) {
		$pal_client->setAccessToken($_SESSION['access_token']);
		if (!$pal_client->isAccessTokenExpired()) {
			return true;
		}
	}

    // If no access_token in SESSION, check cookies for refresh_token, and refresh
	if (isset($_COOKIE['refresh_token'])) {
		$pal_client->refreshToken($_COOKIE['refresh_token']);
		$_SESSION['access_token'] = $pal_client->getAccessToken();
		setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());

		$token_dump                = json_decode($_SESSION['access_token']);
		$_SESSION['refresh_token'] = $token_dump->{'refresh_token'};
		setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());

		return true;
	}

    // If none of that worked, no access granted
	return false;
}

function show_page($pal_client) {
    $pal_drive = new Google_DriveService($pal_client);

	// first, let's try to get the user from the database based on root folder ID

	// this will get the user ID of someone already established as a palindrome member
	$aboutg  = $pal_drive->about->get();
	$my_name = $aboutg["user"]["displayName"];
	$my_root = $aboutg["rootFolderId"];

	$member = MemberQuery::create()
		->filterByGoogleID($my_root)
		->findOne();

	if ($member) {
		$_SESSION['user']    = $member;
		$_SESSION['user_id'] = $member->getID();
	} else {
		$_SESSION["user_id"] = 0;
	}

	// we should always check to see if they have access
	// check to see if they have write access to the palindrome folder
	//let's check to see if the user has access
	$isUserInPalindrome = FALSE;

	// Find the current Mystery Hunt folder.
	$hunt_folder = new Google_DriveFile();
	try {
		$hunt_folder = $pal_drive->files->get("0B5NGrtZ8ORMrYzY0MzFjYWEtZDRkZC00ZDNhLTg2N2YtZDljM2FiNmJhMjg5");
		if ($hunt_folder["userPermission"]["id"] == "me") {
			$isUserInPalindrome = TRUE;
		} else {
			$isUserInPalindrome = FALSE;
		}
	} catch (Exception $e) {
		return displayError($e->getMessage());
	}

	// if they do have access, let's take root and name from before and create a user
	if ($isUserInPalindrome && $_SESSION["user_id"] == 0) {
		$_SESSION["user_id"] = createUserDriveID($my_root, $my_name);
	}

	if ($_SESSION["user_id"] == 0) {
		// if someone is not a member of palindrome, let's tell them to bugger off
		return render('buggeroff.twig');
	}

	show_content();
}
