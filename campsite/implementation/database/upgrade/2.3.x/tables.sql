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

-- 
-- Add UserConfig table
--
CREATE TABLE `UserConfig` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`fk_user_id` INT UNSIGNED NOT NULL ,
`varname` VARCHAR( 100 ) NOT NULL ,
`value` VARCHAR( 100 ) ,
`last_modified` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `fk_user_id` )
) TYPE = MYISAM ;

ALTER TABLE `UserConfig` ADD UNIQUE `unique_var_name_index` ( `fk_user_id` , `varname` );

--
-- Add temporary User Types Table which will become the main UserTypes table.
--
CREATE TABLE `TmpUserTypes` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_type_name` VARCHAR( 140 ) NOT NULL ,
`varname` VARCHAR( 100 ) NOT NULL ,
`value` VARCHAR( 100 ) ,
`last_modified` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `user_type_name` )
) TYPE = MYISAM ;

ALTER TABLE `TmpUserTypes` ADD UNIQUE `unique_var_name_index` ( `user_type_name` , `varname` );

-- Run the upgrade script
system php ./update_user_perms.php

-- Rename the tables after the upgrade script is run.
DROP TABLE `UserTypes`;
ALTER TABLE `TmpUserTypes` RENAME `UserTypes` ;
DROP TABLE `UserPerm`;

--
-- Article Attachments
--
CREATE TABLE `Attachments` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`fk_language_id` INT UNSIGNED,
`file_name` VARCHAR( 255 ) ,
`extension` VARCHAR( 50 ),
`mime_type` VARCHAR( 255 ) ,
`content_disposition` ENUM( 'attachment' ) NULL,
`http_charset` VARCHAR( 50 ) ,
`size_in_bytes` BIGINT UNSIGNED,
`fk_description_id` INT,
`fk_user_id` INT UNSIGNED,
`last_modified` TIMESTAMP NOT NULL ,
`time_created` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `id` ) 
) TYPE = MYISAM ;

CREATE TABLE `ArticleAttachments` (
`fk_article_number` INT UNSIGNED NOT NULL ,
`fk_attachment_id` INT UNSIGNED NOT NULL ,
INDEX ( `fk_article_number` ) ,
INDEX ( `fk_attachment_id` ),
UNIQUE `article_attachment_index` ( `fk_article_number` , `fk_attachment_id` )
) TYPE = MYISAM ;

CREATE TABLE `Translations` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`phrase_id` INT UNSIGNED NOT NULL ,
`fk_language_id` INT UNSIGNED NOT NULL ,
`translation_text` TEXT NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `phrase_id` )
) TYPE = MYISAM ;

ALTER TABLE `Translations` ADD UNIQUE `phrase_language_index` ( `phrase_id` , `fk_language_id` );

--
-- AutoId table
--

-- Remove unused columns.
ALTER TABLE `AutoId` DROP `DictionaryId` ,
DROP `ClassId` ,
DROP `KeywordId` ;

-- Add counter for translation table
ALTER TABLE `AutoId` ADD `translation_phrase_id` INT UNSIGNED NOT NULL ;
