CREATE TABLE IF NOT EXISTS `ingest_feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `mode` varchar(25) DEFAULT 'manual',
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ingest_feed_entry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) unsigned NOT NULL,
  `date_id` varchar(20) DEFAULT NULL,
  `news_item_id` varchar(255) DEFAULT NULL,
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

ALTER TABLE  `ingest_feed_entry` CHANGE  `news_item_id`  `news_item_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
