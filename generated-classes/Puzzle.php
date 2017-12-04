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
	// GET

	public function getSlackURL() {
		return "http://palindrome2017.slack.com/messages/".$this->getSlackChannel();
	}

	// ADD

	public function addNewMember($member) {
		$this->addmember($member);
		$this->save();
		$message = "You joined ".$this->getTitle().".";
		$this->postJoin($member);
	}

	// SLACK STUFF

	public function postInfoToSlack() {
		postPuzzle($this, $this->getSlackChannel());
		postPuzzle($this);// big-board channel
	}

	public function postSolve() {
		postSolve($this, $this->getSlackChannel());
		// postSolve($this, 'general');
		// TODO: uncomment above
	}

	public function postJoin($member) {
		$memberCount = $this->countMembers();
		if ($$memberCount > 0) {
			$this->postMembers($member->getNameForSlack()." joined *".$this->getTitle()."*! All members:");
		} else {
			$client = getSlackClient(":wave:", "JoinBot");
			$client->to($this->getSlackChannel())->attach([
					'text'  => $member->getNameForSlack(),
					'color' => 'good',
				])->send('First member of *'.$this->getTitle().'*!');
		}

		// TODO: list all members
	}

	public function postMembers($header_msg = "All members:") {
		$members = $this->getMembers();
		$text    = [];
		foreach ($members as $key => $member) {
			$text[] = $member->getNameForSlack();
		}

		$client = getSlackClient(":wave:", "JoinBot");
		$client->to($channel)->attach([
				'text'  => join("\n", $text),
				'color' => 'good',
			])->send($header_msg);
	}
}
