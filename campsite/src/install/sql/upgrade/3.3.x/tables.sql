-- Add field for URL error template definition
ALTER TABLE `Publications` ADD COLUMN `url_error_tpl_id` INT UNSIGNED DEFAULT NULL AFTER `comments_spam_blocking_enabled`;

-- Add field for SEO
ALTER TABLE `Publications` ADD COLUMN `seo` varchar(128) DEFAULT NULL;

-- Set default NULL for more strict MySQL versions
ALTER TABLE `Authors` CHANGE `first_name` `first_name` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `Authors` CHANGE `last_name` `last_name` varchar(100) NOT NULL DEFAULT '';

ALTER TABLE `ObjectTypes` CHANGE `name` `name` varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `Plugins` CHANGE `Version` `Version` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `Plugins` CHANGE `Enabled` `Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `RequestObjects` CHANGE `object_type_id` `object_type_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `RequestObjects` CHANGE `request_count` `request_count` int(11) NOT NULL DEFAULT 0;

ALTER TABLE `Requests` CHANGE `last_stats_update` `last_stats_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `Sessions` CHANGE `start_time` `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `Translations` CHANGE `translation_text` `translation_text` text NULL DEFAULT NULL;

ALTER TABLE `URLTypes` CHANGE `Description` `Description` mediumblob NULL DEFAULT NULL;

ALTER TABLE `liveuser_users` CHANGE `Interests` `Interests` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `liveuser_users` CHANGE `Improvements` `Improvements` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `liveuser_users` CHANGE `Text1` `Text1` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `liveuser_users` CHANGE `Text2` `Text2` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `liveuser_users` CHANGE `Text3` `Text3` MEDIUMBLOB NULL DEFAULT NULL;
ALTER TABLE `liveuser_users` CHANGE `time_created` `time_created` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `phorum_files` CHANGE `file_data` `file_data` mediumtext NULL DEFAULT NULL;

ALTER TABLE `phorum_forums` CHANGE `description` `description` text NULL DEFAULT NULL;
ALTER TABLE `phorum_forums` CHANGE `template_settings` `template_settings` text NULL DEFAULT NULL;

ALTER TABLE `phorum_messages` CHANGE `body` `body` text NOT NULL DEFAULT '';
ALTER TABLE `phorum_messages` CHANGE `meta` `meta` mediumtext NOT NULL DEFAULT '';

ALTER TABLE `phorum_pm_messages` CHANGE `message` `message` text NOT NULL DEFAULT '';

ALTER TABLE `phorum_search` CHANGE `search_text` `search_text` mediumtext NOT NULL DEFAULT '';

ALTER TABLE `phorum_settings` CHANGE `data` `data` text NULL DEFAULT NULL;

ALTER TABLE `phorum_user_custom_fields` CHANGE `data` `data` text NULL DEFAULT NULL;

ALTER TABLE `phorum_users` CHANGE `user_data` `user_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `phorum_users` CHANGE `signature` `signature` TEXT NULL DEFAULT NULL;
ALTER TABLE `phorum_users` CHANGE `moderator_data` `moderator_data` TEXT NULL DEFAULT NULL;


-- new columns for month and weekday short names
ALTER TABLE `Languages` ADD COLUMN `ShortMonth1` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth2` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth3` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth4` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth5` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth6` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth7` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth8` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth9` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth10` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth11` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortMonth12` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay1` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay2` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay3` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay4` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay5` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay6` VARCHAR(20);
ALTER TABLE `Languages` ADD COLUMN `ShortWDay7` VARCHAR(20);

ALTER TABLE `ArticleTopics`
  ADD INDEX `article_topics_nrarticle_idx`(`NrArticle`),
  ADD INDEX `article_topics_topicid_idx`(`TopicId`);

-- Position field (Ticket #2667)
ALTER TABLE `Topics` ADD  `TopicOrder` INT NOT NULL AFTER `Name`;
