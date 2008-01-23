-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 23. Januar 2008 um 18:43
-- Server Version: 5.0.32
-- PHP-Version: 5.2.0-8+etch7
-- 
-- Datenbank: `campsite_30_interview`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `plugin_interview_interviews`
-- 

CREATE TABLE `plugin_interview_interviews` (
  `interview_id` int(10) unsigned NOT NULL auto_increment,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_moderator_user_id` int(10) unsigned NOT NULL,
  `fk_guest_user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `fk_image_id` int(10) unsigned default NULL,
  `description_short` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `interview_begin` datetime NOT NULL,
  `interview_end` datetime NOT NULL,
  `questions_begin` datetime NOT NULL,
  `questions_end` datetime NOT NULL,
  `questions_limit` int(10) unsigned NOT NULL,
  `status` enum('draft','pending','published','offline') NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`interview_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `plugin_interview_items`
-- 

CREATE TABLE `plugin_interview_items` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `fk_interview_id` int(10) unsigned NOT NULL,
  `fk_questioneer_user_id` int(11) default NULL,
  `question` text NOT NULL,
  `status` enum('draft','pending','public','offline') NOT NULL default 'draft',
  `answer` text NOT NULL,
  `item_order` int(10) unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- Setup additional rights

INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_notify',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);


INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_guest',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);

INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_moderator',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);


INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_admin',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);


