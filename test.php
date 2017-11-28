<?php
session_start();
require_once 'globals.php';
require_once 'vendor/autoload.php';
require_once 'generated-conf/config.php';

$available_metas = PuzzleQuery::create()
	->joinWith('Puzzle.PuzzleChild')
	->withColumn('Sum(PuzzleChild.Id = 7)', 'IsInMeta')
	->groupBy('Puzzle.Id')
	->find();

preprint($available_metas);

echo "<pre>";
foreach ($available_metas as $puzzle) {
	// print_r($meta);
	echo $puzzle->getParent();
	// echo " ";
	// echo $puzzle->getParent()->getTitle();
	// echo " ";
	// echo $puzzle->getChild()->getTitle();
	// echo "<br>";
}
echo "</pre>";
