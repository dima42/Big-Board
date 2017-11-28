<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';

use Propel\Runtime\ActiveQuery\Criteria;

$my_metas = PuzzleParentQuery::create()
    ->joinWith('PuzzleParent.Parent')
    ->filterByPuzzleID(156)
    ->find();

// $query = "select a.puz_id as MID, a.puz_ttl as MTTL, sum(b.puz_id = " . $puzzle_id . ") as INMETA from puz_tbl a, puz_rel_tbl b where a.puz_id = b.puz_par_id group by a.puz_id, a.puz_ttl";
// $puzzle_metas = getData($query);

echo "<pre>";
foreach ($my_metas as $puzzle) {
    // print_r($meta);
    echo $puzzle->getParent();
    // echo " ";
    // echo $puzzle->getParent()->getTitle();
    // echo " ";
    // echo $puzzle->getChild()->getTitle();
    // echo "<br>";
}
echo "</pre>";
