<?php
require_once "globals.php";

function refreshSomePuzzle($orderBy = 'Title', $orderHow = 'asc') {


        Global $cache;
        $puzzles = PuzzleQuery::create()
                ->orderBy($orderBy, $orderHow)
                ->find();

        $skipped = [];
        $updated = [];
        foreach ($puzzles as $puzzle){
            if ($puzzle->getStatus() == 'solved') {
                continue;
            }

            $max_age = getenv("MAX_CACHE_AGE")/10; // more aggressive than for end users
            $offset = rand(0, $max_age/2);
            $modified_max_age = $max_age-$offset;

            if ($cache->existsNoOlderThan($puzzle->getSpreadsheetID() . " sheet data", $modified_max_age)) {
                array_push($skipped, $puzzle->getSpreadsheetID());
                continue;
            }

            $puzzle->getProperties($modified_max_age);
            array_push($updated, $puzzle->getSpreadsheetID());
        }
        //error_log("skipped " . count($skipped) . ", updated " . count($updated) . "puzzles");
        usleep(random_int(1000*1000, 5*1000*1000));
}


refreshSomePuzzle();
