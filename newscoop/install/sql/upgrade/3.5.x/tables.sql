-- Alter log table
ALTER TABLE `Log` ADD `id` int(10) unsigned NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY(`id`);
ALTER TABLE `Log` ADD `priority` smallint(1) unsigned NOT NULL DEFAULT '6';
ALTER TABLE `Log` CHANGE `user_ip` `user_ip` VARCHAR(39) NOT NULL DEFAULT '';
ALTER TABLE `Log` DROP KEY `IdEvent`;
ALTER TABLE `Log` ADD KEY `priority` (`priority`);

-- Add Acl Role table
CREATE TABLE IF NOT EXISTS `acl_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Add Acl Rule table
CREATE TABLE IF NOT EXISTS `acl_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `role_id` int(10) NOT NULL,
  `resource` varchar(80) NOT NULL DEFAULT '',
  `action` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE (`role_id`, `resource`, `action`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Add role id to user/group table
ALTER TABLE `liveuser_groups` ADD `role_id` int(10) DEFAULT NULL; -- to be altered to NOT NULL when populated
ALTER TABLE `liveuser_users` ADD `role_id` int(10) DEFAULT NULL; -- to be altered to NOT NULL when populated

-- Add autoincremet to groups
ALTER TABLE `liveuser_groups` CHANGE `group_id` `group_id` int(11) NOT NULL AUTO_INCREMENT;

-- Remove article audioclips tables
DROP TABLE IF EXISTS `ArticleAudioclips`;
DROP TABLE IF EXISTS `AudioclipMetadata`;


-- Add Ouput table
CREATE TABLE IF NOT EXISTS `output` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 

INSERT INTO `output` (`name`) VALUES('Web');



-- Add Resources table
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 



-- Add Output Themes table
CREATE TABLE IF NOT EXISTS `output_theme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL,
  `fk_theme_path_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned NOT NULL,
  `fk_section_page_id` int(10) unsigned NOT NULL,
  `fk_article_page_id` int(10) unsigned NOT NULL,
  `fk_error_page_id` int(10) unsigned NOT NULL,

  PRIMARY KEY (`id`),
  UNIQUE (`fk_output_id`, `fk_publication_id`, `fk_theme_path_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Add Output Issue table
CREATE TABLE IF NOT EXISTS `output_issue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_issue_id` int(10) unsigned NOT NULL,
  `fk_theme_path_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned,
  `fk_section_page_id` int(10) unsigned,
  `fk_article_page_id` int(10) unsigned,
  `fk_error_page_id` int(10) unsigned,

  PRIMARY KEY (`id`),
  UNIQUE (`fk_output_id`, `fk_issue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- Add Output Section table
CREATE TABLE IF NOT EXISTS `output_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_section_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned,
  `fk_section_page_id` int(10) unsigned,
  `fk_article_page_id` int(10) unsigned,
  `fk_error_page_id` int(10) unsigned,

  PRIMARY KEY (`id`),
  UNIQUE (`fk_output_id`, `fk_section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


 -- The issue table will no longer need the TPL (emaplate) fields
 -- Change the old code to also provide for the section the issue foreign key. ***

ALTER TABLE `Issues` ADD COLUMN `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
DROP PRIMARY KEY,
ADD PRIMARY KEY  USING BTREE(`id`);
ALTER TABLE `Issues` ADD UNIQUE INDEX `issue_unique`(`IdPublication`, `Number`, `IdLanguage`);

ALTER TABLE `Sections` ADD COLUMN `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
DROP PRIMARY KEY,
ADD PRIMARY KEY  USING BTREE(`id`);
ALTER TABLE `Sections` ADD UNIQUE INDEX `section_unique`(`IdPublication`, `NrIssue`, `IdLanguage`, `Number`);


ALTER TABLE `Sections` ADD COLUMN `fk_issue_id` INTEGER UNSIGNED NOT NULL AFTER `id`;

ALTER TABLE `Publications` 
 ADD COLUMN `comments_public_enabled` tinyint(1)  NOT NULL DEFAULT '0' AFTER `comments_public_moderated`,
 ADD COLUMN `comments_moderator_to` VARCHAR(255)  NOT NULL DEFAULT '' AFTER `comments_spam_blocking_enabled`,
 ADD COLUMN `comments_moderator_from` VARCHAR(255)  NOT NULL DEFAULT '' AFTER `comments_moderator_to`;

-- Comment main table
DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS  `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_comment_commenter_id` int(10) unsigned NOT NULL,
  `fk_forum_id` int(10) unsigned NOT NULL,
  `fk_thread_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned DEFAULT '0',  
  `fk_parent_id` int(10) unsigned DEFAULT NULL,
  `subject` varchar(140) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `thread_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thread_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(39) NOT NULL DEFAULT '',
  `likes` tinyint(3) unsigned DEFAULT '0',
  `dislikes` tinyint(3) unsigned DEFAULT '0',
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `comments_users` (`fk_comment_commenter_id`),
  KEY `publication` (`fk_forum_id`),
  KEY `article` (`fk_thread_id`),
  KEY `parent` (`fk_parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Comment Commenter main table
DROP TABLE IF EXISTS `comment_commenter`;
CREATE TABLE  `comment_commenter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(39) NOT NULL DEFAULT '',
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Comment Acceptance main table
DROP TABLE IF EXISTS `comment_acceptance`;
CREATE TABLE  `comment_acceptance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fk_forum_id` int(10) DEFAULT '0',
  `for_column` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `search_type` tinyint(4) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_forum_id` (`fk_forum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Update SubsSections
ALTER TABLE `SubsSections` MODIFY COLUMN `IdLanguage` INTEGER UNSIGNED NOT NULL DEFAULT 0,
 ADD COLUMN `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
 DROP PRIMARY KEY,
 ADD PRIMARY KEY (`id`),
 ADD UNIQUE (`IdSubscription`, `SectionNumber`, `IdLanguage`);


-- Upgrade templates to themes
system php ./create_themes.php

-- Importing the stored function for 'Point in Polygon' checking
system php ./checkpp.php
