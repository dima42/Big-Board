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
                return preg_replace("/[^a-zA-Z0-9:.\/]+/", "", "https://meet.jit.si/".getenv('TEAM_NAME')."/".$this->getTitle());
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

        public function getDisplayAge($timestr) {
            $age_in_minutes = (time()-strtotime($timestr))/60;
            $last_mod       = intval($age_in_minutes)." min ago";
            if ($age_in_minutes > 60*24) {
                $last_mod = intval($age_in_minutes/(24*60))." days ago";
            } else if ($age_in_minutes > 60) {
                $last_mod = intval($age_in_minutes/60)." hrs ago";
            }
            return $last_mod;
        }

        public function getProperties() {
           return array_merge(
                $this->toArray(),
            [
                'SpreadsheetId' => $this->parseSpreadsheetID(),
                'SpreadsheetURL' => $this->getSpreadsheetURL(),
                'SlackChannelURL' => $this->getSlackURL(),
                'JitsiURL' => $this->getJitsiURL(),
                'SheetData' => $this->getMaybeCachedSheetData(),
                'LastModifiedAge' => $this->getDisplayAge($this->getMaybeCachedLastMod()['when']),
                'CreatedAge' => $this->getDisplayAge($this->getCreatedAt("Y-m-d H:i:s")),
            ]
            );
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
                    '=HYPERLINK("'.$this->getUrl().'", "Puzzle")',
                    '=HYPERLINK("'.$this->getSlackURL().'", "Slack")',
                    '=HYPERLINK("'.$this->getJitsiURL().'", "Video")',
                    '=HYPERLINK("'.$this->getBigBoardURL().'", "Big Board")',
                ]];
                $range = "A1:D1";
                $body = new Google_Service_Sheets_ValueRange(['majorDimension' => 'ROWS', 'values' => $values]);
                $spreadsheetID = $this->parseSpreadsheetID();
                $valueInputOption = 'USER_ENTERED';
		$params = [
  		  'valueInputOption' => $valueInputOption
		];
                $result = $shared_sheets->spreadsheets_values->update($spreadsheetID, $range,
        $body, $params);
        }

        public function getMaybeCachedSheetData() {
            Global $cache;
            $callable = function () { return $this->getSheetData(); };
            return $cache->get($this->parseSpreadsheetID(), $callable);
        }

        public function getSheetData() {
            if ($this->getStatus() == 'solved') {
                return '';
            } else {
                Global $shared_sheets;
                $range = "A4:B8";
                $response = $shared_sheets->spreadsheets_values->get($this->parseSpreadsheetID(), $range);
                $values = $response->getValues();
                return $values;
            }
        }


	public function solve($solution, $shared_drive) {
            $oldSolution = $this->getSolution();
            $newSolution = strtoupper(trim($solution));

            if ($newSolution == $oldSolution) {
                    return;
            }

            if ($newSolution != '') {
                $newStatus = 'solved';
                $title = $this->getTitle()." SOLVED: ".$newSolution;
                $alert = $this->getTitle()." is solved! Great work, team! 🎓";
            } else {
                $newStatus = 'open';
                $title = $this->getTitle();
                $alert = $this->getTitle()." is open again";
            }

            // SET SOLUTION
            $this->setSolution($newSolution);

            $fileID = $this->parseSpreadsheetID();
            $emptyFile = new Google_Service_Drive_DriveFile();
            $emptyFile->setName($title);
            $updatedFile = $shared_drive->files->update($fileID, $emptyFile);

            Global $shared_sheets;
            $values = [[
                '=if(not(A4=""), "no", "")',
                '=if(not(A5=""), "no", "")',
                '=if(not(A6=""), "no", "")',
                '=if(not(A7=""), "no", "")',
                '=if(not(A8=""), "no", "")'
            ]];
            $range = "B4:B8";
            $body = new Google_Service_Sheets_ValueRange(['majorDimension' => 'COLUMNS', 'values' => $values]);
            $spreadsheetID = $this->parseSpreadsheetID();
            $valueInputOption = 'USER_ENTERED';
            $params = [
                'valueInputOption' => $valueInputOption
            ];
            $result = $shared_sheets->spreadsheets_values->update($spreadsheetID, $range, $body, $params);

            // SET STATUS
            $this->setStatus($newStatus);

            // POST TO SLACK
            $channel = $this->getSlackChannel();
            postToChannel('*'.$this->getTitle().'* is '.$newStatus.': `'.$this->getSolution().'`', $this->getSlackAttachmentMedium(), ":checkered_flag:", "SolveBot", $channel);
            postToHuntChannel('*'.$this->getTitle().'* is '.$newStatus.': `'.$this->getSolution().'`', $this->getSlackAttachmentMedium(), ":checkered_flag:", "SolveBot");
            
            $this->save();

	    return $alert;
	}

	// LAST MOD
        public function getMaybeCachedLastMod() {

            $max_age = 180;
            if ($this->getStatus() == 'solved') {
                $max_age = 1000*1000*1000*1000;
            }

            Global $cache;
            $callable = function () { return $this->getLastMod(); };
            return $cache->get($this->parseSpreadsheetID()." last mod", $callable, $max_age);
        }

	public function getLastMod() {
		Global $shared_drive;
		$fileID = $this->parseSpreadsheetID();
                
                
		$file   = $shared_drive->files->get($fileID, array('fields' => 'modifiedTime, createdTime'));
		debug('Fetching Google file info for '.$this->title);
                $revisions = $shared_drive->revisions->listRevisions($fileID);
                
                // google drive modifiedAt is affected by some background process
                $last_modified = $file['createdTime'];
                foreach($revisions['revisions'] as $revision) {
                    if ($revision['modifiedTime'] > $last_modified) {
                        $last_modified = $revision['modifiedTime'];
                    }
                }

                if (strtotime($last_modified)-strtotime($file['createdTime']) < 60) {
                        $last_mod = "not yet started";
                }

                $this->setSheetModDate($last_modified);
                $this->save();

		return [
			'when' => $last_modified,
			'who'  => $file['lastModifyingUserName']??"",
		];
	}

	// SLACK STUFF

	public function getSlackAttachmentSmall() {
                $properties = $this->getProperties();
		$content = [
			":".$this ->getStatus().":",
			'<'.$this ->getBigBoardURL().'|:boar:> ',
			'<'.$this ->getUrl().'|:hh:>',
			'<'.$this ->getSpreadsheetURL().'|:drive:> ',
                        '<'.$this ->getJitsiURL().'|:camera:> ',
			'*'.$this ->getTitle().'*',
			'<#'.$this->getSlackChannelId().'>',
                        join(", ", array_map(function ($input) {return $input[0];}, $properties['SheetData'])),
                        $properties['LastModifiedAge'],
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
                        ':camera: <'.$this ->getJitsiURL().'| Video Meeting>',
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

}
