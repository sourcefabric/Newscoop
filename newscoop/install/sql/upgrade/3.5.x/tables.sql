-- Alter log table
ALTER TABLE `Log` ADD `id` int(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY(`id`);
ALTER TABLE `Log` ADD `priority` SMALLINT(1) NOT NULL DEFAULT '6';
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
