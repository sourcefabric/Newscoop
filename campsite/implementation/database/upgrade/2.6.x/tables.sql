-- ticket #2220 - A new field to input section description
ALTER TABLE `Sections` ADD `Description` BLOB AFTER `ShortName`;

-- create the tables for article audioclip attachments
CREATE TABLE `ArticleAudioclips` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_audioclip_gunid` varchar(20) NOT NULL default '0',
  `fk_language_id` int(10) unsigned default NULL,
  `order_no` smallint unsigned NOT NULL default '0',
  PRIMARY KEY (`fk_article_number`, `fk_audioclip_gunid`)
);

CREATE TABLE  `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` varchar(20) NOT NULL default '0',
  `predicate_ns` varchar(10) default '',
  `predicate` varchar(30) NOT NULL default '',
  `object` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`, `predicate_ns`, `predicate`)
);


-- create table for system preferences
CREATE TABLE `SystemPreferences` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `varname` varchar(100) NOT NULL default '',
  `value` varchar(100) default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `varname` (`varname`)
);


-- create table for LiveUser single sign-on
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL default '0',
  `application_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`application_id`),
  UNIQUE KEY `applications_define_name_i_idx` (`application_define_name`)
);

INSERT INTO `liveuser_applications` VALUES (1,'Campsite');
INSERT INTO `liveuser_applications` VALUES (2,'Campcaster');

CREATE TABLE `liveuser_applications_application_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
);

INSERT INTO `liveuser_applications_application_id_seq` VALUES (2);

CREATE TABLE `liveuser_applications_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_area_admin_areas` (
  `area_id` int(11) NOT NULL default '0',
  `perm_user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `area_admin_areas_id_i_idx` (`area_id`,`perm_user_id`)
);

CREATE TABLE `liveuser_areas` (
  `area_id` int(11) NOT NULL default '0',
  `application_id` int(11) NOT NULL default '0',
  `area_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`area_id`),
  UNIQUE KEY `areas_define_name_i_idx` (`application_id`,`area_define_name`)
);

INSERT INTO `liveuser_areas` VALUES (1,1,'Articles');

CREATE TABLE `liveuser_areas_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_group_subgroups` (
  `group_id` int(11) NOT NULL default '0',
  `subgroup_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_subgroups_id_i_idx` (`group_id`,`subgroup_id`)
);

CREATE TABLE `liveuser_grouprights` (
  `group_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `grouprights_id_i_idx` (`group_id`,`right_id`)
);

CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL default '0',
  `group_type` int(11) NOT NULL default '0',
  `group_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `groups_define_name_i_idx` (`group_define_name`)
);

CREATE TABLE `liveuser_groups_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_groupusers` (
  `perm_user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  UNIQUE KEY `groupusers_id_i_idx` (`perm_user_id`,`group_id`)
);

CREATE TABLE `liveuser_perm_users` (
  `perm_user_id` int(11) NOT NULL default '0',
  `auth_user_id` varchar(32) NOT NULL default ' ',
  `auth_container_name` varchar(32) NOT NULL default ' ',
  `perm_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`perm_user_id`),
  UNIQUE KEY `perm_users_auth_id_i_idx` (`auth_user_id`,`auth_container_name`)
);

CREATE TABLE `liveuser_perm_users_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_right_implied` (
  `right_id` int(11) NOT NULL default '0',
  `implied_right_id` int(11) NOT NULL default '0',
  UNIQUE KEY `right_implied_id_i_idx` (`right_id`,`implied_right_id`)
);

CREATE TABLE `liveuser_rights` (
  `right_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `right_define_name` varchar(32) NOT NULL default ' ',
  `has_implied` tinyint(1) default '1',
  PRIMARY KEY  (`right_id`),
  UNIQUE KEY `rights_define_name_i_idx` (`area_id`,`right_define_name`)
);

INSERT INTO `liveuser_rights` VALUES (1,0,'AddArticle',1),(2,0,'AddAudioclip',1),(3,0,'AddFile',1),(4,0,'AddImage',1),(5,0,'AttachAudioclipToArticle',1),(6,0,'AttachImageToArticle',1),(7,0,'AttachTopicToArticle',1),(8,0,'ChangeArticle',1),(9,0,'ChangeFile',1),(10,0,'ChangeImage',1),(11,0,'ChangeSystemPreferences',1),(12,0,'CommentEnable',1),(13,0,'CommentModerate',1),(14,0,'DeleteArticle',1),(15,0,'DeleteArticleTypes',1),(16,0,'DeleteCountries',1),(17,0,'DeleteFile',1),(18,0,'DeleteImage',1),(19,0,'DeleteIssue',1),(20,0,'DeleteLanguages',1),(21,0,'DeletePub',1),(22,0,'DeleteSection',1),(23,0,'DeleteTempl',1),(24,0,'DeleteUsers',1),(25,0,'EditorBold',1),(26,0,'EditorCharacterMap',1),(27,0,'EditorCopyCutPaste',1),(28,0,'EditorEnlarge',1),(29,0,'EditorFindReplace',1),(30,0,'EditorFontColor',1),(31,0,'EditorFontFace',1),(32,0,'EditorFontSize',1),(33,0,'EditorHorizontalRule',1),(34,0,'EditorImage',1),(35,0,'EditorIndent',1),(36,0,'EditorItalic',1),(37,0,'EditorLink',1),(38,0,'EditorListBullet',1),(39,0,'EditorListNumber',1),(40,0,'EditorSourceView',1),(41,0,'EditorStrikethrough',1),(42,0,'EditorSubhead',1),(43,0,'EditorSubscript',1),(44,0,'EditorSuperscript',1),(45,0,'EditorTable',1),(46,0,'EditorTextAlignment',1),(47,0,'EditorTextDirection',1),(48,0,'EditorUnderline',1),(49,0,'EditorUndoRedo',1),(50,0,'InitializeTemplateEngine',1),(51,0,'MailNotify',1),(52,0,'ManageArticleTypes',1),(53,0,'ManageCountries',1),(54,0,'ManageIndexer',1),(55,0,'ManageIssue',1),(56,0,'ManageLanguages',1),(57,0,'ManageLocalizer',1),(58,0,'ManagePub',1),(59,0,'ManageReaders',1),(60,0,'ManageSection',1),(61,0,'ManageSubscriptions',1),(62,0,'ManageTempl',1),(63,0,'ManageTopics',1),(64,0,'ManageUserTypes',1),(65,0,'ManageUsers',1),(66,0,'MoveArticle',1),(67,0,'Publish',1),(68,0,'TranslateArticle',1),(69,0,'ViewLogs',1);

CREATE TABLE `liveuser_rights_right_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
);

INSERT INTO `liveuser_rights_right_id_seq` VALUES (69);

CREATE TABLE `liveuser_rights_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_translations` (
  `translation_id` int(11) NOT NULL default '0',
  `section_id` int(11) NOT NULL default '0',
  `section_type` int(11) NOT NULL default '0',
  `language_id` varchar(32) NOT NULL default ' ',
  `name` varchar(32) NOT NULL default ' ',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`translation_id`),
  UNIQUE KEY `translations_translation_i_idx` (`section_id`,`section_type`,`language_id`)
);

CREATE TABLE `liveuser_translations_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
);

CREATE TABLE `liveuser_userrights` (
  `perm_user_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `userrights_id_i_idx` (`perm_user_id`,`right_id`)
);

CREATE TABLE `liveuser_users` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `KeyId` int(10) unsigned default NULL,
  `Name` varchar(255) collate utf8_bin NOT NULL default '',
  `UName` varchar(70) collate utf8_bin NOT NULL default '',
  `Password` varchar(64) collate utf8_bin NOT NULL default '',
  `EMail` varchar(255) collate utf8_bin NOT NULL default '',
  `Reader` enum('Y','N') collate utf8_bin NOT NULL default 'Y',
  `fk_user_type` varchar(140) collate utf8_bin default NULL,
  `City` varchar(100) collate utf8_bin NOT NULL default '',
  `StrAddress` varchar(255) collate utf8_bin NOT NULL default '',
  `State` varchar(32) collate utf8_bin NOT NULL default '',
  `CountryCode` varchar(21) collate utf8_bin default NULL,
  `Phone` varchar(20) collate utf8_bin NOT NULL default '',
  `Fax` varchar(20) collate utf8_bin NOT NULL default '',
  `Contact` varchar(64) collate utf8_bin NOT NULL default '',
  `Phone2` varchar(20) collate utf8_bin NOT NULL default '',
  `Title` enum('Mr.','Mrs.','Ms.','Dr.') collate utf8_bin NOT NULL default 'Mr.',
  `Gender` enum('M','F') collate utf8_bin default NULL,
  `Age` enum('0-17','18-24','25-39','40-49','50-65','65-') collate utf8_bin NOT NULL default '0-17',
  `PostalCode` varchar(70) collate utf8_bin NOT NULL default '',
  `Employer` varchar(140) collate utf8_bin NOT NULL default '',
  `EmployerType` varchar(140) collate utf8_bin NOT NULL default '',
  `Position` varchar(70) collate utf8_bin NOT NULL default '',
  `Interests` mediumblob NOT NULL,
  `How` varchar(255) collate utf8_bin NOT NULL default '',
  `Languages` varchar(100) collate utf8_bin NOT NULL default '',
  `Improvements` mediumblob NOT NULL,
  `Pref1` enum('N','Y') collate utf8_bin NOT NULL default 'N',
  `Pref2` enum('N','Y') collate utf8_bin NOT NULL default 'N',
  `Pref3` enum('N','Y') collate utf8_bin NOT NULL default 'N',
  `Pref4` enum('N','Y') collate utf8_bin NOT NULL default 'N',
  `Field1` varchar(150) collate utf8_bin NOT NULL default '',
  `Field2` varchar(150) collate utf8_bin NOT NULL default '',
  `Field3` varchar(150) collate utf8_bin NOT NULL default '',
  `Field4` varchar(150) collate utf8_bin NOT NULL default '',
  `Field5` varchar(150) collate utf8_bin NOT NULL default '',
  `Text1` mediumblob NOT NULL,
  `Text2` mediumblob NOT NULL,
  `Text3` mediumblob NOT NULL,
  `time_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `lastLogin` datetime default '1970-01-01 00:00:00',
  `isActive` tinyint(1) default '1',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `UName` (`UName`)
);
