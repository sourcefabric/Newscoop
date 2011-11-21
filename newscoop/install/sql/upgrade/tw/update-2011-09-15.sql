ALTER TABLE `liveuser_users` ADD `last_name` varchar(80) DEFAULT NULL;
ALTER TABLE `liveuser_users` ADD `status` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `liveuser_users` ADD `is_admin` boolean NOT NULL DEFAULT '0';
ALTER TABLE `liveuser_users` ADD `is_public` boolean NOT NULL DEFAULT '0';
ALTER TABLE `liveuser_users` ADD `points` int(10) DEFAULT '0';
ALTER TABLE `liveuser_users` CHANGE `time_created` `time_created` datetime NOT NULL;
ALTER TABLE `liveuser_users` CHANGE `time_updated` `time_updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `liveuser_users` ADD `image` varchar(255) DEFAULT NULL;
ALTER TABLE `liveuser_users` ADD `subscriber` int(10) DEFAULT NULL;
ALTER TABLE `ArticleAuthors` ADD `order` int(2) unsigned;

UPDATE `liveuser_users` SET `status` = 1, `is_admin` = 1, `is_public` = 1;

DROP TABLE IF EXISTS `audit_event`;
CREATE TABLE IF NOT EXISTS `audit_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `resource_type` varchar(80) NOT NULL,
  `resource_id` varchar(80) DEFAULT NULL,
  `resource_title` varchar(255) DEFAULT NULL,
  `resource_diff` longtext,
  `action` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_attribute`;
CREATE TABLE IF NOT EXISTS `user_attribute` (
  `user_id` int(11) unsigned NOT NULL,
  `attribute` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `community_ticker_event`;
CREATE TABLE IF NOT EXISTS `community_ticker_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(80) NOT NULL,
  `params` text,
  `created` datetime NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_points_index`;
CREATE TABLE IF NOT EXISTS `user_points_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `points` int(10) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_token`;
CREATE TABLE IF NOT EXISTS `user_token` (
  `user_id` int(11) unsigned NOT NULL,
  `action` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`action`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ingest_feed`;
CREATE TABLE IF NOT EXISTS `ingest_feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `ingest_feed` (`title`) VALUES ('SDA');

DROP TABLE IF EXISTS `ingest_feed_entry`;
CREATE TABLE IF NOT EXISTS `ingest_feed_entry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) unsigned NOT NULL,
  `date_id` varchar(20) DEFAULT NULL,
  `news_item_id` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `summary` text,
  `category` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `embargoed` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `attributes` text,
  PRIMARY KEY (`id`),
  UNIQUE (`date_id`, `news_item_id`),
  KEY (`status`, `updated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- Article playlist tables 
DROP TABLE IF EXISTS `playlist`;
CREATE TABLE `playlist` (
  `id_playlist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id_playlist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `playlist_article`;
CREATE TABLE `playlist_article` (
  `id_playlist_article` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_playlist` int(10) unsigned NOT NULL,
  `article_no` int(10) unsigned NOT NULL,
  UNIQUE KEY `id_playlist` (`id_playlist`,`article_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Article popularity table --
DROP TABLE IF EXISTS `article_popularity`;
CREATE TABLE `article_popularity` (
  `fk_article_id` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL,
  `url` varchar(256) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `unique_views` int(10) unsigned NOT NULL DEFAULT '0',
  `avg_time_on_page` float NOT NULL DEFAULT '0',
  `tweets` int(10) unsigned DEFAULT NULL,
  `likes` int(10) unsigned DEFAULT NULL,
  `comments` int(10) unsigned DEFAULT NULL,
  `popularity` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_article_id`,`fk_language_id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

