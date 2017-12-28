<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1514436733.
 * Generated on 2017-12-28 04:52:13 by sandor
 */

class PropelMigration_1514436733 {
	public $comment = '';

	public function preUp(MigrationManager $manager) {
		// add the pre-migration code here
	}

	public function postUp(MigrationManager $manager) {
		// add the post-migration code here
	}

	public function preDown(MigrationManager $manager) {
		// add the pre-migration code here
	}

	public function postDown(MigrationManager $manager) {
		// add the post-migration code here
	}

	/**
	 * Get the SQL statements for the Up migration
	 *
	 * @return array list of the SQL strings to execute for the Up migration
	 *               the keys being the datasources
	 */
	public function getUpSQL() {
		return array(
			'palindrome' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `topic`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slack_channel` VARCHAR(48),
    `slack_channel_id` VARCHAR(24),
    `tree_left` INTEGER,
    `tree_right` INTEGER,
    `tree_level` INTEGER,
    `tree_scope` INTEGER,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT INTO `topic` (`id`, `title`, `slack_channel`, `slack_channel_id`, `tree_left`, `tree_right`, `tree_level`, `tree_scope`)
VALUES
    (1, "Category", NULL, NULL, 1, 2, 0, 1),
    (2, "Skill", NULL, NULL, 1, 2, 0, 2);

CREATE TABLE `puzzleTopic`
(
    `puzzle_id` INTEGER NOT NULL,
    `topic_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`puzzle_id`,`topic_id`),
    UNIQUE INDEX `puzzleTopic_u_b508ba` (`puzzle_id`, `topic_id`),
    INDEX `puzzleTopic_fi_5f1143` (`topic_id`),
    CONSTRAINT `puzzleTopic_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `puzzleTopic_fk_5f1143`
        FOREIGN KEY (`topic_id`)
        REFERENCES `topic` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		);
	}

	/**
	 * Get the SQL statements for the Down migration
	 *
	 * @return array list of the SQL strings to execute for the Down migration
	 *               the keys being the datasources
	 */
	public function getDownSQL() {
		return array(
			'palindrome' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `topic`;

DROP TABLE IF EXISTS `puzzleTopic`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		);
	}

}
