CREATE TABLE IF NOT EXISTS `output_publication` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_output_id` int(11) unsigned NOT NULL,
  `fk_publication_id` int(11) unsigned NOT NULL,
  `fk_language_id` int(11) unsigned NOT NULL,
  `fk_theme_path_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publication_language` (`fk_publication_id`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
