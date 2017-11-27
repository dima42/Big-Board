
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- puzzle
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `puzzle`;

CREATE TABLE `puzzle`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(128),
    `url` VARCHAR(128),
    `spreadsheet_id` VARCHAR(128),
    `solution` VARCHAR(128),
    `status` VARCHAR(24),
    `slack_channel` VARCHAR(48),
    `slack_channel_id` VARCHAR(24),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `puzzle_u_639136` (`title`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- note
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `note`;

CREATE TABLE `note`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `text` VARCHAR(255) NOT NULL,
    `puzzle_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `note_fi_937852` (`puzzle_id`),
    CONSTRAINT `note_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- member
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `member`;

CREATE TABLE `member`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(64) NOT NULL,
    `google_id` VARCHAR(64),
    `google_referrer` VARCHAR(64),
    `slack_id` VARCHAR(24),
    `slack_handle` VARCHAR(48),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `member_u_060aec` (`full_name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- solver
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `solver`;

CREATE TABLE `solver`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `puzzle_id` INTEGER NOT NULL,
    `member_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `solver_fi_937852` (`puzzle_id`),
    INDEX `solver_fi_8220d5` (`member_id`),
    CONSTRAINT `solver_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`),
    CONSTRAINT `solver_fk_8220d5`
        FOREIGN KEY (`member_id`)
        REFERENCES `puzzle` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- relationship
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `relationship`;

CREATE TABLE `relationship`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `puzzle_id` INTEGER NOT NULL,
    `parent_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `relationship_fi_937852` (`puzzle_id`),
    INDEX `relationship_fi_f840ee` (`parent_id`),
    CONSTRAINT `relationship_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`),
    CONSTRAINT `relationship_fk_f840ee`
        FOREIGN KEY (`parent_id`)
        REFERENCES `puzzle` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- news
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(16),
    `text` VARCHAR(255) NOT NULL,
    `member_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `news_fi_672062` (`member_id`),
    CONSTRAINT `news_fk_672062`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
