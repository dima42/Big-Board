<?php
require_once "globals.php";

function refreshSomePuzzle($orderBy = 'Title', $orderHow = 'asc') {


        Global $cache;
        $puzzles = PuzzleQuery::create()
                ->orderBy($orderBy, $orderHow)
                ->find();

        foreach ($puzzles as $puzzle){
            if ($puzzle->getStatus() == 'solved') {
                continue;
            }

            $max_age = getenv("MAX_CACHE_AGE")/10; // more aggressive than for end users
            $offset = rand(0, $max_age/2);
            $modified_max_age = $max_age-$offset;

            if ($cache->existsNoOlderThan($puzzle->getSpreadsheetID() . " sheet data", $modified_max_age)) {
                error_log("skipping updating: ".$puzzle->getTitle());
                continue;
            }

            error_log("updating: ".$puzzle->getTitle());
            $puzzle->getProperties($modified_max_age);
        }
        sleep(1);
}


refreshSomePuzzle();
