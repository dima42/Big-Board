<?php
require_once 'sitevars.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';

function get_new_drive_service() {
    $pal_client = new Google_Client();
    $pal_client ->setAccessType("offline");
    $pal_client ->setApplicationName("Palindrome Big Board");
    $pal_client ->setClientId('938479797888.apps.googleusercontent.com');
    $pal_client ->setClientSecret('TOi6cB4Ao_N0iLnIbYj-Aeij');
    $pal_client ->setRedirectUri('http://palindrome.spandexters.com');
    $access_token = $_COOKIE['PAL_ACCESS_TOKEN'];
    if ($access_token == "") {
        $_SESSION['error_string'] .= "You must have cookies active.";
        return;
    } else {
        $pal_client->setAccessToken(stripslashes($access_token));
        if ($pal_client->isAccessTokenExpired()) {
            $_SESSION['error_string'] .= "This token is no good";
        }
	  return new Google_DriveService($pal_client);
  }
}

function create_file_from_template($title) {
    $service = get_new_drive_service();
    $file = new Google_DriveFile();
    $file->setTitle($title);
    // 1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4 is ID of our Template file, which is inside Mystery Hunt 2018/All Puzzles.
    $copy = $service->files->copy('1nXyGRx_EJTXeK7_dpewFnRjzL6eiM7prC6-T02cdMu4', $file);

    return $copy['id'];
}

// Call this to find the folder we are using to store puzzle spreadsheets.
// When setting up an upcoming Mystery Hunt folder, create an All Puzzle folders.
// function getCurrentParentFolder() {
//     return  "1FI0ClFnc1bU0H8hCDVZOWGz3H02Kv773"; // current "Mystery Hunt 2018/All Puzzles" folder
// }

// function create_new_file($title) {
//     $service = get_new_drive_service();
//     $file = new Google_DriveFile();
//     $file->setTitle($title);
//     if (in_array("error_string", $_SESSION)) {
//         $file->setDescription($_SESSION['error_string']);
//     }
//     $file->setMimeType("application/vnd.google-apps.spreadsheet");

//     // Set the parent folder.
//     $parents = new Google_ParentReference();
//     $parents->setId(getCurrentParentFolder());
//     $file->setParents(array($parents));
//     try {
//         $brandNewFiel = new Google_DriveFile();
//         $brandNewFiel = $service->files->insert($file, array(
//             'data' => "",
//             'mimeType' => "application/vnd.google-apps.spreadsheet",
//         ));
//     	return $brandNewFiel["id"];
//     } catch (Exception $e) {
//         return "";
//     }
// }

function getDrivesFiles() {
	$myDriveService = get_new_drive_service();

	// this gives us all available files to the user and shows when they were last updated.
	$current_puzzles = array();
	$all_files = $myDriveService->files->listFiles();
	foreach($all_files["items"] as $k => $v) {
		$current_puzzles[$v["id"]][0] = $v["lastModifyingUserName"];
		$current_puzzles[$v["id"]][1] = $v["modifiedDate"];
	}
	return $current_puzzles;
}

function sessionTest() {
	return "Session test is ". $_SESSION['access_token'];
}

?>
