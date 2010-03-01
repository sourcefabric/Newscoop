-- MySQL dump 10.11
--
-- Host: localhost    Database: campsite_3_3_5
-- ------------------------------------------------------
-- Server version	5.0.75-0ubuntu10.2

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
-- Table structure for table `plugin_blog_blog`
--

DROP TABLE IF EXISTS `plugin_blog_blog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_blog_blog` (
  `blog_id` int(10) unsigned NOT NULL auto_increment,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `info` text NOT NULL,
  `admin_remark` text NOT NULL,
  `request_text` text NOT NULL,
  `status` enum('online','offline','moderated') NOT NULL default 'online',
  `admin_status` enum('online','offline','moderated','readonly','pending') NOT NULL default 'pending',
  `entries_online` int(10) unsigned NOT NULL default '0',
  `entries_offline` int(10) unsigned NOT NULL default '0',
  `comments_online` int(10) unsigned NOT NULL,
  `comments_offline` int(10) unsigned NOT NULL,
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`blog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugin_blog_comment`
--

DROP TABLE IF EXISTS `plugin_blog_comment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_blog_comment` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `fk_entry_id` int(10) unsigned NOT NULL default '0',
  `fk_blog_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL default '0',
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fk_mood_id` varchar(255) NOT NULL,
  `status` enum('online','offline','pending') NOT NULL default 'pending',
  `admin_status` enum('online','offline','pending') NOT NULL default 'pending',
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugin_blog_entry`
--

DROP TABLE IF EXISTS `plugin_blog_entry`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_blog_entry` (
  `entry_id` int(10) unsigned NOT NULL auto_increment,
  `fk_blog_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL default '0',
  `date` datetime NOT NULL,
  `released` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fk_mood_id` int(10) unsigned NOT NULL,
  `status` enum('online','offline') NOT NULL default 'online',
  `admin_status` enum('online','offline','pending') NOT NULL default 'pending',
  `comments_online` int(10) unsigned NOT NULL default '0',
  `comments_offline` int(10) unsigned NOT NULL default '0',
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugin_blog_topic`
--

DROP TABLE IF EXISTS `plugin_blog_topic`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_blog_topic` (
  `fk_blog_id` int(10) unsigned NOT NULL,
  `fk_topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_blog_id`,`fk_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugin_poll`
--

DROP TABLE IF EXISTS `plugin_poll`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_poll` (
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
  PRIMARY KEY  (`poll_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `plugin_poll_answer`
--

DROP TABLE IF EXISTS `plugin_poll_answer`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plugin_poll_answer` (
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
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Xnews_article`
--

DROP TABLE IF EXISTS `Xnews_article`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Xnews_article` (
  `NrArticle` int(10) unsigned NOT NULL,
  `IdLanguage` int(10) unsigned NOT NULL,
  `FDeck` varchar(255) NOT NULL,
  `FLead_and_SMS` mediumblob,
  `Fbody` mediumblob NOT NULL,
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-02-15 17:57:36
