-- ticket #2220 - A new field to input section description
ALTER TABLE `Sections` ADD `Description` BLOB AFTER `ShortName`;

-- create the ArticleAudioclips table
CREATE TABLE `ArticleAudioclips` (
    `fk_article_number` INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `fk_audioclip_gunid` VARCHAR(16) NOT NULL,
	`order_no` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`fk_article_number`, `fk_audioclip_gunid`)
);

CREATE TABLE  `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` bigint(20) unsigned NOT NULL default '0',
  `subject_ns` varchar(255) default '',
  `subject` varchar(255) NOT NULL default '',
  `predicate_ns` varchar(255) default '',
  `predicate` varchar(255) NOT NULL default '',
  `predicate_xml` char(1) NOT NULL default '',
  `object_ns` varchar(255) default '',
  `object` text,
  PRIMARY KEY  (`id`)
);
