<?php
require_once "sitevars.php";
require_once "new_file_management.php";
require_once "html.php";
require_once "sql.php";
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

Global $link;
$link = connectToDB();

// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.

$pal_client = new Google_Client();
$pal_client ->setAccessType("offline");
$pal_client ->setApplicationName("Palindrome Big Board");
$pal_client ->setClientId('938479797888.apps.googleusercontent.com');
$pal_client ->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
$pal_client ->setRedirectUri('http://' . $_SERVER['HTTP_HOST']);

$pal_drive = new Google_DriveService($pal_client);

$noAccessYet=TRUE;

// user is logging out
if (isset($_REQUEST['logout'])) {unset($_SESSION['access_token']);session_destroy();}

// let's get the persons access token for future use. This is automatica only the login takes place.
if (isset($_GET['code'])) {
    $pal_client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $pal_client->getAccessToken();
    setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());
    header('Location: http://palindrome.spandexters.com/');

    $token_dump = json_decode($_SESSION['access_token']);
    $_SESSION['refresh_token'] = $token_dump->{'refresh_token'};
    setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());

    $noAccessYet=FALSE;
}

// let's check to see if we have an access token. If we do, then we can get all sorts of fun information
// if we do not have a session token, check the cookies
if (!isset($_SESSION['access_token']) && isset($_COOKIE['PAL_ACCESS_TOKEN'])) { $_SESSION['access_token'] = stripslashes($_COOKIE['PAL_ACCESS_TOKEN']); }

if (isset($_SESSION['access_token'])) {
    $pal_client->setAccessToken($_SESSION['access_token']);
    if (!$pal_client->isAccessTokenExpired()) {
        $noAccessYet=FALSE;
    }
}

if ($noAccessYet) {
    if (isset($_COOKIE['refresh_token'])) {
        $pal_client->refreshToken($_COOKIE['refresh_token']);
        $_SESSION['access_token'] = $pal_client->getAccessToken();
        setcookie("PAL_ACCESS_TOKEN", $_SESSION['access_token'], 5184000+time());

        $token_dump = json_decode($_SESSION['access_token']);
        $_SESSION['refresh_token'] = $token_dump->{'refresh_token'};
        setcookie("refresh_token", $_SESSION['refresh_token'], 5184000+time());

        $noAccessYet=FALSE;
    }
}

// next, if there is no access token, user has not authorized app. So let's begin by checking that.
if ($noAccessYet) {
    writeHeader(FALSE);
    $authUrl = $pal_client->createAuthUrl();
    print "<p><a class='login' href='$authUrl'>Connect Me!</a>. Only Palindrome members may access this big board.</p>";
} else {
    // first, let's try to get the user from the database based on root folder ID

    // this will get the user ID of someone already established as a palindrome member
    $aboutg = $pal_drive->about->get();
    $my_name = $aboutg["user"]["displayName"];
    $my_root = $aboutg["rootFolderId"];
    $_SESSION["user_id"] = getUserDriveID($my_root, $my_name);

    writeHeader(TRUE);

    //if ($_SESSION["user_id"] == 0) {
        // we should always check to see if they have access
        // check to see if they have write access to the palindrome folder
          //let's check to see if the user has access
          $isUserInPalindrome = FALSE;
          // we need to find the current Mystery Hunt folder.
          $hunt_folder = new Google_DriveFile();
          try {
              $hunt_folder = $pal_drive->files->get("0B5NGrtZ8ORMrYzY0MzFjYWEtZDRkZC00ZDNhLTg2N2YtZDljM2FiNmJhMjg5");
              if ($hunt_folder["userPermission"]["id"] == "me") {
                  $isUserInPalindrome = TRUE;
              } else {
                  $isUserInPalindrome = FALSE;
              }
          } catch (Exception $e) {
              print "<P>An error has occured. <!--".$e->getMessage()."-->";
              $isUserInPalindrome = FALSE;
          }
      // if they do have access, let's take root and name from before and create a user
      if ($isUserInPalindrome && $_SESSION["user_id"] == 0) { $_SESSION["user_id"] = createUserDriveID($my_root, $my_name); }
    //}

    if ($_SESSION["user_id"] != 0) {
      $my_puzzle_list = getCurrentPuzzle($_SESSION["user_id"]);
      writeIntro();
      if (isset($_GET['meta'])) {
          displayMeta($my_puzzle_list,$_GET['meta']);
      } else if (isset($_GET['updates'])) {
          displayUpdates($_GET['updates']);
      } else if (isset($_GET['bylastmod'])) {
          displayAbandonedPuzzles();
      } else if (isset($_GET['puzzle'])) {
          if ($_GET['puzzle'] == 'F') {
              displayFeature($my_puzzle_list);
          } else {
              displayPuzzle($my_puzzle_list,$_GET['puzzle']);
          }
      } else {
          writeKey();
          displayPuzzles($my_puzzle_list);
          //writeKey();
          writeInstructions();
      }
    } else {
        // if someone is not a member of palindrome, let's tell them to bugger off
        displayWarning();
    }
}

writeFooter();

function getCurrentPuzzle($user_id) {
    // since we have removed the check out feature, we are commenting out most of this function

    //$results = getCurrentPuzzleSQL($user_id);
    // for now, we want an array of puzzles
    $my_puzzles = array();
    //while ($row=$results->fetch_array(MYSQLI_ASSOC)) {
        //$my_puzzles[$row['PUZID']]=$row['CHECKOUT'];
    //}

    return $my_puzzles;
}
?>
