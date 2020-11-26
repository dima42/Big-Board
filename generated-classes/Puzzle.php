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
			'open'     => '#006AD8',
			'stuck'    => "#FFEFE5",
			'priority' => "#FFD1B2",
			'solved'   => "#000000",
            'lowpriority' => "ADD8E6"
		];
		return $colors[$this->getStatus()]??'green';
	}

        public function getJitsiURL() {
                return "https://meet.jit.si/".str_replace(' ', '', getenv('TEAM_NAME'))."/".str_replace(' ', '', $this->getTitle());
        }

	public function getBigBoardURL() {
		return "http://".getenv("APP_DOMAIN")."/puzzle/".$this->getId();
	}

	public function getSlackURL() {
		return "http://".getenv('SLACK_DOMAIN').".slack.com/messages/".$this->getSlackChannelID();
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

        public function postMetadataToSheet($shared_sheets) {
                $values = [[
                    'HYPERLINK($this->getSlackURL(), "Slack")',
                    'HYPERLINK($this->getJitsURL(), "Video"),
                    'HYPERLINK($this->getBigBoardURL(), "Big Board")
                ]];
                $range = "A1:C1";
                $body = new Google_Service_Sheets_ValueRange(['majorDimension' => 'COLUMNS', 'values' => $values]);
                $spreadsheetID = $this->parseSpreadsheetID();
                $result = $shared_sheets->spreadsheets_values->update($spreadsheetId, $range,
        $body);
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

	public function solve($solution, $shared_drive) {
		$oldSolution = $this->getSolution();
		$newSolution = strtoupper(trim($solution));

		if ($newSolution == $oldSolution) {
			return;
		}

		// SET SOLUTION
		$this->setSolution($newSolution);
		$this->save();

		if ($newSolution != '') {
			$newStatus = 'solved';

                        $fileID = $this->parseSpreadsheetID();
                        $emptyFile = new Google_Service_Drive_DriveFile();
                        $emptyFile->setName($this->getTitle()." SOLVED: ".$newSolution);
                        $updatedFile = $shared_drive->files->update($fileID, $emptyFile);

			// SET STATUS
			$this->setStatus($newStatus);
			$alert = $this->getTitle()." is solved! Great work, team! 🎓";

			// REMOVE MEMBERS
			$this->removeMembers();

			// POST NEWS UPDATE
			$news_text = "`".$this->getSolution()."`";
			addNews($news_text, $newStatus, $this);

			// POST TO SLACK
			$channel = $this->getSlackChannel();
			postToChannel('*'.$this->getTitle().'* is solved: `'.$this->getSolution().'`', $this->getSlackAttachmentMedium(), ":checkered_flag:", "SolveBot", $channel);
			postToHuntChannel('*'.$this->getTitle().'* is solved: `'.$this->getSolution().'`', $this->getSlackAttachmentMedium(), ":checkered_flag:", "SolveBot");
		} else {
			$this->setStatus('open');
			$alert = $this->getTitle()." is open again.";
		}
		$this->save();

		return $alert;
	}

	public function removeMembers() {
		PuzzleMemberQuery::create()
			->filterByPuzzle($this)
			->delete();
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

	public function getSlackAttachmentSmall() {
		$content = [
			":".$this ->getStatus().":",
			'<'.$this ->getBigBoardURL().'|:boar:> ',
			'<'.$this ->getUrl().'|:hh:>',
			'<'.$this ->getSpreadsheetURL().'|:drive:> ',
                        '<'.$this ->getJitsiURL().'|:camera:> ',
			'*'.$this ->getTitle().'*',
			'<#'.$this->getSlackChannelId().'>',
		];

		return [
			"text"      => join(" ", $content),
			'color'     => $this->getStatusColor(),
			"mrkdwn_in" => ['text'],
		];
	}

	public function getSlackAttachmentMedium() {
		$content = [
			':boar: <'.$this  ->getBigBoardURL().'|Big Board> ',
			':hh: <'.$this    ->getUrl().'|Puzzle>',
			':drive: <'.$this ->getSpreadsheetURL().'|Sheet> ',
                        ':camera: <'.$this ->getJitsiURL().'| Video> ',
			':slack: <#'.$this->getSlackChannelId().'|'.$this->getSlackChannel().'>'
		];

		return [[
				'text'  => join(" ", $content),
				'color' => '#000000',
			]];
	}

	public function getSlackAttachmentLarge() {
		// No need to include Slack channel in attachment list b/c this info can only be returned to the puzzle's Slack channel.
		$puzzle_info = [
			':boar: <'.$this ->getBigBoardURL().'|Big Board>',
			':hh: <'.$this   ->getUrl().'|Puzzle page>',
			':drive: <'.$this->getSpreadsheetURL().'|Google Spreadsheet>',
                        ':camera: <'.$this ->getJitsURL().'| Video Meeting>',
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

	public function postJoin($member) {
		$memberCount = $this->countMembers();

		$channel = $this->getSlackChannel();

		inviteToSlackChannel($this->getSlackChannelId(), $member->getSlackID());

		// if ($memberCount > 0) {
		// 	$this->postMembers($member->getNameForSlack()." joined *".$this->getTitle()."*. Current roster:", $channel);
		// } else {
		// 	postToChannel(
		// 		'First member of *'.$this->getTitle().'*!',
		// 		[[
		// 				'text'  => $member->getNameForSlack(),
		// 				'color' => 'good',
		// 			]],
		// 		":wave:",
		// 		"JoinBot",
		// 		$channel
		// 	);
		// }
	}

	public function postLeave($member) {
		$memberCount = $this->countMembers();

		$channel = $this->getSlackChannel();

		if ($memberCount > 0) {
			$this->postMembers($member->getFullName().' left *'.$this->getTitle().'*. Current roster:', $channel);
		} else {
			postToChannel(
				$member->getFullName().' left *'.$this->getTitle().'*. No members remain.',
				[],
				":wave:",
				"JoinBot",
				$channel
			);
		}
	}

	public function getMembersForSlack() {
		$members = $this->getMembers();
		$text    = [];
		foreach ($members as $key => $member) {
			$text[] = $member->getFullName();
		}
		return join("\n", $text);
	}

	public function postMembers($header_msg = "Current roster:", $channel) {
		postToChannel(
			$header_msg,
			[[
					'text'  => $this->getMembersForSlack(),
					'color' => 'good',
				]],
			":wave:",
			"JoinBot",
			$channel
		);
	}

	public function postNoteToSlack($note) {
		$channel = $this->getSlackChannel();

		postToChannel(
			'*'.$note->getAuthor()->getFullName().'* posted a note:',
			[[
					'text'  => $note->getBody(),
					'color' => 'good',
				]],
			":scroll:",
			"NoteBot",
			$channel
		);
	}
}
