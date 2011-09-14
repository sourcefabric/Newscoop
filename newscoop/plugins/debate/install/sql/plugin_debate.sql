-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate`
-- 

CREATE TABLE `plugin_debate` (
  `debate_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `parent_debate_nr` int(11) NOT NULL,
  `is_extended` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `nr_of_answers` tinyint(3) unsigned NOT NULL default '0',
  `allow_not_logged_in` TINYINT( 1 ) NOT NULL DEFAULT '0',
  `votes_per_user` tinyint(3) unsigned NOT NULL default '1',
  `nr_of_votes` int(10) unsigned NOT NULL,
  `nr_of_votes_overall` int(10) unsigned NOT NULL,
  `percentage_of_votes_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `reset_token` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`debate_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate_answer`
-- 

CREATE TABLE `plugin_debate_answer` (
  `fk_debate_nr` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `nr_answer` tinyint(3) unsigned NOT NULL default '0' AUTO_INCREMENT,
  `answer` varchar(255) NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL default '0',
  `percentage` float unsigned NOT NULL,
  `percentage_overall` float unsigned NOT NULL,
  `value` int(11) NOT NULL,
  `average_value` float NOT NULL,
  `on_hitlist` tinyint(4) NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  UNIQUE KEY `Nrdebate` (`fk_debate_nr`,`fk_language_id`,`nr_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate_article`
-- 

CREATE TABLE `plugin_debate_article` (
  `fk_debate_nr` int(10) unsigned NOT NULL default '0',
  `fk_article_nr` int(10) unsigned NOT NULL default '0',
  `fk_article_language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_debate_nr`,`fk_article_nr`,`fk_article_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate_issue`
-- 

CREATE TABLE `plugin_debate_issue` (
  `fk_debate_nr` int(10) unsigned NOT NULL default '0',
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_issue_language_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_debate_nr`,`fk_issue_nr`,`fk_issue_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate_publication`
-- 

CREATE TABLE `plugin_debate_publication` (
  `fk_debate_nr` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_debate_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debate_section`
-- 

CREATE TABLE `plugin_debate_section` (
  `fk_debate_nr` int(10) unsigned NOT NULL default '0',
  `fk_section_nr` int(10) unsigned NOT NULL default '0',
  `fk_section_language_id` int(10) unsigned NOT NULL,
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_debate_nr`,`fk_section_nr`,`fk_section_language_id`,`fk_issue_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for `plugin_debateanswer_attachment`
-- 

CREATE TABLE `plugin_debateanswer_attachment` (
  `fk_debate_nr` int(11) NOT NULL,
  `fk_debateanswer_nr` int(11) NOT NULL,
  `fk_attachment_id` int(11) NOT NULL,
  PRIMARY KEY  (`fk_debate_nr`,`fk_debateanswer_nr`,`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
