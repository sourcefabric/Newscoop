ALTER TABLE `newscoop`.`Publications` 
 ADD COLUMN `comments_public_enabled` tinyint(1)  NOT NULL AFTER `comments_public_moderated`,
 ADD COLUMN `comments_moderator_to` VARCHAR(255)  NOT NULL AFTER `comments_spam_blocking_enabled`,
 ADD COLUMN `comments_moderator_from` VARCHAR(255)  NOT NULL AFTER `comments_moderator_to`;

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
  `fk_forum_id` int(10) NOT NULL DEFAULT '0',
  `for_column` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `search_type` tinyint(4) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_forum_id` (`fk_forum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*
-- Comment Preference per Publication
DROP TABLE IF EXISTS `comment_preference_publication`;
CREATE TABLE  `comment_preference_publication` (
  `fk_forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `article_default_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `subscribers_moderated` tinyint(1) NOT NULL DEFAULT '0',
  `public_moderated` tinyint(1) NOT NULL DEFAULT '0',
  `public_enabled` tinyint(1) NOT NULL,  
  `captcha_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `spam_blocking_enabled` tinyint(1) NOT NULL DEFAULT '0', 
  `moderator_to` varchar(255) NOT NULL,
  `moderator_from` varchar(255) NOT NULL,
  PRIMARY KEY (`fk_forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Comment Preference per Article
DROP TABLE IF EXISTS `comment_preference_article`;
CREATE TABLE  `comment_preference_article` (
  `fk_thread_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_thread_id`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Comment Preference per Article Type
DROP TABLE IF EXISTS `comment_preference_article_type`;
CREATE TABLE  `comment_preference_article_type` (
  `type_name` varchar(166) NOT NULL DEFAULT '',
  `field_name` varchar(166) NOT NULL DEFAULT 'NULL',
  `enabled` tinyint(1) NOT NULL DEFAULT '0', 
  PRIMARY KEY (`type_name`,`field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/