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

	public function getStatusColor() {
		$colors = [
			'open'     => 'green',
			'solved'   => "#000",
			'stuck'    => "#FFEFE5",
			'priority' => "#FFD1B2",
			'urgent'   => "#FF6600",
		];
		return $colors[$this->getStatus()]??'green';
	}

	public function getSlackURL() {
		return "http://palindrome2017.slack.com/messages/".$this->getSlackChannel();
	}

	public function getSpreadsheetURL() {
		return "https://docs.google.com/spreadsheets/d/".$this->parseSpreadsheetID();
	}

	public function parseSpreadsheetID() {
		$sid = $this->getSpreadsheetID();
		if (substr($sid, 0, 4) == "http") {
			return substr($sid, strpos($sid, "ccc?key=")+8, 44);
		}
		return $sid;
	}

	// ADD NOTE
	public function note($noteText, $author) {
		$note = new Note();
		$note->setPuzzle($this);
		$note->setBody($noteText);
		$note->setAuthor($author);
		$note->save();

		$this->postNoteToSlack($note);

		return "Saved a note to ".$this->getTitle();
	}

	// SOLVE

	public function solve($solution) {
		$oldSolution = $this->getSolution();
		$newSolution = strtoupper(trim($solution));

		if ($newSolution == $oldSolution) {
			return;
		}

		// SET SOLUTION
		$this->setSolution($newSolution);
		$this->save();

		if ($newSolution != '') {
			// SET STATUS
			$this->setStatus('solved');
			$alert = $this->getTitle()." is solved! Great work, team! 🎓";

			// REMOVE MEMBERS
			PuzzleMemberQuery::create()
				->filterByPuzzle($this)
				->delete();

			// POST TO SLACK
			postSolve($this);
			// TODO: post to $this->getSlackChannel());
		} else {
			$this->setStatus('open');
			$alert = $this->getTitle()." is open again.";
		}
		$this->save();

		addPuzzleNews($this, 'solved');

		return $alert;
	}

	// LAST MOD

	public function getLastMod() {
		Global $pal_drive;
		$fileID = $this->parseSpreadsheetID();
		$file   = $pal_drive->files->get($fileID);
		debug('Fetching Google file info for '.$this->title);

		$age_in_minutes = (time()-strtotime($file['modifiedDate']??"2017-12-31"))/60;
		$last_mod       = intval($age_in_minutes)." min";
		if ($age_in_minutes > 60*24) {
			$last_mod = intval($age_in_minutes/(24*60))." days";
		} else if ($age_in_minutes > 60) {
			$last_mod = intval($age_in_minutes/60)." hrs";
		}

		return [
			'when' => $last_mod,
			'who'  => $file['lastModifyingUserName']??"",
		];
	}

	// SLACK STUFF

	public function getAttachmentsForSlack() {
		$puzzle_info = [
			':boar: <http://team-palindrome.herokuapp.com/puzzle/'.$this ->getId().'|Big Board>',
			':mit: <'.$this                                              ->getUrl().'|Puzzle page>',
			':drive: <https://docs.google.com/spreadsheet/ccc?key='.$this->getSpreadsheetId().'|Google Spreadsheet>',
		];

		$response = array_map(function ($info) {
				return [
					"text"  => $info,
					"color" => $this->getStatusColor(),
				];
			}, $puzzle_info);

		if ($this->countMembers() > 0) {
			$response[] = [
				"pretext"   => "*Roster:*",
				"mrkdwn_in" => ["pretext"],
				"text"      => $this->getMembersForSlack(),
				"color"     => "grey",
			];
		}

		return $response;
	}

	public function postInfoToSlack() {
		postPuzzle($this, $this->getSlackChannel());
		postPuzzle($this);// TODO: make this the big-board channel
	}

	public function postJoin($member) {
		$memberCount = $this->countMembers();
		$channel     = $this->getSlackChannel();
		if ($memberCount > 0) {
			$this->postMembers($member->getNameForSlack()." joined *".$this->getTitle()."*. Current roster:");
		} else {
			$client = getSlackClient(":wave:", "JoinBot");
			$client->to($channel)->attach([
					'text'  => $member->getNameForSlack(),
					'color' => 'good',
				])->send('First member of *'.$this->getTitle().'*!');
		}
	}

	public function postLeave($member) {
		$memberCount = $this->countMembers();
		$channel     = $this->getSlackChannel();
		if ($memberCount > 0) {
			$this->postMembers($member->getNameForSlack().' left *'.$this->getTitle().'*. Current roster:');
		} else {
			$client = getSlackClient(':wave:', 'JoinBot');
			$client->to($channel)->send($member->getNameForSlack().' left *'.$this->getTitle().'*. No members remain.');
		}
	}

	public function getMembersForSlack() {
		$members = $this->getMembers();
		$text    = [];
		foreach ($members as $key => $member) {
			$text[] = $member->getNameForSlack();
		}
		return join("\n", $text);
	}

	public function postMembers($header_msg = "Current roster:", $channel = "sandbox") {
		$client = getSlackClient(":wave:", "JoinBot");
		$client->to($channel)->attach([
				'text'  => $this->getMembersForSlack(),
				'color' => 'good',
			])->send($header_msg);
	}

	public function postNoteToSlack($note) {
		$client = getSlackClient(":scroll:", "NoteBot");
		$client->to($channel)->attach([
				'text'  => $note->getBody(),
				'color' => 'good',
			])->send('*'.$note->getAuthor()->getFullName().'* posted a note:');
	}
}
