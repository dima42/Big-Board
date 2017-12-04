<?php
require_once "globals.php";
require_once "controller.php";
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

// SET UP GOOGLE_CLIENT OBJECT
$pal_client = new Google_Client();
$pal_client->setAccessType("offline");
$pal_client->setApplicationName("Palindrome Big Board");
$pal_client->setClientId('938479797888.apps.googleusercontent.com');
// TODO put the following in a environment variable
$pal_client->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
$pal_client->setRedirectUri('http://'.$_SERVER['HTTP_HOST']."/oauth");

$pal_drive = new Google_DriveService($pal_client);

$klein->respond('GET', '/oauth', function ($request, $response) use ($pal_client) {
		// If 'code' is set in the request, that's Google trying to authenticate
		error_log("OAUTH. Code: ".$_GET['code']);
		if (isset($_GET['code'])) {
			$pal_client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $pal_client->getAccessToken();
			$token_dump = json_decode($_SESSION['access_token']);
			$_SESSION['refresh_token'] = $token_dump->{'refresh_token'};

			setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
			setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());
		}
		return redirect("/");
	});

$klein->respond(function () use ($pal_client, $pal_drive) {
		if (!is_authorized($pal_client)) {
			$authUrl = $pal_client->createAuthUrl();
			return render('loggedout.twig', array(
					'auth_url' => $authUrl,
				));
		}

		if (!is_in_palindrome($pal_drive)) {
			return render('buggeroff.twig');
		}

		return show_content();
	});

$klein->dispatch();

function is_authorized($pal_client) {
	// If no access_token in session, check the cookies
	if (!isset($_SESSION['access_token']) && isset($_COOKIE['PAL_ACCESS_TOKEN'])) {
		error_log("No access_token IN SESSION, checking cookies");
		$_SESSION['access_token'] = stripslashes($_COOKIE['PAL_ACCESS_TOKEN']);
	}

	// Now check for access_token in the SESSION
	if (isset($_SESSION['access_token'])) {
		error_log("access token in SESSION: ".$_SESSION['access_token']);
		$pal_client->setAccessToken($_SESSION['access_token']);
		if (!$pal_client->isAccessTokenExpired()) {
			return true;
		}
	}

	// If no access_token in SESSION, check cookies for refresh_token, and refresh
	if (isset($_COOKIE['refresh_token'])) {
		error_log("refresh token in SESSION: ".$_SESSION['refresh_token']);
		$pal_client->refreshToken($_COOKIE['refresh_token']);
		$_SESSION['access_token']  = $pal_client->getAccessToken();
		$token_dump                = json_decode($_SESSION['access_token']);
		$_SESSION['refresh_token'] = $token_dump->{'refresh_token'};

		setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
		setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());

		return true;
	}

	return false;
}

function is_in_palindrome($pal_drive) {
	// If 'user_id' is set in SESSION and 'user' is a Member, then we're good.
	if (($_SESSION["user_id"]??0) > 0) {
		error_log('user_id in SESSION: '.$_SESSION['user_id']);
		return true;
	}

	// If there's a member whose googleID matches the current user's rootFolderId, then we're good.
	$drive_user     = $pal_drive->about->get();
	$user_google_id = $drive_user["rootFolderId"];
	$user_full_name = $drive_user["user"]["displayName"];

	$member = MemberQuery::create()
		->filterByGoogleID($user_google_id)
		->findOne();

	if ($member) {
		error_log("Member exists. Google ID: ".$user_google_id);
		$_SESSION['user']    = $member;
		$_SESSION['user_id'] = $member->getID();
		return true;
	}

	// If it's a new user, make sure they have access to our drive
	$hunt_folder = new Google_DriveFile();
	try {
		$hunt_folder = $pal_drive->files->get("0B5NGrtZ8ORMrYzY0MzFjYWEtZDRkZC00ZDNhLTg2N2YtZDljM2FiNmJhMjg5");
		error_log("userPermission.id: ".$hunt_folder["userPermission"]["id"]);
		if ($hunt_folder["userPermission"]["id"] == "me") {
			// TODO: set up both user and user_id session vars
			$member = new Member();
			$member->setFullName($user_full_name);
			$member->setGoogleId($user_google_id);
			$member->setGoogleRefresh($_SESSION['refresh_token']);
			$member->save();
			$_SESSION["user"]    = $member;
			$_SESSION["user_id"] = $member->getId();
			return true;
		}
	} catch (Exception $e) {
		error_log($e->getMessage());
	}

	// If none of that worked, they're not on the team
	return false;
}
