-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 23. April 2008 um 15:55
-- Server Version: 5.0.32
-- PHP-Version: 5.2.0-8+etch7
-- 
-- Datenbank: `campsite_30_poll`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll` (
  `poll_nr` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `parent_poll_nr` int(11) NOT NULL,
  `is_extended` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `nr_of_answers` tinyint(3) unsigned NOT NULL default '0',
  `votes_per_user` tinyint(3) unsigned NOT NULL default '1',
  `nr_of_votes` int(10) unsigned NOT NULL,
  `nr_of_votes_overall` int(10) unsigned NOT NULL,
  `percentage_of_votes_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reset_token` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`poll_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll_answer`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll_answer` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `nr_answer` tinyint(3) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL default '0',
  `percentage` float unsigned NOT NULL,
  `percentage_overall` float unsigned NOT NULL,
  `value` int(11) NOT NULL,
  `average_value` float NOT NULL,
  `on_hitlist` tinyint(4) NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  UNIQUE KEY `NrPoll` (`fk_poll_nr`,`fk_language_id`,`nr_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll_article`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll_article` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_article_nr` int(10) unsigned NOT NULL default '0',
  `fk_article_language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_poll_nr`,`fk_article_nr`,`fk_article_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll_issue`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll_issue` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_issue_language_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_issue_nr`,`fk_issue_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll_publication`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll_publication` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_poll_section`
-- 

CREATE TABLE IF NOT EXISTS `plugin_poll_section` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_section_nr` int(10) unsigned NOT NULL default '0',
  `fk_section_language_id` int(10) unsigned NOT NULL,
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_section_nr`,`fk_section_language_id`,`fk_issue_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur f�r Tabelle `plugin_pollanswer_attachment`
-- 

CREATE TABLE IF NOT EXISTS `plugin_pollanswer_attachment` (
  `fk_poll_nr` int(11) NOT NULL,
  `fk_pollanswer_nr` int(11) NOT NULL,
  `fk_attachment_id` int(11) NOT NULL,
  PRIMARY KEY  (`fk_poll_nr`,`fk_pollanswer_nr`,`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
