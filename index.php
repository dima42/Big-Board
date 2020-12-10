<?php
require_once "globals.php";

$klein = new \Klein\Klein();

$klein->with('/tobybot', function () use ($klein) {
		$bot = new Bot();

		$klein->respond('POST', $request->command??'', function ($request, $response) use ($bot) {
				return $bot->execute($request, $response);
			});
	});

$klein->respond('GET', '/oauth', function ($request, $response) use ($klein, $pal_client) {
		// If 'picked' is set in the request, the user has granted access to the
		// puzzle folder, so finalize the credentials and continue.
		if (isset($_GET['picked'])) {
                        $_SESSION['access_token'] = $_SESSION['temporary_access_token'];
			setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
			return redirect("/");
		}
                error_log("hi");

		// If 'code' is set in the request, that's Google trying to authenticate.
		// Extract the access token and continue to the file picker.
		if (isset($_GET['code'])) {
			debug("OAUTH. Code: ".$_GET['code']);
                        error_log("OAUTH. Code: ".$_GET['code']);
			$token = $pal_client->authenticate($_GET['code']);
                        error_log(json_encode($token));
			$_SESSION['temporary_access_token'] = json_encode($token);
		}
		// Once we have an access token, show the file picker to get access to the
		// puzzle folder.
		if (isset($_SESSION['temporary_access_token'])) {
                        $token_dump = json_decode($_SESSION['temporary_access_token']);
                        var_dump($token_dump);
			render('picker.twig', 'picker', array(
				'access_token' => $token_dump->{'access_token'},
				'app_id' => getenv('GOOGLE_APP_ID'),
				'developer_key' => getenv('GOOGLE_DEVELOPER_KEY'),
				'puzzles_folder_id' => getenv('GOOGLE_DRIVE_PUZZLES_FOLDER_ID'),
			));
			$klein->skipRemaining();
			return;
		}

		return redirect("/");
	});

$klein->respond('GET', '/privacy', function ($request, $response) {
		return render('privacy.twig', 'privacy');
	});

// If user not authorized or not in palindrome do not allow them to get matched to any remaining routes
$klein->respond(function () use ($klein, $pal_client, $pal_drive) {
		debug('');// Add a break to the output to help with debugging
		if (!is_authorized($pal_client, $pal_drive)) {
			$authUrl = $pal_client->createAuthUrl();
			render('loggedout.twig', 'loggedout', array(
					'auth_url' => $authUrl,
				));
			$klein->skipRemaining();
		}

		if (!is_in_palindrome($pal_drive)) {
			render('buggeroff.twig', 'buggeroff');
			$klein->skipRemaining();
		}

	});

$klein->with('', 'controller.php');

$klein->dispatch();

function is_authorized($pal_client, $pal_drive) {
	// If no access_token in session, check the cookies and fill it
	if (!isset($_SESSION['access_token']) && isset($_COOKIE['PAL_ACCESS_TOKEN'])) {
		debug("No access_token IN SESSION, checking cookies");
		$_SESSION['access_token'] = stripslashes($_COOKIE['PAL_ACCESS_TOKEN']);
	}

	// Now check for access_token in the SESSION
	if (isset($_SESSION['access_token'])) {
                $token_dump = json_decode($_SESSION['access_token']);
                debug("Found access_token in SESSION: ".$_SESSION['access_token']);
		$pal_client->setAccessToken($_SESSION['access_token']);
		if (!$pal_client->isAccessTokenExpired()) {
			return true;
		} else {
		    debug("This token is no good");

		    $pal_client->refreshToken($token_dump->{'refresh_token'});
		    
                    $_SESSION['access_token']  = json_encode($pal_client->getAccessToken());
		    $token_dump                = json_decode($_SESSION['access_token']);
		    $_SESSION['refresh_token'] = $token_dump->{'refresh_token'};
                    
		    setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
		    return !$pal_client->isAccessTokenExpired();
                }
	}

	return false;
}

function is_in_palindrome($pal_drive) {
	// If 'user' is set in SESSION and 'user' is a Member, then we're good.
	if (isset($_SESSION['user']) && is_a($_SESSION['user'], 'Member') > 0) {
		debug('Found member in SESSION: '.$_SESSION['user']->getFullName());
		return true;
	}

	debug("Member not found in SESSION");
	$drive_user     = $pal_drive->about->get(array('fields' => '*'));
	$user_full_name = $drive_user["user"]["displayName"];
	debug("user name: ". $user_full_name);

	// If there's a member whose googleID matches the current user's permissionId, then we're good.
	$user_google_id = $drive_user["user"]["permissionId"];

	debug("user id: ". $user_google_id);

	$member = MemberQuery::create()
		->filterByFullName($user_full_name)
		->findOne();

	if ($member) {
		debug("Member exists. Google ID: ".$user_google_id);
		$_SESSION['user']    = $member;
		$_SESSION['user_id'] = $member->getID();
		return true;
	}

	// If it's a new user, make sure they have access to our drive
	$hunt_folder = new Google_Service_Drive_DriveFile();
	try {
		$hunt_folder = $pal_drive->files->get(getenv('GOOGLE_DRIVE_PUZZLES_FOLDER_ID'), array('fields' => 'capabilities'));
		debug("canAddChildren permissions: ".$hunt_folder['capabilities']->canAddChildren);
		if ($hunt_folder['capabilities']->canAddChildren) {
			$member = new Member();
			$member->setFullName($user_full_name);
			$member->setGoogleId($user_google_id);
			$member->setGoogleRefresh($_SESSION['refresh_token']);
			$member->save();
			$_SESSION['user']    = $member;
			$_SESSION['user_id'] = $member->getId();
			return true;
		}
	} catch (Exception $e) {
		debug($e->getMessage());
	}

	// If none of that worked, they're not on the team
	return false;
}
