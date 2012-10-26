-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: campsite34
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.3

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Aliases` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` char(128) NOT NULL DEFAULT '',
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Aliases`
--

LOCK TABLES `Aliases` WRITE;
/*!40000 ALTER TABLE `Aliases` DISABLE KEYS */;
INSERT INTO `Aliases` (`Id`, `Name`, `IdPublication`) VALUES (5,'localhost',5);
/*!40000 ALTER TABLE `Aliases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAttachments`
--

DROP TABLE IF EXISTS `ArticleAttachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleAttachments` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_attachment_id` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `article_attachment_index` (`fk_article_number`,`fk_attachment_id`),
  KEY `fk_article_number` (`fk_article_number`),
  KEY `fk_attachment_id` (`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleAttachments`
--

LOCK TABLES `ArticleAttachments` WRITE;
/*!40000 ALTER TABLE `ArticleAttachments` DISABLE KEYS */;
INSERT INTO `ArticleAttachments` (`fk_article_number`, `fk_attachment_id`) VALUES (38,2);
/*!40000 ALTER TABLE `ArticleAttachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAudioclips`
--

DROP TABLE IF EXISTS `ArticleAudioclips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleAudioclips` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_audioclip_gunid` varchar(20) NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned DEFAULT NULL,
  `order_no` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_article_number`,`fk_audioclip_gunid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleAuthors` (
  `fk_article_number` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_author_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_article_number`,`fk_language_id`,`fk_author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleComments` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_comment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_first` tinyint(1) NOT NULL DEFAULT '0',
  KEY `fk_comment_id` (`fk_comment_id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `first_message_index` (`fk_article_number`,`fk_language_id`,`is_first`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleComments`
--

LOCK TABLES `ArticleComments` WRITE;
/*!40000 ALTER TABLE `ArticleComments` DISABLE KEYS */;
INSERT INTO `ArticleComments` (`fk_article_number`, `fk_language_id`, `fk_comment_id`, `is_first`) VALUES (34,1,3,1);
INSERT INTO `ArticleComments` (`fk_article_number`, `fk_language_id`, `fk_comment_id`, `is_first`) VALUES (34,1,4,0);
/*!40000 ALTER TABLE `ArticleComments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleImages`
--

DROP TABLE IF EXISTS `ArticleImages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleImages` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdImage` int(10) unsigned NOT NULL DEFAULT '0',
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`NrArticle`,`IdImage`),
  UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`),
  KEY `IdImage` (`IdImage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleImages`
--

LOCK TABLES `ArticleImages` WRITE;
/*!40000 ALTER TABLE `ArticleImages` DISABLE KEYS */;
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (40,14,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (38,20,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (37,12,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (31,10,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (32,11,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (30,9,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (24,18,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (39,19,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (41,15,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (43,13,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (42,16,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (44,17,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (45,13,2);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (47,11,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (48,9,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (49,9,1);
INSERT INTO `ArticleImages` (`NrArticle`, `IdImage`, `Number`) VALUES (50,9,1);
/*!40000 ALTER TABLE `ArticleImages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleIndex`
--

DROP TABLE IF EXISTS `ArticleIndex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleIndex` (
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `IdKeyword` int(10) unsigned NOT NULL DEFAULT '0',
  `NrIssue` int(10) unsigned NOT NULL DEFAULT '0',
  `NrSection` int(10) unsigned NOT NULL DEFAULT '0',
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdPublication`,`IdLanguage`,`IdKeyword`,`NrIssue`,`NrSection`,`NrArticle`),
  UNIQUE KEY `article_keyword_idx` (`NrArticle`,`IdLanguage`,`IdKeyword`),
  KEY `keyword_idx` (`IdKeyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleIndex`
--

LOCK TABLES `ArticleIndex` WRITE;
/*!40000 ALTER TABLE `ArticleIndex` DISABLE KEYS */;
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,1,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,2,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,3,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,4,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,5,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,6,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,6,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,6,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,7,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,8,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,9,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,10,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,11,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,12,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,13,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,14,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,15,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,16,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,17,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,18,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,19,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,20,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,21,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,22,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,23,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,24,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,25,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,26,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,27,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,28,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,29,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,30,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,31,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,32,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,33,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,34,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,35,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,36,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,37,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,38,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,39,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,40,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,41,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,42,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,43,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,44,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,45,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,46,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,47,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,48,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,49,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,50,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,51,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,52,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,53,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,54,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,55,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,56,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,57,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,58,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,59,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,60,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,61,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,62,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,63,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,64,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,65,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,66,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,67,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,68,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,69,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,70,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,71,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,72,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,73,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,74,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,75,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,76,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,77,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,78,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,79,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,80,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,81,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,82,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,83,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,84,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,85,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,86,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,87,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,88,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,89,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,90,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,91,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,92,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,93,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,94,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,95,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,96,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,97,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,98,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,99,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,100,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,101,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,102,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,103,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,104,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,105,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,106,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,107,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,20,34);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,108,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,109,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,110,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,111,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,111,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,111,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,111,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,111,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,112,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,113,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,20,25);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,114,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,115,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,116,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,116,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,116,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,116,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,116,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,117,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,118,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,119,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,120,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,121,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,122,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,123,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,124,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,125,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,126,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,127,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,128,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,129,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,130,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,10,35);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,10,36);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,50,51);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,60,52);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,131,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,132,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,133,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,133,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,133,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,133,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,134,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,134,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,134,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,134,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,135,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,135,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,135,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,135,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,136,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,136,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,136,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,136,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,137,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,137,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,137,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,137,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,138,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,138,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,138,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,138,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,139,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,139,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,139,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,139,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,140,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,140,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,140,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,140,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,141,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,141,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,141,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,141,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,142,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,142,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,142,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,142,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,143,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,143,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,143,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,143,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,144,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,144,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,144,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,144,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,145,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,145,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,145,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,145,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,146,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,147,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,20,33);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,148,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,149,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,10,29);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,150,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,151,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,151,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,151,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,151,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,152,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,152,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,152,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,152,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,10,31);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,153,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,10,30);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,30,48);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,50,49);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,154,1,60,50);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,155,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,155,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,155,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,156,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,156,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,156,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,157,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,157,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,157,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,158,1,10,32);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,158,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,158,1,40,47);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,158,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,159,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,160,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,161,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,162,1,10,37);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,163,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,20,44);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,30,43);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,40,40);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,50,41);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,164,1,60,42);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,165,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,165,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,166,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,166,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,167,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,168,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,169,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,170,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,170,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,171,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,171,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,171,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,172,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,173,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,174,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,175,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,175,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,175,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,176,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,177,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,177,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,178,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,179,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,179,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,180,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,181,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,182,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,182,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,183,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,183,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,184,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,185,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,185,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,186,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,186,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,186,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,187,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,188,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,188,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,188,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,189,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,189,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,190,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,191,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,191,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,192,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,193,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,193,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,194,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,195,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,196,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,197,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,198,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,199,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,200,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,201,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,202,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,203,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,203,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,204,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,204,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,205,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,205,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,206,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,207,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,207,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,207,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,208,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,208,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,209,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,209,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,210,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,211,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,212,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,212,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,213,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,214,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,214,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,214,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,215,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,216,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,217,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,218,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,218,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,218,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,219,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,219,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,220,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,221,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,221,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,222,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,222,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,223,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,224,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,224,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,224,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,225,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,225,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,226,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,227,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,227,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,228,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,229,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,230,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,230,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,231,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,232,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,233,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,234,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,235,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,236,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,237,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,238,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,239,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,240,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,241,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,241,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,242,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,243,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,244,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,245,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,246,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,246,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,247,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,248,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,248,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,249,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,249,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,250,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,250,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,251,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,252,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,252,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,253,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,253,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,254,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,255,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,255,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,256,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,257,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,258,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,259,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,260,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,261,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,261,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,262,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,263,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,264,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,265,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,266,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,267,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,268,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,269,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,270,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,270,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,270,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,271,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,271,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,271,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,272,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,273,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,274,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,275,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,276,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,277,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,277,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,278,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,279,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,280,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,281,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,282,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,282,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,283,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,284,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,285,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,286,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,287,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,288,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,289,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,289,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,290,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,290,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,291,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,291,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,291,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,292,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,293,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,294,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,295,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,296,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,297,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,297,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,298,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,299,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,299,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,300,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,300,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,301,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,302,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,303,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,304,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,305,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,306,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,307,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,308,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,309,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,309,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,310,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,310,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,311,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,312,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,313,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,314,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,314,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,315,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,316,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,317,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,318,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,319,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,320,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,321,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,322,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,323,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,324,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,325,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,325,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,326,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,327,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,327,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,328,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,329,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,330,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,330,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,330,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,331,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,332,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,333,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,334,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,335,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,336,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,337,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,338,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,338,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,338,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,339,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,340,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,341,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,342,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,343,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,343,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,343,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,344,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,344,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,345,1,20,38);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,346,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,347,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,348,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,348,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,349,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,349,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,350,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,351,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,352,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,353,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,354,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,355,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,356,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,357,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,358,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,359,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,360,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,361,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,362,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,362,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,363,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,363,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,364,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,364,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,365,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,366,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,366,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,367,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,368,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,369,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,370,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,371,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,372,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,373,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,374,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,375,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,376,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,376,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,377,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,377,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,378,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,378,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,379,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,380,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,380,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,381,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,382,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,383,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,384,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,385,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,386,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,387,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,388,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,388,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,389,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,390,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,391,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,392,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,393,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,394,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,395,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,396,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,397,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,398,1,30,39);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,399,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,400,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,401,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,402,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,403,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,404,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,405,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,406,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,407,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,408,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,409,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,410,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,411,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,412,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,413,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,414,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,415,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,416,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,417,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,418,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,419,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,420,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,421,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,422,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,423,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,424,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,425,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,426,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,427,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,428,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,429,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,430,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,431,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,432,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,433,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,434,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,435,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,436,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,437,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,438,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,439,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,440,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,441,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,442,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,443,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,444,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,445,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,446,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,447,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,448,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,449,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,449,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,449,1,230,54);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,450,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,451,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,452,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,453,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,454,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,455,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,456,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,457,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,458,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,459,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,460,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,461,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,462,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,463,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,464,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,465,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,466,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,467,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,468,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,469,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,470,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,471,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,472,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,473,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,474,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,475,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,476,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,477,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,478,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,479,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,480,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,481,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,482,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,483,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,484,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,485,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,486,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,487,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,488,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,489,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,490,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,491,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,492,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,493,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,494,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,495,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,496,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,497,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,498,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,499,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,500,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,501,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,502,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,503,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,504,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,505,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,506,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,507,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,508,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,509,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,510,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,511,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,512,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,513,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,514,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,515,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,516,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,517,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,518,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,519,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,520,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,521,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,522,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,523,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,524,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,525,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,526,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,527,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,528,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,529,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,530,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,531,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,532,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,533,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,534,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,535,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,536,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,537,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,538,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,539,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,540,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,541,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,542,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,543,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,544,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,545,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,546,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,547,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,548,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,549,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,550,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,551,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,552,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,553,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,554,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,555,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,556,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,557,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,558,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,559,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,560,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,561,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,562,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,563,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,564,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,565,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,566,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,567,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,568,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,569,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,570,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,571,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,572,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,573,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,574,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,575,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,576,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,577,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,578,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,579,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,580,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,581,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,582,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,583,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,584,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,585,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,586,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,587,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,588,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,589,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,590,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,591,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,592,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,593,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,594,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,595,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,596,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,597,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,598,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,599,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,600,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,601,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,602,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,603,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,604,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,605,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,606,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,607,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,608,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,609,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,610,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,611,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,612,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,613,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,614,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,615,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,616,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,617,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,618,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,619,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,620,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,621,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,622,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,623,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,624,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,625,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,626,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,627,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,628,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,629,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,630,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,631,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,632,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,633,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,634,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,635,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,636,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,637,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,638,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,639,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,640,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,641,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,642,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,643,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,644,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,645,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,646,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,647,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,648,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,649,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,650,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,651,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,652,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,653,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,654,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,655,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,656,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,657,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,658,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,659,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,660,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,661,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,662,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,663,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,664,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,665,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,666,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,667,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,668,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,669,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,670,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,671,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,672,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,673,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,674,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,675,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,676,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,677,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,678,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,679,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,680,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,681,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,682,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,683,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,684,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,685,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,686,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,687,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,688,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,689,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,690,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,691,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,692,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,693,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,694,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,695,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,696,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,697,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,698,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,699,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,700,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,701,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,702,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,703,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,704,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,705,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,706,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,707,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,708,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,709,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,710,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,711,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,712,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,713,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,714,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,715,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,716,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,717,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,718,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,719,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,720,1,10,24);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,721,1,40,45);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,723,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,723,1,230,54);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,725,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,727,1,200,56);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,728,1,210,57);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,729,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,731,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,731,1,230,54);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,732,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,733,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,734,1,230,53);
INSERT INTO `ArticleIndex` (`IdPublication`, `IdLanguage`, `IdKeyword`, `NrIssue`, `NrSection`, `NrArticle`) VALUES (5,1,735,1,230,53);
/*!40000 ALTER TABLE `ArticleIndex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticlePublish`
--

DROP TABLE IF EXISTS `ArticlePublish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticlePublish` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_action` enum('P','U') DEFAULT NULL,
  `publish_on_front_page` enum('S','R') DEFAULT NULL,
  `publish_on_section_page` enum('S','R') DEFAULT NULL,
  `is_completed` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `event_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleTopics` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `TopicId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`NrArticle`,`TopicId`),
  KEY `article_topics_nrarticle_idx` (`NrArticle`),
  KEY `article_topics_topicid_idx` (`TopicId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleTopics`
--

LOCK TABLES `ArticleTopics` WRITE;
/*!40000 ALTER TABLE `ArticleTopics` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArticleTopics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleTypeMetadata`
--

DROP TABLE IF EXISTS `ArticleTypeMetadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleTypeMetadata` (
  `type_name` varchar(166) NOT NULL DEFAULT '',
  `field_name` varchar(166) NOT NULL DEFAULT 'NULL',
  `field_weight` int(11) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `comments_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `fk_phrase_id` int(10) unsigned DEFAULT NULL,
  `field_type` varchar(255) DEFAULT NULL,
  `field_type_param` varchar(255) DEFAULT NULL,
  `is_content_field` tinyint(1) NOT NULL DEFAULT '0',
  `max_size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`type_name`,`field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ArticleTypeMetadata`
--

LOCK TABLES `ArticleTypeMetadata` WRITE;
/*!40000 ALTER TABLE `ArticleTypeMetadata` DISABLE KEYS */;
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','NULL',NULL,0,1,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Byline',0,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Intro',1,0,0,NULL,'body',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Article','Full_text',2,0,0,NULL,'body',NULL,1,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','NULL',NULL,0,1,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Deck',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Byline',2,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Teaser_a',3,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Teaser_b',4,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Intro',5,0,0,NULL,'body',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Interview','Full_text',6,0,0,NULL,'body',NULL,1,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Service','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Service','Deck',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Service','Full_text',2,0,0,NULL,'body',NULL,1,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Deck',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Byline',2,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Teaser_a',3,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Teaser_b',4,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Intro',5,0,0,NULL,'body',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('Special','Full_text',6,0,0,NULL,'body',NULL,1,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('link','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('link','url',1,0,0,NULL,'text',NULL,0,NULL);
/*!40000 ALTER TABLE `ArticleTypeMetadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Articles`
--

DROP TABLE IF EXISTS `Articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Articles` (
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `NrIssue` int(10) unsigned NOT NULL DEFAULT '0',
  `NrSection` int(10) unsigned NOT NULL DEFAULT '0',
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(140) NOT NULL DEFAULT '',
  `Type` varchar(70) NOT NULL DEFAULT '',
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_default_author_id` int(10) unsigned DEFAULT NULL,
  `OnFrontPage` enum('N','Y') NOT NULL DEFAULT 'N',
  `OnSection` enum('N','Y') NOT NULL DEFAULT 'N',
  `Published` enum('N','S','M','Y') NOT NULL DEFAULT 'N',
  `PublishDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UploadDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Keywords` varchar(255) NOT NULL DEFAULT '',
  `Public` enum('N','Y') NOT NULL DEFAULT 'N',
  `IsIndexed` enum('N','Y') NOT NULL DEFAULT 'N',
  `LockUser` int(10) unsigned NOT NULL DEFAULT '0',
  `LockTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ShortName` varchar(32) NOT NULL DEFAULT '',
  `ArticleOrder` int(10) unsigned NOT NULL DEFAULT '0',
  `comments_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_locked` tinyint(1) NOT NULL DEFAULT '0',
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `object_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdPublication`,`NrIssue`,`NrSection`,`Number`,`IdLanguage`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Name`),
  UNIQUE KEY `Number` (`Number`,`IdLanguage`),
  UNIQUE KEY `other_key` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Number`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`ShortName`),
  KEY `Type` (`Type`),
  KEY `ArticleOrderIdx` (`ArticleOrder`),
  FULLTEXT KEY `articles_name_skey` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Articles`
--

LOCK TABLES `Articles` WRITE;
/*!40000 ALTER TABLE `Articles` DISABLE KEYS */;
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,40,40,1,'Morbi lacinia lacus','Article',3,NULL,'Y','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','40',39,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,60,52,1,'Phasellus in metus','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','52',51,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,36,1,'Phasellus in risus','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','36',36,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,37,1,'Donec pretium molestie','Special',3,0,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','37',38,0,0,'2010-07-09 15:28:44',0);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,20,38,1,'Open Source Democracy','Article',3,NULL,'Y','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','38',24,1,0,'2010-07-09 15:44:03',1);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,30,39,1,'Free E-Book Released','Article',3,0,'Y','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','39',24,1,0,'2010-07-09 15:44:38',2);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,35,1,'Praesent nulla magna','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','35',29,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,24,1,'Belgian government chooses OpenDocument','Article',3,NULL,'Y','N','Y','2005-08-03 00:00:00','2005-08-03 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','24',24,1,0,'2010-07-09 18:44:52',8);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,20,25,1,'Maecenas rutrum','Interview',3,0,'N','Y','Y','2005-08-03 00:00:00','2005-08-03 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','25',25,1,0,'2010-07-09 15:28:45',0);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,29,1,'Integer sit amet elit','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','29',30,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,30,1,'Lorem ipsum dolor sit amet','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','30',31,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,31,1,'Cras vestibulum ultrices arcu','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','31',32,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,10,32,1,'Praesent tincidunt vestibulum orci','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','32',33,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,20,33,1,'In iaculis lacus eu lorem','Interview',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','33',26,1,0,'2010-07-09 15:28:44',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,20,34,1,'Aenean rhoncus','Interview',3,0,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','34',34,1,0,'2010-07-09 15:28:44',0);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,20,44,1,'Integer et arcu lore','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','44',39,1,0,'2010-07-09 18:10:23',5);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,50,41,1,'Etiam aliquet euismod','Article',3,NULL,'Y','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','41',40,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,60,42,1,'Nunc adipiscing sodales','Article',3,NULL,'Y','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','42',41,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,30,43,1,'Cras semper lacus vel nunc','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','43',39,1,0,'2010-07-09 15:28:45',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,50,51,1,'Phasellus in','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','51',36,1,0,'2010-07-09 15:28:46',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,40,45,1,'Cras semper lacus vel nun','Article',3,NULL,'N','N','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','45',43,1,0,'2010-07-09 15:28:46',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,40,47,1,'Praesent tincidunt vestibulum','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','47',32,1,0,'2010-07-09 15:28:46',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,30,48,1,'Lorem ipsum dolor sit','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','48',30,1,0,'2010-07-09 18:10:32',6);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,50,49,1,'Lorem ipsum dolor','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','49',48,1,0,'2010-07-09 18:11:09',7);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,60,50,1,'Lorem ipsum','Article',3,NULL,'N','Y','Y','2005-08-04 00:00:00','2005-08-04 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','50',49,1,0,'2010-07-09 15:28:46',NULL);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,230,53,1,'Campsite','link',3,0,'N','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','53',53,0,0,'2010-07-09 18:14:16',0);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,230,54,1,'Sourcefabric','link',3,0,'N','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','54',54,0,0,'2010-07-09 18:13:35',0);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,200,56,1,'About us','Service',3,0,'N','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','56',26,0,0,'2010-07-09 18:07:09',3);
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (5,1,210,57,1,'Contact','Service',3,0,'N','N','Y','2005-08-15 00:00:00','2005-08-15 00:00:00','','Y','Y',0,'0000-00-00 00:00:00','57',46,0,0,'2010-07-09 18:07:12',4);
/*!40000 ALTER TABLE `Articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Attachments`
--

DROP TABLE IF EXISTS `Attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_language_id` int(10) unsigned DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `extension` varchar(50) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `content_disposition` enum('attachment') DEFAULT NULL,
  `http_charset` varchar(50) DEFAULT NULL,
  `size_in_bytes` bigint(20) unsigned DEFAULT NULL,
  `fk_description_id` int(11) DEFAULT NULL,
  `fk_user_id` int(10) unsigned DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachments`
--

LOCK TABLES `Attachments` WRITE;
/*!40000 ALTER TABLE `Attachments` DISABLE KEYS */;
INSERT INTO `Attachments` (`id`, `fk_language_id`, `file_name`, `extension`, `mime_type`, `content_disposition`, `http_charset`, `size_in_bytes`, `fk_description_id`, `fk_user_id`, `last_modified`, `time_created`) VALUES (2,NULL,'opensourcedemocracy.pdf','pdf','application/pdf','attachment',NULL,281519,2,1,'2006-07-06 03:20:37','2006-07-06 03:20:37');
/*!40000 ALTER TABLE `Attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AudioclipMetadata`
--

DROP TABLE IF EXISTS `AudioclipMetadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AudioclipMetadata` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gunid` varchar(20) NOT NULL DEFAULT '0',
  `predicate_ns` varchar(10) DEFAULT '',
  `predicate` varchar(30) NOT NULL DEFAULT '',
  `object` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gunid_tag_id` (`gunid`,`predicate_ns`,`predicate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Authors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authors_name_ukey` (`first_name`,`last_name`),
  FULLTEXT KEY `authors_name_skey` (`first_name`,`last_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Authors`
--

LOCK TABLES `Authors` WRITE;
/*!40000 ALTER TABLE `Authors` DISABLE KEYS */;
INSERT INTO `Authors` (`id`, `first_name`, `last_name`, `email`) VALUES (1,'','',NULL);
/*!40000 ALTER TABLE `Authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AutoId`
--

DROP TABLE IF EXISTS `AutoId`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AutoId` (
  `ArticleId` int(10) unsigned NOT NULL DEFAULT '0',
  `LogTStamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `TopicId` int(10) unsigned NOT NULL DEFAULT '0',
  `translation_phrase_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AutoId`
--

LOCK TABLES `AutoId` WRITE;
/*!40000 ALTER TABLE `AutoId` DISABLE KEYS */;
INSERT INTO `AutoId` (`ArticleId`, `LogTStamp`, `TopicId`, `translation_phrase_id`) VALUES (58,'2006-07-05 15:20:13',3,3);
/*!40000 ALTER TABLE `AutoId` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Classes`
--

DROP TABLE IF EXISTS `Classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Classes` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(140) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Countries` (
  `Code` char(2) NOT NULL DEFAULT '',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(140) NOT NULL DEFAULT '',
  PRIMARY KEY (`Code`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Dictionary` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Keyword` varchar(140) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdLanguage`,`Keyword`),
  UNIQUE KEY `Id` (`Id`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EnumerationElements` (
  `fk_enumeration_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `fk_phrase_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_enumeration_id`,`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Enumerations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Errors` (
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Message` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Number`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Events` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(140) NOT NULL DEFAULT '',
  `Notify` enum('N','Y') NOT NULL DEFAULT 'N',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`,`IdLanguage`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FailedLoginAttempts` (
  `ip_address` varchar(40) NOT NULL DEFAULT '',
  `time_of_attempt` bigint(20) NOT NULL DEFAULT '0',
  KEY `ip_address` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Images` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Description` varchar(255) NOT NULL DEFAULT '',
  `Photographer` varchar(255) NOT NULL DEFAULT '',
  `Place` varchar(255) NOT NULL DEFAULT '',
  `Caption` varchar(255) NOT NULL DEFAULT '',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `ContentType` varchar(64) NOT NULL DEFAULT '',
  `Location` enum('local','remote') NOT NULL DEFAULT 'local',
  `URL` varchar(255) NOT NULL DEFAULT '',
  `ThumbnailFileName` varchar(50) NOT NULL DEFAULT '',
  `ImageFileName` varchar(50) NOT NULL DEFAULT '',
  `UploadedByUser` int(11) DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TimeCreated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Images`
--

LOCK TABLES `Images` WRITE;
/*!40000 ALTER TABLE `Images` DISABLE KEYS */;
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (8,'Cras semper lacus vel nunc','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000008.jpg','cms-image-000000008.jpg',3,'2005-08-05 02:54:35','2005-08-05 01:34:17');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (9,'Image 9','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000009.jpg','cms-image-000000009.jpg',3,'2005-08-05 01:37:08','2005-08-05 01:37:08');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (3,'Image 3','Slobodan Gogic','','','2005-05-16','image/jpeg','local','','cms-thumb-000000003.jpg','cms-image-000000003.jpg',3,'2005-05-17 03:03:54','2005-05-17 03:03:54');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (4,'Image 4','Slobodan Gogic','','','2005-05-16','image/jpeg','local','','cms-thumb-000000004.jpg','cms-image-000000004.jpg',3,'2005-05-17 03:12:49','2005-05-17 03:12:49');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (5,'Image 5','Slobodan Gogic','','','2005-05-16','image/jpeg','local','','cms-thumb-000000005.jpg','cms-image-000000005.jpg',3,'2005-05-17 03:15:57','2005-05-17 03:15:57');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (6,'Image 6','Slobodan Gogic','','','2005-05-20','image/jpeg','local','','cms-thumb-000000006.jpg','cms-image-000000006.jpg',3,'2005-05-20 22:34:12','2005-05-20 22:34:11');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (7,'Image 7','Slobodan Gogic','','','2005-05-28','image/jpeg','local','','cms-thumb-000000007.jpg','cms-image-000000007.jpg',3,'2005-05-29 00:37:05','2005-05-29 00:37:05');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (10,'Image 10','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000010.jpg','cms-image-000000010.jpg',3,'2005-08-05 01:40:20','2005-08-05 01:40:20');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (11,'Image 11','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000011.jpg','cms-image-000000011.jpg',3,'2005-08-05 01:42:47','2005-08-05 01:42:46');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (12,'Lipsum dolor','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000012.jpg','cms-image-000000012.jpg',3,'2005-08-05 02:27:20','2005-08-05 02:26:43');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (13,'Praesent nulla magna','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000013.jpg','cms-image-000000013.jpg',3,'2005-08-05 02:47:36','2005-08-05 02:44:15');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (14,'Morbi lacinia lacus','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000014.jpg','cms-image-000000014.jpg',3,'2005-08-05 02:48:01','2005-08-05 02:44:50');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (15,'Etiam aliquet euismod','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000015.jpg','cms-image-000000015.jpg',3,'2005-08-05 02:49:03','2005-08-05 02:45:24');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (16,'Nunc adipiscing sodales','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000016.jpg','cms-image-000000016.jpg',3,'2005-08-05 02:49:31','2005-08-05 02:46:02');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (17,'Cras semper lacus vel nunc','Slobodan Gogic','','','2005-08-04','image/jpeg','local','','cms-thumb-000000017.jpg','cms-image-000000017.jpg',3,'2005-08-05 02:55:11','2005-08-05 02:46:29');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (18,'Belgium','Administrator','','','2006-07-05','image/png','local','','cms-thumb-000000018.png','cms-image-000000018.png',1,'2006-07-06 02:18:44','2006-07-06 02:18:43');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (19,'Everyone in Silico','Administrator','','','2006-07-05','image/jpeg','local','','cms-thumb-000000019.jpg','cms-image-000000019.jpg',1,'2006-07-06 02:40:30','2006-07-06 02:40:30');
INSERT INTO `Images` (`Id`, `Description`, `Photographer`, `Place`, `Caption`, `Date`, `ContentType`, `Location`, `URL`, `ThumbnailFileName`, `ImageFileName`, `UploadedByUser`, `LastModified`, `TimeCreated`) VALUES (20,'Demos','Administrator','','','2006-07-05','image/gif','local','','cms-thumb-000000020.gif','cms-image-000000020.gif',1,'2006-07-06 03:19:08','2006-07-06 03:19:08');
/*!40000 ALTER TABLE `Images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssuePublish`
--

DROP TABLE IF EXISTS `IssuePublish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssuePublish` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_issue_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_action` enum('P','U') NOT NULL DEFAULT 'P',
  `do_publish_articles` enum('N','Y') NOT NULL DEFAULT 'Y',
  `is_completed` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  KEY `issue_index` (`fk_publication_id`,`fk_issue_id`,`fk_language_id`),
  KEY `action_time_index` (`time_action`,`is_completed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Issues` (
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(140) NOT NULL DEFAULT '',
  `PublicationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Published` enum('N','Y') NOT NULL DEFAULT 'N',
  `IssueTplId` int(10) unsigned DEFAULT NULL,
  `SectionTplId` int(10) unsigned DEFAULT NULL,
  `ArticleTplId` int(10) unsigned DEFAULT NULL,
  `ShortName` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdPublication`,`Number`,`IdLanguage`),
  UNIQUE KEY `ShortName` (`IdPublication`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Issues`
--

LOCK TABLES `Issues` WRITE;
/*!40000 ALTER TABLE `Issues` DISABLE KEYS */;
INSERT INTO `Issues` (`IdPublication`, `Number`, `IdLanguage`, `Name`, `PublicationDate`, `Published`, `IssueTplId`, `SectionTplId`, `ArticleTplId`, `ShortName`) VALUES (5,1,1,'First issue','2005-08-03 00:00:00','Y',163,167,166,'1');
/*!40000 ALTER TABLE `Issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `KeywordClasses`
--

DROP TABLE IF EXISTS `KeywordClasses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `KeywordClasses` (
  `IdDictionary` int(10) unsigned NOT NULL DEFAULT '0',
  `IdClasses` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Definition` mediumblob NOT NULL,
  PRIMARY KEY (`IdDictionary`,`IdClasses`,`IdLanguage`),
  KEY `IdClasses` (`IdClasses`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `KeywordIndex` (
  `Keyword` varchar(70) NOT NULL DEFAULT '',
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `KeywordIndex`
--

LOCK TABLES `KeywordIndex` WRITE;
/*!40000 ALTER TABLE `KeywordIndex` DISABLE KEYS */;
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Aenean',1);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('rhoncus',2);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Sed',3);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('viverra',4);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Etiam',5);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('orc',6);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('In',7);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vitae',8);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dui',9);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vel',10);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sem',11);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('justo',12);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Donec',13);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('felis',14);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pede',15);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pretium',16);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eu',17);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('interdum',18);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('non',19);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lacinia',20);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('semper',21);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('est',22);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('massa',23);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ac',24);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('euismod',25);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('venenatis',26);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tortor',27);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('velit',28);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nulla',29);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vulputate',30);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('diam',31);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('quis',32);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Aliquam',33);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mollis',34);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ornare',35);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eros',36);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lectus',37);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('elit',38);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('molestie',39);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ut',40);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fermentum',41);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('et',42);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sit',43);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('amet',44);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('condimentum',45);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('risus',46);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Mauris',47);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ligula',48);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dolor',49);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('luctus',50);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('scelerisque',51);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('id',52);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nibh',53);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tempus',54);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('odio',55);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lobortis',56);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('erat',57);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('varius',58);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vestibulum',59);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tincidunt',60);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Quisque',61);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('imperdiet',62);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('placerat',63);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Proin',64);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('turpis',65);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vivamus',66);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hendrerit',67);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('egestas',68);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('libero',69);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('accumsan',70);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ante',71);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ipsum',72);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('primis',73);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('faucibus',74);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('orci',75);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ultrices',76);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('posuere',77);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('cubilia',78);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Curae',79);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('blandit',80);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('arcu',81);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eget',82);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('elementum',83);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('at',84);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Praesent',85);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Duis',86);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('suscipit',87);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('auctor',88);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('urna',89);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Nullam',90);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ultricies',91);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Curabitur',92);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nisl',93);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('augue',94);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pharetra',95);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('magna',96);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pulvinar',97);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ullamcorper',98);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Morbi',99);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sapien',100);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Suspendisse',101);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('leo',102);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Nam',103);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('metus',104);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('volutpat',105);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('potenti',106);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Fusce',107);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('consequat',108);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Phasellus',109);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('porta',110);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('laoreet',111);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sodales',112);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Maecenas',113);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('rutrum',114);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lorem',115);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('congue',116);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('feugiat',117);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('neque',118);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Cras',119);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lacus',120);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nunc',121);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tellus',122);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('porttitor',123);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('commodo',124);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nec',125);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mi',126);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('consectetuer',127);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('quam',128);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dapibus',129);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mattis',130);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tempor',131);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Pellentesque',132);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Class',133);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('aptent',134);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('taciti',135);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sociosqu',136);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ad',137);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('litora',138);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('torquent',139);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('per',140);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('conubia',141);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nostra',142);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('inceptos',143);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hymenaeos',144);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('cursus',145);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('gravida',146);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('facilisis',147);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('iaculis',148);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('vehicula',149);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Integer',150);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tristique',151);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nonummy',152);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('bibendum',153);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adipiscing',154);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sollicitudin',155);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('eleifend',156);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('enim',157);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('aliquet',158);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dignissim',159);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('purus',160);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fringilla',161);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('wisi',162);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lore',163);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('malesuada',164);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Open',165);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Source',166);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Democracy',167);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('How',168);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('online',169);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('communication',170);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('is',171);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('changing',172);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('offline',173);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('politics',174);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('The',175);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('internet',176);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('has',177);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('become',178);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('an',179);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('integral',180);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('part',181);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('of',182);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('our',183);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lives',184);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('because',185);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('it',186);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('interactive',187);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('That',188);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('means',189);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('people',190);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('are',191);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('senders',192);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('information',193);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('rather',194);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('than',195);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('simply',196);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('passive',197);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('receivers',198);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('‘old’',199);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('media',200);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Most',201);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('importantly',202);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('all',203);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('we',204);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('can',205);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('talk',206);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('to',207);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('each',208);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('other',209);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('without',210);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('gatekeepers',211);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('or',212);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('editors',213);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('This',214);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('offers',215);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('exciting',216);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('possibilities',217);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('for',218);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('new',219);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('social',220);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('networks',221);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('which',222);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('enabled',223);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('but',224);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('not',225);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('determined',226);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('by',227);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('digital',228);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('technology',229);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('software',230);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('industry',231);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('movement',232);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('emphasises',233);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('collective',234);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('cooperation',235);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('over',236);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('private',237);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ownership',238);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('radical',239);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('idea',240);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('may',241);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('provide',242);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('biggest',243);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('challenge',244);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dominance',245);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Microsoft',246);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('enthusiasts',247);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('have',248);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('found',249);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('more',250);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('efficient',251);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('way',252);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('working',253);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pooling',254);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('their',255);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('knowledge',256);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('encourage',257);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('innovation',258);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('happening',259);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('time',260);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('when',261);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('participation',262);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mainstream',263);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('electoral',264);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('declining',265);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('many',266);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Western',267);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('countries',268);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('including',269);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('US',270);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('and',271);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Britain',272);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('democracies',273);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('increasingly',274);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('resembling',275);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('old',276);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('with',277);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fewer',278);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('real',279);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('opportunities',280);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('interaction',281);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('What',282);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('asks',283);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Douglas',284);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Rushkoff',285);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('original',286);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('essay',287);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Demos',288);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('would',289);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('happen',290);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('if',291);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('\'source',292);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('code\'',293);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('code',294);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('democratic',295);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('systems',296);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('was',297);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('opened',298);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('up',299);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('they',300);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('meant',301);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('serve',302);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('‘An',303);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('model',304);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('participatory',305);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('bottom-up',306);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('bottom',307);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('emergent',308);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('policy',309);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('will',310);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('force',311);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('confront',312);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('issues',313);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('he',314);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('answers',315);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('That’s',316);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('profound',317);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('thought',318);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('governments',319);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('recognising',320);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('limits',321);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('centralised',322);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('political',323);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('institutions',324);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('community',325);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('recognises',326);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('solutions',327);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('problems',328);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('emerge',329);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('from',330);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('lots',331);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('central',332);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('planning',333);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('challenges',334);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('participate',335);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('redesign',336);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('enables',337);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('as',338);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('result',339);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('millions',340);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('interactions',341);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('indeed',342);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('be',343);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('able',344);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('change',345);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Free',346);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('E-Book',347);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Book',348);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Released',349);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Sci-fi',350);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Sci',351);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fi',352);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Everyone',353);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Silico',354);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('futurist',355);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('novel',356);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('set',357);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vancouver',358);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('2036',359);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('came',360);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('out',361);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('couple',362);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('years',363);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ago',364);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('week',365);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('author',366);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('decided',367);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('license',368);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('under',369);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Creative',370);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Commons',371);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('produce',372);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('downloadable',373);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ebook',374);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('versions',375);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('says',376);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('quot',377);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('So',378);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('you',379);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('like',380);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('send',381);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pals',382);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('link',383);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('e-mail',384);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mail',385);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('friends',386);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fileshare',387);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('on',388);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('illegal',389);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('you\'ll',390);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ll',391);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('helping',392);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('me',393);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('know',394);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('experience',395);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('I\'ll',396);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('reap',397);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dividends',398);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Belgian',399);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('government',400);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('chooses',401);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('OpenDocument',402);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Belgium\'s',403);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Belgium',404);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Council',405);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Ministers',406);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('last',407);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('month',408);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('approved',409);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('proposal',410);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('requires',411);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('federal',412);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('departments',413);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('use',414);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('file',415);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('formats',416);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('exchanging',417);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('documents',418);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('stands',419);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('now',420);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('only',421);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('accepted',422);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('standard',423);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Document',424);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Format',425);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ODF',426);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('increases',427);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('pressure',428);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('come',429);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('support',430);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('standards',431);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Peter',432);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Vanvelthoven',433);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Minister',434);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Employment',435);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('e-Government',436);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('explains',437);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Today',438);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('creation',439);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('exchange',440);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('office',441);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('based',442);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('different',443);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('popular',444);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('suites',445);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Corel',446);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('WordPerfect',447);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('OpenOffice',448);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('org',449);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('It\'s',450);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('always',451);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('easy',452);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('users',453);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('XML',454);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('especially',455);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('storage',456);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('draft',457);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('International',458);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Organisation',459);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ISO',460);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('soon',461);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('receives',462);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('final',463);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('approval',464);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('text',465);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('spreadsheets',466);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('presentations',467);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('first',468);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('phase',469);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('department',470);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('must',471);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('read',472);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('files',473);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('There',474);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('transition',475);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('period',476);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('take',477);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('necessary',478);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('measures',479);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('order',480);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('maintain',481);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('continuity',482);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('provision',483);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('services',484);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('exact',485);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('timeline',486);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('depend',487);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('impact',488);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('study',489);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('existence',490);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adequate',491);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('plugins',492);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('write',493);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Interoperability',494);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Strickx',495);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('general',496);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('manager',497);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('architecture',498);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Fedict',499);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('coordinates',500);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ICT',501);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('made',502);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('choice',503);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('want',504);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('advance',505);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('between',506);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Two',507);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('white',508);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('concerning',509);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('specifications',510);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('public',511);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sector',512);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('purchased',513);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('present',514);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('note',515);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('guideline',516);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('concrete',517);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('knows',518);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('receiver',519);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('edit',520);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('maintains',521);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('possible',522);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('word',523);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('processors',524);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('natively',525);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('underscores',526);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('importance',527);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('too',528);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Frank',529);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('De',530);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Graeve',531);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('PR',532);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('back',533);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('decision',534);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('where',535);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('also',536);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('agree',537);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('XML-based',538);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('best',539);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('store',540);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('important',541);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('flexible',542);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('holds',543);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('municipalities',544);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('provinces',545);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('obliged',546);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('same',547);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('goes',548);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('citizens',549);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('companies',550);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('still',551);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('allowed',552);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('internally',553);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('promotes',554);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('good',555);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('authority',556);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('doors',557);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Earlier',558);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('drafts',559);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('listed',560);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Microsoft\'s',561);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('included',562);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('2007',563);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('been',564);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('removed',565);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('no',566);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('market',567);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('supports',568);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Despite',569);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('absence',570);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('leaving',571);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('door',572);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Once',573);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('products',574);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('proposed',575);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('accept',576);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('extreme',577);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('doesn\'t',578);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('doesn',579);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('its',580);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('own',581);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('get',582);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('then',583);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('drop',584);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Consequently',585);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('getting',586);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('supporting',587);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('latter',588);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('thanks',589);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('direct',590);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('competitor',591);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Foundation',592);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('plugin',593);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adoption',594);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('necessarily',595);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mean',596);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('migrate',597);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('away',598);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('do',599);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('choose',600);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('right',601);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('strategy',602);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('move',603);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('However',604);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('unlikely',605);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('wait',606);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('until',607);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('release',608);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('since',609);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('both',610);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('dates',611);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('indefinite',612);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('future',613);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('acceptance',614);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('official',615);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('November',616);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('year',617);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('submitted',618);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('European',619);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('body',620);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ECMA',621);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('admission',622);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('procedure',623);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('going',624);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('well',625);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('fact',626);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('used',627);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('these',628);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('develop',629);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('proves',630);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('need',631);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('second',632);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('besides',633);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('few',634);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('customers',635);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('already',636);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('evaluated',637);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('concluded',638);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('didn\'t',639);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('didn',640);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('suit',641);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('needs',642);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('accepts',643);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('submit',644);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('committee',645);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('gets',646);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('specification',647);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('afterwards',648);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('decide',649);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('moment',650);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tell',651);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('procedures',652);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('completed',653);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('meantime',654);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('researching',655);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('whether',656);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('solution',657);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('suite',658);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('evaluate',659);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('forthcoming',660);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('migration',661);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('another',662);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('parameter',663);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('version',664);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('press',665);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mentioned',666);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('September',667);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('should',668);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('2008',669);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('conference',670);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('after',671);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('date',672);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hours',673);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('later',674);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('had',675);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('correct',676);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('reproduction',677);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('consensus',678);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('managers',679);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('publish',680);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adds',681);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Shortly',682);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('contacted',683);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('cabinet',684);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('things',685);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('unclear',686);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('seems',687);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('agreed',688);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('distributed',689);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('government\'s',690);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('That\'s',691);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('reason',692);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('asked',693);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('extra',694);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('explanation',695);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Moreover',696);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('timing',697);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('very',698);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('complex',699);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('depends',700);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('some',701);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('developments',702);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('ongoing',703);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('certification',704);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hasn\'t',705);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('hasn',706);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('yet',707);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('completely',708);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('clear',709);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('qualitative',710);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('haven\'t',711);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('haven',712);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('remains',713);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('seen',714);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('yield',715);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('details',716);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('one',717);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('thing',718);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('map',719);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('adapt',720);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('nun',721);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Campware',722);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('www',723);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('MDLF',724);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Web',725);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('mediaonweb',726);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('About',727);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Contact',728);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('Campsite',729);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('http',730);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('sourcefabric',731);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('en',732);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('home',733);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('htm',734);
INSERT INTO `KeywordIndex` (`Keyword`, `Id`) VALUES ('tpl',735);
/*!40000 ALTER TABLE `KeywordIndex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Languages` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(140) NOT NULL DEFAULT '',
  `CodePage` varchar(140) NOT NULL DEFAULT '',
  `OrigName` varchar(140) NOT NULL DEFAULT '',
  `Code` varchar(21) NOT NULL DEFAULT '',
  `Month1` varchar(140) NOT NULL DEFAULT '',
  `Month2` varchar(140) NOT NULL DEFAULT '',
  `Month3` varchar(140) NOT NULL DEFAULT '',
  `Month4` varchar(140) NOT NULL DEFAULT '',
  `Month5` varchar(140) NOT NULL DEFAULT '',
  `Month6` varchar(140) NOT NULL DEFAULT '',
  `Month7` varchar(140) NOT NULL DEFAULT '',
  `Month8` varchar(140) NOT NULL DEFAULT '',
  `Month9` varchar(140) NOT NULL DEFAULT '',
  `Month10` varchar(140) NOT NULL DEFAULT '',
  `Month11` varchar(140) NOT NULL DEFAULT '',
  `Month12` varchar(140) NOT NULL DEFAULT '',
  `WDay1` varchar(140) NOT NULL DEFAULT '',
  `WDay2` varchar(140) NOT NULL DEFAULT '',
  `WDay3` varchar(140) NOT NULL DEFAULT '',
  `WDay4` varchar(140) NOT NULL DEFAULT '',
  `WDay5` varchar(140) NOT NULL DEFAULT '',
  `WDay6` varchar(140) NOT NULL DEFAULT '',
  `WDay7` varchar(140) NOT NULL DEFAULT '',
  `ShortMonth1` varchar(20) DEFAULT NULL,
  `ShortMonth2` varchar(20) DEFAULT NULL,
  `ShortMonth3` varchar(20) DEFAULT NULL,
  `ShortMonth4` varchar(20) DEFAULT NULL,
  `ShortMonth5` varchar(20) DEFAULT NULL,
  `ShortMonth6` varchar(20) DEFAULT NULL,
  `ShortMonth7` varchar(20) DEFAULT NULL,
  `ShortMonth8` varchar(20) DEFAULT NULL,
  `ShortMonth9` varchar(20) DEFAULT NULL,
  `ShortMonth10` varchar(20) DEFAULT NULL,
  `ShortMonth11` varchar(20) DEFAULT NULL,
  `ShortMonth12` varchar(20) DEFAULT NULL,
  `ShortWDay1` varchar(20) DEFAULT NULL,
  `ShortWDay2` varchar(20) DEFAULT NULL,
  `ShortWDay3` varchar(20) DEFAULT NULL,
  `ShortWDay4` varchar(20) DEFAULT NULL,
  `ShortWDay5` varchar(20) DEFAULT NULL,
  `ShortWDay6` varchar(20) DEFAULT NULL,
  `ShortWDay7` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Languages`
--

LOCK TABLES `Languages` WRITE;
/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (9,'Portuguese','ISO_8859-1','Português','pt','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (12,'French','ISO_8859-1','Français','fr','Janvier','Février','Mars','Avril','Peut','Juin','Juli','Août','Septembre','Octobre','Novembre','Décembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (13,'Spanish','ISO_8859-1','Español','es','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (2,'Romanian','ISO_8859-2','Română','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (7,'Croatian','ISO_8859-2','Hrvatski','hr','Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (8,'Czech','ISO_8859-2','Český','cz','Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec','Neděle','Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (11,'Serbo-Croatian','ISO_8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (10,'Serbian (Cyrillic)','ISO_8859-5','Српски (Ћирилица)','sr','јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (15,'Russian','ISO_8859-5','Русский','ru','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','воскресенье','понедельник','вторник','среда','четверг','пятница','суббота',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (18,'Swedish','','Svenska','sv','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','söndag','måndag','tisdag','onsdag','torsdag','fredag','lördag',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (16,'Chinese','UTF-8','中文','zh','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','星期','星期','星期','星期','星期','星期','星期',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES (17,'Arabic','UTF-8','عربي','ar','كانون \nالثاني','شباط','آذار','نيسان','آيار','حزيران','تموز','آب','أيلول','تشرين \nأول','تشرين الثاني','كانون \nأول','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `Languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Log` (
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fk_event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_user_id` int(10) unsigned DEFAULT NULL,
  `text` varchar(255) NOT NULL DEFAULT '',
  `user_ip` int(10) unsigned DEFAULT NULL,
  KEY `IdEvent` (`fk_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Log`
--

LOCK TABLES `Log` WRITE;
/*!40000 ALTER TABLE `Log` DISABLE KEYS */;
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 13:00:40',54,0,'Password changed for Administrator (admin)',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 13:01:13',3,1,'Publication Dynamic (5) changed',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 13:01:25',3,1,'Publication My Publication (5) changed',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 13:03:40',55,1,'User permissions for Administrator (admin) changed',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:17:47',42,1,'Image 8 unlinked from 24',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:18:44',41,1,'The image Belgium (18) has been added.',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:18:44',42,1,'Image 18 linked to article 24',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:19:06',43,1,'Changed image properties of 18',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:27:21',72,1,'Article type field Deck deleted',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:40:06',42,1,'Image 13 unlinked from 39',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:40:30',41,1,'The image Everyone in Silico (19) has been added.',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:40:30',42,1,'Image 19 linked to article 39',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:40:40',43,1,'Changed image properties of 19',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:59:32',72,1,'Article type field Teaser_a deleted',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 14:59:36',72,1,'Article type field Teaser_b deleted',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 15:18:49',42,1,'Image 17 unlinked from 38',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 15:19:08',41,1,'The image Demos (20) has been added.',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 15:19:08',42,1,'Image 20 linked to article 38',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-07-05 15:19:26',43,1,'Changed image properties of 20',NULL);
INSERT INTO `Log` (`time_created`, `fk_event_id`, `fk_user_id`, `text`, `user_ip`) VALUES ('2006-08-24 18:50:01',3,1,'Publication My Publication (5) changed',NULL);
/*!40000 ALTER TABLE `Log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ObjectTypes`
--

DROP TABLE IF EXISTS `ObjectTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ObjectTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `OBJECTTYPES_NAME` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Plugins` (
  `Name` varchar(255) NOT NULL,
  `Version` varchar(255) NOT NULL DEFAULT '',
  `Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publications` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `IdDefaultLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `TimeUnit` enum('D','W','M','Y') NOT NULL DEFAULT 'D',
  `UnitCost` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `UnitCostAllLang` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `Currency` varchar(140) NOT NULL DEFAULT '',
  `TrialTime` int(10) unsigned NOT NULL DEFAULT '0',
  `PaidTime` int(10) unsigned NOT NULL DEFAULT '0',
  `IdDefaultAlias` int(10) unsigned NOT NULL DEFAULT '0',
  `IdURLType` int(10) unsigned NOT NULL DEFAULT '1',
  `fk_forum_id` int(11) DEFAULT NULL,
  `comments_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_article_default_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_subscribers_moderated` tinyint(1) NOT NULL DEFAULT '0',
  `comments_public_moderated` tinyint(1) NOT NULL DEFAULT '0',
  `comments_captcha_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_spam_blocking_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `url_error_tpl_id` int(10) unsigned DEFAULT NULL,
  `seo` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Alias` (`IdDefaultAlias`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publications`
--

LOCK TABLES `Publications` WRITE;
/*!40000 ALTER TABLE `Publications` DISABLE KEYS */;
INSERT INTO `Publications` (`Id`, `Name`, `IdDefaultLanguage`, `TimeUnit`, `UnitCost`, `UnitCostAllLang`, `Currency`, `TrialTime`, `PaidTime`, `IdDefaultAlias`, `IdURLType`, `fk_forum_id`, `comments_enabled`, `comments_article_default_enabled`, `comments_subscribers_moderated`, `comments_public_moderated`, `comments_captcha_enabled`, `comments_spam_blocking_enabled`, `url_error_tpl_id`, `seo`) VALUES (5,'My Publication',1,'M',1.00,2.00,'0',1,1,5,2,1,1,1,0,0,1,0,163,'a:0:{}');
/*!40000 ALTER TABLE `Publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RequestObjects`
--

DROP TABLE IF EXISTS `RequestObjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestObjects` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type_id` int(11) NOT NULL DEFAULT '0',
  `request_count` int(11) NOT NULL DEFAULT '0',
  `last_update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RequestObjects`
--

LOCK TABLES `RequestObjects` WRITE;
/*!40000 ALTER TABLE `RequestObjects` DISABLE KEYS */;
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (1,1,2,'2010-07-09 18:16:23');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (2,1,2,'2010-07-09 18:16:12');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (3,1,3,'2010-07-09 18:45:01');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (4,1,3,'2010-07-09 18:45:00');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (5,1,1,'2010-07-09 18:10:23');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (6,1,1,'2010-07-09 18:10:32');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (7,1,2,'2010-07-09 18:44:58');
INSERT INTO `RequestObjects` (`object_id`, `object_type_id`, `request_count`, `last_update_time`) VALUES (8,1,1,'2010-07-09 18:44:52');
/*!40000 ALTER TABLE `RequestObjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RequestStats`
--

DROP TABLE IF EXISTS `RequestStats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RequestStats` (
  `object_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL,
  `request_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`date`,`hour`),
  KEY `stats_object_idx` (`object_id`),
  KEY `stats_object_date_idx` (`object_id`,`date`),
  KEY `stats_object_hour_idx` (`object_id`,`hour`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RequestStats`
--

LOCK TABLES `RequestStats` WRITE;
/*!40000 ALTER TABLE `RequestStats` DISABLE KEYS */;
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (1,'2010-07-09',18,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (2,'2010-07-09',18,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (3,'2010-07-09',21,3);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (4,'2010-07-09',21,3);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (5,'2010-07-09',21,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (6,'2010-07-09',21,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (7,'2010-07-09',21,2);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (2,'2010-07-09',21,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (1,'2010-07-09',21,1);
INSERT INTO `RequestStats` (`object_id`, `date`, `hour`, `request_count`) VALUES (8,'2010-07-09',21,1);
/*!40000 ALTER TABLE `RequestStats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Requests`
--

DROP TABLE IF EXISTS `Requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Requests` (
  `session_id` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL,
  `last_stats_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_id`,`object_id`),
  KEY `requests_session_idx` (`session_id`),
  KEY `requests_object_idx` (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Requests`
--

LOCK TABLES `Requests` WRITE;
/*!40000 ALTER TABLE `Requests` DISABLE KEYS */;
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('d4fa3ko8tu1r7u2bmb6fu7mie5',1,'2010-07-09 18:44:03');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('d4fa3ko8tu1r7u2bmb6fu7mie5',2,'2010-07-09 18:44:38');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('26r7ilabvpmcmiq9gots63odq2',3,'2010-07-09 21:07:09');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('26r7ilabvpmcmiq9gots63odq2',4,'2010-07-09 21:07:12');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',5,'2010-07-09 21:10:23');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',6,'2010-07-09 21:10:32');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',3,'2010-07-09 21:10:59');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',4,'2010-07-09 21:11:00');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',7,'2010-07-09 21:11:09');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',2,'2010-07-09 21:16:12');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('optnk7f9313lpp23ecpi1amsh2',1,'2010-07-09 21:16:23');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('6f8lb0k3bi9trlvbhu1a6fb486',8,'2010-07-09 21:44:52');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('6f8lb0k3bi9trlvbhu1a6fb486',7,'2010-07-09 21:44:58');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('6f8lb0k3bi9trlvbhu1a6fb486',4,'2010-07-09 21:45:00');
INSERT INTO `Requests` (`session_id`, `object_id`, `last_stats_update`) VALUES ('6f8lb0k3bi9trlvbhu1a6fb486',3,'2010-07-09 21:45:01');
/*!40000 ALTER TABLE `Requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sections`
--

DROP TABLE IF EXISTS `Sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sections` (
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `NrIssue` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `ShortName` varchar(32) NOT NULL DEFAULT '',
  `Description` blob,
  `SectionTplId` int(10) unsigned DEFAULT NULL,
  `ArticleTplId` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdPublication`,`NrIssue`,`IdLanguage`,`Number`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`IdLanguage`,`Name`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`IdLanguage`,`ShortName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Sections`
--

LOCK TABLES `Sections` WRITE;
/*!40000 ALTER TABLE `Sections` DISABLE KEYS */;
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,200,'About us','about',NULL,122,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,210,'Contact','contact',NULL,122,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,60,'Sport','sport',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,10,'News','news',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,20,'Culture','culture',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,30,'Economy','economy',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,40,'Education','education',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,50,'Politics','politics',NULL,NULL,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,220,'Archive','archive',NULL,109,NULL);
INSERT INTO `Sections` (`IdPublication`, `NrIssue`, `IdLanguage`, `Number`, `Name`, `ShortName`, `Description`, `SectionTplId`, `ArticleTplId`) VALUES (5,1,1,230,'Links','links',NULL,NULL,NULL);
/*!40000 ALTER TABLE `Sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sessions`
--

DROP TABLE IF EXISTS `Sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sessions` (
  `id` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Sessions`
--

LOCK TABLES `Sessions` WRITE;
/*!40000 ALTER TABLE `Sessions` DISABLE KEYS */;
INSERT INTO `Sessions` (`id`, `start_time`, `user_id`) VALUES ('d4fa3ko8tu1r7u2bmb6fu7mie5','2010-07-09 18:44:03',NULL);
INSERT INTO `Sessions` (`id`, `start_time`, `user_id`) VALUES ('26r7ilabvpmcmiq9gots63odq2','2010-07-09 21:07:09',NULL);
INSERT INTO `Sessions` (`id`, `start_time`, `user_id`) VALUES ('optnk7f9313lpp23ecpi1amsh2','2010-07-09 21:10:23',NULL);
INSERT INTO `Sessions` (`id`, `start_time`, `user_id`) VALUES ('6f8lb0k3bi9trlvbhu1a6fb486','2010-07-09 21:44:52',NULL);
/*!40000 ALTER TABLE `Sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SubsByIP`
--

DROP TABLE IF EXISTS `SubsByIP`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SubsByIP` (
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `StartIP` int(10) unsigned NOT NULL DEFAULT '0',
  `Addresses` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdUser`,`StartIP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SubsDefTime` (
  `CountryCode` char(21) NOT NULL DEFAULT '',
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `TrialTime` int(10) unsigned NOT NULL DEFAULT '0',
  `PaidTime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`CountryCode`,`IdPublication`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SubsSections` (
  `IdSubscription` int(10) unsigned NOT NULL DEFAULT '0',
  `SectionNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) NOT NULL DEFAULT '0',
  `StartDate` date NOT NULL DEFAULT '0000-00-00',
  `Days` int(10) unsigned NOT NULL DEFAULT '0',
  `PaidDays` int(10) unsigned NOT NULL DEFAULT '0',
  `NoticeSent` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`IdSubscription`,`SectionNumber`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Subscriptions` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `Active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `ToPay` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `Currency` varchar(70) NOT NULL DEFAULT '',
  `Type` enum('T','P') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdUser` (`IdUser`,`IdPublication`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SystemPreferences` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `varname` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(100) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SystemPreferences`
--

LOCK TABLES `SystemPreferences` WRITE;
/*!40000 ALTER TABLE `SystemPreferences` DISABLE KEYS */;
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (1,'ExternalSubscriptionManagement','N','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (2,'KeywordSeparator',',','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (3,'LoginFailedAttemptsNum','3','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (4,'MaxUploadFileSize','40M','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (5,'UseDBReplication','N','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (6,'DBReplicationHost','','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (7,'DBReplicationUser','','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (8,'DBReplicationPass','','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (9,'DBReplicationPort','3306','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (10,'UseCampcasterAudioclips','N','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (11,'CampcasterHostName','localhost','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (12,'CampcasterHostPort','80','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (13,'CampcasterXRPCPath','/campcaster/storageServer/var/xmlrpc/','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (14,'CampcasterXRPCFile','xrLocStor.php','2008-05-02 15:30:43');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (15,'SiteOnline','Y','2007-10-07 06:49:11');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (16,'SiteCharset','utf-8','2007-07-26 09:49:32');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (17,'SiteLocale','en-US','2007-07-26 09:49:56');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (18,'SiteCacheEnabled','N','2010-07-09 18:08:08');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (22,'SiteMetaKeywords','Campsite, MDLF, Campware, CMS, OpenSource, Media','2007-10-05 06:31:36');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (19,'SiteSecretKey','4b506c2968184be185f6282f5dcac832','2007-10-05 01:51:41');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (20,'SiteSessionLifeTime','1400','2007-10-05 01:51:51');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (21,'SiteTitle','Campsite 3.0','2007-10-07 06:39:13');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (23,'SiteMetaDescription','Campsite 3.0 site, try it out!','2007-10-07 06:36:18');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (24,'SMTPHost','localhost','2007-10-26 06:30:45');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (25,'SMTPPort','25','2007-10-26 06:30:45');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (26,'CacheEngine','APC','2010-07-09 14:04:29');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (27,'EditorImageRatio','100','2010-07-09 14:04:29');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (28,'TemplateFilter','.*, CVS','2010-07-09 14:04:29');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (29,'ImagecacheLifetime','86400','2010-07-09 14:04:29');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (30,'EditorImageResizeWidth','','2010-07-09 14:04:31');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (31,'EditorImageResizeHeight','','2010-07-09 14:04:31');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (32,'EditorImageZoom','N','2010-07-09 14:04:31');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (33,'TimeZone',NULL,'2010-07-09 14:04:31');
INSERT INTO `SystemPreferences` (`id`, `varname`, `value`, `last_modified`) VALUES (34,'ExternalCronManagement','Y','2010-07-09 14:04:31');
/*!40000 ALTER TABLE `SystemPreferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TemplateTypes`
--

DROP TABLE IF EXISTS `TemplateTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TemplateTypes` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Templates` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` char(255) NOT NULL DEFAULT '',
  `Type` int(10) unsigned NOT NULL DEFAULT '1',
  `Level` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Templates`
--

LOCK TABLES `Templates` WRITE;
/*!40000 ALTER TABLE `Templates` DISABLE KEYS */;
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (68,'search-box.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (165,'search.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (88,'home-article.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (86,'logout.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (120,'home-news.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (123,'service-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (85,'footer-01.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (110,'right.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (122,'service.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (162,'header-01.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (109,'archive.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (164,'footer.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (112,'section-rest.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (111,'search-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (116,'article-special.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (98,'article-specijal.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (115,'article-interview.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (94,'login-box.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (95,'section-article.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (108,'archive-middle.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (107,'menu.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (99,'do_login.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (166,'article.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (92,'header.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (114,'article-article.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (167,'section.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (163,'home.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (113,'section-news.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (119,'home-rest.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (102,'article-intervju.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (103,'do_login-cont.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (104,'banner.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (117,'article-complete.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (118,'home-culture.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (121,'section-special.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (128,'subscribe.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (129,'subscribe-form.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (130,'subscribe-info.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (131,'do_subscribe.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (132,'rss.tpl',1,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (133,'01-style.css',5,0);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (134,'img/tb.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (135,'img/01.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (136,'img/baner4.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (137,'img/06linija.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (138,'img/napred.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (139,'img/ffox.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (140,'img/04bgmeni.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (141,'img/nazad.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (142,'img/05bgmeni2.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (143,'img/islinija1.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (144,'img/cover.jpg',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (145,'img/transpa.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (146,'img/bann1.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (147,'img/desno2.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (148,'img/packaged.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (149,'img/bann2.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (150,'img/03bg.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (151,'img/desno1.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (152,'img/06linijabel.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (153,'img/05bgmeni2b.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (154,'img/02bg.gif',5,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (155,'system_templates/_subscription_notifier.tpl',1,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (156,'system_templates/_campsite_offline.tpl',1,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (157,'system_templates/_events_notifier.tpl',1,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (158,'system_templates/_campsite_error.tpl',1,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (159,'system_templates/_campsite_message.tpl',1,1);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (160,'system_templates/img/campsite_logo_gn.jpg',5,2);
INSERT INTO `Templates` (`Id`, `Name`, `Type`, `Level`) VALUES (161,'system_templates/css/_style_offline.css',5,2);
/*!40000 ALTER TABLE `Templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TimeUnits`
--

DROP TABLE IF EXISTS `TimeUnits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TimeUnits` (
  `Unit` char(1) NOT NULL DEFAULT '',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`Unit`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TopicFields` (
  `ArticleType` varchar(166) NOT NULL DEFAULT '',
  `FieldName` varchar(166) NOT NULL DEFAULT '',
  `RootTopicId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ArticleType`,`FieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Topics` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `LanguageId` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `TopicOrder` int(11) NOT NULL DEFAULT '0',
  `ParentId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`,`LanguageId`),
  UNIQUE KEY `Name` (`LanguageId`,`Name`),
  KEY `topic_id` (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Topics`
--

LOCK TABLES `Topics` WRITE;
/*!40000 ALTER TABLE `Topics` DISABLE KEYS */;
INSERT INTO `Topics` (`Id`, `LanguageId`, `Name`, `TopicOrder`, `ParentId`) VALUES (2,1,'Specijal',1,0);
/*!40000 ALTER TABLE `Topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Translations`
--

DROP TABLE IF EXISTS `Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phrase_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `translation_text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phrase_language_index` (`phrase_id`,`fk_language_id`),
  KEY `phrase_id` (`phrase_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Translations`
--

LOCK TABLES `Translations` WRITE;
/*!40000 ALTER TABLE `Translations` DISABLE KEYS */;
INSERT INTO `Translations` (`id`, `phrase_id`, `fk_language_id`, `translation_text`) VALUES (2,2,1,'\"Open Source Democracy\" paper');
INSERT INTO `Translations` (`id`, `phrase_id`, `fk_language_id`, `translation_text`) VALUES (3,3,1,'article');
/*!40000 ALTER TABLE `Translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `URLTypes`
--

DROP TABLE IF EXISTS `URLTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `URLTypes` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(15) NOT NULL DEFAULT '',
  `Description` mediumblob,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `XArticle` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `FByline` varchar(255) NOT NULL DEFAULT '',
  `FIntro` mediumblob NOT NULL,
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `XArticle`
--

LOCK TABLES `XArticle` WRITE;
/*!40000 ALTER TABLE `XArticle` DISABLE KEYS */;
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (24,1,'','Belgium\'s Council of Ministers last month approved a\r\nproposal that requires federal government departments to use open file\r\nformats for exchanging documents. As it stands now, the only accepted\r\nstandard is the Open Document Format (ODF). This increases the pressure\r\non Microsoft to come up with support for open standards.<br />','Peter Vanvelthoven, Minister of Employment and\r\ne-Government, explains: &quot;Today the creation and exchange of office\r\ndocuments is based on different popular office suites, like Microsoft\r\nOffice, Corel WordPerfect Office, OpenOffice.org ... It\'s not always\r\neasy to exchange documents with users of other software. But with XML\r\nand especially ODF we have a standard for the creation and storage of\r\ndocuments.&quot;<br />\r\n\r\n A draft of ODF was approved by the International\r\nStandards Organisation (ISO) in May. As soon as it receives final\r\napproval, Vanvelthoven says, &quot;the federal government departments will\r\nhave to use ODF for the exchange of office documents like text\r\ndocuments, spreadsheets, and presentations.&quot;<br />\r\n\r\nIn a first phase, each federal government department\r\nmust be able to read ODF files. There will be a transition period to\r\ntake the necessary measures in order to maintain the continuity of the\r\nprovision of services. &quot;The exact timeline will depend on an impact\r\nstudy and the existence of adequate plugins to read and write the ODF\r\nformat.&quot;<br />\r\n\r\n <strong><br />Interoperability</strong> <br />\r\n\r\nPeter Strickx is general manager for architecture\r\nand standards of Fedict, the government department that coordinates the\r\nICT policy of the Belgian federal government. He says, &quot;We made the\r\nchoice for open standards because we want to advance interoperability\r\nbetween the different federal government departments. Two years ago the\r\nCouncil of Ministers approved a white book concerning the use of open\r\nstandards and open specifications for public sector purchased software.\r\nThe present note of the Council of Ministers made this guideline\r\nconcrete for the federal government departments.<br />\r\n\r\n&quot;The author of an ODF document knows that the\r\nreceiver can read and edit the document,&quot; Strickx maintains. &quot;This is\r\npossible in all word processors that support open standards, natively\r\nor with plugins.&quot; <br />\r\n\r\nMicrosoft underscores the importance of open\r\nstandards too. Frank De Graeve, PR manager of Microsoft Belgium, says,\r\n&quot;We back the decision of the government to use open standards where\r\nit\'s possible. We also agree that XML-based file formats are the best\r\nsolutions to store important information in a flexible way.&quot;<br />\r\n\r\nThe note of the Council of Ministers holds only for\r\nfederal government departments. This means that municipalities and\r\nprovinces are not obliged to use open standards for document exchange.\r\nThe same goes for communication between the government and citizens or\r\ncompanies. And &quot;federal government departments are still allowed to use\r\nother formats internally,&quot; Strickx says. &quot;Fedict promotes open\r\nstandards because it\'s good for the interoperability. What the\r\ndepartments use internally is not the authority of Fedict.&quot;<br />\r\n\r\n <strong><br />Open standards, open doors</strong> <br />\r\n\r\nIn the present note of the Council of Ministers, the\r\nonly accepted format is ODF. Earlier drafts of the proposal also listed\r\nMicrosoft\'s Open XML format, which is to be included in Office 2007,\r\nbut that format has been removed because there is no software on the\r\nmarket that supports Open XML. <br />\r\n\r\nDespite the absence of Open XML in the present note,\r\nthe Belgian government is leaving the door open for Microsoft\'s format.\r\nOnce products support the format and it\'s proposed as a standard to\r\nISO, it\'s possible the government may accept Open XML. At the other\r\nextreme, if Microsoft doesn\'t support ODF and if its own Open XML\r\nformat doesn\'t get approved as an open standard, then the government\r\ndepartments will have to drop Microsoft Office. Consequently, Microsoft\r\nhas the choice between getting Open XML approved as an open standard or\r\nsupporting ODF. The latter may happen thanks to Microsoft\'s direct\r\ncompetitor: the OpenDocument Foundation is working on a plugin for\r\nMicrosoft Office that supports ODF.<br />\r\n\r\nSo the adoption of ODF doesn\'t necessarily mean that\r\nthe Belgian government will migrate away from Microsoft Office. Fedict\r\nwill do an impact study to choose the right strategy for the move to\r\nODF. However, it\'s unlikely it will wait until the release of Microsoft\r\nOffice 2007 or the ISO approval of Open XML, since both dates are in\r\nthe indefinite future.<br />\r\n\r\n <strong><br />Open XML</strong> <br />\r\n\r\nMicrosoft\'s De Graeve maintains that Open XML is on\r\nits way to acceptance as an official open standard. &quot;In November last\r\nyear we submitted the Open XML file format to the European standards\r\nbody ECMA, and the admission procedure is going well. The fact that the\r\nopen source community has used these specifications to develop a plugin\r\nto open Open XML files in OpenOffice.org proves that it\'s an open\r\nstandard. It also proves that there is a need for a second XML-based\r\nopen document format besides ODF. A few of our customers have already\r\nevaluated ODF and they have concluded that the file format didn\'t suit\r\ntheir needs.&quot;<br />\r\n\r\nAs soon as ECMA accepts Open XML, Microsoft can\r\nsubmit the format to the ISO committee. If the format gets an ISO\r\nspecification afterwards, the Belgian government can decide to use Open\r\nXML too. &quot;But at the moment we are not able to tell when the ECMA and\r\nISO procedures will get completed,&quot; De Graeve says. In the meantime, he\r\nsays, Microsoft is researching whether ODF support in Microsoft Office\r\nwould be a good solution for the government departments that use the\r\noffice suite. &quot;We will evaluate forthcoming ODF plugins as soon as\r\npossible.&quot;<br />\r\n\r\n <strong><br />Timeline</strong> <br />\r\n\r\nThe timeline of the ODF migration is another\r\nindefinite parameter. The first version of the press release of the\r\nCouncil of Ministers mentioned two dates: from September 2007 on, all\r\nfederal government departments should be able to read ODF documents,\r\nand in September 2008 ODF would be the only accepted document format\r\nfor exchange between departments. In the press conference after the\r\nCouncil of Ministers, Minister Vanvelthoven mentioned the September\r\n2007 date. A few hours later, both dates had been removed from the\r\npress release. Strickx explains: &quot;The first press release was not a\r\ncorrect reproduction of the decision of the Council of Ministers.\r\nFedict is now working on a consensus on the dates between the IT\r\nmanagers. We will publish the dates as soon as the proposal has been\r\napproved.&quot; De Graeve adds: &quot;Shortly after we read the press release, we\r\nhave contacted the cabinet of Minister Vanvelthoven to get more\r\ninformation. We found a couple of things in the press release unclear.\r\nIt seems Fedict agreed with us, because they distributed a new press\r\nrelease.&quot;<br />\r\n\r\nWhat the Belgian government\'s move to ODF means for\r\nMicrosoft is still unclear, De Graeve says. &quot;That\'s the reason we asked\r\nfor an extra explanation concerning the exact decision of the Council\r\nof Ministers. Moreover, the timing of the decision is very complex\r\nbecause it depends on some developments which are ongoing. The\r\ncertification of Open XML hasn\'t been completed yet, it\'s not yet\r\ncompletely clear if there will be qualitative good ODF plugins for\r\nMicrosoft Office, and we haven\'t released Microsoft Office 2007 yet.&quot;<br />\r\n\r\nIt remains to be seen what the decision of the\r\nBelgian government to use ODF for exchanging documents will yield. The\r\nexact details are still to come. But one thing is clear: open standards\r\nare on the map in Belgium, and software companies have to adapt if they\r\nwant their products to be used by the government.<br />');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (30,1,'Morbi a ligula','Integer sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (29,1,'Sed lectus elit','\r\nn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.\r\n\r\nIn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo. Nam aliquam suscipit augue. Duis mi dui, consectetuer id, venenatis lobortis, luctus a, quam. Ut et diam. Fusce id turpis ut nulla dapibus lacinia. Nam eget felis sed risus mattis rhoncus. Aenean vitae nulla. In vel lacus at turpis sodales tempor.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (31,1,'Gravida sed, euismod quis','Cras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos.','\r\nCras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. In sit amet risus quis eros porttitor cursus. Phasellus elit odio, euismod vitae, scelerisque eget, pellentesque eu, felis. Proin lorem. Duis sapien est, gravida sed, euismod quis, feugiat eu, lacus. Sed ultrices semper leo. Nulla nec tortor facilisis leo auctor pellentesque. Sed porta iaculis nisl. Morbi lacinia lacus. Nunc eget libero nec mi tempor vulputate. Sed interdum placerat mi. Curabitur a lacus. Aliquam vehicula. Suspendisse rutrum facilisis justo. Quisque nisl. Integer tristique dolor faucibus ipsum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mollis. In nonummy, libero ut consequat vehicula, ipsum diam commodo libero, at bibendum dolor lorem eget odio.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (32,1,'Nunc adipiscing','\r\nPraesent tincidunt vestibulum orci. Nunc adipiscing sodales massa. Sed nisl. Praesent nulla magna, blandit sed, vulputate et, pellentesque non, metus.','\r\nPraesent tincidunt vestibulum orci. Nunc adipiscing sodales massa. Sed nisl. Praesent nulla magna, blandit sed, vulputate et, pellentesque non, metus. Ut erat risus, sollicitudin et, eleifend non, aliquam id, turpis. Nunc ut dolor quis nibh egestas gravida. Aenean hendrerit arcu et lacus. Phasellus nisl ipsum, lacinia ac, dapibus eu, eleifend ut, enim. Fusce facilisis magna sit amet mauris. Etiam vestibulum. Curabitur in lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mattis ligula sed metus. Suspendisse interdum tempus metus. Mauris aliquet auctor nisl.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (35,1,'Sed lectus elit','\r\nn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.\r\n\r\nIn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo. Nam aliquam suscipit augue. Duis mi dui, consectetuer id, venenatis lobortis, luctus a, quam. Ut et diam. Fusce id turpis ut nulla dapibus lacinia. Nam eget felis sed risus mattis rhoncus. Aenean vitae nulla. In vel lacus at turpis sodales tempor.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (36,1,'Sed lectus elit','\r\nn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.\r\n\r\nIn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo. Nam aliquam suscipit augue. Duis mi dui, consectetuer id, venenatis lobortis, luctus a, quam. Ut et diam. Fusce id turpis ut nulla dapibus lacinia. Nam eget felis sed risus mattis rhoncus. Aenean vitae nulla. In vel lacus at turpis sodales tempor.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (38,1,'How online communication is changing offline politics','The internet has become an integral part of our\r\nlives because it is  interactive. That means people are senders of\r\ninformation, rather  than simply passive receivers of ‘old’ media. Most\r\nimportantly of all, we can  talk to each other without gatekeepers or\r\neditors. This offers  exciting possibilities for new social networks,\r\nwhich are enabled - but not determined - by digital technology. <br />','In the software industry, the open source\r\nmovement emphasises  collective cooperation over private ownership.\r\nThis radical idea may provide the biggest challenge to the dominance of\r\nMicrosoft. Open  source enthusiasts have found a more efficient way of\r\nworking by  pooling their knowledge to encourage innovation.         <br /><br />\r\n\r\nAll this is happening at a time when\r\nparticipation in  mainstream electoral politics is declining in many\r\nWestern countries,  including the US and Britain. Our democracies are\r\nincreasingly  resembling old media, with fewer real opportunities for\r\ninteraction.        <br /><br />\r\n\r\nWhat, asks Douglas Rushkoff in this original\r\nessay for Demos, would happen if the \'source code\' of our democratic\r\nsystems was  opened up to the people they are meant to serve? ‘An open\r\nsource  model for participatory, bottom-up and emergent policy will\r\nforce us to confront the issues of our time,’ he answers.<br /><br />\r\n\r\nThat’s a profound thought at a time when\r\ngovernments are  recognising the limits of centralised political\r\ninstitutions. The open source community recognises that solutions to\r\nproblems emerge  from the interaction and participation of lots of\r\npeople, not by central planning.  <br />     <br />\r\n\r\nRushkoff challenges us all to participate in the\r\nredesign of  political institutions in a way which enables new\r\nsolutions to social  problems to emerge as the result of millions\r\ninteractions. In this way, online communication may indeed be able to\r\nchange offline politics.<br />');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (39,1,'Sci-fi for free','<a href=\"http://nomediakings.org/IMadeMain.htm\">Everyone in Silico</a> is a futurist sci-fi novel set in Vancouver, 2036. It came out <a href=\"http://www.amazon.com/exec/obidos/tg/detail/-/1568582404/\">a couple years ago</a>, but this week <a href=\"http://nomediakings.org/writing/free_ebook_released.html\">the author decided to license it under Creative Commons</a>\r\nand produce free downloadable ebook versions. As the author says &quot;So if\r\nyou like the book, send pals this link, e-mail it to friends, fileshare\r\nit on illegal networks -- you\'ll be helping me out. I know from\r\nexperience that I\'ll reap dividends.&quot;<br />','<br />');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (40,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (41,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (42,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (43,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (44,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (45,1,'Nam aliquam','\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi semper gravida dui. Vivamus orci. Fusce eget nibh ut mauris blandit semper. Nulla sodales vehicula augue.','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate. Aliquam at sapien et eros porta mattis. Pellentesque hendrerit ornare ante. Nam vitae justo lobortis pede posuere scelerisque. Quisque malesuada libero. Maecenas nulla mi, feugiat a, fermentum id, iaculis interdum, metus. Phasellus accumsan velit in massa.\r\n\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit. ');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (47,1,'Nunc adipiscing','\r\nPraesent tincidunt vestibulum orci. Nunc adipiscing sodales massa. Sed nisl. Praesent nulla magna, blandit sed, vulputate et, pellentesque non, metus.','\r\nPraesent tincidunt vestibulum orci. Nunc adipiscing sodales massa. Sed nisl. Praesent nulla magna, blandit sed, vulputate et, pellentesque non, metus. Ut erat risus, sollicitudin et, eleifend non, aliquam id, turpis. Nunc ut dolor quis nibh egestas gravida. Aenean hendrerit arcu et lacus. Phasellus nisl ipsum, lacinia ac, dapibus eu, eleifend ut, enim. Fusce facilisis magna sit amet mauris. Etiam vestibulum. Curabitur in lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mattis ligula sed metus. Suspendisse interdum tempus metus. Mauris aliquet auctor nisl.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (48,1,'Morbi a ligula','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (49,1,'Morbi a ligula','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (50,1,'Morbi a ligula','\r\nInteger sit amet elit. Aenean accumsan bibendum libero. Integer nec ligula. Nunc egestas. Morbi a ligula at mi sodales pretium. Phasellus adipiscing. Vivamus eu elit ac ante pulvinar vulputate.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (51,1,'Sed lectus elit','\r\nn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.\r\n\r\nIn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo. Nam aliquam suscipit augue. Duis mi dui, consectetuer id, venenatis lobortis, luctus a, quam. Ut et diam. Fusce id turpis ut nulla dapibus lacinia. Nam eget felis sed risus mattis rhoncus. Aenean vitae nulla. In vel lacus at turpis sodales tempor.');
INSERT INTO `XArticle` (`NrArticle`, `IdLanguage`, `FByline`, `FIntro`, `FFull_text`) VALUES (52,1,'Sed lectus elit','\r\nn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo.','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero.\r\n\r\nIn vitae dui vel sem porta laoreet. Morbi sodales ullamcorper turpis. Maecenas rutrum, lorem in congue feugiat, sapien massa vestibulum orci, a fermentum neque dui eget eros. Cras semper lacus vel nunc. Suspendisse velit tellus, porttitor sit amet, commodo sed, sodales nec, leo. Nam aliquam suscipit augue. Duis mi dui, consectetuer id, venenatis lobortis, luctus a, quam. Ut et diam. Fusce id turpis ut nulla dapibus lacinia. Nam eget felis sed risus mattis rhoncus. Aenean vitae nulla. In vel lacus at turpis sodales tempor.');
/*!40000 ALTER TABLE `XArticle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XInterview`
--

DROP TABLE IF EXISTS `XInterview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `XInterview` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `FDeck` varchar(255) NOT NULL DEFAULT '',
  `FByline` varchar(255) NOT NULL DEFAULT '',
  `FTeaser_a` varchar(255) NOT NULL DEFAULT '',
  `FTeaser_b` varchar(255) NOT NULL DEFAULT '',
  `FIntro` mediumblob NOT NULL,
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `XInterview`
--

LOCK TABLES `XInterview` WRITE;
/*!40000 ALTER TABLE `XInterview` DISABLE KEYS */;
INSERT INTO `XInterview` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (25,1,'Sed viverra','Etiam a orc','In vitae dui vel sem','In vitae justo','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis.','\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit.\r\n\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero. ');
INSERT INTO `XInterview` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (33,1,'Sed viverra','Etiam a orc','In vitae dui vel sem','In vitae justo','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis.','\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit.\r\n\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero. ');
INSERT INTO `XInterview` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (34,1,'Sed viverra','Etiam a orc','In vitae dui vel sem','In vitae justo','\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis.','\r\nAliquam mollis diam ornare eros. Sed lectus elit, molestie ut, fermentum et, aliquam sit amet, eros. Ut condimentum interdum risus. Mauris ligula dolor, luctus sit amet, scelerisque id, fermentum id, nibh. In venenatis, sem in condimentum tempus, dolor odio aliquam risus, non lobortis erat nibh ut pede. Sed varius. Vestibulum tincidunt. Quisque imperdiet placerat elit. Proin sit amet turpis. Vivamus hendrerit egestas libero. Etiam accumsan. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus blandit. Donec arcu massa, imperdiet eget, elementum at, lobortis et, elit.\r\n\r\nDonec felis pede, pretium eu, interdum non, lacinia semper, est. Sed viverra, massa ac euismod venenatis, tortor velit semper nulla, non vulputate diam velit quis felis. Praesent vel justo. Duis diam nulla, placerat sed, suscipit vitae, auctor sed, tortor. In urna. Nullam venenatis ultricies ante. Curabitur in nibh sit amet nisl accumsan imperdiet. Quisque dolor augue, pharetra a, tincidunt vitae, egestas ut, nulla. Nulla ornare, magna ut faucibus ultrices, libero est pulvinar mauris, ac ullamcorper sem ligula ac lectus. Etiam a orci. Morbi suscipit sapien eu sapien. Suspendisse et ipsum. Duis aliquam elit et leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam venenatis vulputate mauris. Donec in nulla. Praesent id metus volutpat odio semper imperdiet. Suspendisse potenti. Fusce massa mauris, accumsan fermentum, consequat non, ornare volutpat, libero. ');
/*!40000 ALTER TABLE `XInterview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XService`
--

DROP TABLE IF EXISTS `XService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `XService` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `FDeck` varchar(255) NOT NULL DEFAULT '',
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `XService`
--

LOCK TABLES `XService` WRITE;
/*!40000 ALTER TABLE `XService` DISABLE KEYS */;
INSERT INTO `XService` (`NrArticle`, `IdLanguage`, `FDeck`, `FFull_text`) VALUES (56,1,'About us','\r\nCras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. In sit amet risus quis eros porttitor cursus. Phasellus elit odio, euismod vitae, scelerisque eget, pellentesque eu, felis. Proin lorem. Duis sapien est, gravida sed, euismod quis, feugiat eu, lacus. Sed ultrices semper leo. Nulla nec tortor facilisis leo auctor pellentesque. Sed porta iaculis nisl. Morbi lacinia lacus. Nunc eget libero nec mi tempor vulputate. Sed interdum placerat mi. Curabitur a lacus. Aliquam vehicula. Suspendisse rutrum facilisis justo. Quisque nisl. Integer tristique dolor faucibus ipsum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mollis. In nonummy, libero ut consequat vehicula, ipsum diam commodo libero, at bibendum dolor lorem eget odio.\r\n');
INSERT INTO `XService` (`NrArticle`, `IdLanguage`, `FDeck`, `FFull_text`) VALUES (57,1,'Contact','\r\nCras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. In sit amet risus quis eros porttitor cursus. Phasellus elit odio, euismod vitae, scelerisque eget, pellentesque eu, felis. Proin lorem. Duis sapien est, gravida sed, euismod quis, feugiat eu, lacus. Sed ultrices semper leo. Nulla nec tortor facilisis leo auctor pellentesque. Sed porta iaculis nisl. Morbi lacinia lacus. Nunc eget libero nec mi tempor vulputate. Sed interdum placerat mi. Curabitur a lacus. Aliquam vehicula. Suspendisse rutrum facilisis justo. Quisque nisl. Integer tristique dolor faucibus ipsum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mollis. In nonummy, libero ut consequat vehicula, ipsum diam commodo libero, at bibendum dolor lorem eget odio.\r\n');
/*!40000 ALTER TABLE `XService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `XSpecial`
--

DROP TABLE IF EXISTS `XSpecial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `XSpecial` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `FDeck` varchar(255) NOT NULL DEFAULT '',
  `FByline` varchar(255) NOT NULL DEFAULT '',
  `FTeaser_a` varchar(255) NOT NULL DEFAULT '',
  `FTeaser_b` varchar(255) NOT NULL DEFAULT '',
  `FIntro` mediumblob NOT NULL,
  `FFull_text` mediumblob NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `XSpecial`
--

LOCK TABLES `XSpecial` WRITE;
/*!40000 ALTER TABLE `XSpecial` DISABLE KEYS */;
INSERT INTO `XSpecial` (`NrArticle`, `IdLanguage`, `FDeck`, `FByline`, `FTeaser_a`, `FTeaser_b`, `FIntro`, `FFull_text`) VALUES (37,1,'Nam pharetra blandit','Nunc blandit libero','Proin neque','Pellentesque commodo','Cras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. In sit amet risus quis eros porttitor cursus. Phasellus elit odio, euismod vitae, scelerisque eget, pellentesque eu, felis.','\r\nCras vestibulum ultrices arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. In sit amet risus quis eros porttitor cursus. Phasellus elit odio, euismod vitae, scelerisque eget, pellentesque eu, felis. Proin lorem. Duis sapien est, gravida sed, euismod quis, feugiat eu, lacus. Sed ultrices semper leo. Nulla nec tortor facilisis leo auctor pellentesque. Sed porta iaculis nisl. Morbi lacinia lacus. Nunc eget libero nec mi tempor vulputate. Sed interdum placerat mi. Curabitur a lacus. Aliquam vehicula. Suspendisse rutrum facilisis justo. Quisque nisl. Integer tristique dolor faucibus ipsum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mollis. In nonummy, libero ut consequat vehicula, ipsum diam commodo libero, at bibendum dolor lorem eget odio.\r\n\r\nPraesent tincidunt vestibulum orci. Nunc adipiscing sodales massa. Sed nisl. Praesent nulla magna, blandit sed, vulputate et, pellentesque non, metus. Ut erat risus, sollicitudin et, eleifend non, aliquam id, turpis. Nunc ut dolor quis nibh egestas gravida. Aenean hendrerit arcu et lacus. Phasellus nisl ipsum, lacinia ac, dapibus eu, eleifend ut, enim. Fusce facilisis magna sit amet mauris. Etiam vestibulum. Curabitur in lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis mattis ligula sed metus. Suspendisse interdum tempus metus. Mauris aliquet auctor nisl.\r\n\r\nIn iaculis lacus eu lorem mattis dignissim. Aenean rhoncus eros eget velit mollis faucibus. Aliquam erat volutpat. Vivamus id purus. Donec dapibus tellus vel augue. Curabitur tortor. Morbi mi quam, accumsan eget, tempus at, nonummy in, quam. Maecenas suscipit. Nulla at nibh ac metus placerat sodales. Etiam iaculis. Maecenas eget odio. Duis fermentum scelerisque purus. Pellentesque fringilla, massa id molestie commodo, neque turpis rhoncus lorem, eu tempus tortor lorem et leo. Nullam ut wisi. Aenean sollicitudin justo ac urna. ');
/*!40000 ALTER TABLE `XSpecial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Xlink`
--

DROP TABLE IF EXISTS `Xlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xlink` (
  `NrArticle` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Furl` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Xlink`
--

LOCK TABLES `Xlink` WRITE;
/*!40000 ALTER TABLE `Xlink` DISABLE KEYS */;
INSERT INTO `Xlink` (`NrArticle`, `IdLanguage`, `Furl`) VALUES (53,1,'www.sourcefabric.org/en/home/web/6/campsite.htm?tpl=18');
INSERT INTO `Xlink` (`NrArticle`, `IdLanguage`, `Furl`) VALUES (54,1,'www.sourcefabric.org');
/*!40000 ALTER TABLE `Xlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_applications`
--

DROP TABLE IF EXISTS `liveuser_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL DEFAULT '0',
  `application_define_name` varchar(32) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `applications_define_name_i_idx` (`application_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_applications_application_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_applications_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_area_admin_areas` (
  `area_id` int(11) NOT NULL DEFAULT '0',
  `perm_user_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `area_admin_areas_id_i_idx` (`area_id`,`perm_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_areas` (
  `area_id` int(11) NOT NULL DEFAULT '0',
  `application_id` int(11) NOT NULL DEFAULT '0',
  `area_define_name` varchar(32) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`area_id`),
  UNIQUE KEY `areas_define_name_i_idx` (`application_id`,`area_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_areas_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_group_subgroups` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `subgroup_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `group_subgroups_id_i_idx` (`group_id`,`subgroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_grouprights` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `right_id` int(11) NOT NULL DEFAULT '0',
  `right_level` int(11) NOT NULL DEFAULT '3',
  UNIQUE KEY `grouprights_id_i_idx` (`group_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,66,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (3,68,3);
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,71,3);
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
INSERT INTO `liveuser_grouprights` (`group_id`, `right_id`, `right_level`) VALUES (1,83,3);
/*!40000 ALTER TABLE `liveuser_grouprights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groups`
--

DROP TABLE IF EXISTS `liveuser_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `group_type` int(11) NOT NULL DEFAULT '0',
  `group_define_name` varchar(32) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `groups_define_name_i_idx` (`group_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_groups_group_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_groups_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_groupusers` (
  `perm_user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `groupusers_id_i_idx` (`perm_user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_perm_users` (
  `perm_user_id` int(11) NOT NULL DEFAULT '0',
  `auth_user_id` varchar(32) NOT NULL DEFAULT ' ',
  `auth_container_name` varchar(32) NOT NULL DEFAULT ' ',
  `perm_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`perm_user_id`),
  UNIQUE KEY `perm_users_auth_id_i_idx` (`auth_user_id`,`auth_container_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_perm_users_perm_user_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_perm_users_perm_user_id_seq`
--

LOCK TABLES `liveuser_perm_users_perm_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_perm_users_perm_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_perm_users_perm_user_id_seq` (`id`) VALUES (5);
/*!40000 ALTER TABLE `liveuser_perm_users_perm_user_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_perm_users_seq`
--

DROP TABLE IF EXISTS `liveuser_perm_users_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_perm_users_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_right_implied` (
  `right_id` int(11) NOT NULL DEFAULT '0',
  `implied_right_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `right_implied_id_i_idx` (`right_id`,`implied_right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_rights` (
  `right_id` int(11) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL DEFAULT '0',
  `right_define_name` varchar(32) NOT NULL DEFAULT ' ',
  `has_implied` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`right_id`),
  UNIQUE KEY `rights_define_name_i_idx` (`area_id`,`right_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (72,0,'ManageSection10_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (73,0,'ManageSection20_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (74,0,'ManageSection30_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (75,0,'ManageSection40_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (76,0,'ManageSection50_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (77,0,'ManageSection60_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (78,0,'ManageSection200_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (79,0,'ManageSection210_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (80,0,'ManageSection220_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (81,0,'ManageSection230_P5_I1_L1',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (82,0,'EditorStatusBar',1);
INSERT INTO `liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `has_implied`) VALUES (83,0,'ManageBackup',1);
/*!40000 ALTER TABLE `liveuser_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_rights_right_id_seq`
--

DROP TABLE IF EXISTS `liveuser_rights_right_id_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_rights_right_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_rights_right_id_seq`
--

LOCK TABLES `liveuser_rights_right_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_rights_right_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_rights_right_id_seq` (`id`) VALUES (83);
/*!40000 ALTER TABLE `liveuser_rights_right_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_rights_seq`
--

DROP TABLE IF EXISTS `liveuser_rights_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_rights_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_translations` (
  `translation_id` int(11) NOT NULL DEFAULT '0',
  `section_id` int(11) NOT NULL DEFAULT '0',
  `section_type` int(11) NOT NULL DEFAULT '0',
  `language_id` varchar(32) NOT NULL DEFAULT ' ',
  `name` varchar(32) NOT NULL DEFAULT ' ',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`translation_id`),
  UNIQUE KEY `translations_translation_i_idx` (`section_id`,`section_type`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_translations_seq` (
  `sequence` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_userrights` (
  `perm_user_id` int(11) NOT NULL DEFAULT '0',
  `right_id` int(11) NOT NULL DEFAULT '0',
  `right_level` int(11) NOT NULL DEFAULT '3',
  UNIQUE KEY `userrights_id_i_idx` (`perm_user_id`,`right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_users` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `KeyId` int(10) unsigned DEFAULT NULL,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `UName` varchar(70) NOT NULL DEFAULT '',
  `Password` varchar(64) NOT NULL DEFAULT '',
  `EMail` varchar(255) NOT NULL DEFAULT '',
  `Reader` enum('Y','N') NOT NULL DEFAULT 'Y',
  `fk_user_type` int(10) unsigned DEFAULT NULL,
  `City` varchar(100) NOT NULL DEFAULT '',
  `StrAddress` varchar(255) NOT NULL DEFAULT '',
  `State` varchar(32) NOT NULL DEFAULT '',
  `CountryCode` varchar(21) DEFAULT NULL,
  `Phone` varchar(20) NOT NULL DEFAULT '',
  `Fax` varchar(20) NOT NULL DEFAULT '',
  `Contact` varchar(64) NOT NULL DEFAULT '',
  `Phone2` varchar(20) NOT NULL DEFAULT '',
  `Title` enum('Mr.','Mrs.','Ms.','Dr.') NOT NULL DEFAULT 'Mr.',
  `Gender` enum('M','F') DEFAULT NULL,
  `Age` enum('0-17','18-24','25-39','40-49','50-65','65-') NOT NULL DEFAULT '0-17',
  `PostalCode` varchar(70) NOT NULL DEFAULT '',
  `Employer` varchar(140) NOT NULL DEFAULT '',
  `EmployerType` varchar(140) NOT NULL DEFAULT '',
  `Position` varchar(70) NOT NULL DEFAULT '',
  `Interests` mediumblob,
  `How` varchar(255) NOT NULL DEFAULT '',
  `Languages` varchar(100) NOT NULL DEFAULT '',
  `Improvements` mediumblob,
  `Pref1` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref2` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref3` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref4` enum('N','Y') NOT NULL DEFAULT 'N',
  `Field1` varchar(150) NOT NULL DEFAULT '',
  `Field2` varchar(150) NOT NULL DEFAULT '',
  `Field3` varchar(150) NOT NULL DEFAULT '',
  `Field4` varchar(150) NOT NULL DEFAULT '',
  `Field5` varchar(150) NOT NULL DEFAULT '',
  `Text1` mediumblob,
  `Text2` mediumblob,
  `Text3` mediumblob,
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_created` timestamp NULL DEFAULT NULL,
  `lastLogin` datetime DEFAULT '1970-01-01 00:00:00',
  `isActive` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UName` (`UName`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_users`
--

LOCK TABLES `liveuser_users` WRITE;
/*!40000 ALTER TABLE `liveuser_users` DISABLE KEYS */;
INSERT INTO `liveuser_users` (`Id`, `KeyId`, `Name`, `UName`, `Password`, `EMail`, `Reader`, `fk_user_type`, `City`, `StrAddress`, `State`, `CountryCode`, `Phone`, `Fax`, `Contact`, `Phone2`, `Title`, `Gender`, `Age`, `PostalCode`, `Employer`, `EmployerType`, `Position`, `Interests`, `How`, `Languages`, `Improvements`, `Pref1`, `Pref2`, `Pref3`, `Pref4`, `Field1`, `Field2`, `Field3`, `Field4`, `Field5`, `Text1`, `Text2`, `Text3`, `time_updated`, `time_created`, `lastLogin`, `isActive`) VALUES (1,NULL,'Administrator','admin','b2d716fb2328a246e8285f47b1500ebcb349c187','','N',1,'','','','AD','','','','','Mr.','M','0-17','','','Other','','','','','','N','N','N','N','','','','','','','','','2010-07-09 18:45:40','0000-00-00 00:00:00','2010-07-09 21:42:49',1);
/*!40000 ALTER TABLE `liveuser_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_users_auth_user_id_seq`
--

DROP TABLE IF EXISTS `liveuser_users_auth_user_id_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_users_auth_user_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_users_auth_user_id_seq`
--

LOCK TABLES `liveuser_users_auth_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_users_auth_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_users_auth_user_id_seq` (`id`) VALUES (5);
/*!40000 ALTER TABLE `liveuser_users_auth_user_id_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_banlists`
--

DROP TABLE IF EXISTS `phorum_banlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_banlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `pcre` tinyint(4) NOT NULL DEFAULT '0',
  `string` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `filesize` int(11) NOT NULL DEFAULT '0',
  `file_data` mediumtext,
  `add_datetime` int(10) unsigned NOT NULL DEFAULT '0',
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `link` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY `add_datetime` (`add_datetime`),
  KEY `message_id_link` (`message_id`,`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_forum_group_xref` (
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `permission` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_forums` (
  `forum_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` smallint(6) NOT NULL DEFAULT '0',
  `description` text,
  `template` varchar(50) NOT NULL DEFAULT '',
  `folder_flag` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `list_length_flat` int(10) unsigned NOT NULL DEFAULT '0',
  `list_length_threaded` int(10) unsigned NOT NULL DEFAULT '0',
  `moderation` int(10) unsigned NOT NULL DEFAULT '0',
  `threaded_list` tinyint(4) NOT NULL DEFAULT '0',
  `threaded_read` tinyint(4) NOT NULL DEFAULT '0',
  `float_to_top` tinyint(4) NOT NULL DEFAULT '0',
  `check_duplicate` tinyint(4) NOT NULL DEFAULT '0',
  `allow_attachment_types` varchar(100) NOT NULL DEFAULT '',
  `max_attachment_size` int(10) unsigned NOT NULL DEFAULT '0',
  `max_totalattachment_size` int(10) unsigned NOT NULL DEFAULT '0',
  `max_attachments` int(10) unsigned NOT NULL DEFAULT '0',
  `pub_perms` int(10) unsigned NOT NULL DEFAULT '0',
  `reg_perms` int(10) unsigned NOT NULL DEFAULT '0',
  `display_ip_address` smallint(5) unsigned NOT NULL DEFAULT '1',
  `allow_email_notify` smallint(5) unsigned NOT NULL DEFAULT '1',
  `language` varchar(100) NOT NULL DEFAULT 'english',
  `email_moderators` tinyint(1) NOT NULL DEFAULT '0',
  `message_count` int(10) unsigned NOT NULL DEFAULT '0',
  `sticky_count` int(10) unsigned NOT NULL DEFAULT '0',
  `thread_count` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post_time` int(10) unsigned NOT NULL DEFAULT '0',
  `display_order` int(10) unsigned NOT NULL DEFAULT '0',
  `read_length` int(10) unsigned NOT NULL DEFAULT '0',
  `vroot` int(10) unsigned NOT NULL DEFAULT '0',
  `edit_post` tinyint(1) NOT NULL DEFAULT '1',
  `template_settings` text,
  `count_views` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `display_fixed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reverse_threading` tinyint(1) NOT NULL DEFAULT '0',
  `inherit_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`forum_id`),
  KEY `name` (`name`),
  KEY `active` (`active`,`parent_id`),
  KEY `group_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phorum_forums`
--

LOCK TABLES `phorum_forums` WRITE;
/*!40000 ALTER TABLE `phorum_forums` DISABLE KEYS */;
INSERT INTO `phorum_forums` (`forum_id`, `name`, `active`, `description`, `template`, `folder_flag`, `parent_id`, `list_length_flat`, `list_length_threaded`, `moderation`, `threaded_list`, `threaded_read`, `float_to_top`, `check_duplicate`, `allow_attachment_types`, `max_attachment_size`, `max_totalattachment_size`, `max_attachments`, `pub_perms`, `reg_perms`, `display_ip_address`, `allow_email_notify`, `language`, `email_moderators`, `message_count`, `sticky_count`, `thread_count`, `last_post_time`, `display_order`, `read_length`, `vroot`, `edit_post`, `template_settings`, `count_views`, `display_fixed`, `reverse_threading`, `inherit_id`) VALUES (1,'My Publication',1,'','default',0,0,30,15,0,0,0,1,1,'',0,0,0,10,15,0,0,'english',0,3,0,1,1278685899,0,30,0,1,'',2,0,0,NULL);
/*!40000 ALTER TABLE `phorum_forums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_groups`
--

DROP TABLE IF EXISTS `phorum_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '0',
  `open` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_messages` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `thread` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `author` varchar(37) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '2',
  `msgid` varchar(100) NOT NULL DEFAULT '',
  `modifystamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `thread_count` int(10) unsigned NOT NULL DEFAULT '0',
  `moderator_post` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sort` tinyint(4) NOT NULL DEFAULT '2',
  `datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `meta` mediumtext NOT NULL,
  `viewcount` int(10) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `thread_depth` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thread_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phorum_messages`
--

LOCK TABLES `phorum_messages` WRITE;
/*!40000 ALTER TABLE `phorum_messages` DISABLE KEYS */;
INSERT INTO `phorum_messages` (`message_id`, `forum_id`, `thread`, `parent_id`, `author`, `subject`, `body`, `email`, `ip`, `status`, `msgid`, `modifystamp`, `user_id`, `thread_count`, `moderator_post`, `sort`, `datestamp`, `meta`, `viewcount`, `closed`, `thread_depth`, `thread_order`) VALUES (3,1,3,0,'','Aenean rhoncus','','','localhost',2,'549f1b8c4268f8d67ff0ca3b9a74ddb1',1278685899,0,2,0,2,1278685899,'a:3:{s:11:\"recent_post\";a:3:{s:7:\"user_id\";s:1:\"0\";s:6:\"author\";s:9:\"anonymous\";s:10:\"message_id\";s:1:\"4\";}s:11:\"message_ids\";a:2:{i:0;i:3;i:1;i:4;}s:21:\"message_ids_moderator\";a:2:{i:0;i:3;i:1;i:4;}}',0,0,0,0);
INSERT INTO `phorum_messages` (`message_id`, `forum_id`, `thread`, `parent_id`, `author`, `subject`, `body`, `email`, `ip`, `status`, `msgid`, `modifystamp`, `user_id`, `thread_count`, `moderator_post`, `sort`, `datestamp`, `meta`, `viewcount`, `closed`, `thread_depth`, `thread_order`) VALUES (4,1,3,3,'anonymous','test1','comment1','test1','localhost',2,'e10c958675cfa605c1a6518fdcdc2eb6',0,0,2,0,2,1278685899,'',0,0,1,1);
/*!40000 ALTER TABLE `phorum_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_pm_buddies`
--

DROP TABLE IF EXISTS `phorum_pm_buddies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_pm_buddies` (
  `pm_buddy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buddy_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pm_buddy_id`),
  UNIQUE KEY `userids` (`user_id`,`buddy_user_id`),
  KEY `buddy_user_id` (`buddy_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_pm_folders` (
  `pm_folder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `foldername` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`pm_folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_pm_messages` (
  `pm_message_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from_username` varchar(50) NOT NULL DEFAULT '',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `meta` mediumtext NOT NULL,
  PRIMARY KEY (`pm_message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_pm_xref` (
  `pm_xref_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pm_folder_id` int(10) unsigned NOT NULL DEFAULT '0',
  `special_folder` varchar(10) DEFAULT NULL,
  `pm_message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `read_flag` tinyint(1) NOT NULL DEFAULT '0',
  `reply_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pm_xref_id`),
  KEY `xref` (`user_id`,`pm_folder_id`,`pm_message_id`),
  KEY `read_flag` (`read_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_search` (
  `message_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `search_text` mediumtext NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `forum_id` (`forum_id`),
  FULLTEXT KEY `search_text` (`search_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phorum_search`
--

LOCK TABLES `phorum_search` WRITE;
/*!40000 ALTER TABLE `phorum_search` DISABLE KEYS */;
INSERT INTO `phorum_search` (`message_id`, `forum_id`, `search_text`) VALUES (3,1,' | Aenean rhoncus | ');
INSERT INTO `phorum_search` (`message_id`, `forum_id`, `search_text`) VALUES (4,1,'anonymous | test1 | comment1');
/*!40000 ALTER TABLE `phorum_search` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phorum_settings`
--

DROP TABLE IF EXISTS `phorum_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_settings` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` enum('V','S') NOT NULL DEFAULT 'V',
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_subscribers` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sub_type` int(10) unsigned NOT NULL DEFAULT '0',
  `thread` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`forum_id`,`thread`),
  KEY `forum_id` (`forum_id`,`thread`,`sub_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_user_custom_fields` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `data` text,
  PRIMARY KEY (`user_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_user_group_xref` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_user_newflags` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL DEFAULT '0',
  `message_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`forum_id`,`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_user_permissions` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `permission` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phorum_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_campsite_user_id` int(10) unsigned DEFAULT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `cookie_sessid_lt` varchar(50) NOT NULL DEFAULT '',
  `sessid_st` varchar(50) NOT NULL DEFAULT '',
  `sessid_st_timeout` int(10) unsigned NOT NULL DEFAULT '0',
  `password_temp` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `email_temp` varchar(110) NOT NULL DEFAULT '',
  `hide_email` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `user_data` text,
  `signature` text,
  `threaded_list` tinyint(4) NOT NULL DEFAULT '0',
  `posts` int(10) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `threaded_read` tinyint(4) NOT NULL DEFAULT '0',
  `date_added` int(10) unsigned NOT NULL DEFAULT '0',
  `date_last_active` int(10) unsigned NOT NULL DEFAULT '0',
  `last_active_forum` int(10) unsigned NOT NULL DEFAULT '0',
  `hide_activity` tinyint(1) NOT NULL DEFAULT '0',
  `show_signature` tinyint(1) NOT NULL DEFAULT '0',
  `email_notify` tinyint(1) NOT NULL DEFAULT '0',
  `pm_email_notify` tinyint(1) NOT NULL DEFAULT '1',
  `tz_offset` tinyint(2) NOT NULL DEFAULT '-99',
  `is_dst` tinyint(1) NOT NULL DEFAULT '0',
  `user_language` varchar(100) NOT NULL DEFAULT '',
  `user_template` varchar(100) NOT NULL DEFAULT '',
  `moderator_data` text,
  `moderation_email` tinyint(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `fk_campsite_user_id` (`fk_campsite_user_id`),
  KEY `active` (`active`),
  KEY `userpass` (`username`,`password`),
  KEY `sessid_st` (`sessid_st`),
  KEY `cookie_sessid_lt` (`cookie_sessid_lt`),
  KEY `activity` (`date_last_active`,`hide_activity`,`last_active_forum`),
  KEY `date_added` (`date_added`),
  KEY `email_temp` (`email_temp`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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

-- Dump completed on 2010-07-09 21:46:02
