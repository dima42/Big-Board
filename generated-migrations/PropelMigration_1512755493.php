<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1512755493.
 * Generated on 2017-12-08 17:51:33 by sandor
 */

class PropelMigration_1512755493 {
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

ALTER TABLE `note` ADD CONSTRAINT `note_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`)
    ON DELETE SET NULL;

ALTER TABLE `note` ADD CONSTRAINT `note_fk_672062`
    FOREIGN KEY (`member_id`)
    REFERENCES `member` (`id`)
    ON DELETE SET NULL;

ALTER TABLE `relationship` DROP FOREIGN KEY `relationship_fk_937852`;

ALTER TABLE `relationship` DROP FOREIGN KEY `relationship_fk_f840ee`;

ALTER TABLE `relationship` ADD CONSTRAINT `relationship_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`)
    ON DELETE CASCADE;

ALTER TABLE `relationship` ADD CONSTRAINT `relationship_fk_f840ee`
    FOREIGN KEY (`parent_id`)
    REFERENCES `puzzle` (`id`)
    ON DELETE CASCADE;

ALTER TABLE `solver` DROP FOREIGN KEY `solver_fk_672062`;

ALTER TABLE `solver` DROP FOREIGN KEY `solver_fk_937852`;

ALTER TABLE `solver` ADD CONSTRAINT `solver_fk_672062`
    FOREIGN KEY (`member_id`)
    REFERENCES `member` (`id`)
    ON DELETE CASCADE;

ALTER TABLE `solver` ADD CONSTRAINT `solver_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`)
    ON DELETE CASCADE;

CREATE TABLE `puzzle_archive`
(
    `id` INTEGER NOT NULL,
    `title` VARCHAR(128),
    `url` VARCHAR(128),
    `spreadsheet_id` VARCHAR(128),
    `solution` VARCHAR(128),
    `status` VARCHAR(24),
    `slack_channel` VARCHAR(48),
    `slack_channel_id` VARCHAR(24),
    `post_count` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `archived_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `puzzle_archive_i_639136` (`title`)
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

DROP TABLE IF EXISTS `puzzle_archive`;

ALTER TABLE `note` DROP FOREIGN KEY `note_fk_937852`;

ALTER TABLE `note` DROP FOREIGN KEY `note_fk_672062`;

ALTER TABLE `relationship` DROP FOREIGN KEY `relationship_fk_937852`;

ALTER TABLE `relationship` DROP FOREIGN KEY `relationship_fk_f840ee`;

ALTER TABLE `relationship` ADD CONSTRAINT `relationship_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`);

ALTER TABLE `relationship` ADD CONSTRAINT `relationship_fk_f840ee`
    FOREIGN KEY (`parent_id`)
    REFERENCES `puzzle` (`id`);

ALTER TABLE `solver` DROP FOREIGN KEY `solver_fk_672062`;

ALTER TABLE `solver` DROP FOREIGN KEY `solver_fk_937852`;

ALTER TABLE `solver` ADD CONSTRAINT `solver_fk_672062`
    FOREIGN KEY (`member_id`)
    REFERENCES `member` (`id`);

ALTER TABLE `solver` ADD CONSTRAINT `solver_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		);
	}

}
