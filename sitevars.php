<?php
session_start();
require_once 'vendor/autoload.php';
require_once "sql.php";

$DEBUG = false;

if ($_SERVER['HTTP_HOST'] == "localhost:8888") {
    $DEBUG = true;
}

$loader = new Twig_Loader_Filesystem('templates');

Global $twig;
$twig = new Twig_Environment($loader, array(
));

$emojify = new Twig_Filter('emojify', function ($status) {
    switch ($status) {
        case "solved":
            return "✅";
        case "open":
            return "🤔";
    }
    return "";
});
$twig->addFilter($emojify);

function render($template, $vars = array()) {
    $news = "Type over this text to send out a message.";
    $news_from = "";

    $query = "select a.pal_upd_txt as NEWS, b.pal_usr_nme as WHO from pal_upd_tbl a, pal_usr_tbl b " .
                "where a.pal_upd_code = 'URG' and a.usr_id = b.pal_id " .
                "order by a.row_id DESC " .
                "limit 1";
    $latest_updates = getData($query);
    $row = $latest_updates->fetch_assoc();
    $news = $row["NEWS"];
    $news_from = $row["WHO"];

    Global $twig;
    $vars['user_id'] = $_SESSION['user_id'];
    $vars['time'] = strftime('%c');
    $vars['news'] = $news;
    $vars['news_from'] = $news_from;

    $query = "SELECT a.puz_id as MID, a.puz_ttl as MTTL FROM puz_tbl a, puz_rel_tbl b WHERE a.puz_id = b.puz_par_id GROUP BY a.puz_id, a.puz_ttl";
    $vars['metas'] = getData($query);

    if (in_array("error_string", $_SESSION)) {
        $vars['error'] = $_SESSION['error_string'];
    }
    echo $twig->render($template, $vars);
}

function connectToDB() {
    $url = parse_url(getenv("PALINDROME_DATABASE_URL"));
    $server = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $db = substr($url["path"], 1);

    $link = mysqli_connect(
       $server,
       $username,
       $password,
       $db
    );
    if (!$link) {
        writeHeader('Could not select database');
    }
    return $link;
}
?>
