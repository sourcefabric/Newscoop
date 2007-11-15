-- MySQL dump 10.11
--
-- Host: localhost    Database: campsite_3_0_plugins
-- ------------------------------------------------------
-- Server version	5.0.32-Debian_7etch1-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `plugin_poll`
--

DROP TABLE IF EXISTS `plugin_poll`;
CREATE TABLE `plugin_poll` (
  `poll_nr` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `date_begin` date NOT NULL default '0000-00-00',
  `date_end` date NOT NULL default '0000-00-00',
  `nr_of_answers` tinyint(3) unsigned NOT NULL default '0',
  `is_show_after_expiration` tinyint(3) unsigned NOT NULL default '0',
  `is_used_as_default` tinyint(3) unsigned NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL,
  `nr_of_votes_overall` int(10) unsigned NOT NULL,
  `percentage_of_votes_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`poll_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `plugin_poll_answer`
--

DROP TABLE IF EXISTS `plugin_poll_answer`;
CREATE TABLE `plugin_poll_answer` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `nr_answer` tinyint(3) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL default '0',
  `percentage` float unsigned NOT NULL,
  `percentage_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  UNIQUE KEY `NrPoll` (`fk_poll_nr`,`fk_language_id`,`nr_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `plugin_poll_article`
--

DROP TABLE IF EXISTS `plugin_poll_article`;
CREATE TABLE `plugin_poll_article` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_poll_language_id` int(10) unsigned NOT NULL default '0',
  `fk_article_nr` int(10) unsigned NOT NULL default '0',
  `fk_article_language_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `NrPoll` (`fk_poll_nr`,`fk_poll_language_id`,`fk_article_nr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `plugin_poll_issue`
--

DROP TABLE IF EXISTS `plugin_poll_issue`;
CREATE TABLE `plugin_poll_issue` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_poll_language_id` int(10) unsigned NOT NULL default '0',
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_issue_language_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_poll_language_id`,`fk_issue_nr`,`fk_issue_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `plugin_poll_publication`
--

DROP TABLE IF EXISTS `plugin_poll_publication`;
CREATE TABLE `plugin_poll_publication` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_poll_language_id` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_poll_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `plugin_poll_section`
--

DROP TABLE IF EXISTS `plugin_poll_section`;
CREATE TABLE `plugin_poll_section` (
  `fk_poll_nr` int(10) unsigned NOT NULL default '0',
  `fk_poll_language_id` int(10) unsigned NOT NULL default '0',
  `fk_section_nr` int(10) unsigned NOT NULL default '0',
  `fk_section_language_id` int(10) unsigned NOT NULL,
  `fk_issue_nr` int(10) unsigned NOT NULL default '0',
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_poll_nr`,`fk_poll_language_id`,`fk_section_nr`,`fk_section_language_id`,`fk_issue_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-11-14 19:23:55


-- INSERT ROW FOR liveuser tables
INSERT INTO `liveuser_rights` ( `right_id` , `area_id` , `right_define_name` , `has_implied` ) 
VALUES ('71', '1', 'ManagePoll', '1');
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES ('1', '71', '3');
UPDATE `liveuser_rights_right_id_seq` SET `id` = '71' WHERE `liveuser_rights_right_id_seq`.`id` =70 LIMIT 1 ;

-- INSERT Polls section
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) 
VALUES (1, 1, 1, 90, 'Polls', '90', 0x506f6c6c73, 240, 0);
