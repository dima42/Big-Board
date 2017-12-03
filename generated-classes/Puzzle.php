<?php
use Base\Puzzle as BasePuzzle;

/**
 * Skeleton subclass for representing a row from the 'puzzle' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */

class Puzzle extends BasePuzzle {
	// public function postUpdate() {
	// return true;
	// }

	public function postInfoToSlack() {
		postPuzzle($this, $this->getSlackChannel());
		postPuzzle($this);// big-board channel
	}

	public function postSolve() {
		postSolve($this, $this->getSlackChannel());
		// postSolve($this, 'general');
		// TODO: uncomment above
	}
}
