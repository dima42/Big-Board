
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
    `post_count` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
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
    `body` VARCHAR(255) NOT NULL,
    `puzzle_id` INTEGER,
    `member_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `note_fi_937852` (`puzzle_id`),
    INDEX `note_fi_672062` (`member_id`),
    CONSTRAINT `note_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `note_fk_672062`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE SET NULL
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
    `google_refresh` VARCHAR(128),
    `slack_id` VARCHAR(24),
    `slack_handle` VARCHAR(48),
    `strengths` VARCHAR(128),
    `avatar` VARCHAR(128),
    `phone_number` VARCHAR(24),
    PRIMARY KEY (`id`),
    UNIQUE INDEX `member_u_060aec` (`full_name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- solver
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `solver`;

CREATE TABLE `solver`
(
    `puzzle_id` INTEGER NOT NULL,
    `member_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`puzzle_id`,`member_id`),
    UNIQUE INDEX `solver_u_b374db` (`puzzle_id`, `member_id`),
    INDEX `solver_fi_672062` (`member_id`),
    CONSTRAINT `solver_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `solver_fk_672062`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- relationship
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `relationship`;

CREATE TABLE `relationship`
(
    `puzzle_id` INTEGER NOT NULL,
    `parent_id` INTEGER NOT NULL,
    PRIMARY KEY (`puzzle_id`,`parent_id`),
    INDEX `relationship_fi_f840ee` (`parent_id`),
    CONSTRAINT `relationship_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `relationship_fk_f840ee`
        FOREIGN KEY (`parent_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- news
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `news_type` VARCHAR(16),
    `content` VARCHAR(255) NOT NULL,
    `member_id` INTEGER,
    `puzzle_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `news_fi_672062` (`member_id`),
    INDEX `news_fi_937852` (`puzzle_id`),
    CONSTRAINT `news_fk_672062`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE SET NULL,
    CONSTRAINT `news_fk_937852`
        FOREIGN KEY (`puzzle_id`)
        REFERENCES `puzzle` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- puzzle_archive
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `puzzle_archive`;

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

-- ---------------------------------------------------------------------
-- note_archive
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `note_archive`;

CREATE TABLE `note_archive`
(
    `id` INTEGER NOT NULL,
    `body` VARCHAR(255) NOT NULL,
    `puzzle_id` INTEGER,
    `member_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `archived_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `note_fi_937852` (`puzzle_id`),
    INDEX `note_fi_672062` (`member_id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- news_archive
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `news_archive`;

CREATE TABLE `news_archive`
(
    `id` INTEGER NOT NULL,
    `news_type` VARCHAR(16),
    `content` VARCHAR(255) NOT NULL,
    `member_id` INTEGER,
    `puzzle_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    `archived_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `news_fi_672062` (`member_id`),
    INDEX `news_fi_937852` (`puzzle_id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
