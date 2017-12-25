<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1514168715.
 * Generated on 2017-12-25 02:25:15 by sandor
 */
class PropelMigration_1514168715
{
    public $comment = '';

    public function preUp(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postUp(MigrationManager $manager)
    {
        // add the post-migration code here
    }

    public function preDown(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postDown(MigrationManager $manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'palindrome' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `puzzle`

  ADD `wrangler_id` INTEGER AFTER `slack_channel_id`;

CREATE INDEX `puzzle_fi_707a36` ON `puzzle` (`wrangler_id`);

ALTER TABLE `puzzle` ADD CONSTRAINT `puzzle_fk_707a36`
    FOREIGN KEY (`wrangler_id`)
    REFERENCES `member` (`id`)
    ON DELETE SET NULL;

ALTER TABLE `puzzle_archive`

  ADD `wrangler_id` INTEGER AFTER `slack_channel_id`;

CREATE INDEX `puzzle_fi_707a36` ON `puzzle_archive` (`wrangler_id`);

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
    public function getDownSQL()
    {
        return array (
  'palindrome' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `puzzle` DROP FOREIGN KEY `puzzle_fk_707a36`;

DROP INDEX `puzzle_fi_707a36` ON `puzzle`;

ALTER TABLE `puzzle`

  DROP `wrangler_id`;

DROP INDEX `puzzle_fi_707a36` ON `puzzle_archive`;

ALTER TABLE `puzzle_archive`

  DROP `wrangler_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}