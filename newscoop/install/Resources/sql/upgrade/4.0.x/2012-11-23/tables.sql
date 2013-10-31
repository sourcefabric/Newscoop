CREATE TABLE IF NOT EXISTS `rating` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `article_number` int(10) unsigned NOT NULL,
      `user_id` int(10) unsigned NOT NULL,
      `rating_score` int(10) NOT NULL DEFAULT 0,
      `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `time_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

