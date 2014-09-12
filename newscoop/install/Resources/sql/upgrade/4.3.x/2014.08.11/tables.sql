ALTER TABLE `ArticleImageCaptions` CHANGE `caption` `caption` text COLLATE 'utf8_general_ci' NOT NULL DEFAULT '';
ALTER TABLE `Images` DROP INDEX `Description`;
ALTER TABLE `Images` CHANGE `Description` `Description` text COLLATE 'utf8_general_ci' NOT NULL DEFAULT '';
ALTER TABLE `Images` ADD INDEX `Description` (`Description`(100));
