BEGIN;

ALTER TABLE `Articles` ADD `PublishDate` DATETIME DEFAULT '0' NOT NULL AFTER `Published`;
ALTER TABLE `ArticlePublish` CHANGE `PublishTime` `ActionTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `ArticlePublish` ADD COLUMN `Completed` ENUM('N', 'Y') NOT NULL DEFAULT 'N';

ALTER TABLE `IssuePublish` CHANGE `PublishTime` `ActionTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `IssuePublish` ADD `Completed` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL;

ALTER TABLE `UserPerm` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `UserTypes` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `Topics` MODIFY COLUMN `Name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `Users` MODIFY COLUMN `CountryCode` VARCHAR(21);
ALTER TABLE `Users` MODIFY COLUMN `Gender` ENUM('M','F');

system php ./update_article_types.php

COMMIT;
