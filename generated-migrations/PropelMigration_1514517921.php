<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1514517921.
 * Generated on 2017-12-29 03:25:21 by sandor
 */

class PropelMigration_1514517921 {
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

CREATE TABLE `tag`
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

INSERT INTO `tag` (`id`, `title`, `slack_channel`, `slack_channel_id`, `tree_left`, `tree_right`, `tree_level`, `tree_scope`)
VALUES
    (1, "Puzzle Types", NULL, NULL, 1, 2, 0, 1),
    (2, "Topics", NULL, NULL, 1, 2, 0, 2),
    (3, "Skills", NULL, NULL, 1, 2, 0, 3);

CREATE TABLE `tag_alert`
(
    `puzzle_id` INTEGER NOT NULL,
    `tag_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`puzzle_id`,`tag_id`),
    UNIQUE INDEX `tag_alert_u_878027` (`puzzle_id`, `tag_id`),
    INDEX `tag_alert_fi_022a95` (`tag_id`),
    CONSTRAINT `tag_alert_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `tag_alert_fk_022a95`
        FOREIGN KEY (`tag_id`)
        REFERENCES `tag` (`id`)
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

DROP TABLE IF EXISTS `tag`;

DROP TABLE IF EXISTS `tag_alert`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		);
	}

}
