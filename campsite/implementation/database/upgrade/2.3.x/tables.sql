ALTER TABLE UserTypes ADD COLUMN InitializeTemplateEngine ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE UserPerm ADD COLUMN InitializeTemplateEngine ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE Users CHANGE COLUMN Password Password VARCHAR(64) NOT NULL DEFAULT '';

--
-- Log table
--

-- Fix the log table - use the USER ID instead of the user name to identify
-- the user.
ALTER TABLE `Log` ADD `fk_user_id` INT UNSIGNED AFTER `User` ;
UPDATE Log, Users SET Log.fk_user_id=Users.Id WHERE Log.User=Users.UName;
ALTER TABLE `Log` DROP `User` ;

-- Fix the names of the "Log" table to coorespond to database naming conventions
ALTER TABLE `Log` CHANGE `TStamp` `time_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `Log` CHANGE `IdEvent` `fk_event_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Log` CHANGE `Text` `text` VARCHAR( 255 ) NOT NULL;


--
-- ArticlePublish table
--

-- Fix names of "ArticlePublish" table to use database naming conventions
ALTER TABLE `ArticlePublish` CHANGE `NrArticle` `fk_article_number` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `IdLanguage` `fk_language_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `ActionTime` `time_action` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
CHANGE `Publish` `publish_action` ENUM( 'P', 'U' ) NULL DEFAULT NULL ,
CHANGE `FrontPage` `publish_on_front_page` ENUM( 'S', 'R' ) NULL DEFAULT NULL ,
CHANGE `SectionPage` `publish_on_section_page` ENUM( 'S', 'R' ) NULL DEFAULT NULL ,
CHANGE `Completed` `is_completed` ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'N';

-- Delete the old primary key.
ALTER TABLE `ArticlePublish` DROP PRIMARY KEY;

-- Add new indexes.
ALTER TABLE `ArticlePublish` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `ArticlePublish` ADD INDEX `article_index` ( `fk_article_number` , `fk_language_id` );
ALTER TABLE `ArticlePublish` ADD INDEX `event_time_index` ( `time_action` , `is_completed` );

--
-- IssuePublish table
--

-- Drop old index.
ALTER TABLE `IssuePublish` DROP PRIMARY KEY;

-- Fix column names.
ALTER TABLE `IssuePublish` CHANGE `IdPublication` `fk_publication_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `NrIssue` `fk_issue_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `IdLanguage` `fk_language_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `ActionTime` `time_action` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
CHANGE `Action` `publish_action` ENUM( 'P', 'U' ) NOT NULL DEFAULT 'P',
CHANGE `PublishArticles` `do_publish_articles` ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'Y',
CHANGE `Completed` `is_completed` ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'N';

-- Add new indexes.
ALTER TABLE `IssuePublish` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `IssuePublish` ADD INDEX `issue_index` ( `fk_publication_id` , `fk_issue_id` , `fk_language_id` );
ALTER TABLE `IssuePublish` ADD INDEX `action_time_index` ( `time_action` , `is_completed` );

-- Add time_created and time_updated fields
ALTER TABLE `campsite`.`Users` ADD COLUMN `time_updated` TIMESTAMP  NOT NULL AFTER `Text3`;
ALTER TABLE `campsite`.`Users` ADD COLUMN `time_created` TIMESTAMP  NOT NULL AFTER `time_updated`;
