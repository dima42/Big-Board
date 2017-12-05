<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1512515431.
 * Generated on 2017-12-05 23:10:31 by sandor
 */
class PropelMigration_1512515431
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

ALTER TABLE `news`

  ADD `puzzle_id` INTEGER AFTER `member_id`;

CREATE INDEX `news_fi_937852` ON `news` (`puzzle_id`);

ALTER TABLE `news` ADD CONSTRAINT `news_fk_937852`
    FOREIGN KEY (`puzzle_id`)
    REFERENCES `puzzle` (`id`);

ALTER TABLE `news_archive`

  ADD `puzzle_id` INTEGER AFTER `member_id`;

CREATE INDEX `news_fi_937852` ON `news_archive` (`puzzle_id`);

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

ALTER TABLE `news` DROP FOREIGN KEY `news_fk_937852`;

DROP INDEX `news_fi_937852` ON `news`;

ALTER TABLE `news`

  DROP `puzzle_id`;

DROP INDEX `news_fi_937852` ON `news_archive`;

ALTER TABLE `news_archive`

  DROP `puzzle_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}