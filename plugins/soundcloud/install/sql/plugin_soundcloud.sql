DROP TABLE IF EXISTS `plugin_soundcloud`;
CREATE TABLE `plugin_soundcloud` (
  `article_id` int(10) unsigned NOT NULL DEFAULT '0',
  `track_id` int(10) unsigned NOT NULL DEFAULT '0',
  `track_data` blob DEFAULT NULL,
  PRIMARY KEY (`article_id`,`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
