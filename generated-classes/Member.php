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

}
