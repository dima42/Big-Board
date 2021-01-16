<?php
require_once "globals.php";

function refreshPuzzles($orderBy = 'Title', $orderHow = 'asc') {


        Global $cache;
        while (true) {
            try {

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

                    if ($cache->existsNoOlderThan($puzzle->getSpreadsheetID(), $modified_max_age)) {
                        continue;
                    }

                    error_log("updating: ".$puzzle->getTitle());
                    $puzzle->getProperties();
                }
                error_log('worker loop complete');
            }
            catch (Exception $e) {
                error_log("caught: ".$e->getMessage());
            }
            sleep(1);
        }
}

refreshPuzzles();
