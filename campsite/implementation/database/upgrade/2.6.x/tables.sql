-- ticket #2220 - A new field to input section description
ALTER TABLE `Sections` ADD `Description` BLOB AFTER `ShortName`;

-- create the ArticleAudioclips table
CREATE TABLE `ArticleAudioclips` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT 0,
  `fk_audioclip_gunid` varchar(20) NOT NULL DEFAULT 0,
  `order_no` smallint unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`fk_article_number`, `fk_audioclip_gunid`)
);

CREATE TABLE  `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` varchar(20) NOT NULL default '0',
  `predicate_ns` varchar(255) default '',
  `predicate` varchar(255) NOT NULL default '',
  `object` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`, `predicate_ns`, `predicate`)
);
