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

	// JOIN

	public function joinPuzzle($puzzle) {
		$memberPuzzles = $this->getPuzzles();

		foreach ($memberPuzzles as $key => $memberPuzzle) {
			if ($puzzle->getId() == $memberPuzzle->getId()) {
				continue;
			}
			PuzzleMemberQuery::create()
				->filterByMember($this)
				->filterByPuzzle($memberPuzzle)
				->delete();
			$memberPuzzle->postLeave($this);
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

}
