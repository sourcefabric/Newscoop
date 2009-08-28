-- MySQL dump 10.11
--
-- Host: localhost    Database: campsite_3_3_3_dev
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.1

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
-- Table structure for table `Aliases`
--

DROP TABLE IF EXISTS `Aliases`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Aliases` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(128) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Aliases`
--

LOCK TABLES `Aliases` WRITE;
/*!40000 ALTER TABLE `Aliases` DISABLE KEYS */;
INSERT INTO `Aliases` (`Id`, `Name`, `IdPublication`) VALUES (1,'set.your.site.alias',1);
/*!40000 ALTER TABLE `Aliases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAttachments`
--

DROP TABLE IF EXISTS `ArticleAttachments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleAttachments` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_attachment_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `article_attachment_index` (`fk_article_number`,`fk_attachment_id`),
  KEY `fk_article_number` (`fk_article_number`),
  KEY `fk_attachment_id` (`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleAttachments`
--

LOCK TABLES `ArticleAttachments` WRITE;
/*!40000 ALTER TABLE `ArticleAttachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticleAttachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAudioclips`
--

DROP TABLE IF EXISTS `ArticleAudioclips`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleAudioclips` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_audioclip_gunid` varchar(20) NOT NULL default '0',
  `fk_language_id` int(10) unsigned default NULL,
  `order_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fk_article_number`,`fk_audioclip_gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleAudioclips`
--

LOCK TABLES `ArticleAudioclips` WRITE;
/*!40000 ALTER TABLE `ArticleAudioclips` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticleAudioclips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAuthors`
--

DROP TABLE IF EXISTS `ArticleAuthors`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleAuthors` (
  `fk_article_number` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_author_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_article_number`,`fk_language_id`,`fk_author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleAuthors`
--

LOCK TABLES `ArticleAuthors` WRITE;
/*!40000 ALTER TABLE `ArticleAuthors` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticleAuthors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleComments`
--

DROP TABLE IF EXISTS `ArticleComments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleComments` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `fk_comment_id` int(10) unsigned NOT NULL default '0',
  `is_first` tinyint(1) NOT NULL default '0',
  KEY `fk_comment_id` (`fk_comment_id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `first_message_index` (`fk_article_number`,`fk_language_id`,`is_first`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleComments`
--

LOCK TABLES `ArticleComments` WRITE;
/*!40000 ALTER TABLE `ArticleComments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticleComments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleImages`
--

DROP TABLE IF EXISTS `ArticleImages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleImages` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdImage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`IdImage`),
  UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`),
  KEY `IdImage` (`IdImage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleImages`
--

LOCK TABLES `ArticleImages` WRITE;
/*!40000 ALTER TABLE `ArticleImages` DISABLE KEYS */;
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (4,98,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (47,95,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (48,99,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (14,106,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (12,97,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (1,93,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (18,111,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (49,105,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (50,110,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (51,114,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (20,94,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (13,102,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (32,101,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (35,104,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (38,108,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (44,89,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (41,113,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (28,91,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (28,90,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (12,96,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (32,100,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (35,103,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (38,107,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (41,112,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (7,113,2);
/*!40000 ALTER TABLE `ArticleImages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleIndex`
--

DROP TABLE IF EXISTS `ArticleIndex`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleIndex` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `IdKeyword` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `NrArticle` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPublication`,`IdLanguage`,`IdKeyword`,`NrIssue`,`NrSection`,`NrArticle`),
  UNIQUE KEY `article_keyword_idx` (`NrArticle`,`IdLanguage`,`IdKeyword`),
  KEY `keyword_idx` (`IdKeyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleIndex`
--

LOCK TABLES `ArticleIndex` WRITE;
/*!40000 ALTER TABLE `ArticleIndex` DISABLE KEYS */;
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,1,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,2,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,3,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,4,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,5,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,6,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,7,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,8,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,9,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,10,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,11,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,12,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,13,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,14,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,15,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,16,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,17,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,18,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,19,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,20,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,21,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,22,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,23,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,24,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,25,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,26,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,27,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,28,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,29,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,30,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,31,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,32,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,33,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,34,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,35,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,36,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,37,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,38,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,39,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,40,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,41,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,42,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,43,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,44,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,45,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,46,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,47,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,48,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,49,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,50,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,51,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,52,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,53,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,54,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,55,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,56,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,57,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,58,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,59,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,60,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,61,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,62,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,63,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,64,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,65,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,66,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,67,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,68,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,69,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,70,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,71,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,72,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,73,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,74,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,75,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,76,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,77,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,78,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,79,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,80,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,81,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,82,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,83,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,84,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,85,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,86,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,87,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,88,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,89,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,90,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,91,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,92,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,93,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,94,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,95,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,96,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,97,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,98,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,99,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,100,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,101,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,10,1);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,10,7);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,30,21);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,102,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,10,19);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,10,28);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,10,46);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,20,2);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,20,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,20,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,30,4);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,30,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,30,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,30,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,40,13);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,40,22);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,40,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,40,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,40,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,50,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,50,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,50,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,50,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,60,18);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,60,26);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,60,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,103,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,104,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,105,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,106,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,107,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,108,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,108,1,230,16);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,108,1,230,17);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,109,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,110,1,230,3);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,110,1,230,16);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,110,1,230,17);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,111,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,112,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,113,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,113,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,114,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,114,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,115,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,115,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,116,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,116,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,117,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,117,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,118,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,118,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,119,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,119,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,120,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,120,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,121,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,121,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,122,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,122,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,123,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,123,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,124,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,124,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,125,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,125,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,126,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,126,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,127,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,127,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,128,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,128,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,129,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,129,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,130,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,130,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,131,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,131,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,132,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,132,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,133,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,133,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,134,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,134,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,135,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,135,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,136,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,136,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,137,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,137,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,138,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,138,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,139,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,139,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,140,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,140,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,141,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,141,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,142,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,142,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,143,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,143,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,144,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,144,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,145,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,145,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,146,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,146,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,147,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,147,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,148,1,200,8);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,148,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,149,1,210,11);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,150,1,20,12);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,150,1,20,20);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,151,1,50,14);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,152,1,230,16);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,153,1,230,17);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,154,1,60,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,10,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,20,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,40,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,50,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (1,1,155,1,60,51);
/*!40000 ALTER TABLE `ArticleIndex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticlePublish`
--

DROP TABLE IF EXISTS `ArticlePublish`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticlePublish` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `time_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_action` enum('P','U') default NULL,
  `publish_on_front_page` enum('S','R') default NULL,
  `publish_on_section_page` enum('S','R') default NULL,
  `is_completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `event_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticlePublish`
--

LOCK TABLES `ArticlePublish` WRITE;
/*!40000 ALTER TABLE `ArticlePublish` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticlePublish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleTopics`
--

DROP TABLE IF EXISTS `ArticleTopics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleTopics` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `TopicId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`TopicId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleTopics`
--

LOCK TABLES `ArticleTopics` WRITE;
/*!40000 ALTER TABLE `ArticleTopics` DISABLE KEYS */;
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (2,2);
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (18,1);
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (20,2);
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (28,3);
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (39,3);
INSERT INTO `ArticleTopics` (`NrArticle`, `TopicId`) VALUES (40,3);
/*!40000 ALTER TABLE `ArticleTopics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleTypeMetadata`
--

DROP TABLE IF EXISTS `ArticleTypeMetadata`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ArticleTypeMetadata` (
  `type_name` varchar(166) NOT NULL default '',
  `field_name` varchar(166) NOT NULL default 'NULL',
  `field_weight` int(11) default NULL,
  `is_hidden` tinyint(1) NOT NULL default '0',
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `fk_phrase_id` int(10) unsigned default NULL,
  `field_type` varchar(255) default NULL,
  `field_type_param` varchar(255) default NULL,
  `is_content_field` tinyint(1) NOT NULL default '0',
  `max_size` int(10) unsigned default NULL,
  PRIMARY KEY  (`type_name`,`field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ArticleTypeMetadata`
--

LOCK TABLES `ArticleTypeMetadata` WRITE;
/*!40000 ALTER TABLE `ArticleTypeMetadata` DISABLE KEYS */;
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','NULL',NULL,0,1,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Deck',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Byline',2,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Teaser_a',3,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Teaser_b',4,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Intro',5,0,0,NULL,'body',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Full_text',6,0,0,NULL,'body',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Link','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Link','url',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Photo','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Service','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Service','Full_text',1,0,0,NULL,'body',NULL,0,NULL);
/*!40000 ALTER TABLE `ArticleTypeMetadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Articles`
--

DROP TABLE IF EXISTS `Articles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Articles` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Type` varchar(70) NOT NULL default '',
  `IdUser` int(10) unsigned NOT NULL default '0',
  `fk_default_author_id` int(10) unsigned default NULL,
  `OnFrontPage` enum('N','Y') NOT NULL default 'N',
  `OnSection` enum('N','Y') NOT NULL default 'N',
  `Published` enum('N','S','Y') NOT NULL default 'N',
  `PublishDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `UploadDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Keywords` varchar(255) NOT NULL default '',
  `Public` enum('N','Y') NOT NULL default 'N',
  `IsIndexed` enum('N','Y') NOT NULL default 'N',
  `LockUser` int(10) unsigned NOT NULL default '0',
  `LockTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ShortName` varchar(32) NOT NULL default '',
  `ArticleOrder` int(10) unsigned NOT NULL default '0',
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `comments_locked` tinyint(1) NOT NULL default '0',
  `time_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `object_id` int(11) default NULL,
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`NrSection`,`Number`,`IdLanguage`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Name`),
  UNIQUE KEY `Number` (`Number`,`IdLanguage`),
  UNIQUE KEY `other_key` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Number`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`ShortName`),
  KEY `Type` (`Type`),
  KEY `ArticleOrderIdx` (`ArticleOrder`),
  FULLTEXT KEY `articles_name_skey` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Articles`
--

LOCK TABLES `Articles` WRITE;
/*!40000 ALTER TABLE `Articles` DISABLE KEYS */;
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,1,1,'Lorem Ipsum dolor','Article',1,NULL,'Y','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','1',1,1,0,'2009-08-28 03:15:36',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,2,1,'Ipsum dolor','Article',1,NULL,'N','Y','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','2',3,1,0,'2009-08-28 03:15:36',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,230,3,1,'Media On The Web','Link',1,NULL,'N','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','3',3,0,0,'2009-08-28 03:15:36',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,4,1,'Lorem Ipsum','Article',1,NULL,'Y','N','Y','2005-08-16 00:00:00','2005-08-16 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','4',2,1,0,'2009-08-28 03:15:36',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,7,1,'Lorem dolor','Article',1,NULL,'Y','Y','Y','2005-08-17 00:00:00','2005-08-17 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','7',1,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,200,8,1,'About us','Service',1,NULL,'N','N','Y','2005-08-17 00:00:00','2005-08-17 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','8',8,0,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,48,1,'Photo','Photo',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','48',48,0,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,12,1,'Lorem Ipsum','Article',1,NULL,'N','N','Y','2005-08-18 00:00:00','2005-08-18 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','12',8,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,46,1,'Lorem val','Article',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','46',44,1,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,35,1,'Vestibulum luctus','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','35',34,1,0,'2009-08-28 03:15:40',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,210,11,1,'Contact','Service',1,NULL,'N','N','Y','2005-08-17 00:00:00','2005-08-17 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','11',8,0,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,44,1,'Photo','Photo',1,NULL,'Y','N','Y','2005-08-25 00:00:00','2005-08-25 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','44',41,0,0,'2009-08-28 03:15:41',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,18,1,'Lorem Dolor','Article',1,NULL,'Y','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','18',14,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,13,1,'Lorem Ipsum val','Article',1,NULL,'Y','N','Y','2005-08-18 00:00:00','2005-08-18 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','13',10,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,14,1,'Lorem lpsum','Article',1,NULL,'Y','N','Y','2005-08-18 00:00:00','2005-08-18 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','14',13,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,230,17,1,'Campware','Link',1,NULL,'N','N','Y','2005-08-18 00:00:00','2005-08-18 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','17',17,0,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,230,16,1,'MDLF','Link',1,NULL,'N','N','Y','2005-08-18 00:00:00','2005-08-18 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','16',16,0,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,19,1,'Lorem sit','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','19',43,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,20,1,'Lorem eu','Article',1,NULL,'Y','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','20',2,1,0,'2009-08-28 03:15:37',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,21,1,'Lorem dolor','Article',1,NULL,'N','Y','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','21',5,1,0,'2009-08-28 03:15:38',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,22,1,'Lorem Ipsum','Article',1,NULL,'N','Y','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','22',13,1,0,'2009-08-28 03:15:38',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,49,1,'Photo','Photo',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','49',49,0,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,24,1,'Lorem Dolor','Article',1,NULL,'N','Y','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','24',14,1,0,'2009-08-28 03:15:38',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,50,1,'Photo','Photo',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','50',50,0,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,26,1,'Ipsum Dolor','Article',1,NULL,'N','Y','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','26',18,1,0,'2009-08-28 03:15:38',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,51,1,'Photo','Photo',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','51',51,0,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,10,28,1,'Dolor val','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','28',42,1,0,'2009-08-28 03:15:38',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,47,1,'Photo','Photo',1,NULL,'N','N','Y','2005-08-26 00:00:00','2005-08-26 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','47',47,0,0,'2009-08-28 03:15:42',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,30,1,'Sodales mauris','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','30',30,1,0,'2009-08-28 03:15:39',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,20,31,1,'Mauris pellentesque','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','31',31,1,0,'2009-08-28 03:15:39',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,32,1,'Vestibulum luctus','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','32',29,1,0,'2009-08-28 03:15:39',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,33,1,'Quisque bibendum','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','33',32,1,0,'2009-08-28 03:15:39',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,30,34,1,'Mauris pellentesque','Article',1,NULL,'N','N','Y','2005-08-22 00:00:00','2005-08-22 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','34',33,1,0,'2009-08-28 03:15:40',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,36,1,'Maecenas porttitor','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','36',35,1,0,'2009-08-28 03:15:40',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,38,1,'Vestibulum ante','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','38',35,1,0,'2009-08-28 03:15:40',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,40,37,1,'Duis blandit','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','37',36,1,0,'2009-08-28 03:15:40',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,41,1,'Proin sit amet neque','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','41',38,1,0,'2009-08-28 03:15:41',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,39,1,'Duis sodales','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','39',38,1,0,'2009-08-28 03:15:41',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,50,40,1,'Fusce hendrerit','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','40',39,1,0,'2009-08-28 03:15:41',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,42,1,'Fusce hendrerit','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','42',41,1,0,'2009-08-28 03:15:41',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (1,1,60,43,1,'Vestibulum luctu','Article',1,NULL,'N','N','Y','2005-08-23 00:00:00','2005-08-23 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','43',42,1,0,'2009-08-28 03:15:41',NULL);
/*!40000 ALTER TABLE `Articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Attachments`
--

DROP TABLE IF EXISTS `Attachments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Attachments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_language_id` int(10) unsigned default NULL,
  `file_name` varchar(255) default NULL,
  `extension` varchar(50) default NULL,
  `mime_type` varchar(255) default NULL,
  `content_disposition` enum('attachment') default NULL,
  `http_charset` varchar(50) default NULL,
  `size_in_bytes` bigint(20) unsigned default NULL,
  `fk_description_id` int(11) default NULL,
  `fk_user_id` int(10) unsigned default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Attachments`
--

LOCK TABLES `Attachments` WRITE;
/*!40000 ALTER TABLE `Attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AudioclipMetadata`
--

DROP TABLE IF EXISTS `AudioclipMetadata`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `gunid` varchar(20) NOT NULL default '0',
  `predicate_ns` varchar(10) default '',
  `predicate` varchar(30) NOT NULL default '',
  `object` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`,`predicate_ns`,`predicate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `AudioclipMetadata`
--

LOCK TABLES `AudioclipMetadata` WRITE;
/*!40000 ALTER TABLE `AudioclipMetadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `AudioclipMetadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Authors`
--

DROP TABLE IF EXISTS `Authors`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Authors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `authors_name_ukey` (`first_name`,`last_name`),
  FULLTEXT KEY `authors_name_skey` (`first_name`,`last_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Authors`
--

LOCK TABLES `Authors` WRITE;
/*!40000 ALTER TABLE `Authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `Authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AutoId`
--

DROP TABLE IF EXISTS `AutoId`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AutoId` (
  `ArticleId` int(10) unsigned NOT NULL default '0',
  `LogTStamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `TopicId` int(10) unsigned NOT NULL default '0',
  `translation_phrase_id` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `AutoId`
--

LOCK TABLES `AutoId` WRITE;
/*!40000 ALTER TABLE `AutoId` DISABLE KEYS */;
INSERT INTO `AutoId` (`ArticleId`, `LogTStamp`, `TopicId`, `translation_phrase_id`) VALUES (51,'2005-08-26 14:31:38',3,1);
/*!40000 ALTER TABLE `AutoId` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Classes`
--

DROP TABLE IF EXISTS `Classes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Classes` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Classes`
--

LOCK TABLES `Classes` WRITE;
/*!40000 ALTER TABLE `Classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Countries`
--

DROP TABLE IF EXISTS `Countries`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Countries` (
  `Code` char(2) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Code`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Countries`
--

LOCK TABLES `Countries` WRITE;
/*!40000 ALTER TABLE `Countries` DISABLE KEYS */;
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AR',1,'Argentina');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AG',1,'Antigua And Barbuda');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AQ',1,'Antarctica');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AI',1,'Anguilla');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AO',1,'Angola');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AD',1,'Andorra');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AS',1,'American Samoa');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DZ',1,'Algeria');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AL',1,'Albania');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AF',1,'Afghanistan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AM',1,'Armenia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AW',1,'Aruba');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AU',1,'Australia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AT',1,'Austria');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AZ',1,'Azerbaijan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BS',1,'Bahamas');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BH',1,'Bahrain');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BD',1,'Bangladesh');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BB',1,'Barbados');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BY',1,'Belarus');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BE',1,'Belgium');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BZ',1,'Belize');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BJ',1,'Benin');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BM',1,'Bermuda');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BT',1,'Bhutan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BO',1,'Bolivia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BA',1,'Bosnia And Herzegovina');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BW',1,'Botswana');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BV',1,'Bouvet Island');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BR',1,'Brazil');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IO',1,'British Indian Ocean Territory');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BN',1,'Brunei Darussalam');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BG',1,'Bulgaria');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BF',1,'Burkina Faso');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('BI',1,'Burundi');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KH',1,'Cambodia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CM',1,'Cameroon');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CA',1,'Canada');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CV',1,'Cape Verde');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KY',1,'Cayman Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CF',1,'Central African Republic');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TD',1,'Chad');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CL',1,'Chile');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CN',1,'China');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CX',1,'Christmas Island');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CC',1,'Cocos (Keeling) Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CO',1,'Colombia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KM',1,'Comoros');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CG',1,'Congo');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CD',1,'Congo, The Democratic Republic Of The');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CK',1,'Cook Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CR',1,'Costa Rica');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CI',1,'Cote Divoire');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HR',1,'Croatia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CU',1,'Cuba');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CY',1,'Cyprus');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CZ',1,'Czech Republic');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DK',1,'Denmark');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DJ',1,'Djibouti');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DM',1,'Dominica');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DO',1,'Dominican Republic');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TP',1,'East Timor');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('EC',1,'Ecuador');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('EG',1,'Egypt');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SV',1,'El Salvador');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GQ',1,'Equatorial Guinea');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ER',1,'Eritrea');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('EE',1,'Estonia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ET',1,'Ethiopia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FK',1,'Falkland Islands (Malvinas)');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FO',1,'Faroe Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FJ',1,'Fiji');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FI',1,'Finland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FR',1,'France');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FX',1,'France, Metropolitan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GF',1,'French Guiana');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PF',1,'French Polynesia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TF',1,'French Southern Territories');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GA',1,'Gabon');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GM',1,'Gambia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GE',1,'Georgia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('DE',1,'Germany');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GH',1,'Ghana');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GI',1,'Gibraltar');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GR',1,'Greece');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GL',1,'Greenland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GD',1,'Grenada');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GP',1,'Guadeloupe');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GU',1,'Guam');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GT',1,'Guatemala');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GN',1,'Guinea');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GW',1,'Guinea-bissau');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GY',1,'Guyana');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HT',1,'Haiti');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HM',1,'Heard Island And Mcdonald Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VA',1,'Holy See (Vatican City State)');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HN',1,'Honduras');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HK',1,'Hong Kong');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('HU',1,'Hungary');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IS',1,'Iceland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IN',1,'India');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ID',1,'Indonesia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IR',1,'Iran, Islamic Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IQ',1,'Iraq');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IE',1,'Ireland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IL',1,'Israel');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('IT',1,'Italy');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('JM',1,'Jamaica');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('JP',1,'Japan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('JO',1,'Jordan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KZ',1,'Kazakstan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KE',1,'Kenya');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KI',1,'Kiribati');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KP',1,'Korea, Democratic Peoples Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KR',1,'Korea, Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KW',1,'Kuwait');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KG',1,'Kyrgyzstan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LA',1,'Lao Peoples Democratic Republic');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LV',1,'Latvia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LB',1,'Lebanon');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LS',1,'Lesotho');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LR',1,'Liberia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LY',1,'Libyan Arab Jamahiriya');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LI',1,'Liechtenstein');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LT',1,'Lithuania');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LU',1,'Luxembourg');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MO',1,'Macau');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MK',1,'Macedonia, The Former Yugoslav Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MG',1,'Madagascar');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MW',1,'Malawi');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MY',1,'Malaysia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MV',1,'Maldives');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ML',1,'Mali');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MT',1,'Malta');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MH',1,'Marshall Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MQ',1,'Martinique');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MR',1,'Mauritania');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MU',1,'Mauritius');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('YT',1,'Mayotte');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MX',1,'Mexico');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('FM',1,'Micronesia, Federated States Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MD',1,'Moldova, Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MC',1,'Monaco');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MN',1,'Mongolia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MS',1,'Montserrat');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MA',1,'Morocco');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MZ',1,'Mozambique');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MM',1,'Myanmar');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NA',1,'Namibia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NR',1,'Nauru');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NP',1,'Nepal');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NL',1,'Netherlands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AN',1,'Netherlands Antilles');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NC',1,'New Caledonia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NZ',1,'New Zealand');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NI',1,'Nicaragua');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NE',1,'Niger');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NG',1,'Nigeria');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NU',1,'Niue');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NF',1,'Norfolk Island');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('MP',1,'Northern Mariana Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('NO',1,'Norway');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('OM',1,'Oman');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PK',1,'Pakistan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PW',1,'Palau');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PS',1,'Palestinian Territory, Occupied');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PA',1,'Panama');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PG',1,'Papua New Guinea');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PY',1,'Paraguay');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PE',1,'Peru');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PH',1,'Philippines');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PN',1,'Pitcairn');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PL',1,'Poland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PT',1,'Portugal');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PR',1,'Puerto Rico');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('QA',1,'Qatar');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('RE',1,'Reunion');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('RO',1,'Romania');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('RU',1,'Russian Federation');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('RW',1,'Rwanda');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SH',1,'Saint Helena');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('KN',1,'Saint Kitts And Nevis');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LC',1,'Saint Lucia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('PM',1,'Saint Pierre And Miquelon');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VC',1,'Saint Vincent And The Grenadines');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('WS',1,'Samoa');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SM',1,'San Marino');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ST',1,'Sao Tome And Principe');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SA',1,'Saudi Arabia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SN',1,'Senegal');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CS',1,'Serbia and Montenegro');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SC',1,'Seychelles');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SL',1,'Sierra Leone');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SG',1,'Singapore');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SK',1,'Slovakia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SI',1,'Slovenia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SB',1,'Solomon Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SO',1,'Somalia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ZA',1,'South Africa');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GS',1,'South Georgia And The South Sandwich Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ES',1,'Spain');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('LK',1,'Sri Lanka');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SD',1,'Sudan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SR',1,'Suriname');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SJ',1,'Svalbard And Jan Mayen');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SZ',1,'Swaziland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SE',1,'Sweden');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('CH',1,'Switzerland');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('SY',1,'Syrian Arab Republic');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TW',1,'Taiwan, Province Of China');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TJ',1,'Tajikistan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TZ',1,'Tanzania, United Republic Of');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TH',1,'Thailand');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TG',1,'Togo');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TK',1,'Tokelau');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TO',1,'Tonga');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TT',1,'Trinidad And Tobago');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TN',1,'Tunisia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TR',1,'Turkey');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TM',1,'Turkmenistan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TC',1,'Turks And Caicos Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('TV',1,'Tuvalu');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('UG',1,'Uganda');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('UA',1,'Ukraine');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AE',1,'United Arab Emirates');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('GB',1,'United Kingdom');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('US',1,'United States');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('UM',1,'United States Minor Outlying Islands');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('UY',1,'Uruguay');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('UZ',1,'Uzbekistan');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VU',1,'Vanuatu');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VE',1,'Venezuela');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VN',1,'Vietnam');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VG',1,'Virgin Islands, British');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('VI',1,'Virgin Islands, U.S.');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('WF',1,'Wallis And Futuna');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('EH',1,'Western Sahara');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('YE',1,'Yemen');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ZM',1,'Zambia');
INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('ZW',1,'Zimbabwe');
/*!40000 ALTER TABLE `Countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Dictionary`
--

DROP TABLE IF EXISTS `Dictionary`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Dictionary` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Keyword` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`IdLanguage`,`Keyword`),
  UNIQUE KEY `Id` (`Id`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Dictionary`
--

LOCK TABLES `Dictionary` WRITE;
/*!40000 ALTER TABLE `Dictionary` DISABLE KEYS */;
/*!40000 ALTER TABLE `Dictionary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EnumerationElements`
--

DROP TABLE IF EXISTS `EnumerationElements`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `EnumerationElements` (
  `fk_enumeration_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `fk_phrase_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fk_enumeration_id`,`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `EnumerationElements`
--

LOCK TABLES `EnumerationElements` WRITE;
/*!40000 ALTER TABLE `EnumerationElements` DISABLE KEYS */;
/*!40000 ALTER TABLE `EnumerationElements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Enumerations`
--

DROP TABLE IF EXISTS `Enumerations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Enumerations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Enumerations`
--

LOCK TABLES `Enumerations` WRITE;
/*!40000 ALTER TABLE `Enumerations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Enumerations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Errors`
--

DROP TABLE IF EXISTS `Errors`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Errors` (
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Message` char(255) NOT NULL default '',
  PRIMARY KEY  (`Number`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Errors`
--

LOCK TABLES `Errors` WRITE;
/*!40000 ALTER TABLE `Errors` DISABLE KEYS */;
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4000,1,'Internal error.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4001,1,'Username not specified.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4002,1,'Invalid username.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4003,1,'Password not specified.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4004,1,'Invalid password.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2000,1,'Internal error');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2001,1,'Username is not specified. Please fill out login name field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2002,1,'You are not a reader.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2003,1,'Publication not specified.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2004,1,'There are other subscriptions not payed.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (2005,1,'Time unit not specified.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3000,1,'Internal error.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3001,1,'Username already exists.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3002,1,'Name is not specified. Please fill out name field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3003,1,'Username is not specified. Please fill out login name field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3004,1,'Password is not specified. Please fill out password field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3005,1,'EMail is not specified. Please fill out EMail field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3006,1,'EMail address already exists. Please try to login with your old account.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3007,1,'Invalid user identifier');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3008,1,'No country specified. Please select a country.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3009,1,'Password (again) is not specified. Please fill out password (again) field.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3010,1,'Passwords do not match. Please fill out the same password to both password fields.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (3011,1,'Password is too simple. Please choose a better password (at least 6 characters).');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5009,1,'The code you entered is not the same with the one shown in the image.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5008,1,'Please enter the code shown in the image.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5007,1,'EMail field is empty. You must fill in your EMail address.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5006,1,'The comment was rejected by the spam filters.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5005,1,'You are banned from submitting comments.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5004,1,'Comments are not enabled for this publication/article.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5003,1,'The article was not selected. You must view an article in order to post comments.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5002,1,'The comment content was empty.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5001,1,'You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.');
INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (5000,1,'There was an internal error when submitting the comment.');
/*!40000 ALTER TABLE `Errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Events` (
  `Id` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Notify` enum('N','Y') NOT NULL default 'N',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Events`
--

LOCK TABLES `Events` WRITE;
/*!40000 ALTER TABLE `Events` DISABLE KEYS */;
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (1,'Add Publication','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (2,'Delete Publication','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (11,'Add Issue','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (12,'Delete Issue','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (13,'Change Issue Template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (14,'Change issue status','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (15,'Add Issue Translation','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (21,'Add Section','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (22,'Delete section','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (31,'Add Article','Y',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (32,'Delete article','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (33,'Change article field','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (34,'Change article properties','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (35,'Change article status','Y',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (41,'Add Image','Y',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (42,'Delete image','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (43,'Change image properties','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (51,'Add User','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (52,'Delete User','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (53,'Changes Own Password','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (54,'Change User Password','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (55,'Change User Permissions','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (56,'Change user information','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (61,'Add article type','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (62,'Delete article type','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (71,'Add article type field','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (72,'Delete article type field','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (81,'Add dictionary class','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (82,'Delete dictionary class','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (91,'Add dictionary keyword','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (92,'Delete dictionary keyword','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (101,'Add language','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (102,'Delete language','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (103,'Modify language','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (112,'Delete templates','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (111,'Add templates','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (121,'Add user type','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (122,'Delete user type','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (123,'Change user type','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (3,'Change publication information','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (36,'Change article template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (57,'Add IP Group','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (58,'Delete IP Group','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (131,'Add country','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (132,'Add country translation','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (133,'Change country name','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (134,'Delete country','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (4,'Add default subscription time','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (5,'Delete default subscription time','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (6,'Change default subscription time','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (113,'Edit template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (114,'Create template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (115,'Duplicate template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (141,'Add topic','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (142,'Delete topic','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (143,'Update topic','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (144,'Add topic to article','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (145,'Delete topic from article','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (151,'Add alias','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (152,'Delete alias','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (153,'Update alias','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (154,'Duplicate section','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (155,'Duplicate article','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (161,'Sync campsite and phorum users','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (171,'Change system preferences','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (116,'Rename Template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (117,'Move Template','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (37,'Edit article content','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (38,'Add file to article','N',1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (39,'Delete file from article','N',1);
/*!40000 ALTER TABLE `Events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FailedLoginAttempts`
--

DROP TABLE IF EXISTS `FailedLoginAttempts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `FailedLoginAttempts` (
  `ip_address` varchar(40) NOT NULL default '',
  `time_of_attempt` bigint(20) NOT NULL default '0',
  KEY `ip_address` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `FailedLoginAttempts`
--

LOCK TABLES `FailedLoginAttempts` WRITE;
/*!40000 ALTER TABLE `FailedLoginAttempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `FailedLoginAttempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Images` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Description` varchar(255) NOT NULL default '',
  `Photographer` varchar(255) NOT NULL default '',
  `Place` varchar(255) NOT NULL default '',
  `Caption` varchar(255) NOT NULL default '',
  `Date` date NOT NULL default '0000-00-00',
  `ContentType` varchar(64) NOT NULL default '',
  `Location` enum('local','remote') NOT NULL default 'local',
  `URL` varchar(255) NOT NULL default '',
  `ThumbnailFileName` varchar(50) NOT NULL default '',
  `ImageFileName` varchar(50) NOT NULL default '',
  `UploadedByUser` int(11) default NULL,
  `LastModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `TimeCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Images`
--

LOCK TABLES `Images` WRITE;
/*!40000 ALTER TABLE `Images` DISABLE KEYS */;
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (102,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000102.jpg','cms-image-000000102.jpg',1,'2005-08-26 20:45:54','2005-08-26 20:45:54');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (97,'Ipsum','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000097.jpg','cms-image-000000097.jpg',1,'2005-08-26 20:39:32','2005-08-26 20:39:21');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (98,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000098.jpg','cms-image-000000098.jpg',1,'2005-08-26 20:40:59','2005-08-26 20:40:59');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (110,'Image 110','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000110.jpg','cms-image-000000110.jpg',1,'2005-08-26 20:55:31','2005-08-26 20:55:31');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (111,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000111.jpg','cms-image-000000111.jpg',1,'2005-08-26 20:56:52','2005-08-26 20:56:51');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (106,'Dolores','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000106.jpg','cms-image-000000106.jpg',1,'2005-08-26 20:50:25','2005-08-26 20:50:25');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (99,'Image 99','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000099.jpg','cms-image-000000099.jpg',1,'2005-08-26 20:41:51','2005-08-26 20:41:51');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (96,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000096.jpg','cms-image-000000096.jpg',1,'2005-08-26 21:46:51','2005-08-26 20:39:10');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (101,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000101.jpg','cms-image-000000101.jpg',1,'2005-08-26 20:44:58','2005-08-26 20:44:57');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (100,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000100.jpg','cms-image-000000100.jpg',1,'2005-08-26 21:47:23','2005-08-26 20:44:41');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (105,'Image 105','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000105.jpg','cms-image-000000105.jpg',1,'2005-08-26 20:49:28','2005-08-26 20:49:28');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (104,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000104.jpg','cms-image-000000104.jpg',1,'2005-08-26 21:47:48','2005-08-26 20:48:37');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (103,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000103.jpg','cms-image-000000103.jpg',1,'2005-08-26 20:47:24','2005-08-26 20:47:16');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (108,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000108.jpg','cms-image-000000108.jpg',1,'2005-08-26 20:53:24','2005-08-26 20:53:24');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (107,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000107.jpg','cms-image-000000107.jpg',1,'2005-08-26 21:48:08','2005-08-26 20:53:09');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (114,'Image 114','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000114.jpg','cms-image-000000114.jpg',1,'2005-08-26 20:59:47','2005-08-26 20:59:47');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (113,'Dolores','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000113.jpg','cms-image-000000113.jpg',1,'2005-08-26 20:58:30','2005-08-26 20:58:30');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (112,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000112.jpg','cms-image-000000112.jpg',1,'2005-08-26 21:48:26','2005-08-26 20:58:17');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (93,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000093.jpg','cms-image-000000093.jpg',1,'2005-08-26 20:35:51','2005-08-26 20:35:51');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (95,'Image 95','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000095.jpg','cms-image-000000095.jpg',1,'2005-08-26 20:37:41','2005-08-26 20:37:41');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (94,'Dolor','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000094.jpg','cms-image-000000094.jpg',1,'2005-08-26 20:37:02','2005-08-26 20:37:02');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (89,'Image 89','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000089.jpg','cms-image-000000089.jpg',1,'2005-08-26 20:25:56','2005-08-26 20:25:54');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (90,'Lorem Ipsum Dolor val sit amet','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000090.jpg','cms-image-000000090.jpg',1,'2005-08-26 21:46:31','2005-08-26 20:27:36');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (91,'Ipsum','Administrator','','','2005-08-26','image/jpeg','local','','cms-thumb-000000091.jpg','cms-image-000000091.jpg',1,'2005-08-26 20:27:50','2005-08-26 20:27:49');
/*!40000 ALTER TABLE `Images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssuePublish`
--

DROP TABLE IF EXISTS `IssuePublish`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `IssuePublish` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_publication_id` int(10) unsigned NOT NULL default '0',
  `fk_issue_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `time_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_action` enum('P','U') NOT NULL default 'P',
  `do_publish_articles` enum('N','Y') NOT NULL default 'Y',
  `is_completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `issue_index` (`fk_publication_id`,`fk_issue_id`,`fk_language_id`),
  KEY `action_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `IssuePublish`
--

LOCK TABLES `IssuePublish` WRITE;
/*!40000 ALTER TABLE `IssuePublish` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssuePublish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Issues`
--

DROP TABLE IF EXISTS `Issues`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Issues` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `PublicationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Published` enum('N','Y') NOT NULL default 'N',
  `IssueTplId` int(10) unsigned default NULL,
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  `ShortName` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`IdPublication`,`Number`,`IdLanguage`),
  UNIQUE KEY `ShortName` (`IdPublication`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Issues`
--

LOCK TABLES `Issues` WRITE;
/*!40000 ALTER TABLE `Issues` DISABLE KEYS */;
INSERT INTO `Issues` (`IdPublication`, `Number`, `IdLanguage`, `Name`, `PublicationDate`, `Published`, `IssueTplId`, `SectionTplId`, `ArticleTplId`, `ShortName`) VALUES (1,1,1,'First issue','2005-08-15 00:00:00','Y',15,1,4,'first');
/*!40000 ALTER TABLE `Issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `KeywordClasses`
--

DROP TABLE IF EXISTS `KeywordClasses`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `KeywordClasses` (
  `IdDictionary` int(10) unsigned NOT NULL default '0',
  `IdClasses` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Definition` mediumblob NOT NULL,
  PRIMARY KEY  (`IdDictionary`,`IdClasses`,`IdLanguage`),
  KEY `IdClasses` (`IdClasses`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `KeywordClasses`
--

LOCK TABLES `KeywordClasses` WRITE;
/*!40000 ALTER TABLE `KeywordClasses` DISABLE KEYS */;
/*!40000 ALTER TABLE `KeywordClasses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `KeywordIndex`
--

DROP TABLE IF EXISTS `KeywordIndex`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `KeywordIndex` (
  `Keyword` varchar(70) NOT NULL default '',
  `Id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `KeywordIndex`
--

LOCK TABLES `KeywordIndex` WRITE;
/*!40000 ALTER TABLE `KeywordIndex` DISABLE KEYS */;
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Lorem',1);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Ipsum',2);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dolor',3);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Lipsum',4);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Fringilla',5);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vitae',6);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Duis',7);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('wisi',8);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mauris',9);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pellentesque',10);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sit',11);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('amet',12);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('consectetuer',13);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adipiscing',14);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('elit',15);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mollis',16);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nec',17);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('varius',18);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sodales',19);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Quisque',20);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('bibendum',21);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('gravida',22);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ligula',23);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Cras',24);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('elementum',25);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nisl',26);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('in',27);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tempor',28);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dapibus',29);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sapien',30);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('erat',31);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('faucibus',32);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eu',33);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tempus',34);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eros',35);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('quam',36);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Donec',37);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lobortis',38);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tellus',39);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('posuere',40);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('rhoncus',41);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Proin',42);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('neque',43);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ante',44);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('blandit',45);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('enim',46);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('imperdiet',47);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tincidunt',48);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Maecenas',49);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('porttitor',50);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vestibulum',51);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('euismod',52);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('malesuada',53);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nibh',54);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Sed',55);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('augue',56);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lectus',57);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('interdum',58);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ullamcorper',59);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('quis',60);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('volutpat',61);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('scelerisque',62);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('luctus',63);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Fusce',64);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('metus',65);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pede',66);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('laoreet',67);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tristique',68);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mi',69);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hendrerit',70);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lacus',71);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('non',72);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('rutrum',73);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pharetra',74);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ac',75);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('leo',76);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('purus',77);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Curabitur',78);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fermentum',79);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('magna',80);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('commodo',81);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Praesent',82);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('auctor',83);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('accumsan',84);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Nulla',85);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('facilisi',86);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('massa',87);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('justo',88);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('et',89);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Nullam',90);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('placerat',91);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('orci',92);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pretium',93);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('odio',94);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('molestie',95);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('congue',96);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mattis',97);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('consequat',98);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('arcu',99);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('facilisis',100);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ut',101);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sem',102);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('val',103);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Media',104);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('On',105);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('The',106);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Web',107);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('www',108);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mediaonweb',109);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('org',110);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('About',111);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('us',112);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eleifend',113);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vel',114);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('risus',115);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('at',116);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vehicula',117);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('semper',118);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('venenatis',119);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('id',120);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('feugiat',121);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nunc',122);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('est',123);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Etiam',124);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('porta',125);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Integer',126);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('urna',127);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('felis',128);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('aliquam',129);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('iaculis',130);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ultricies',131);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Phasellus',132);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eget',133);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dictum',134);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('primis',135);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ultrices',136);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('cubilia',137);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Curae',138);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('turpis',139);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('egestas',140);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dui',141);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Nam',142);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('aliquet',143);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Morbi',144);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('diam',145);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('suscipit',146);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tortor',147);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vivamus',148);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Contact',149);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('aecenas',150);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lpsum',151);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('MDLF',152);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Campware',153);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('luctu',154);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Photo',155);
/*!40000 ALTER TABLE `KeywordIndex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Languages` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(140) NOT NULL default '',
  `CodePage` varchar(140) NOT NULL default '',
  `OrigName` varchar(140) NOT NULL default '',
  `Code` varchar(21) NOT NULL default '',
  `Month1` varchar(140) NOT NULL default '',
  `Month2` varchar(140) NOT NULL default '',
  `Month3` varchar(140) NOT NULL default '',
  `Month4` varchar(140) NOT NULL default '',
  `Month5` varchar(140) NOT NULL default '',
  `Month6` varchar(140) NOT NULL default '',
  `Month7` varchar(140) NOT NULL default '',
  `Month8` varchar(140) NOT NULL default '',
  `Month9` varchar(140) NOT NULL default '',
  `Month10` varchar(140) NOT NULL default '',
  `Month11` varchar(140) NOT NULL default '',
  `Month12` varchar(140) NOT NULL default '',
  `WDay1` varchar(140) NOT NULL default '',
  `WDay2` varchar(140) NOT NULL default '',
  `WDay3` varchar(140) NOT NULL default '',
  `WDay4` varchar(140) NOT NULL default '',
  `WDay5` varchar(140) NOT NULL default '',
  `WDay6` varchar(140) NOT NULL default '',
  `WDay7` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Languages`
--

LOCK TABLES `Languages` WRITE;
/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (9,'Portuguese','ISO_8859-1','Português','pt','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (12,'French','ISO_8859-1','Français','fr','Janvier','Février','Mars','Avril','Peut','Juin','Juli','Août','Septembre','Octobre','Novembre','Décembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (13,'Spanish','ISO_8859-1','Español','es','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (2,'Romanian','ISO_8859-2','Română','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (7,'Croatian','ISO_8859-2','Hrvatski','hr','Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (8,'Czech','ISO_8859-2','Český','cz','Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec','Neděle','Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (11,'Serbo-Croatian','ISO_8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (10,'Serbian (Cyrillic)','ISO_8859-5','Српски (Ћирилица)','sr','јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (15,'Russian','ISO_8859-5','Русский','ru','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','воскресенье','понедельник','вторник','среда','четверг','пятница','суббота');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (18,'Swedish','','Svenska','sv','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','söndag','måndag','tisdag','onsdag','torsdag','fredag','lördag');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (16,'Chinese','UTF-8','中文','zh','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','星期','星期','星期','星期','星期','星期','星期');
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (17,'Arabic','UTF-8','عربي','ar','كانون \nالثاني','شباط','آذار','نيسان','آيار','حزيران','تموز','آب','أيلول','تشرين \nأول','تشرين الثاني','كانون \nأول','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت');
/*!40000 ALTER TABLE `Languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Log` (
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `fk_event_id` int(10) unsigned NOT NULL default '0',
  `fk_user_id` int(10) unsigned default NULL,
  `text` varchar(255) NOT NULL default '',
  `user_ip` int(10) unsigned default NULL,
  KEY `IdEvent` (`fk_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Log`
--

LOCK TABLES `Log` WRITE;
/*!40000 ALTER TABLE `Log` DISABLE KEYS */;
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-14 15:36:32',54,0,'Password changed for Administrator (admin)',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-14 15:37:32',3,1,'Publication Dynamic (1) changed',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-14 15:44:24',42,1,'Image 113 linked to article 7',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-14 15:44:39',43,1,'Changed image properties of 113',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-24 22:03:39',3,1,'Publication My Publication (1) changed',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2009-08-27 22:11:20',153,1,'The site alias for publication \"My Publication\" has been modified to \"172.16.0.2\".',2886729730);
/*!40000 ALTER TABLE `Log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ObjectTypes`
--

DROP TABLE IF EXISTS `ObjectTypes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ObjectTypes` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `OBJECTTYPES_NAME` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ObjectTypes`
--

LOCK TABLES `ObjectTypes` WRITE;
/*!40000 ALTER TABLE `ObjectTypes` DISABLE KEYS */;
INSERT INTO `ObjectTypes` (`id`, `name`) VALUES (1,'article');
/*!40000 ALTER TABLE `ObjectTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Plugins`
--

DROP TABLE IF EXISTS `Plugins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Plugins` (
  `Name` varchar(255) NOT NULL,
  `Version` varchar(255) NOT NULL,
  `Enabled` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Plugins`
--

LOCK TABLES `Plugins` WRITE;
/*!40000 ALTER TABLE `Plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `Plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publications`
--

DROP TABLE IF EXISTS `Publications`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Publications` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `IdDefaultLanguage` int(10) unsigned NOT NULL default '0',
  `TimeUnit` enum('D','W','M','Y') NOT NULL default 'D',
  `UnitCost` float(10,2) unsigned NOT NULL default '0.00',
  `UnitCostAllLang` float(10,2) unsigned NOT NULL default '0.00',
  `Currency` varchar(140) NOT NULL default '',
  `TrialTime` int(10) unsigned NOT NULL default '0',
  `PaidTime` int(10) unsigned NOT NULL default '0',
  `IdDefaultAlias` int(10) unsigned NOT NULL default '0',
  `IdURLType` int(10) unsigned NOT NULL default '1',
  `fk_forum_id` int(11) default NULL,
  `comments_enabled` tinyint(1) NOT NULL default '0',
  `comments_article_default_enabled` tinyint(1) NOT NULL default '0',
  `comments_subscribers_moderated` tinyint(1) NOT NULL default '0',
  `comments_public_moderated` tinyint(1) NOT NULL default '0',
  `comments_captcha_enabled` tinyint(1) NOT NULL default '0',
  `comments_spam_blocking_enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Alias` (`IdDefaultAlias`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Publications`
--

LOCK TABLES `Publications` WRITE;
/*!40000 ALTER TABLE `Publications` DISABLE KEYS */;
INSERT INTO `Publications` (`Id`, `Name`, `IdDefaultLanguage`, `TimeUnit`, `UnitCost`, `UnitCostAllLang`, `Currency`, `TrialTime`, `PaidTime`, `IdDefaultAlias`, `IdURLType`, `fk_forum_id`, `comments_enabled`, `comments_article_default_enabled`, `comments_subscribers_moderated`, `comments_public_moderated`, `comments_captcha_enabled`, `comments_spam_blocking_enabled`) VALUES (1,'My Publication',1,'M',1.00,2.00,'USD',1,1,1,2,1,1,0,0,0,1,0);
/*!40000 ALTER TABLE `Publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RequestObjects`
--

DROP TABLE IF EXISTS `RequestObjects`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RequestObjects` (
  `object_id` int(11) NOT NULL auto_increment,
  `object_type_id` int(11) NOT NULL,
  `request_count` int(11) NOT NULL,
  `last_update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `RequestObjects`
--

LOCK TABLES `RequestObjects` WRITE;
/*!40000 ALTER TABLE `RequestObjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `RequestObjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RequestStats`
--

DROP TABLE IF EXISTS `RequestStats`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `RequestStats` (
  `object_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `request_count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`date`,`hour`),
  KEY `stats_object_idx` (`object_id`),
  KEY `stats_object_date_idx` (`object_id`,`date`),
  KEY `stats_object_hour_idx` (`object_id`,`hour`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `RequestStats`
--

LOCK TABLES `RequestStats` WRITE;
/*!40000 ALTER TABLE `RequestStats` DISABLE KEYS */;
/*!40000 ALTER TABLE `RequestStats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Requests`
--

DROP TABLE IF EXISTS `Requests`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Requests` (
  `session_id` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL,
  `last_stats_update` datetime NOT NULL,
  PRIMARY KEY  (`session_id`,`object_id`),
  KEY `requests_session_idx` (`session_id`),
  KEY `requests_object_idx` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Requests`
--

LOCK TABLES `Requests` WRITE;
/*!40000 ALTER TABLE `Requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `Requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sections`
--

DROP TABLE IF EXISTS `Sections`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Sections` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ShortName` varchar(32) NOT NULL default '',
  `Description` blob,
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`IdLanguage`,`Number`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`IdLanguage`,`Name`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Sections`
--

LOCK TABLES `Sections` WRITE;
/*!40000 ALTER TABLE `Sections` DISABLE KEYS */;
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,10,'News','news',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,20,'Culture','culture',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,30,'Economy','economy',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,40,'Education','education',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,50,'Politics','politics',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,60,'Sport','sport',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,200,'About us','about',NULL,38,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,210,'Contact','contact',NULL,38,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,220,'Archive','archive',NULL,35,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (1,1,1,230,'Links','links',NULL,NULL,NULL);
/*!40000 ALTER TABLE `Sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sessions`
--

DROP TABLE IF EXISTS `Sessions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Sessions` (
  `id` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Sessions`
--

LOCK TABLES `Sessions` WRITE;
/*!40000 ALTER TABLE `Sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `Sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SubsByIP`
--

DROP TABLE IF EXISTS `SubsByIP`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SubsByIP` (
  `IdUser` int(10) unsigned NOT NULL default '0',
  `StartIP` int(10) unsigned NOT NULL default '0',
  `Addresses` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdUser`,`StartIP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `SubsByIP`
--

LOCK TABLES `SubsByIP` WRITE;
/*!40000 ALTER TABLE `SubsByIP` DISABLE KEYS */;
/*!40000 ALTER TABLE `SubsByIP` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SubsDefTime`
--

DROP TABLE IF EXISTS `SubsDefTime`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SubsDefTime` (
  `CountryCode` char(21) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `TrialTime` int(10) unsigned NOT NULL default '0',
  `PaidTime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CountryCode`,`IdPublication`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `SubsDefTime`
--

LOCK TABLES `SubsDefTime` WRITE;
/*!40000 ALTER TABLE `SubsDefTime` DISABLE KEYS */;
/*!40000 ALTER TABLE `SubsDefTime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SubsSections`
--

DROP TABLE IF EXISTS `SubsSections`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SubsSections` (
  `IdSubscription` int(10) unsigned NOT NULL default '0',
  `SectionNumber` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) NOT NULL default '0',
  `StartDate` date NOT NULL default '0000-00-00',
  `Days` int(10) unsigned NOT NULL default '0',
  `PaidDays` int(10) unsigned NOT NULL default '0',
  `NoticeSent` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdSubscription`,`SectionNumber`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `SubsSections`
--

LOCK TABLES `SubsSections` WRITE;
/*!40000 ALTER TABLE `SubsSections` DISABLE KEYS */;
/*!40000 ALTER TABLE `SubsSections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Subscriptions`
--

DROP TABLE IF EXISTS `Subscriptions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Subscriptions` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdUser` int(10) unsigned NOT NULL default '0',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `Active` enum('Y','N') NOT NULL default 'Y',
  `ToPay` float(10,2) unsigned NOT NULL default '0.00',
  `Currency` varchar(70) NOT NULL default '',
  `Type` enum('T','P') NOT NULL default 'T',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `IdUser` (`IdUser`,`IdPublication`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Subscriptions`
--

LOCK TABLES `Subscriptions` WRITE;
/*!40000 ALTER TABLE `Subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `Subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SystemPreferences`
--

DROP TABLE IF EXISTS `SystemPreferences`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SystemPreferences` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `varname` varchar(100) NOT NULL default '',
  `value` varchar(100) default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `SystemPreferences`
--

LOCK TABLES `SystemPreferences` WRITE;
/*!40000 ALTER TABLE `SystemPreferences` DISABLE KEYS */;
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (1,'ExternalSubscriptionManagement','N','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (2,'KeywordSeparator',',','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (3,'LoginFailedAttemptsNum','3','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (4,'MaxUploadFileSize','40M','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (5,'UseDBReplication','N','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (6,'DBReplicationHost','','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (7,'DBReplicationUser','','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (8,'DBReplicationPass','','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (9,'DBReplicationPort','3306','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (10,'UseCampcasterAudioclips','N','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (11,'CampcasterHostName','localhost','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (12,'CampcasterHostPort','80','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (13,'CampcasterXRPCPath','/campcaster/storageServer/var/xmlrpc/','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (14,'CampcasterXRPCFile','xrLocStor.php','2008-05-02 15:14:15');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (15,'SiteOnline','Y','2007-10-07 06:49:11');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (16,'SiteCharset','utf-8','2007-07-26 09:49:32');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (17,'SiteLocale','en-US','2007-07-26 09:49:56');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (18,'SiteCacheEnabled','N','2007-07-26 09:50:19');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (22,'SiteMetaKeywords','Campsite, MDLF, Campware, CMS, OpenSource, Media','2007-10-05 06:31:36');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (19,'SiteSecretKey','4b506c2968184be185f6282f5dcac832','2007-10-05 01:51:41');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (20,'SiteSessionLifeTime','1400','2007-10-05 01:51:51');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (21,'SiteTitle','Campsite 3.0','2007-10-07 06:39:13');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (23,'SiteMetaDescription','Campsite 3.0 site, try it out!','2007-10-07 06:36:18');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (24,'SMTPHost','localhost','2007-10-26 06:30:45');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (25,'SMTPPort','25','2007-10-26 06:30:45');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (26,'CacheEngine','APC','2009-08-28 03:09:30');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (27,'EditorImageRatio','100','2009-08-28 03:09:30');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (28,'TemplateFilter','.*, CVS','2009-08-28 03:09:30');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (29,'ImagecacheLifetime','86400','2009-08-28 03:09:30');
/*!40000 ALTER TABLE `SystemPreferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TemplateTypes`
--

DROP TABLE IF EXISTS `TemplateTypes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TemplateTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(20) NOT NULL default '',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `TemplateTypes`
--

LOCK TABLES `TemplateTypes` WRITE;
/*!40000 ALTER TABLE `TemplateTypes` DISABLE KEYS */;
INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (1,'default');
INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (2,'issue');
INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (3,'section');
INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (4,'article');
INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (5,'nontpl');
/*!40000 ALTER TABLE `TemplateTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Templates`
--

DROP TABLE IF EXISTS `Templates`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Templates` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(255) NOT NULL default '',
  `Type` int(10) unsigned NOT NULL default '1',
  `Level` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Templates`
--

LOCK TABLES `Templates` WRITE;
/*!40000 ALTER TABLE `Templates` DISABLE KEYS */;
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (1,'section.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (87,'home-rest-old.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (4,'article.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (105,'archive-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (15,'home.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (93,'login-box.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (86,'section-tpl-right.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (81,'home-top.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (98,'do_login.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (91,'section-top-main.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (85,'footer.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (101,'logout.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (90,'section-top-right.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (106,'left-banners.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (95,'article-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (100,'subscribe-form.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (84,'home-top-right.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (76,'menu.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (77,'header.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (78,'header-menu.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (94,'search-box.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (35,'archive.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (79,'home-rest.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (80,'home-banner.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (38,'service.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (89,'section-top.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (96,'search.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (99,'subscribe.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (103,'print.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (102,'service-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (97,'search-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (92,'right.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (82,'home-top-main.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (104,'rss.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (88,'section-rest.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (107,'do_subscribe.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (108,'subscribe-info.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (109,'style02.css',5,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (110,'img/tb.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (111,'img/01.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (112,'img/main.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (113,'img/banner.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (114,'img/07linija.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (115,'img/02.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (116,'img/l06.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (117,'img/strelica.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (118,'img/l05.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (119,'img/vert-lin.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (120,'img/dalje.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (121,'img/tizer.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (122,'img/cover.jpg',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (123,'img/03.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (124,'img/camplogo.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (125,'img/00.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (126,'img/rss.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (127,'img/04.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (128,'img/crtice.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (129,'img/ffoxbanner.gif',5,1);
/*!40000 ALTER TABLE `Templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TimeUnits`
--

DROP TABLE IF EXISTS `TimeUnits`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TimeUnits` (
  `Unit` char(1) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(70) NOT NULL default '',
  PRIMARY KEY  (`Unit`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `TimeUnits`
--

LOCK TABLES `TimeUnits` WRITE;
/*!40000 ALTER TABLE `TimeUnits` DISABLE KEYS */;
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('D',1,'days');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('W',1,'weeks');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('M',1,'months');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('Y',1,'years');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('D',13,'dÃ­as');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('W',13,'semanas');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('M',13,'meses');
INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('Y',13,'aÃ±os');
/*!40000 ALTER TABLE `TimeUnits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TopicFields`
--

DROP TABLE IF EXISTS `TopicFields`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TopicFields` (
  `ArticleType` varchar(166) NOT NULL default '',
  `FieldName` varchar(166) NOT NULL default '',
  `RootTopicId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ArticleType`,`FieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `TopicFields`
--

LOCK TABLES `TopicFields` WRITE;
/*!40000 ALTER TABLE `TopicFields` DISABLE KEYS */;
/*!40000 ALTER TABLE `TopicFields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Topics`
--

DROP TABLE IF EXISTS `Topics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Topics` (
  `Id` int(10) unsigned NOT NULL default '0',
  `LanguageId` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ParentId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`LanguageId`),
  UNIQUE KEY `Name` (`LanguageId`,`Name`),
  KEY `topic_id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Topics`
--

LOCK TABLES `Topics` WRITE;
/*!40000 ALTER TABLE `Topics` DISABLE KEYS */;
INSERT INTO `Topics` (`Id`, `LanguageId`, `Name`, `ParentId`) VALUES (1,1,'Home main',0);
INSERT INTO `Topics` (`Id`, `LanguageId`, `Name`, `ParentId`) VALUES (2,1,'Home right',0);
INSERT INTO `Topics` (`Id`, `LanguageId`, `Name`, `ParentId`) VALUES (3,1,'Home rest',0);
/*!40000 ALTER TABLE `Topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Translations`
--

DROP TABLE IF EXISTS `Translations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Translations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `phrase_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `translation_text` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `phrase_language_index` (`phrase_id`,`fk_language_id`),
  KEY `phrase_id` (`phrase_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Translations`
--

LOCK TABLES `Translations` WRITE;
/*!40000 ALTER TABLE `Translations` DISABLE KEYS */;
INSERT INTO `Translations` (`id`, `phrase_id`, `fk_language_id`, `translation_text`) VALUES (1,1,1,'article');
/*!40000 ALTER TABLE `Translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `URLTypes`
--

DROP TABLE IF EXISTS `URLTypes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `URLTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(15) NOT NULL default '',
  `Description` mediumblob NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `URLTypes`
--

LOCK TABLES `URLTypes` WRITE;
/*!40000 ALTER TABLE `URLTypes` DISABLE KEYS */;
INSERT INTO `URLTypes` (`Id`, `Name`, `Description`) VALUES (1,'template path','');
INSERT INTO `URLTypes` (`Id`, `Name`, `Description`) VALUES (2,'short names','');
/*!40000 ALTER TABLE `URLTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XArticle`
--

DROP TABLE IF EXISTS `XArticle`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `XArticle` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `FDeck` varchar(255) NOT NULL default '',
  `FByline` varchar(255) NOT NULL default '',
  `FTeaser_a` varchar(255) NOT NULL default '',
  `FTeaser_b` varchar(255) NOT NULL default '',
  `FIntro` mediumblob NOT NULL,
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `XArticle`
--

LOCK TABLES `XArticle` WRITE;
/*!40000 ALTER TABLE `XArticle` DISABLE KEYS */;
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (1,1,'Lipsum','Fringilla vitae','Duis wisi mauris','Mauris pellentesque','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (2,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (4,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (7,1,'Lipsum','Fringilla vitae','Duis wisi mauris','Mauris pellentesque','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris.','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (12,1,'Vestibulum euismod','Sed eu augue','Vestibulum euismod','Sed eu augue','\r\naecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (13,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (14,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (18,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (19,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (20,1,'Vestibulum euismod','Sed eu augue','Dolor val','Lorem ipsum','\r\naecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (21,1,'Vestibulum euismod','Sed eu augue','Sed eu augue','Sed eu augue','Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (22,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (24,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (26,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (28,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (46,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. ','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (30,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (31,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (32,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (33,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (34,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (35,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (36,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (37,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (38,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (39,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (40,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (41,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (42,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (43,1,'Lipsum','Dolor','Lorem ipsum','Dolor val','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi\r\nmauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris\r\npellentesque sodales mauris.','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis wisi mauris, mollis nec, fringilla vitae, varius mollis, elit. Mauris pellentesque sodales mauris. Quisque bibendum gravida ligula. Cras elementum, nisl in tempor dapibus, sapien erat faucibus ipsum, eu tempus eros dolor sit amet quam. Donec lobortis tellus vitae tellus posuere rhoncus. Proin sit amet neque. Proin in ante blandit enim imperdiet tincidunt. Maecenas porttitor rhoncus dolor. Vestibulum euismod malesuada nibh. Sed eu augue in lectus interdum ullamcorper. Vestibulum ante. Vestibulum quis eros vitae elit volutpat scelerisque. Sed blandit volutpat dolor.\r\n\r\nVestibulum luctus scelerisque nibh. Fusce metus pede, bibendum quis, laoreet sit amet, tristique vitae, mi. Fusce hendrerit pellentesque eros. Duis sodales, lacus non rutrum blandit, pede pede pharetra metus, ac porttitor leo purus sed metus. Duis blandit lectus in lacus. Curabitur fermentum. Quisque volutpat metus sit amet mauris. Duis dolor magna, commodo nec, elementum non, malesuada sed, eros. Praesent vitae nibh vitae ipsum auctor accumsan. Nulla facilisi. Vestibulum rutrum massa non ipsum. Duis justo lorem, fermentum et, pharetra sed, luctus quis, pede. Pellentesque mauris. Donec quis massa. Nullam ullamcorper placerat orci. Maecenas commodo, elit quis pretium lobortis, mauris odio molestie augue, ac congue metus mi a magna. Mauris mattis consequat eros. Sed purus arcu, facilisis ut, consectetuer quis, rhoncus ut, erat. In ut quam. Praesent blandit adipiscing sem. ');
/*!40000 ALTER TABLE `XArticle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XLink`
--

DROP TABLE IF EXISTS `XLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `XLink` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Furl` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `XLink`
--

LOCK TABLES `XLink` WRITE;
/*!40000 ALTER TABLE `XLink` DISABLE KEYS */;
INSERT INTO `XLink` (`NrArticle`, `IdLanguage`, `Furl`) VALUES (3,1,'www.mediaonweb.org');
INSERT INTO `XLink` (`NrArticle`, `IdLanguage`, `Furl`) VALUES (16,1,'www.mdlf.org');
INSERT INTO `XLink` (`NrArticle`, `IdLanguage`, `Furl`) VALUES (17,1,'www.campware.org');
/*!40000 ALTER TABLE `XLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XPhoto`
--

DROP TABLE IF EXISTS `XPhoto`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `XPhoto` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `XPhoto`
--

LOCK TABLES `XPhoto` WRITE;
/*!40000 ALTER TABLE `XPhoto` DISABLE KEYS */;
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (44,1);
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (47,1);
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (48,1);
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (49,1);
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (50,1);
INSERT INTO `XPhoto` (`NrArticle`, `IdLanguage`) VALUES (51,1);
/*!40000 ALTER TABLE `XPhoto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XService`
--

DROP TABLE IF EXISTS `XService`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `XService` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY  (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `XService`
--

LOCK TABLES `XService` WRITE;
/*!40000 ALTER TABLE `XService` DISABLE KEYS */;
INSERT INTO `XService` (`NrArticle`, `IdLanguage`, `FFull_text`) VALUES (8,1,'\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed in nisl. Nulla luctus, arcu sed varius lobortis, orci massa eleifend pede, vel molestie augue risus nec leo. Nulla at ante sed nibh vehicula semper. Vestibulum vitae nulla. Nulla metus eros, consectetuer nec, venenatis id, feugiat non, nibh. Praesent at metus. Curabitur accumsan. Vestibulum nunc magna, interdum in, dapibus at, tempor non, est. Etiam porta eros et nibh. Integer venenatis urna sit amet purus.\r\n\r\nQuisque rhoncus posuere ligula. Curabitur imperdiet odio vel odio. Fusce posuere metus nec elit. Nullam sed ante. Integer felis. Nunc pede erat, rutrum non, aliquam nec, iaculis ultricies, lectus. Integer lobortis urna eu ipsum. Maecenas et purus. Phasellus placerat dolor quis lorem. Praesent malesuada lectus eget odio. Nunc dictum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis ipsum neque, posuere eu, gravida sed, adipiscing ut, dolor. Maecenas auctor dictum augue.\r\n\r\nDonec in metus. Praesent turpis nibh, adipiscing in, egestas vel, tempus sit amet, ligula. Praesent massa. Sed placerat auctor dui. Nam luctus aliquet enim. In sed ipsum et ipsum ullamcorper molestie. Morbi at diam pharetra nibh adipiscing tristique. Praesent suscipit, erat in accumsan dictum, massa tortor gravida nunc, eu imperdiet lectus enim sit amet sem. Nulla euismod mollis diam. Cras dictum suscipit elit. Pellentesque eu quam. Vivamus nec mi. \r\n');
INSERT INTO `XService` (`NrArticle`, `IdLanguage`, `FFull_text`) VALUES (11,1,'\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed in nisl. Nulla luctus, arcu sed varius lobortis, orci massa eleifend pede, vel molestie augue risus nec leo. Nulla at ante sed nibh vehicula semper. Vestibulum vitae nulla. Nulla metus eros, consectetuer nec, venenatis id, feugiat non, nibh. Praesent at metus. Curabitur accumsan. Vestibulum nunc magna, interdum in, dapibus at, tempor non, est. Etiam porta eros et nibh. Integer venenatis urna sit amet purus.\r\n\r\nQuisque rhoncus posuere ligula. Curabitur imperdiet odio vel odio. Fusce posuere metus nec elit. Nullam sed ante. Integer felis. Nunc pede erat, rutrum non, aliquam nec, iaculis ultricies, lectus. Integer lobortis urna eu ipsum. Maecenas et purus. Phasellus placerat dolor quis lorem. Praesent malesuada lectus eget odio. Nunc dictum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis ipsum neque, posuere eu, gravida sed, adipiscing ut, dolor. Maecenas auctor dictum augue.\r\n\r\nDonec in metus. Praesent turpis nibh, adipiscing in, egestas vel, tempus sit amet, ligula. Praesent massa. Sed placerat auctor dui. Nam luctus aliquet enim. In sed ipsum et ipsum ullamcorper molestie. Morbi at diam pharetra nibh adipiscing tristique. Praesent suscipit, erat in accumsan dictum, massa tortor gravida nunc, eu imperdiet lectus enim sit amet sem. Nulla euismod mollis diam. Cras dictum suscipit elit. Pellentesque eu quam. Vivamus nec mi. \r\n');
/*!40000 ALTER TABLE `XService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_applications`
--

DROP TABLE IF EXISTS `liveuser_applications`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL default '0',
  `application_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`application_id`),
  UNIQUE KEY `applications_define_name_i_idx` (`application_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_applications`
--

LOCK TABLES `liveuser_applications` WRITE;
/*!40000 ALTER TABLE `liveuser_applications` DISABLE KEYS */;
INSERT INTO `liveuser_applications` (`application_id`, `application_define_name`) VALUES (1,'Campsite');
INSERT INTO `liveuser_applications` (`application_id`, `application_define_name`) VALUES (2,'Campcaster');
/*!40000 ALTER TABLE `liveuser_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_applications_application_id_seq`
--

DROP TABLE IF EXISTS `liveuser_applications_application_id_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_applications_application_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_applications_application_id_seq`
--

LOCK TABLES `liveuser_applications_application_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_applications_application_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_applications_application_id_seq` (`id`) VALUES (2);
/*!40000 ALTER TABLE `liveuser_applications_application_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_applications_seq`
--

DROP TABLE IF EXISTS `liveuser_applications_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_applications_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_applications_seq`
--

LOCK TABLES `liveuser_applications_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_applications_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_applications_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_area_admin_areas`
--

DROP TABLE IF EXISTS `liveuser_area_admin_areas`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_area_admin_areas` (
  `area_id` int(11) NOT NULL default '0',
  `perm_user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `area_admin_areas_id_i_idx` (`area_id`,`perm_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_area_admin_areas`
--

LOCK TABLES `liveuser_area_admin_areas` WRITE;
/*!40000 ALTER TABLE `liveuser_area_admin_areas` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_area_admin_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_areas`
--

DROP TABLE IF EXISTS `liveuser_areas`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_areas` (
  `area_id` int(11) NOT NULL default '0',
  `application_id` int(11) NOT NULL default '0',
  `area_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`area_id`),
  UNIQUE KEY `areas_define_name_i_idx` (`application_id`,`area_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_areas`
--

LOCK TABLES `liveuser_areas` WRITE;
/*!40000 ALTER TABLE `liveuser_areas` DISABLE KEYS */;
INSERT INTO `liveuser_areas` (`area_id`, `application_id`, `area_define_name`) VALUES (1,1,'Articles');
/*!40000 ALTER TABLE `liveuser_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_areas_seq`
--

DROP TABLE IF EXISTS `liveuser_areas_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_areas_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_areas_seq`
--

LOCK TABLES `liveuser_areas_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_areas_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_areas_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_group_subgroups`
--

DROP TABLE IF EXISTS `liveuser_group_subgroups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_group_subgroups` (
  `group_id` int(11) NOT NULL default '0',
  `subgroup_id` int(11) NOT NULL default '0',
  UNIQUE KEY `group_subgroups_id_i_idx` (`group_id`,`subgroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_group_subgroups`
--

LOCK TABLES `liveuser_group_subgroups` WRITE;
/*!40000 ALTER TABLE `liveuser_group_subgroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_group_subgroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_grouprights`
--

DROP TABLE IF EXISTS `liveuser_grouprights`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_grouprights` (
  `group_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `grouprights_id_i_idx` (`group_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_grouprights`
--

LOCK TABLES `liveuser_grouprights` WRITE;
/*!40000 ALTER TABLE `liveuser_grouprights` DISABLE KEYS */;
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,1,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,2,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,3,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,4,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,5,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,6,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,7,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,8,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,9,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,10,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,11,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,12,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,13,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,14,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,15,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,16,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,17,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,18,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,19,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,20,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,21,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,22,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,23,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,24,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,25,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,26,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,27,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,28,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,29,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,30,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,31,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,32,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,33,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,34,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,35,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,36,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,37,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,38,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,39,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,40,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,41,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,42,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,43,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,44,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,45,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,46,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,47,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,48,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,49,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,72,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,52,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,53,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,55,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,56,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,57,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,58,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,59,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,60,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,61,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,62,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,63,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,65,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,64,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,66,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,67,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,70,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,68,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,69,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,1,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,2,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,3,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,4,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,5,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,6,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,7,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,8,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,9,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,10,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,12,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,13,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,14,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,15,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,17,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,18,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,19,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,22,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,23,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,25,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,26,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,27,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,28,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,29,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,30,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,34,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,35,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,36,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,37,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,38,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,39,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,41,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,42,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,43,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,44,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,45,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,47,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,48,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,49,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,52,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,55,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,57,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,60,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,62,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,63,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,66,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,67,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,68,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,69,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,1,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,2,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,3,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,4,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,5,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,6,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,7,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,8,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,9,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,10,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,14,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,17,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,18,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,25,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,26,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,27,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,28,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,29,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,34,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,35,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,36,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,37,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,38,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,39,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,42,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,43,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,44,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,45,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,46,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,47,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,48,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,49,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,78,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,66,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,68,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,71,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,73,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,79,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,80,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,81,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,74,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,75,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,76,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,77,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,51,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,72,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,78,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,73,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,79,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,80,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,81,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,74,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,75,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,76,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (2,77,3);
/*!40000 ALTER TABLE `liveuser_grouprights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groups`
--

DROP TABLE IF EXISTS `liveuser_groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL default '0',
  `group_type` int(11) NOT NULL default '0',
  `group_define_name` varchar(32) NOT NULL default ' ',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `groups_define_name_i_idx` (`group_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_groups`
--

LOCK TABLES `liveuser_groups` WRITE;
/*!40000 ALTER TABLE `liveuser_groups` DISABLE KEYS */;
INSERT INTO `liveuser_groups` (`group_id`, `group_type`, `group_define_name`) VALUES (1,0,'Administrator');
INSERT INTO `liveuser_groups` (`group_id`, `group_type`, `group_define_name`) VALUES (2,0,'Chief Editor');
INSERT INTO `liveuser_groups` (`group_id`, `group_type`, `group_define_name`) VALUES (3,0,'Editor');
/*!40000 ALTER TABLE `liveuser_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groups_group_id_seq`
--

DROP TABLE IF EXISTS `liveuser_groups_group_id_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_groups_group_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_groups_group_id_seq`
--

LOCK TABLES `liveuser_groups_group_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_groups_group_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_groups_group_id_seq` (`id`) VALUES (3);
/*!40000 ALTER TABLE `liveuser_groups_group_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groups_seq`
--

DROP TABLE IF EXISTS `liveuser_groups_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_groups_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_groups_seq`
--

LOCK TABLES `liveuser_groups_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_groups_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_groups_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groupusers`
--

DROP TABLE IF EXISTS `liveuser_groupusers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_groupusers` (
  `perm_user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  UNIQUE KEY `groupusers_id_i_idx` (`perm_user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_groupusers`
--

LOCK TABLES `liveuser_groupusers` WRITE;
/*!40000 ALTER TABLE `liveuser_groupusers` DISABLE KEYS */;
INSERT INTO `liveuser_groupusers` (`perm_user_id`, `group_id`) VALUES (1,1);
/*!40000 ALTER TABLE `liveuser_groupusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_perm_users`
--

DROP TABLE IF EXISTS `liveuser_perm_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_perm_users` (
  `perm_user_id` int(11) NOT NULL default '0',
  `auth_user_id` varchar(32) NOT NULL default ' ',
  `auth_container_name` varchar(32) NOT NULL default ' ',
  `perm_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`perm_user_id`),
  UNIQUE KEY `perm_users_auth_id_i_idx` (`auth_user_id`,`auth_container_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_perm_users`
--

LOCK TABLES `liveuser_perm_users` WRITE;
/*!40000 ALTER TABLE `liveuser_perm_users` DISABLE KEYS */;
INSERT INTO `liveuser_perm_users` (`perm_user_id`, `auth_user_id`, `auth_container_name`, `perm_type`) VALUES (1,'1','DB',1);
/*!40000 ALTER TABLE `liveuser_perm_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_perm_users_perm_user_id_seq`
--

DROP TABLE IF EXISTS `liveuser_perm_users_perm_user_id_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_perm_users_perm_user_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_perm_users_perm_user_id_seq`
--

LOCK TABLES `liveuser_perm_users_perm_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_perm_users_perm_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_perm_users_perm_user_id_seq` (`id`) VALUES (3);
/*!40000 ALTER TABLE `liveuser_perm_users_perm_user_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_perm_users_seq`
--

DROP TABLE IF EXISTS `liveuser_perm_users_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_perm_users_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_perm_users_seq`
--

LOCK TABLES `liveuser_perm_users_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_perm_users_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_perm_users_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_right_implied`
--

DROP TABLE IF EXISTS `liveuser_right_implied`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_right_implied` (
  `right_id` int(11) NOT NULL default '0',
  `implied_right_id` int(11) NOT NULL default '0',
  UNIQUE KEY `right_implied_id_i_idx` (`right_id`,`implied_right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_right_implied`
--

LOCK TABLES `liveuser_right_implied` WRITE;
/*!40000 ALTER TABLE `liveuser_right_implied` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_right_implied` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_rights`
--

DROP TABLE IF EXISTS `liveuser_rights`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_rights` (
  `right_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `right_define_name` varchar(32) NOT NULL default ' ',
  `has_implied` tinyint(1) default '1',
  PRIMARY KEY  (`right_id`),
  UNIQUE KEY `rights_define_name_i_idx` (`area_id`,`right_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_rights`
--

LOCK TABLES `liveuser_rights` WRITE;
/*!40000 ALTER TABLE `liveuser_rights` DISABLE KEYS */;
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (1,0,'AddArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (2,0,'AddAudioclip',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (3,0,'AddFile',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (4,0,'AddImage',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (5,0,'AttachAudioclipToArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (6,0,'AttachImageToArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (7,0,'AttachTopicToArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (8,0,'ChangeArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (9,0,'ChangeFile',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (10,0,'ChangeImage',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (11,0,'ChangeSystemPreferences',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (12,0,'CommentEnable',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (13,0,'CommentModerate',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (14,0,'DeleteArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (15,0,'DeleteArticleTypes',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (16,0,'DeleteCountries',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (17,0,'DeleteFile',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (18,0,'DeleteImage',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (19,0,'DeleteIssue',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (20,0,'DeleteLanguages',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (21,0,'DeletePub',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (22,0,'DeleteSection',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (23,0,'DeleteTempl',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (24,0,'DeleteUsers',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (25,0,'EditorBold',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (26,0,'EditorCharacterMap',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (27,0,'EditorCopyCutPaste',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (28,0,'EditorEnlarge',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (29,0,'EditorFindReplace',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (30,0,'EditorFontColor',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (31,0,'EditorFontFace',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (32,0,'EditorFontSize',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (33,0,'EditorHorizontalRule',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (34,0,'EditorImage',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (35,0,'EditorIndent',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (36,0,'EditorItalic',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (37,0,'EditorLink',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (38,0,'EditorListBullet',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (39,0,'EditorListNumber',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (40,0,'EditorSourceView',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (41,0,'EditorStrikethrough',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (42,0,'EditorSubhead',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (43,0,'EditorSubscript',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (44,0,'EditorSuperscript',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (45,0,'EditorTable',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (46,0,'EditorTextAlignment',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (47,0,'EditorTextDirection',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (48,0,'EditorUnderline',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (49,0,'EditorUndoRedo',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (50,0,'InitializeTemplateEngine',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (51,0,'plugin_manager',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (52,0,'ManageArticleTypes',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (53,0,'ManageCountries',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (54,0,'ManageIndexer',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (55,0,'ManageIssue',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (56,0,'ManageLanguages',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (57,0,'ManageLocalizer',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (58,0,'ManagePub',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (59,0,'ManageReaders',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (60,0,'ManageSection',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (61,0,'ManageSubscriptions',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (62,0,'ManageTempl',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (63,0,'ManageTopics',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (64,0,'ManageUserTypes',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (65,0,'ManageUsers',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (66,0,'MoveArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (67,0,'Publish',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (68,0,'TranslateArticle',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (69,0,'ViewLogs',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (70,0,'SyncPhorumUsers',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (71,0,'ClearCache',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (72,0,'ManageSection10_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (73,0,'ManageSection20_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (74,0,'ManageSection30_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (75,0,'ManageSection40_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (76,0,'ManageSection50_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (77,0,'ManageSection60_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (78,0,'ManageSection200_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (79,0,'ManageSection210_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (80,0,'ManageSection220_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (81,0,'ManageSection230_P1_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (82,0,'EditorStatusBar',1);
/*!40000 ALTER TABLE `liveuser_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_rights_right_id_seq`
--

DROP TABLE IF EXISTS `liveuser_rights_right_id_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_rights_right_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_rights_right_id_seq`
--

LOCK TABLES `liveuser_rights_right_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_rights_right_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_rights_right_id_seq` (`id`) VALUES (82);
/*!40000 ALTER TABLE `liveuser_rights_right_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_rights_seq`
--

DROP TABLE IF EXISTS `liveuser_rights_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_rights_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_rights_seq`
--

LOCK TABLES `liveuser_rights_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_rights_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_rights_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_translations`
--

DROP TABLE IF EXISTS `liveuser_translations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_translations` (
  `translation_id` int(11) NOT NULL default '0',
  `section_id` int(11) NOT NULL default '0',
  `section_type` int(11) NOT NULL default '0',
  `language_id` varchar(32) NOT NULL default ' ',
  `name` varchar(32) NOT NULL default ' ',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`translation_id`),
  UNIQUE KEY `translations_translation_i_idx` (`section_id`,`section_type`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_translations`
--

LOCK TABLES `liveuser_translations` WRITE;
/*!40000 ALTER TABLE `liveuser_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_translations_seq`
--

DROP TABLE IF EXISTS `liveuser_translations_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_translations_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_translations_seq`
--

LOCK TABLES `liveuser_translations_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_translations_seq` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_translations_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_userrights`
--

DROP TABLE IF EXISTS `liveuser_userrights`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_userrights` (
  `perm_user_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  UNIQUE KEY `userrights_id_i_idx` (`perm_user_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_userrights`
--

LOCK TABLES `liveuser_userrights` WRITE;
/*!40000 ALTER TABLE `liveuser_userrights` DISABLE KEYS */;
/*!40000 ALTER TABLE `liveuser_userrights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_users`
--

DROP TABLE IF EXISTS `liveuser_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_users` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `KeyId` int(10) unsigned default NULL,
  `Name` varchar(255) NOT NULL default '',
  `UName` varchar(70) NOT NULL default '',
  `Password` varchar(64) NOT NULL default '',
  `EMail` varchar(255) NOT NULL default '',
  `Reader` enum('Y','N') NOT NULL default 'Y',
  `fk_user_type` int(10) unsigned default NULL,
  `City` varchar(100) NOT NULL default '',
  `StrAddress` varchar(255) NOT NULL default '',
  `State` varchar(32) NOT NULL default '',
  `CountryCode` varchar(21) default NULL,
  `Phone` varchar(20) NOT NULL default '',
  `Fax` varchar(20) NOT NULL default '',
  `Contact` varchar(64) NOT NULL default '',
  `Phone2` varchar(20) NOT NULL default '',
  `Title` enum('Mr.','Mrs.','Ms.','Dr.') NOT NULL default 'Mr.',
  `Gender` enum('M','F') default NULL,
  `Age` enum('0-17','18-24','25-39','40-49','50-65','65-') NOT NULL default '0-17',
  `PostalCode` varchar(70) NOT NULL default '',
  `Employer` varchar(140) NOT NULL default '',
  `EmployerType` varchar(140) NOT NULL default '',
  `Position` varchar(70) NOT NULL default '',
  `Interests` mediumblob NOT NULL,
  `How` varchar(255) NOT NULL default '',
  `Languages` varchar(100) NOT NULL default '',
  `Improvements` mediumblob NOT NULL,
  `Pref1` enum('N','Y') NOT NULL default 'N',
  `Pref2` enum('N','Y') NOT NULL default 'N',
  `Pref3` enum('N','Y') NOT NULL default 'N',
  `Pref4` enum('N','Y') NOT NULL default 'N',
  `Field1` varchar(150) NOT NULL default '',
  `Field2` varchar(150) NOT NULL default '',
  `Field3` varchar(150) NOT NULL default '',
  `Field4` varchar(150) NOT NULL default '',
  `Field5` varchar(150) NOT NULL default '',
  `Text1` mediumblob NOT NULL,
  `Text2` mediumblob NOT NULL,
  `Text3` mediumblob NOT NULL,
  `time_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `lastLogin` datetime default '1970-01-01 00:00:00',
  `isActive` tinyint(1) default '1',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `UName` (`UName`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_users`
--

LOCK TABLES `liveuser_users` WRITE;
/*!40000 ALTER TABLE `liveuser_users` DISABLE KEYS */;
INSERT INTO `liveuser_users` (`Id`, `KeyId`, `Name`, `UName`, `Password`, `EMail`, `Reader`, `fk_user_type`, `City`, `StrAddress`, `State`, `CountryCode`, `Phone`, `Fax`, `Contact`, `Phone2`, `Title`, `Gender`, `Age`, `PostalCode`, `Employer`, `EmployerType`, `Position`, `Interests`, `How`, `Languages`, `Improvements`, `Pref1`, `Pref2`, `Pref3`, `Pref4`, `Field1`, `Field2`, `Field3`, `Field4`, `Field5`, `Text1`, `Text2`, `Text3`, `time_updated`, `time_created`, `lastLogin`, `isActive`) VALUES (1,186655017,'Administrator','admin','b2d716fb2328a246e8285f47b1500ebcb349c187','','N',1,'','','','AD','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','','2009-08-28 03:10:12','0000-00-00 00:00:00','2009-08-27 22:10:12',1);
/*!40000 ALTER TABLE `liveuser_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_users_auth_user_id_seq`
--

DROP TABLE IF EXISTS `liveuser_users_auth_user_id_seq`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `liveuser_users_auth_user_id_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `liveuser_users_auth_user_id_seq`
--

LOCK TABLES `liveuser_users_auth_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_users_auth_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_users_auth_user_id_seq` (`id`) VALUES (3);
/*!40000 ALTER TABLE `liveuser_users_auth_user_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_banlists`
--

DROP TABLE IF EXISTS `phorum_banlists`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_banlists` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `pcre` tinyint(4) NOT NULL default '0',
  `string` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_banlists`
--

LOCK TABLES `phorum_banlists` WRITE;
/*!40000 ALTER TABLE `phorum_banlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_banlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_files`
--

DROP TABLE IF EXISTS `phorum_files`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_files` (
  `file_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `file_data` mediumtext NOT NULL,
  `add_datetime` int(10) unsigned NOT NULL default '0',
  `message_id` int(10) unsigned NOT NULL default '0',
  `link` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`file_id`),
  KEY `add_datetime` (`add_datetime`),
  KEY `message_id_link` (`message_id`,`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_files`
--

LOCK TABLES `phorum_files` WRITE;
/*!40000 ALTER TABLE `phorum_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_forum_group_xref`
--

DROP TABLE IF EXISTS `phorum_forum_group_xref`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_forum_group_xref` (
  `forum_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_forum_group_xref`
--

LOCK TABLES `phorum_forum_group_xref` WRITE;
/*!40000 ALTER TABLE `phorum_forum_group_xref` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_forum_group_xref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_forums`
--

DROP TABLE IF EXISTS `phorum_forums`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_forums` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `description` text NOT NULL,
  `template` varchar(50) NOT NULL default '',
  `folder_flag` tinyint(1) NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `list_length_flat` int(10) unsigned NOT NULL default '0',
  `list_length_threaded` int(10) unsigned NOT NULL default '0',
  `moderation` int(10) unsigned NOT NULL default '0',
  `threaded_list` tinyint(4) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `float_to_top` tinyint(4) NOT NULL default '0',
  `check_duplicate` tinyint(4) NOT NULL default '0',
  `allow_attachment_types` varchar(100) NOT NULL default '',
  `max_attachment_size` int(10) unsigned NOT NULL default '0',
  `max_totalattachment_size` int(10) unsigned NOT NULL default '0',
  `max_attachments` int(10) unsigned NOT NULL default '0',
  `pub_perms` int(10) unsigned NOT NULL default '0',
  `reg_perms` int(10) unsigned NOT NULL default '0',
  `display_ip_address` smallint(5) unsigned NOT NULL default '1',
  `allow_email_notify` smallint(5) unsigned NOT NULL default '1',
  `language` varchar(100) NOT NULL default 'english',
  `email_moderators` tinyint(1) NOT NULL default '0',
  `message_count` int(10) unsigned NOT NULL default '0',
  `sticky_count` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `last_post_time` int(10) unsigned NOT NULL default '0',
  `display_order` int(10) unsigned NOT NULL default '0',
  `read_length` int(10) unsigned NOT NULL default '0',
  `vroot` int(10) unsigned NOT NULL default '0',
  `edit_post` tinyint(1) NOT NULL default '1',
  `template_settings` text NOT NULL,
  `count_views` tinyint(1) unsigned NOT NULL default '0',
  `display_fixed` tinyint(1) unsigned NOT NULL default '0',
  `reverse_threading` tinyint(1) NOT NULL default '0',
  `inherit_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `name` (`name`),
  KEY `active` (`active`,`parent_id`),
  KEY `group_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_forums`
--

LOCK TABLES `phorum_forums` WRITE;
/*!40000 ALTER TABLE `phorum_forums` DISABLE KEYS */;
INSERT INTO `phorum_forums` (`forum_id`, `name`, `active`, `description`, `template`, `folder_flag`, `parent_id`, `list_length_flat`, `list_length_threaded`, `moderation`, `threaded_list`, `threaded_read`, `float_to_top`, `check_duplicate`, `allow_attachment_types`, `max_attachment_size`, `max_totalattachment_size`, `max_attachments`, `pub_perms`, `reg_perms`, `display_ip_address`, `allow_email_notify`, `language`, `email_moderators`, `message_count`, `sticky_count`, `thread_count`, `last_post_time`, `display_order`, `read_length`, `vroot`, `edit_post`, `template_settings`, `count_views`, `display_fixed`, `reverse_threading`, `inherit_id`) VALUES (1,'My Publication',1,'','default',0,0,30,15,0,0,0,1,1,'',0,0,0,10,15,0,0,'english',0,2,0,2,1238580831,0,30,0,1,'',2,0,0,NULL);
/*!40000 ALTER TABLE `phorum_forums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_groups`
--

DROP TABLE IF EXISTS `phorum_groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '0',
  `open` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_groups`
--

LOCK TABLES `phorum_groups` WRITE;
/*!40000 ALTER TABLE `phorum_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_messages`
--

DROP TABLE IF EXISTS `phorum_messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `author` varchar(37) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '2',
  `msgid` varchar(100) NOT NULL default '',
  `modifystamp` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `moderator_post` tinyint(3) unsigned NOT NULL default '0',
  `sort` tinyint(4) NOT NULL default '2',
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  `viewcount` int(10) unsigned NOT NULL default '0',
  `closed` tinyint(4) NOT NULL default '0',
  `thread_depth` tinyint(3) unsigned NOT NULL default '0',
  `thread_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`message_id`),
  KEY `thread_message` (`thread`,`message_id`),
  KEY `thread_forum` (`thread`,`forum_id`),
  KEY `special_threads` (`sort`,`forum_id`),
  KEY `status_forum` (`status`,`forum_id`),
  KEY `list_page_float` (`forum_id`,`parent_id`,`modifystamp`),
  KEY `list_page_flat` (`forum_id`,`parent_id`,`thread`),
  KEY `post_count` (`forum_id`,`status`,`parent_id`),
  KEY `dup_check` (`forum_id`,`author`,`subject`,`datestamp`),
  KEY `forum_max_message` (`forum_id`,`message_id`,`status`,`parent_id`),
  KEY `last_post_time` (`forum_id`,`status`,`modifystamp`),
  KEY `next_prev_thread` (`forum_id`,`status`,`thread`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_messages`
--

LOCK TABLES `phorum_messages` WRITE;
/*!40000 ALTER TABLE `phorum_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_pm_buddies`
--

DROP TABLE IF EXISTS `phorum_pm_buddies`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_pm_buddies` (
  `pm_buddy_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `buddy_user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pm_buddy_id`),
  UNIQUE KEY `userids` (`user_id`,`buddy_user_id`),
  KEY `buddy_user_id` (`buddy_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_pm_buddies`
--

LOCK TABLES `phorum_pm_buddies` WRITE;
/*!40000 ALTER TABLE `phorum_pm_buddies` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_pm_buddies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_pm_folders`
--

DROP TABLE IF EXISTS `phorum_pm_folders`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_pm_folders` (
  `pm_folder_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `foldername` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`pm_folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_pm_folders`
--

LOCK TABLES `phorum_pm_folders` WRITE;
/*!40000 ALTER TABLE `phorum_pm_folders` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_pm_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_pm_messages`
--

DROP TABLE IF EXISTS `phorum_pm_messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_pm_messages` (
  `pm_message_id` int(10) unsigned NOT NULL auto_increment,
  `from_user_id` int(10) unsigned NOT NULL default '0',
  `from_username` varchar(50) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `message` text NOT NULL,
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  PRIMARY KEY  (`pm_message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_pm_messages`
--

LOCK TABLES `phorum_pm_messages` WRITE;
/*!40000 ALTER TABLE `phorum_pm_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_pm_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_pm_xref`
--

DROP TABLE IF EXISTS `phorum_pm_xref`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_pm_xref` (
  `pm_xref_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `pm_folder_id` int(10) unsigned NOT NULL default '0',
  `special_folder` varchar(10) default NULL,
  `pm_message_id` int(10) unsigned NOT NULL default '0',
  `read_flag` tinyint(1) NOT NULL default '0',
  `reply_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pm_xref_id`),
  KEY `xref` (`user_id`,`pm_folder_id`,`pm_message_id`),
  KEY `read_flag` (`read_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_pm_xref`
--

LOCK TABLES `phorum_pm_xref` WRITE;
/*!40000 ALTER TABLE `phorum_pm_xref` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_pm_xref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_search`
--

DROP TABLE IF EXISTS `phorum_search`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_search` (
  `message_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `search_text` mediumtext NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `forum_id` (`forum_id`),
  FULLTEXT KEY `search_text` (`search_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_search`
--

LOCK TABLES `phorum_search` WRITE;
/*!40000 ALTER TABLE `phorum_search` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_settings`
--

DROP TABLE IF EXISTS `phorum_settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_settings` (
  `name` varchar(255) NOT NULL default '',
  `type` enum('V','S') NOT NULL default 'V',
  `data` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_settings`
--

LOCK TABLES `phorum_settings` WRITE;
/*!40000 ALTER TABLE `phorum_settings` DISABLE KEYS */;
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('title','V','Phorum 5');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('cache','V','/tmp');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('session_timeout','V','30');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('short_session_timeout','V','60');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('tight_security','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('session_path','V','/');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('session_domain','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('admin_session_salt','V','0.62629000 1146135136');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('cache_users','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('register_email_confirm','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('default_template','V','default');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('default_language','V','english');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('use_cookies','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('use_bcc','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('use_rss','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('internal_version','V','2006032300');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('PROFILE_FIELDS','S','a:1:{i:0;a:3:{s:4:\"name\";s:9:\"real_name\";s:6:\"length\";i:255;s:13:\"html_disabled\";i:1;}}');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('enable_pm','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('user_edit_timelimit','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('enable_new_pm_count','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('enable_dropdown_userlist','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('enable_moderator_notifications','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('show_new_on_index','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('dns_lookup','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('tz_offset','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('user_time_zone','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('user_template','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('registration_control','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('file_uploads','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('file_types','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('max_file_size','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('file_space_quota','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('file_offsite','V','0');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('system_email_from_name','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('hide_forums','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('track_user_activity','V','86400');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('html_title','V','Phorum');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('head_tags','V','');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('redirect_after_post','V','list');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('reply_on_read_page','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('status','V','normal');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('use_new_folder_style','V','1');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('default_forum_options','S','a:24:{s:8:\"forum_id\";i:0;s:10:\"moderation\";i:0;s:16:\"email_moderators\";i:0;s:9:\"pub_perms\";i:1;s:9:\"reg_perms\";i:15;s:13:\"display_fixed\";i:0;s:8:\"template\";s:7:\"default\";s:8:\"language\";s:7:\"english\";s:13:\"threaded_list\";i:0;s:13:\"threaded_read\";i:0;s:17:\"reverse_threading\";i:0;s:12:\"float_to_top\";i:1;s:16:\"list_length_flat\";i:30;s:20:\"list_length_threaded\";i:15;s:11:\"read_length\";i:30;s:18:\"display_ip_address\";i:0;s:18:\"allow_email_notify\";i:0;s:15:\"check_duplicate\";i:1;s:11:\"count_views\";i:2;s:15:\"max_attachments\";i:0;s:22:\"allow_attachment_types\";s:0:\"\";s:19:\"max_attachment_size\";i:0;s:24:\"max_totalattachment_size\";i:0;s:5:\"vroot\";i:0;}');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('hooks','S','a:1:{s:6:\"format\";a:2:{s:4:\"mods\";a:2:{i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";}s:5:\"funcs\";a:2:{i:0;s:18:\"phorum_mod_smileys\";i:1;s:14:\"phorum_bb_code\";}}}');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('mods','S','a:4:{s:4:\"html\";i:0;s:7:\"replace\";i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";i:1;}');
INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('mod_emailcomments','S','a:2:{s:9:\"addresses\";a:1:{i:1;s:0:\"\";}s:14:\"from_addresses\";a:1:{i:1;s:0:\"\";}}');
/*!40000 ALTER TABLE `phorum_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_subscribers`
--

DROP TABLE IF EXISTS `phorum_subscribers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_subscribers` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `sub_type` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`thread`),
  KEY `forum_id` (`forum_id`,`thread`,`sub_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_subscribers`
--

LOCK TABLES `phorum_subscribers` WRITE;
/*!40000 ALTER TABLE `phorum_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_user_custom_fields`
--

DROP TABLE IF EXISTS `phorum_user_custom_fields`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_user_custom_fields` (
  `user_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`user_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_user_custom_fields`
--

LOCK TABLES `phorum_user_custom_fields` WRITE;
/*!40000 ALTER TABLE `phorum_user_custom_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_user_custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_user_group_xref`
--

DROP TABLE IF EXISTS `phorum_user_group_xref`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_user_group_xref` (
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `status` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_user_group_xref`
--

LOCK TABLES `phorum_user_group_xref` WRITE;
/*!40000 ALTER TABLE `phorum_user_group_xref` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_user_group_xref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_user_newflags`
--

DROP TABLE IF EXISTS `phorum_user_newflags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_user_newflags` (
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `message_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_user_newflags`
--

LOCK TABLES `phorum_user_newflags` WRITE;
/*!40000 ALTER TABLE `phorum_user_newflags` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_user_newflags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_user_permissions`
--

DROP TABLE IF EXISTS `phorum_user_permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_user_permissions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_user_permissions`
--

LOCK TABLES `phorum_user_permissions` WRITE;
/*!40000 ALTER TABLE `phorum_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_users`
--

DROP TABLE IF EXISTS `phorum_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `phorum_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `fk_campsite_user_id` int(10) unsigned default NULL,
  `username` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `cookie_sessid_lt` varchar(50) NOT NULL default '',
  `sessid_st` varchar(50) NOT NULL default '',
  `sessid_st_timeout` int(10) unsigned NOT NULL default '0',
  `password_temp` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `email_temp` varchar(110) NOT NULL default '',
  `hide_email` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `user_data` text NOT NULL,
  `signature` text NOT NULL,
  `threaded_list` tinyint(4) NOT NULL default '0',
  `posts` int(10) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `date_added` int(10) unsigned NOT NULL default '0',
  `date_last_active` int(10) unsigned NOT NULL default '0',
  `last_active_forum` int(10) unsigned NOT NULL default '0',
  `hide_activity` tinyint(1) NOT NULL default '0',
  `show_signature` tinyint(1) NOT NULL default '0',
  `email_notify` tinyint(1) NOT NULL default '0',
  `pm_email_notify` tinyint(1) NOT NULL default '1',
  `tz_offset` tinyint(2) NOT NULL default '-99',
  `is_dst` tinyint(1) NOT NULL default '0',
  `user_language` varchar(100) NOT NULL default '',
  `user_template` varchar(100) NOT NULL default '',
  `moderator_data` text NOT NULL,
  `moderation_email` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `fk_campsite_user_id` (`fk_campsite_user_id`),
  KEY `active` (`active`),
  KEY `userpass` (`username`,`password`),
  KEY `sessid_st` (`sessid_st`),
  KEY `cookie_sessid_lt` (`cookie_sessid_lt`),
  KEY `activity` (`date_last_active`,`hide_activity`,`last_active_forum`),
  KEY `date_added` (`date_added`),
  KEY `email_temp` (`email_temp`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `phorum_users`
--

LOCK TABLES `phorum_users` WRITE;
/*!40000 ALTER TABLE `phorum_users` DISABLE KEYS */;
INSERT INTO `phorum_users` (`user_id`, `fk_campsite_user_id`, `username`, `password`, `cookie_sessid_lt`, `sessid_st`, `sessid_st_timeout`, `password_temp`, `email`, `email_temp`, `hide_email`, `active`, `user_data`, `signature`, `threaded_list`, `posts`, `admin`, `threaded_read`, `date_added`, `date_last_active`, `last_active_forum`, `hide_activity`, `show_signature`, `email_notify`, `pm_email_notify`, `tz_offset`, `is_dst`, `user_language`, `user_template`, `moderator_data`, `moderation_email`) VALUES (1,1,'admin','b2d716fb2328a246e8285f47b1500ebcb349c187','','',0,'','','',0,0,'','',0,0,0,0,0,0,0,0,0,0,1,-99,0,'','','',1);
/*!40000 ALTER TABLE `phorum_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-08-28  3:18:05
