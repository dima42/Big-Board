<?php

use Base\Member as BaseMember;

/**
 * Skeleton subclass for representing a row from the 'member' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */

class Member extends BaseMember {
	// GET

	public function getNameForSlack() {
		if ($this->getSlackID()) {
			return "<@".$this->getSlackID().">";
		} else {
			return $this->getFullName();
		}
	}

	public function getSlackURL() {
		return "http://".getenv('SLACK_DOMAIN').".slack.com/team/".$this->getSlackID();
	}

	// JOINING & LEAVING

	public function joinPuzzle($puzzle) {
		// Go thorugh all the puzzles I'm a member of, and leave them all (except don't leave this puzzle, if I'm already a member)
		$memberPuzzles = $this->getPuzzles();
		foreach ($memberPuzzles as $key => $memberPuzzle) {
			if ($puzzle->getId() != $memberPuzzle->getId()) {
				$this->leavePuzzle($memberPuzzle);
			}
		}

		try {
			$this->addPuzzle($puzzle);
			$this->save();
		} catch (Exception $e) {
			debug("Exception: .".$e->getMessage());
			return "You already joined this puzzle.";
		}

		$puzzle->postJoin($this);
		return "You joined ".$puzzle->getTitle().".";
	}

	public function leavePuzzle($puzzle) {
		PuzzleMemberQuery::create()
			->filterByMember($this)
			->filterByPuzzle($puzzle)
			->delete();
		$puzzle->postLeave($this);
		return "You left ".$puzzle->getTitle().".";
	}

}
