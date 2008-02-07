-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 07. Februar 2008 um 15:33
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
  `title` varchar(256) NOT NULL,
  `fk_image_id` int(10) unsigned default NULL,
  `description_short` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `interview_begin` datetime NOT NULL,
  `interview_end` datetime NOT NULL,
  `questions_begin` datetime NOT NULL,
  `questions_end` datetime NOT NULL,
  `questions_limit` int(10) unsigned NOT NULL,
  `status` enum('draft','pending','published','rejected') NOT NULL,
  `invitation_sender` varchar(256) NOT NULL,
  `invitation_subject` varchar(256) NOT NULL,
  `invitation_template` text NOT NULL,
  `invitation_type` enum('text','html') NOT NULL,
  `invitation_sent` datetime default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`interview_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `plugin_interview_items`
-- 

CREATE TABLE `plugin_interview_items` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `fk_interview_id` int(10) unsigned NOT NULL,
  `fk_questioneer_user_id` int(11) default NULL,
  `question` text NOT NULL,
  `status` enum('draft','pending','published','rejected') NOT NULL default 'draft',
  `answer` text NOT NULL,
  `item_order` int(10) unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

