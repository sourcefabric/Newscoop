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
