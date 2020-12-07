<?php
require_once "globals.php";

function refreshPuzzles($orderBy = 'Title', $orderHow = 'asc') {


        Global $cache;
        while (true) {

            $puzzles = PuzzleQuery::create()
                    ->orderBy($orderBy, $orderHow)
                    ->find();

            foreach ($puzzles as $puzzle){
                if ($puzzle->getStatus() == 'solved') {
                    continue;
                }

                $max_age = getenv("MAX_CACHE_AGE")/2;
                $offset = rand(0, $max_age/2);
                $modified_max_age = $max_age-$offset;
                
                if ($cache->existsNoOlderThan($puzzle->getSpreadsheetID(), $modified_max_age)) {
                    continue;
                }

                error_log("updating: ".$puzzle->getTitle());
                $puzzle->getProperties();
                sleep(10);
            }
            error_log('worker loop complete');
            sleep(10);
        }
}

refreshPuzzles();
