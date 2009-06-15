-- new column to store user ip address for Log table
ALTER TABLE `Log` ADD COLUMN `user_ip` INT UNSIGNED;

ALTER TABLE `ArticleTypeMetadata` ADD COLUMN max_size INTEGER UNSIGNED;

CREATE TABLE `Enumerations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `EnumerationElements` (
  `fk_enumeration_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `phrase_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_enumeration_id`, `element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
