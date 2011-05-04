-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: newscoop35
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Aliases`
--

LOCK TABLES `Aliases` WRITE;
/*!40000 ALTER TABLE `Aliases` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `ArticleAttachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ArticleAuthors`
--

DROP TABLE IF EXISTS `ArticleAuthors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleAuthors` (
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_author_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_article_number`,`fk_language_id`,`fk_author_id`,`fk_type_id`)
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachments`
--

LOCK TABLES `Attachments` WRITE;
/*!40000 ALTER TABLE `Attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorAliases`
--

DROP TABLE IF EXISTS `AuthorAliases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorAliases` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_author_id` int(11) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorAliases`
--

LOCK TABLES `AuthorAliases` WRITE;
/*!40000 ALTER TABLE `AuthorAliases` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthorAliases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorAssignedTypes`
--

DROP TABLE IF EXISTS `AuthorAssignedTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorAssignedTypes` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_author_id`,`fk_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorAssignedTypes`
--

LOCK TABLES `AuthorAssignedTypes` WRITE;
/*!40000 ALTER TABLE `AuthorAssignedTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthorAssignedTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorBiographies`
--

DROP TABLE IF EXISTS `AuthorBiographies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorBiographies` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(11) unsigned NOT NULL DEFAULT '0',
  `biography` text NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`fk_author_id`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorBiographies`
--

LOCK TABLES `AuthorBiographies` WRITE;
/*!40000 ALTER TABLE `AuthorBiographies` DISABLE KEYS */;
/*!40000 ALTER TABLE `AuthorBiographies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorTypes`
--

DROP TABLE IF EXISTS `AuthorTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorTypes`
--

LOCK TABLES `AuthorTypes` WRITE;
/*!40000 ALTER TABLE `AuthorTypes` DISABLE KEYS */;
INSERT INTO `AuthorTypes` VALUES (1,'Author'),(2,'Writer'),(3,'Photographer'),(4,'Editor'),(5,'Columnist');
/*!40000 ALTER TABLE `AuthorTypes` ENABLE KEYS */;
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
  `type` int(10) unsigned DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `jabber` varchar(255) DEFAULT NULL,
  `aim` varchar(255) DEFAULT NULL,
  `biography` text,
  `image` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authors_name_ukey` (`first_name`,`last_name`),
  FULLTEXT KEY `authors_name_skey` (`first_name`,`last_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
INSERT INTO `AutoId` VALUES (0,'0000-00-00 00:00:00',0,1);
/*!40000 ALTER TABLE `AutoId` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Cache`
--

DROP TABLE IF EXISTS `Cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cache` (
  `language` int(11) unsigned DEFAULT NULL,
  `publication` int(11) unsigned DEFAULT NULL,
  `issue` int(11) unsigned DEFAULT NULL,
  `section` int(11) unsigned DEFAULT NULL,
  `article` int(11) unsigned DEFAULT NULL,
  `params` varchar(128) DEFAULT NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`params`,`template`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cache`
--

LOCK TABLES `Cache` WRITE;
/*!40000 ALTER TABLE `Cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `Cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CityLocations`
--

DROP TABLE IF EXISTS `CityLocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CityLocations` (
  `id` int(10) unsigned NOT NULL,
  `city_type` varchar(10) DEFAULT NULL,
  `population` int(10) unsigned NOT NULL,
  `position` point NOT NULL,
  `elevation` int(11) DEFAULT NULL,
  `country_code` char(2) NOT NULL,
  `time_zone` varchar(1023) NOT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CityLocations`
--

LOCK TABLES `CityLocations` WRITE;
/*!40000 ALTER TABLE `CityLocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `CityLocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CityNames`
--

DROP TABLE IF EXISTS `CityNames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CityNames` (
  `fk_citylocations_id` int(10) NOT NULL,
  `city_name` varchar(1024) NOT NULL,
  `name_type` varchar(10) NOT NULL,
  KEY `fk_citylocations_id` (`fk_citylocations_id`),
  KEY `city_name` (`city_name`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CityNames`
--

LOCK TABLES `CityNames` WRITE;
/*!40000 ALTER TABLE `CityNames` DISABLE KEYS */;
/*!40000 ALTER TABLE `CityNames` ENABLE KEYS */;
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
  `Code` varchar(2) NOT NULL DEFAULT '',
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
INSERT INTO `Countries` VALUES ('AR',1,'Argentina'),('AG',1,'Antigua and Barbuda'),('AQ',1,'Antarctica'),('AI',1,'Anguilla'),('AO',1,'Angola'),('AD',1,'Andorra'),('AS',1,'American Samoa'),('DZ',1,'Algeria'),('AL',1,'Albania'),('AF',1,'Afghanistan'),('AM',1,'Armenia'),('AW',1,'Aruba'),('AU',1,'Australia'),('AT',1,'Austria'),('AZ',1,'Azerbaijan'),('BS',1,'Bahamas'),('BH',1,'Bahrain'),('BD',1,'Bangladesh'),('BB',1,'Barbados'),('BY',1,'Belarus'),('BE',1,'Belgium'),('BZ',1,'Belize'),('BJ',1,'Benin'),('BM',1,'Bermuda'),('BT',1,'Bhutan'),('BO',1,'Bolivia'),('BA',1,'Bosnia and Herzegovina'),('BW',1,'Botswana'),('BV',1,'Bouvet Island'),('BR',1,'Brazil'),('IO',1,'British Indian Ocean Territory'),('BN',1,'Brunei Darussalam'),('BG',1,'Bulgaria'),('BF',1,'Burkina Faso'),('BI',1,'Burundi'),('KH',1,'Cambodia'),('CM',1,'Cameroon'),('CA',1,'Canada'),('CV',1,'Cape Verde'),('KY',1,'Cayman Islands'),('CF',1,'Central African Republic'),('TD',1,'Chad'),('CL',1,'Chile'),('CN',1,'China'),('CX',1,'Christmas Island'),('CC',1,'Cocos (Keeling) Islands'),('CO',1,'Colombia'),('KM',1,'Comoros'),('CG',1,'Congo'),('CD',1,'Congo, The Democratic Republic Of The'),('CK',1,'Cook Islands'),('CR',1,'Costa Rica'),('CI',1,'Côte d\'Ivoire'),('HR',1,'Croatia'),('CU',1,'Cuba'),('CY',1,'Cyprus'),('CZ',1,'Czech Republic'),('DK',1,'Denmark'),('DJ',1,'Djibouti'),('DM',1,'Dominica'),('DO',1,'Dominican Republic'),('TP',1,'Timor-Leste'),('EC',1,'Ecuador'),('EG',1,'Egypt'),('SV',1,'El Salvador'),('GQ',1,'Equatorial Guinea'),('ER',1,'Eritrea'),('EE',1,'Estonia'),('ET',1,'Ethiopia'),('FK',1,'Falkland Islands (Malvinas)'),('FO',1,'Faroe Islands'),('FJ',1,'Fiji'),('FI',1,'Finland'),('FR',1,'France'),('FX',1,'France, Metropolitan'),('GF',1,'French Guiana'),('PF',1,'French Polynesia'),('TF',1,'French Southern Territories'),('GA',1,'Gabon'),('GM',1,'Gambia'),('GE',1,'Georgia'),('DE',1,'Germany'),('GH',1,'Ghana'),('GI',1,'Gibraltar'),('GR',1,'Greece'),('GL',1,'Greenland'),('GD',1,'Grenada'),('GP',1,'Guadeloupe'),('GU',1,'Guam'),('GT',1,'Guatemala'),('GN',1,'Guinea'),('GW',1,'Guinea-bissau'),('GY',1,'Guyana'),('HT',1,'Haiti'),('HM',1,'Heard Island and Mcdonald Islands'),('VA',1,'Holy See (Vatican City State)'),('HN',1,'Honduras'),('HK',1,'Hong Kong'),('HU',1,'Hungary'),('IS',1,'Iceland'),('IN',1,'India'),('ID',1,'Indonesia'),('IR',1,'Iran, Islamic Republic of'),('IQ',1,'Iraq'),('IE',1,'Ireland'),('IL',1,'Israel'),('IT',1,'Italy'),('JM',1,'Jamaica'),('JP',1,'Japan'),('JO',1,'Jordan'),('KZ',1,'Kazakstan'),('KE',1,'Kenya'),('KI',1,'Kiribati'),('KP',1,'Korea, Democratic Peoples Republic of'),('KR',1,'Korea, Republic of'),('KW',1,'Kuwait'),('KG',1,'Kyrgyzstan'),('LA',1,'Lao People\'s Democratic Republic'),('LV',1,'Latvia'),('LB',1,'Lebanon'),('LS',1,'Lesotho'),('LR',1,'Liberia'),('LY',1,'Libyan Arab Jamahiriya'),('LI',1,'Liechtenstein'),('LT',1,'Lithuania'),('LU',1,'Luxembourg'),('MO',1,'Macau'),('MK',1,'Macedonia, The Former Yugoslav Republic of'),('MG',1,'Madagascar'),('MW',1,'Malawi'),('MY',1,'Malaysia'),('MV',1,'Maldives'),('ML',1,'Mali'),('MT',1,'Malta'),('MH',1,'Marshall Islands'),('MQ',1,'Martinique'),('MR',1,'Mauritania'),('MU',1,'Mauritius'),('YT',1,'Mayotte'),('MX',1,'Mexico'),('FM',1,'Micronesia, Federated States of'),('MD',1,'Moldova, Republic of'),('MC',1,'Monaco'),('MN',1,'Mongolia'),('MS',1,'Montserrat'),('MA',1,'Morocco'),('MZ',1,'Mozambique'),('MM',1,'Myanmar'),('NA',1,'Namibia'),('NR',1,'Nauru'),('NP',1,'Nepal'),('NL',1,'Netherlands'),('AN',1,'Netherlands Antilles'),('NC',1,'New Caledonia'),('NZ',1,'New Zealand'),('NI',1,'Nicaragua'),('NE',1,'Niger'),('NG',1,'Nigeria'),('NU',1,'Niue'),('NF',1,'Norfolk Island'),('MP',1,'Northern Mariana Islands'),('NO',1,'Norway'),('OM',1,'Oman'),('PK',1,'Pakistan'),('PW',1,'Palau'),('PS',1,'Palestinian Territory, Occupied'),('PA',1,'Panama'),('PG',1,'Papua New Guinea'),('PY',1,'Paraguay'),('PE',1,'Peru'),('PH',1,'Philippines'),('PN',1,'Pitcairn'),('PL',1,'Poland'),('PT',1,'Portugal'),('PR',1,'Puerto Rico'),('QA',1,'Qatar'),('RE',1,'Réunion'),('RO',1,'Romania'),('RU',1,'Russian Federation'),('RW',1,'Rwanda'),('SH',1,'Saint Helena'),('KN',1,'Saint Kitts and Nevis'),('LC',1,'Saint Lucia'),('PM',1,'Saint Pierre and Miquelon'),('VC',1,'Saint Vincent and The Grenadines'),('WS',1,'Samoa'),('SM',1,'San Marino'),('ST',1,'Sao Tome and Principe'),('SA',1,'Saudi Arabia'),('SN',1,'Senegal'),('SX',1,'Serbia'),('MB',1,'Montenegro'),('SC',1,'Seychelles'),('SL',1,'Sierra Leone'),('SG',1,'Singapore'),('SK',1,'Slovakia'),('SI',1,'Slovenia'),('SB',1,'Solomon Islands'),('SO',1,'Somalia'),('ZA',1,'South Africa'),('GS',1,'South Georgia and The South Sandwich Islands'),('ES',1,'Spain'),('LK',1,'Sri Lanka'),('SD',1,'Sudan'),('SR',1,'Suriname'),('SJ',1,'Svalbard and Jan Mayen'),('SZ',1,'Swaziland'),('SE',1,'Sweden'),('CH',1,'Switzerland'),('SY',1,'Syrian Arab Republic'),('TW',1,'Taiwan, Province Of China'),('TJ',1,'Tajikistan'),('TZ',1,'Tanzania, United Republic of'),('TH',1,'Thailand'),('TG',1,'Togo'),('TK',1,'Tokelau'),('TO',1,'Tonga'),('TT',1,'Trinidad and Tobago'),('TN',1,'Tunisia'),('TR',1,'Turkey'),('TM',1,'Turkmenistan'),('TC',1,'Turks and Caicos Islands'),('TV',1,'Tuvalu'),('UG',1,'Uganda'),('UA',1,'Ukraine'),('AE',1,'United Arab Emirates'),('GB',1,'United Kingdom'),('US',1,'United States'),('UM',1,'United States Minor Outlying Islands'),('UY',1,'Uruguay'),('UZ',1,'Uzbekistan'),('VU',1,'Vanuatu'),('VE',1,'Venezuela'),('VN',1,'Vietnam'),('VG',1,'Virgin Islands, British'),('VI',1,'Virgin Islands, U.S.'),('WF',1,'Wallis And Futuna'),('EH',1,'Western Sahara'),('YE',1,'Yemen'),('ZM',1,'Zambia'),('ZW',1,'Zimbabwe'),('AX',1,'Åland Islands');
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
INSERT INTO `Errors` VALUES (4000,1,'Internal error.'),(4001,1,'Username not specified.'),(4002,1,'Invalid username.'),(4003,1,'Password not specified.'),(4004,1,'Invalid password.'),(2000,1,'Internal error'),(2001,1,'Username is not specified. Please fill out login name field.'),(2002,1,'You are not a reader.'),(2003,1,'Publication not specified.'),(2004,1,'There are other subscriptions not payed.'),(2005,1,'Time unit not specified.'),(3000,1,'Internal error.'),(3001,1,'Username already exists.'),(3002,1,'Name is not specified. Please fill out name field.'),(3003,1,'Username is not specified. Please fill out login name field.'),(3004,1,'Password is not specified. Please fill out password field.'),(3005,1,'EMail is not specified. Please fill out EMail field.'),(3006,1,'EMail address already exists. Please try to login with your old account.'),(3007,1,'Invalid user identifier'),(3008,1,'No country specified. Please select a country.'),(3009,1,'Password (again) is not specified. Please fill out password (again) field.'),(3010,1,'Passwords do not match. Please fill out the same password to both password fields.'),(3011,1,'Password is too simple. Please choose a better password (at least 6 characters).'),(5009,1,'The code you entered is not the same with the one shown in the image.'),(5008,1,'Please enter the code shown in the image.'),(5007,1,'EMail field is empty. You must fill in your EMail address.'),(5006,1,'The comment was rejected by the spam filters.'),(5005,1,'You are banned from submitting comments.'),(5004,1,'Comments are not enabled for this publication/article.'),(5003,1,'The article was not selected. You must view an article in order to post comments.'),(5002,1,'The comment content was empty.'),(5001,1,'You must be a registered user in order to submit a comment. Please subscribe or log in if you already have a subscription.'),(5000,1,'There was an internal error when submitting the comment.');
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
INSERT INTO `Events` VALUES (1,'Add Publication','N',1),(2,'Delete Publication','N',1),(11,'Add Issue','N',1),(12,'Delete Issue','N',1),(13,'Change Issue Template','N',1),(14,'Change issue status','N',1),(15,'Add Issue Translation','N',1),(21,'Add Section','N',1),(22,'Delete section','N',1),(31,'Add Article','Y',1),(32,'Delete article','N',1),(33,'Change article field','N',1),(34,'Change article properties','N',1),(35,'Change article status','Y',1),(41,'Add Image','Y',1),(42,'Delete image','N',1),(43,'Change image properties','N',1),(51,'Add User','N',1),(52,'Delete User','N',1),(53,'Changes Own Password','N',1),(54,'Change User Password','N',1),(55,'Change User Permissions','N',1),(56,'Change user information','N',1),(61,'Add article type','N',1),(62,'Delete article type','N',1),(71,'Add article type field','N',1),(72,'Delete article type field','N',1),(81,'Add dictionary class','N',1),(82,'Delete dictionary class','N',1),(91,'Add dictionary keyword','N',1),(92,'Delete dictionary keyword','N',1),(101,'Add language','N',1),(102,'Delete language','N',1),(103,'Modify language','N',1),(112,'Delete templates','N',1),(111,'Add templates','N',1),(121,'Add user type','N',1),(122,'Delete user type','N',1),(123,'Change user type','N',1),(3,'Change publication information','N',1),(36,'Change article template','N',1),(57,'Add IP Group','N',1),(58,'Delete IP Group','N',1),(131,'Add country','N',1),(132,'Add country translation','N',1),(133,'Change country name','N',1),(134,'Delete country','N',1),(4,'Add default subscription time','N',1),(5,'Delete default subscription time','N',1),(6,'Change default subscription time','N',1),(113,'Edit template','N',1),(114,'Create template','N',1),(115,'Duplicate template','N',1),(141,'Add topic','N',1),(142,'Delete topic','N',1),(143,'Update topic','N',1),(144,'Add topic to article','N',1),(145,'Delete topic from article','N',1),(151,'Add alias','N',1),(152,'Delete alias','N',1),(153,'Update alias','N',1),(154,'Duplicate section','N',1),(155,'Duplicate article','N',1),(161,'Sync campsite and phorum users','N',1),(171,'Change system preferences','N',1),(116,'Rename Template','N',1),(117,'Move Template','N',1),(37,'Edit article content','N',1),(38,'Add file to article','N',1),(39,'Delete file from article','N',1),(172,'Add Author','N',1),(173,'Edit Author','N',1),(174,'Delete Author','N',1),(175,'Add author type','N',1),(176,'Delete author type','N',1);
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
  PRIMARY KEY (`Id`),
  FULLTEXT KEY `Description` (`Description`),
  FULLTEXT KEY `Photographer` (`Photographer`),
  FULLTEXT KEY `Place` (`Place`),
  FULLTEXT KEY `Caption` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Images`
--

LOCK TABLES `Images` WRITE;
/*!40000 ALTER TABLE `Images` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Languages`
--

LOCK TABLES `Languages` WRITE;
/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`, `ShortMonth1`, `ShortMonth2`, `ShortMonth3`, `ShortMonth4`, `ShortMonth5`, `ShortMonth6`, `ShortMonth7`, `ShortMonth8`, `ShortMonth9`, `ShortMonth10`, `ShortMonth11`, `ShortMonth12`, `ShortWDay1`, `ShortWDay2`, `ShortWDay3`, `ShortWDay4`, `ShortWDay5`, `ShortWDay6`, `ShortWDay7`) VALUES
(1, 'English', 'ISO_8859-1', 'English', 'en', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
(5, 'German', 'ISO_8859-1', 'Deutsch', 'de', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember', 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez', 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'),
(9, 'Portuguese', 'ISO_8859-1', 'Português', 'pt', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro', 'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(12, 'French', 'ISO_8859-1', 'Français', 'fr', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juli', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(13, 'Spanish', 'ISO_8859-1', 'Español', 'es', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic', 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'),
(2, 'Romanian', 'ISO_8859-2', 'Română', 'ro', 'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie', 'Duminică', 'Luni', 'Marţi', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(7, 'Croatian', 'ISO_8859-2', 'Hrvatski', 'hr', 'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac', 'Nedjelja', 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(8, 'Czech', 'ISO_8859-2', 'Český', 'cz', 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec', 'Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(11, 'Serbo-Croatian', 'ISO_8859-2', 'Srpskohrvatski', 'sh', 'januar', 'februar', 'mart', 'april', 'maj', 'jun', 'jul', 'avgust', 'septembar', 'oktobar', 'novembar', 'decembar', 'nedelja', 'ponedeljak', 'utorak', 'sreda', 'četvrtak', 'petak', 'subota', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(10, 'Serbian (Cyrillic)', 'ISO_8859-5', 'Српски (Ћирилица)', 'sr', 'јануар', 'фебруар', 'март', 'април', 'мај', 'јун', 'јул', 'август', 'септембар', 'октобар', 'новембар', 'децембар', 'недеља', 'понедељак', 'уторак', 'среда', 'четвртак', 'петак', 'субота', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(15, 'Russian', 'ISO_8859-5', 'Русский', 'ru', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь', 'воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(18, 'Swedish', '', 'Svenska', 'sv', 'januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december', 'söndag', 'måndag', 'tisdag', 'onsdag', 'torsdag', 'fredag', 'lördag', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(16, 'Chinese (Simplified)', 'UTF-8', '中文', 'zh', '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月', '星期', '星期', '星期', '星期', '星期', '星期', '星期', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(17, 'Arabic', 'UTF-8', 'عربي', 'ar', 'كانون الثاني', 'شباط', 'آذار', 'نيسان', 'آيار', 'حزيران', 'تموز', 'آب', 'أيلول', 'تشرين أول', 'تشرين الثاني', 'كانون أول', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(19, 'Korean', '', '한국어', 'kr', '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월', '일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(20, 'Dutch', '', 'Nederlands', 'nl', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december', 'zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(22, 'Belarus', '', 'Беларуская', 'by', 'студзеня', 'лютага', 'сакавiка', 'красавiка', 'мая', 'чэрвеня', 'лiпеня', 'жніўня', 'верасьня', 'кастрычнiка', 'сьнежня', 'студзеня', 'нядзеля', 'панядзелак', 'аўторак', 'серада', 'чацверг', 'пятнiца', 'субота', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(23, 'Georgian', '', 'ქართული', 'ge', 'იანვარი', 'თებერვალი', 'მარტი', 'აპრილი', 'მაისი', 'ივნისი', 'ივლისი', 'აგვისტო', 'სექტემბერი', 'ოქტომბერი', 'ნოემბერი', 'დეკემბერი', 'კვირა', 'ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(24, 'Chinese (Traditional)', '', '繁體中文', 'zh_TW', '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月', '星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月', '星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'),
(25, 'Polish', '', 'Polski', 'pl', 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień', 'Niedziela:', 'Poniedziałek', 'Wtorek:', 'Środa', 'Czwartek', 'Piątek', 'Sobota', 'Sty:', 'Lt:', 'Mar:', 'Kw:', 'Ma:', 'Cz:', 'Lip:', 'Sier:', 'Wrz:', 'Paź:', 'Lis:', 'Gru:', 'Nd:', 'Pon:', 'Wt:', 'Śr:', 'Czw:', 'Pt:', 'Sob:'),
(26, 'Greek', '', 'Ελληνικά', 'el', 'Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μάιος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος', 'Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέπμτη', 'Παρασκευή', 'Σάββατο', 'Ιαν', 'Φεβ', 'Μαρ', 'Απρ', 'Μαϊ', 'Ιουν', 'Ιουλ', 'Αυγ', 'Σεπ', 'Οκτ', 'Νοε', 'Δεκ', 'Κυ', 'Δε', 'Τρ', 'Τε', 'Πε', 'Παρ', 'Σα:');
UNLOCK TABLES;

--
-- Table structure for table `LocationContents`
--

DROP TABLE IF EXISTS `LocationContents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LocationContents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poi_name` varchar(1023) NOT NULL,
  `poi_link` varchar(1023) NOT NULL DEFAULT '',
  `poi_perex` varchar(15100) NOT NULL DEFAULT '',
  `poi_content_type` tinyint(4) NOT NULL DEFAULT '0',
  `poi_content` text NOT NULL,
  `poi_text` text NOT NULL,
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `location_contents_poi_name` (`poi_name`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LocationContents`
--

LOCK TABLES `LocationContents` WRITE;
/*!40000 ALTER TABLE `LocationContents` DISABLE KEYS */;
/*!40000 ALTER TABLE `LocationContents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Locations`
--

DROP TABLE IF EXISTS `Locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poi_location` geometry NOT NULL,
  `poi_type` varchar(40) NOT NULL,
  `poi_type_style` int(11) NOT NULL DEFAULT '0',
  `poi_center` point NOT NULL,
  `poi_radius` double NOT NULL DEFAULT '0',
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  SPATIAL KEY `locations_poi_location` (`poi_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Locations`
--

LOCK TABLES `Locations` WRITE;
/*!40000 ALTER TABLE `Locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fk_event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_user_id` int(10) unsigned DEFAULT NULL,
  `text` varchar(255) NOT NULL DEFAULT '',
  `user_ip` varchar(39) NOT NULL DEFAULT '',
  `priority` smallint(1) unsigned NOT NULL DEFAULT '6',
  PRIMARY KEY (`id`),
  KEY (`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Log`
--

LOCK TABLES `Log` WRITE;
/*!40000 ALTER TABLE `Log` DISABLE KEYS */;
/*!40000 ALTER TABLE `Log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MapLocationLanguages`
--

DROP TABLE IF EXISTS `MapLocationLanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MapLocationLanguages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_maplocation_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_content_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poi_display` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `map_location_languages_maplocation_id` (`fk_maplocation_id`),
  KEY `map_location_languages_content_id` (`fk_content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MapLocationLanguages`
--

LOCK TABLES `MapLocationLanguages` WRITE;
/*!40000 ALTER TABLE `MapLocationLanguages` DISABLE KEYS */;
/*!40000 ALTER TABLE `MapLocationLanguages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MapLocationMultimedia`
--

DROP TABLE IF EXISTS `MapLocationMultimedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MapLocationMultimedia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_maplocation_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_multimedia_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `maplocationmultimedia_maplocation_id` (`fk_maplocation_id`),
  KEY `maplocationmultimedia_multimedia_id` (`fk_multimedia_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MapLocationMultimedia`
--

LOCK TABLES `MapLocationMultimedia` WRITE;
/*!40000 ALTER TABLE `MapLocationMultimedia` DISABLE KEYS */;
/*!40000 ALTER TABLE `MapLocationMultimedia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MapLocations`
--

DROP TABLE IF EXISTS `MapLocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MapLocations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_map_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poi_style` varchar(1023) NOT NULL,
  `rank` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_locations_point_id` (`fk_location_id`),
  KEY `map_locations_map_id` (`fk_map_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MapLocations`
--

LOCK TABLES `MapLocations` WRITE;
/*!40000 ALTER TABLE `MapLocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `MapLocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Maps`
--

DROP TABLE IF EXISTS `Maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Maps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_article_number` int(10) unsigned NOT NULL DEFAULT '0',
  `MapRank` int(10) unsigned NOT NULL DEFAULT '1',
  `MapUsage` tinyint(4) NOT NULL DEFAULT '1',
  `MapCenterLongitude` double NOT NULL DEFAULT '0',
  `MapCenterLatitude` double NOT NULL DEFAULT '0',
  `MapDisplayResolution` smallint(6) NOT NULL DEFAULT '0',
  `MapProvider` varchar(255) NOT NULL DEFAULT '',
  `MapWidth` int(11) NOT NULL DEFAULT '0',
  `MapHeight` int(11) NOT NULL DEFAULT '0',
  `MapName` varchar(1023) NOT NULL,
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `maps_article_number` (`fk_article_number`),
  KEY `maps_map_name` (`MapName`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Maps`
--

LOCK TABLES `Maps` WRITE;
/*!40000 ALTER TABLE `Maps` DISABLE KEYS */;
/*!40000 ALTER TABLE `Maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Multimedia`
--

DROP TABLE IF EXISTS `Multimedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Multimedia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `media_type` varchar(255) NOT NULL DEFAULT '',
  `media_spec` varchar(255) NOT NULL DEFAULT '',
  `media_src` varchar(1023) NOT NULL DEFAULT '',
  `media_height` int(11) NOT NULL DEFAULT '0',
  `media_width` int(11) NOT NULL DEFAULT '0',
  `options` varchar(1023) NOT NULL DEFAULT '',
  `IdUser` int(10) unsigned NOT NULL DEFAULT '0',
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `multimedia_media_type` (`media_type`(32)),
  KEY `multimedia_media_src` (`media_src`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Multimedia`
--

LOCK TABLES `Multimedia` WRITE;
/*!40000 ALTER TABLE `Multimedia` DISABLE KEYS */;
/*!40000 ALTER TABLE `Multimedia` ENABLE KEYS */;
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
INSERT INTO `ObjectTypes` VALUES (1,'article');
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
INSERT INTO `Plugins` VALUES ('interview','3.4.x-0.3',1),('blog','3.4.x-0.2.1',1),('poll','3.4.x-0.2.1',1);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publications`
--

LOCK TABLES `Publications` WRITE;
/*!40000 ALTER TABLE `Publications` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `id` int(10) AUTO_INCREMENT,
  `IdSubscription` int(10) unsigned NOT NULL DEFAULT '0',
  `SectionNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) NULL,
  `StartDate` date NOT NULL DEFAULT '0000-00-00',
  `Days` int(10) unsigned NOT NULL DEFAULT '0',
  `PaidDays` int(10) unsigned NOT NULL DEFAULT '0',
  `NoticeSent` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE (`IdSubscription`,`SectionNumber`,`IdLanguage`)
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SystemPreferences`
--

LOCK TABLES `SystemPreferences` WRITE;
/*!40000 ALTER TABLE `SystemPreferences` DISABLE KEYS */;
INSERT INTO `SystemPreferences` VALUES (1,'ExternalSubscriptionManagement','N','2007-03-07 07:15:36'),(2,'KeywordSeparator',',','2007-03-07 07:15:36'),(3,'LoginFailedAttemptsNum','3','2007-06-16 04:52:31'),(4,'MaxUploadFileSize','2M','2007-10-04 22:16:54'),(5,'UseDBReplication','N','2007-03-07 07:15:36'),(6,'DBReplicationHost','','2007-03-07 07:15:36'),(7,'DBReplicationUser','','2007-03-07 07:15:36'),(8,'DBReplicationPass','','2007-03-07 07:15:36'),(9,'DBReplicationPort','3306','2007-03-07 07:15:36'),(15,'SiteOnline','Y','2007-10-07 01:49:11'),(16,'SiteCharset','utf-8','2007-07-26 04:49:32'),(17,'SiteLocale','en-US','2007-07-26 04:49:56'),(18,'SiteCacheEnabled','Y','2010-07-28 16:17:03'),(22,'SiteMetaKeywords','Newscoop, Sourcefabric, enterprise content management, open source, media, journalism','2011-01-17 12:29:43'),(19,'SiteSecretKey','4b506c2968184be185f6282f5dcac832','2007-10-04 20:51:41'),(20,'SiteSessionLifeTime','1400','2007-10-04 20:51:51'),(21,'SiteTitle','Newscoop','2011-01-17 12:27:00'),(23,'SiteMetaDescription','Newscoop - The open content management system for professional journalists.','2011-01-17 12:29:43'),(24,'SMTPHost','localhost','2007-10-26 01:30:45'),(25,'SMTPPort','25','2007-10-26 01:30:45'),(26,'DBCacheEngine',NULL,'2011-01-17 12:28:20'),(27,'EditorImageRatio','100','2009-06-15 17:21:08'),(28,'TemplateFilter','.*, CVS','2009-06-15 17:21:08'),(29,'ImagecacheLifetime','86400','2009-06-22 11:54:19'),(30,'EditorImageResizeWidth','','2010-06-29 20:31:14'),(31,'EditorImageResizeHeight','','2010-06-29 20:31:14'),(32,'EditorImageZoom','N','2010-06-29 20:31:14'),(33,'TimeZone',NULL,'2010-06-29 20:31:14'),(34,'ExternalCronManagement','Y','2010-06-29 20:31:14'),(35,'TemplateCacheHandler',NULL,'2011-01-17 12:28:20'),(36,'PasswordRecovery','Y','2011-01-17 12:28:20'),(37,'MapCenterLongitudeDefault','14.424133','2011-01-17 12:28:20'),(38,'MapCenterLatitudeDefault','50.089926','2011-01-17 12:28:20'),(39,'MapDisplayResolutionDefault','4','2011-01-17 12:28:20'),(40,'MapViewWidthDefault','600','2011-01-17 12:28:20'),(41,'MapViewHeightDefault','400','2011-01-17 12:28:20'),(42,'MapProviderAvailableGoogleV3','1','2011-01-17 12:28:20'),(43,'MapProviderAvailableMapQuest','1','2011-01-17 12:28:20'),(44,'MapProviderAvailableOSM','1','2011-01-17 12:28:20'),(45,'MapProviderDefault','GoogleV3','2011-01-17 12:28:20'),(46,'MapMarkerDirectory','/js/geocoding/markers/','2011-01-17 12:28:20'),(47,'MapMarkerSourceDefault','marker-gold.png','2011-01-17 12:28:20'),(48,'MapPopupWidthMin','300','2011-01-17 12:28:20'),(49,'MapPopupHeightMin','200','2011-01-17 12:28:20'),(50,'MapVideoWidthYouTube','425','2011-01-17 12:28:20'),(51,'MapVideoHeightYouTube','350','2011-01-17 12:28:20'),(52,'MapVideoWidthVimeo','400','2011-01-17 12:28:20'),(53,'MapVideoHeightVimeo','225','2011-01-17 12:28:20'),(54,'MapVideoWidthFlash','425','2011-01-17 12:28:20'),(55,'MapVideoHeightFlash','350','2011-01-17 12:28:20'),(56,'MapVideoWidthFlv','300','2011-01-17 12:28:20'),(57,'MapVideoHeightFlv','280','2011-01-17 12:28:20'),(58,'FlashServer','','2011-01-17 12:28:20'),(59,'FlashDirectory','videos/','2011-01-17 12:28:20');
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
INSERT INTO `TemplateTypes` VALUES (1,'default'),(2,'issue'),(3,'section'),(4,'article'),(5,'nontpl');
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
  `CacheLifetime` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Templates`
--

LOCK TABLES `Templates` WRITE;
/*!40000 ALTER TABLE `Templates` DISABLE KEYS */;
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
INSERT INTO `TimeUnits` VALUES ('D',1,'days'),('W',1,'weeks'),('M',1,'months'),('Y',1,'years'),('D',18,'dagar'),('W',18,'veckor'),('M',18,'månader'),('Y',18,'år'),('D',13,'días'),('W',13,'semanas'),('M',13,'meses'),('Y',13,'años'),('D',12,'days'),('W',12,'weeks'),('M',12,'months'),('Y',12,'years');
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
-- Table structure for table `TopicNames`
--

DROP TABLE IF EXISTS `TopicNames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TopicNames` (
  `fk_topic_id` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`fk_topic_id`,`fk_language_id`),
  UNIQUE KEY `fk_language_id` (`fk_language_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TopicNames`
--

LOCK TABLES `TopicNames` WRITE;
/*!40000 ALTER TABLE `TopicNames` DISABLE KEYS */;
/*!40000 ALTER TABLE `TopicNames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Topics`
--

DROP TABLE IF EXISTS `Topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_left` int(10) unsigned NOT NULL,
  `node_right` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_left` (`node_left`),
  KEY `node_right` (`node_right`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Topics`
--

LOCK TABLES `Topics` WRITE;
/*!40000 ALTER TABLE `Topics` DISABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Translations`
--

LOCK TABLES `Translations` WRITE;
/*!40000 ALTER TABLE `Translations` DISABLE KEYS */;
INSERT INTO `Translations` VALUES (1,1,1,'article');
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
INSERT INTO `URLTypes` VALUES (1,'template path',''),(2,'short names','');
/*!40000 ALTER TABLE `URLTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Widget`
--

DROP TABLE IF EXISTS `Widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Widget` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(78) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`,`class`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `WidgetContext`
--

DROP TABLE IF EXISTS `WidgetContext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WidgetContext` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `WidgetContext_Widget`
--

DROP TABLE IF EXISTS `WidgetContext_Widget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WidgetContext_Widget` (
  `id` varchar(13) NOT NULL,
  `fk_widgetcontext_id` smallint(3) unsigned NOT NULL,
  `fk_widget_id` mediumint(8) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL,
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `settings` text NOT NULL,
  PRIMARY KEY (`id`,`fk_user_id`),
  KEY `fk_user_id` (`fk_user_id`,`fk_widgetcontext_id`,`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liveuser_applications`
--

DROP TABLE IF EXISTS `liveuser_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL DEFAULT '0',
  `application_define_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `applications_define_name_i_idx` (`application_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_applications`
--

LOCK TABLES `liveuser_applications` WRITE;
/*!40000 ALTER TABLE `liveuser_applications` DISABLE KEYS */;
INSERT INTO `liveuser_applications` VALUES (1,'Campsite'),(2,'Campcaster');
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
INSERT INTO `liveuser_applications_application_id_seq` VALUES (2);
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
  `area_define_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`area_id`),
  UNIQUE KEY `areas_define_name_i_idx` (`application_id`,`area_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_areas`
--

LOCK TABLES `liveuser_areas` WRITE;
/*!40000 ALTER TABLE `liveuser_areas` DISABLE KEYS */;
INSERT INTO `liveuser_areas` VALUES (1,1,'Articles');
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
INSERT INTO `liveuser_grouprights` VALUES (1,1,3),(1,3,3),(1,4,3),(1,6,3),(1,7,3),(1,8,3),(1,9,3),(1,10,3),(1,11,3),(1,12,3),(1,13,3),(1,14,3),(1,15,3),(1,16,3),(1,17,3),(1,18,3),(1,19,3),(1,20,3),(1,21,3),(1,22,3),(1,23,3),(1,24,3),(1,25,3),(1,26,3),(1,27,3),(1,28,3),(1,29,3),(1,30,3),(1,31,3),(1,32,3),(1,33,3),(1,34,3),(1,35,3),(1,36,3),(1,37,3),(1,38,3),(1,39,3),(1,40,3),(1,41,3),(1,42,3),(1,43,3),(1,44,3),(1,45,3),(1,46,3),(1,47,3),(1,48,3),(1,49,3),(1,50,3),(1,51,3),(1,53,3),(1,56,3),(1,57,3),(1,58,3),(1,59,3),(1,60,3),(1,61,3),(1,62,3),(1,63,3),(1,65,3),(1,64,3),(1,66,3),(1,67,3),(1,68,3),(1,69,3),(1,70,3),(1,71,3),(1,73,3),(2,1,3),(2,3,3),(2,4,3),(2,6,3),(2,7,3),(2,8,3),(2,9,3),(2,10,3),(2,12,3),(2,13,3),(2,14,3),(2,15,3),(2,17,3),(2,18,3),(2,19,3),(2,22,3),(2,24,3),(2,25,3),(2,26,3),(2,27,3),(2,28,3),(2,29,3),(2,30,3),(2,34,3),(2,35,3),(2,36,3),(2,37,3),(2,38,3),(2,39,3),(2,41,3),(2,42,3),(2,43,3),(2,44,3),(2,45,3),(2,47,3),(2,48,3),(2,49,3),(2,55,3),(2,57,3),(2,59,3),(2,60,3),(2,63,3),(2,65,3),(2,66,3),(2,67,3),(2,68,3),(2,69,3),(2,73,3),(3,1,3),(3,3,3),(3,4,3),(3,6,3),(3,7,3),(3,8,3),(3,9,3),(3,10,3),(3,14,3),(3,17,3),(3,18,3),(3,25,3),(3,26,3),(3,27,3),(3,28,3),(3,29,3),(3,34,3),(3,35,3),(3,36,3),(3,37,3),(3,38,3),(3,39,3),(3,42,3),(3,45,3),(3,46,3),(3,47,3),(3,48,3),(3,49,3),(3,66,3),(3,68,3),(3,73,3),(4,1,3),(4,3,3),(4,4,3),(4,6,3),(4,7,3),(4,9,3),(4,10,3),(4,26,3),(4,27,3),(4,28,3),(4,29,3),(4,34,3),(4,36,3),(4,37,3),(4,38,3),(4,39,3),(4,42,3),(4,48,3),(4,49,3),(4,68,3),(4,73,3),(5,59,3),(5,61,3),(1,54,3),(1,74,3),(1,72,3),(1,92,3),(1,91,3),(2,91,3),(1,97,3),(1,98,3),(2,98,3),(1,101,3),(2,101,3),(3,101,3),(1,103,3);
/*!40000 ALTER TABLE `liveuser_grouprights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `liveuser_groups`
--

DROP TABLE IF EXISTS `liveuser_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_type` int(11) NOT NULL DEFAULT '0',
  `group_define_name` varchar(32) NOT NULL DEFAULT '',
  `role_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `groups_define_name_i_idx` (`group_define_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_groups`
--

LOCK TABLES `liveuser_groups` WRITE;
/*!40000 ALTER TABLE `liveuser_groups` DISABLE KEYS */;
INSERT INTO `liveuser_groups` VALUES (1,0,'Administrator', 1),(2,0,'Chief Editor', 3),(3,0,'Editor', 4),(4,0,'Journalist', 5),(5,0,'Subscription manager', 6);
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_groups_group_id_seq`
--

LOCK TABLES `liveuser_groups_group_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_groups_group_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_groups_group_id_seq` VALUES (5);
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
INSERT INTO `liveuser_groupusers` VALUES (1,1);
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
  `auth_user_id` varchar(32) NOT NULL DEFAULT '',
  `auth_container_name` varchar(32) NOT NULL DEFAULT '',
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
INSERT INTO `liveuser_perm_users` VALUES (1,'1','DB',1);
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_perm_users_perm_user_id_seq`
--

LOCK TABLES `liveuser_perm_users_perm_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_perm_users_perm_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_perm_users_perm_user_id_seq` VALUES (1);
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
  `right_define_name` varchar(32) NOT NULL DEFAULT '',
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
INSERT INTO `liveuser_rights` VALUES (1,0,'AddArticle',1),(3,0,'AddFile',1),(4,0,'AddImage',1),(6,0,'AttachImageToArticle',1),(7,0,'AttachTopicToArticle',1),(8,0,'ChangeArticle',1),(9,0,'ChangeFile',1),(10,0,'ChangeImage',1),(11,0,'ChangeSystemPreferences',1),(12,0,'ClearCache',1),(13,0,'CommentEnable',1),(14,0,'CommentModerate',1),(15,0,'DeleteArticle',1),(16,0,'DeleteArticleTypes',1),(17,0,'DeleteCountries',1),(18,0,'DeleteFile',1),(19,0,'DeleteImage',1),(20,0,'DeleteIssue',1),(21,0,'DeleteLanguages',1),(22,0,'DeletePub',1),(23,0,'DeleteSection',1),(24,0,'DeleteTempl',1),(25,0,'DeleteUsers',1),(26,0,'EditorBold',1),(27,0,'EditorCharacterMap',1),(28,0,'EditorCopyCutPaste',1),(29,0,'EditorEnlarge',1),(30,0,'EditorFindReplace',1),(31,0,'EditorFontColor',1),(32,0,'EditorFontFace',1),(33,0,'EditorFontSize',1),(34,0,'EditorHorizontalRule',1),(35,0,'EditorImage',1),(36,0,'EditorIndent',1),(37,0,'EditorItalic',1),(38,0,'EditorLink',1),(39,0,'EditorListBullet',1),(40,0,'EditorListNumber',1),(41,0,'EditorSourceView',1),(42,0,'EditorStrikethrough',1),(43,0,'EditorSubhead',1),(44,0,'EditorSubscript',1),(45,0,'EditorSuperscript',1),(46,0,'EditorTable',1),(47,0,'EditorTextAlignment',1),(48,0,'EditorTextDirection',1),(49,0,'EditorUnderline',1),(50,0,'EditorUndoRedo',1),(51,0,'plugin_manager',1),(52,0,'MailNotify',1),(53,0,'ManageArticleTypes',1),(54,0,'ManageCountries',1),(55,0,'ManageIndexer',1),(56,0,'ManageIssue',1),(57,0,'ManageLanguages',1),(58,0,'ManageLocalizer',1),(59,0,'ManagePub',1),(60,0,'ManageReaders',1),(61,0,'ManageSection',1),(62,0,'ManageSubscriptions',1),(63,0,'ManageTempl',1),(64,0,'ManageTopics',1),(65,0,'ManageUserTypes',1),(66,0,'ManageUsers',1),(67,0,'MoveArticle',1),(68,0,'Publish',1),(69,0,'TranslateArticle',1),(70,0,'ViewLogs',1),(71,0,'SyncPhorumUsers',1),(72,0,'EditorStatusBar',1),(73,0,'EditorSpellcheckerEnabled',1),(74,0,'ManageBackup',1),(89,0,'plugin_interview_notify',1),(90,0,'plugin_interview_guest',1),(91,0,'plugin_interview_moderator',1),(92,0,'plugin_interview_admin',1),(97,0,'plugin_blog_admin',1),(98,0,'plugin_blog_moderator',1),(101,0,'plugin_poll',1),(103,0,'EditAuthors',1);
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
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_rights_right_id_seq`
--

LOCK TABLES `liveuser_rights_right_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_rights_right_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_rights_right_id_seq` VALUES (103);
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
  `language_id` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
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
  `password_reset_token` varchar(85) DEFAULT NULL,
  `role_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UName` (`UName`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_users`
--

LOCK TABLES `liveuser_users` WRITE;
/*!40000 ALTER TABLE `liveuser_users` DISABLE KEYS */;
INSERT INTO `liveuser_users` VALUES (1,NULL,'Administrator','admin','b2d716fb2328a246e8285f47b1500ebcb349c187','admin@email.addr','N',1,'','','','AD','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','','2011-01-17 12:31:17','0000-00-00 00:00:00','2011-01-17 14:29:16',1,NULL,2);
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `liveuser_users_auth_user_id_seq`
--

LOCK TABLES `liveuser_users_auth_user_id_seq` WRITE;
/*!40000 ALTER TABLE `liveuser_users_auth_user_id_seq` DISABLE KEYS */;
INSERT INTO `liveuser_users_auth_user_id_seq` VALUES (1);
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phorum_forums`
--

LOCK TABLES `phorum_forums` WRITE;
/*!40000 ALTER TABLE `phorum_forums` DISABLE KEYS */;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
INSERT INTO `phorum_settings` VALUES ('title','V','Phorum 5'),('cache','V','/tmp'),('session_timeout','V','30'),('short_session_timeout','V','60'),('tight_security','V','0'),('session_path','V','/'),('session_domain','V',''),('admin_session_salt','V','0.62629000 1146135136'),('cache_users','V','0'),('register_email_confirm','V','0'),('default_template','V','default'),('default_language','V','english'),('use_cookies','V','1'),('use_bcc','V','1'),('use_rss','V','1'),('internal_version','V','2006032300'),('PROFILE_FIELDS','S','a:1:{i:0;a:3:{s:4:\"name\";s:9:\"real_name\";s:6:\"length\";i:255;s:13:\"html_disabled\";i:1;}}'),('enable_pm','V','0'),('user_edit_timelimit','V','0'),('enable_new_pm_count','V','1'),('enable_dropdown_userlist','V','1'),('enable_moderator_notifications','V','1'),('show_new_on_index','V','1'),('dns_lookup','V','1'),('tz_offset','V','0'),('user_time_zone','V','1'),('user_template','V','0'),('registration_control','V','1'),('file_uploads','V','0'),('file_types','V',''),('max_file_size','V',''),('file_space_quota','V',''),('file_offsite','V','0'),('system_email_from_name','V',''),('hide_forums','V','1'),('track_user_activity','V','86400'),('html_title','V','Phorum'),('head_tags','V',''),('redirect_after_post','V','list'),('reply_on_read_page','V','1'),('status','V','normal'),('use_new_folder_style','V','1'),('default_forum_options','S','a:24:{s:8:\"forum_id\";i:0;s:10:\"moderation\";i:0;s:16:\"email_moderators\";i:0;s:9:\"pub_perms\";i:1;s:9:\"reg_perms\";i:15;s:13:\"display_fixed\";i:0;s:8:\"template\";s:7:\"default\";s:8:\"language\";s:7:\"english\";s:13:\"threaded_list\";i:0;s:13:\"threaded_read\";i:0;s:17:\"reverse_threading\";i:0;s:12:\"float_to_top\";i:1;s:16:\"list_length_flat\";i:30;s:20:\"list_length_threaded\";i:15;s:11:\"read_length\";i:30;s:18:\"display_ip_address\";i:0;s:18:\"allow_email_notify\";i:0;s:15:\"check_duplicate\";i:1;s:11:\"count_views\";i:2;s:15:\"max_attachments\";i:0;s:22:\"allow_attachment_types\";s:0:\"\";s:19:\"max_attachment_size\";i:0;s:24:\"max_totalattachment_size\";i:0;s:5:\"vroot\";i:0;}'),('hooks','S','a:1:{s:6:\"format\";a:2:{s:4:\"mods\";a:2:{i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";}s:5:\"funcs\";a:2:{i:0;s:18:\"phorum_mod_smileys\";i:1;s:14:\"phorum_bb_code\";}}}'),('mods','S','a:4:{s:4:\"html\";i:0;s:7:\"replace\";i:0;s:7:\"smileys\";i:1;s:6:\"bbcode\";i:1;}');
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
  `fk_campsite_user_id` int(10) unsigned NOT NULL DEFAULT '0',
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
  UNIQUE KEY `fk_campsite_user_id` (`fk_campsite_user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `active` (`active`),
  KEY `userpass` (`username`,`password`),
  KEY `sessid_st` (`sessid_st`),
  KEY `cookie_sessid_lt` (`cookie_sessid_lt`),
  KEY `activity` (`date_last_active`,`hide_activity`,`last_active_forum`),
  KEY `date_added` (`date_added`),
  KEY `email_temp` (`email_temp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phorum_users`
--

LOCK TABLES `phorum_users` WRITE;
/*!40000 ALTER TABLE `phorum_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `phorum_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_blog_blog`
--

DROP TABLE IF EXISTS `plugin_blog_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_blog_blog` (
  `blog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `info` text NOT NULL,
  `admin_remark` text NOT NULL,
  `request_text` text NOT NULL,
  `status` enum('online','offline','moderated') NOT NULL DEFAULT 'online',
  `admin_status` enum('online','offline','moderated','readonly','pending') NOT NULL DEFAULT 'pending',
  `entries_online` int(10) unsigned NOT NULL DEFAULT '0',
  `entries_offline` int(10) unsigned NOT NULL DEFAULT '0',
  `comments_online` int(10) unsigned NOT NULL,
  `comments_offline` int(10) unsigned NOT NULL,
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blog_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_blog_blog`
--

LOCK TABLES `plugin_blog_blog` WRITE;
/*!40000 ALTER TABLE `plugin_blog_blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_blog_blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_blog_comment`
--

DROP TABLE IF EXISTS `plugin_blog_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_blog_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_entry_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_blog_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fk_mood_id` varchar(255) NOT NULL,
  `status` enum('online','offline','pending') NOT NULL DEFAULT 'pending',
  `admin_status` enum('online','offline','pending') NOT NULL DEFAULT 'pending',
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_blog_comment`
--

LOCK TABLES `plugin_blog_comment` WRITE;
/*!40000 ALTER TABLE `plugin_blog_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_blog_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_blog_entry`
--

DROP TABLE IF EXISTS `plugin_blog_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_blog_entry` (
  `entry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_blog_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `released` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `fk_mood_id` int(10) unsigned NOT NULL,
  `status` enum('online','offline') NOT NULL DEFAULT 'online',
  `admin_status` enum('online','offline','pending') NOT NULL DEFAULT 'pending',
  `comments_online` int(10) unsigned NOT NULL DEFAULT '0',
  `comments_offline` int(10) unsigned NOT NULL DEFAULT '0',
  `feature` varchar(255) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_blog_entry`
--

LOCK TABLES `plugin_blog_entry` WRITE;
/*!40000 ALTER TABLE `plugin_blog_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_blog_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_blog_entry_topic`
--

DROP TABLE IF EXISTS `plugin_blog_entry_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_blog_entry_topic` (
  `fk_entry_id` int(10) unsigned NOT NULL,
  `fk_topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_entry_id`,`fk_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_blog_entry_topic`
--

LOCK TABLES `plugin_blog_entry_topic` WRITE;
/*!40000 ALTER TABLE `plugin_blog_entry_topic` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_blog_entry_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_blog_topic`
--

DROP TABLE IF EXISTS `plugin_blog_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_blog_topic` (
  `fk_blog_id` int(10) unsigned NOT NULL,
  `fk_topic_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_blog_id`,`fk_topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_blog_topic`
--

LOCK TABLES `plugin_blog_topic` WRITE;
/*!40000 ALTER TABLE `plugin_blog_topic` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_blog_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_interview_interviews`
--

DROP TABLE IF EXISTS `plugin_interview_interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_interview_interviews` (
  `interview_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_moderator_user_id` int(10) unsigned NOT NULL,
  `fk_guest_user_id` int(10) unsigned NOT NULL,
  `title` varchar(256) NOT NULL,
  `fk_image_id` int(10) unsigned DEFAULT NULL,
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
  `invitation_template_guest` text NOT NULL,
  `invitation_template_questioneer` text NOT NULL,
  `guest_invitation_sent` datetime DEFAULT NULL,
  `questioneer_invitation_sent` datetime DEFAULT NULL,
  `invitation_password` varchar(10) NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`interview_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_interview_interviews`
--

LOCK TABLES `plugin_interview_interviews` WRITE;
/*!40000 ALTER TABLE `plugin_interview_interviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_interview_interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_interview_items`
--

DROP TABLE IF EXISTS `plugin_interview_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_interview_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_interview_id` int(10) unsigned NOT NULL,
  `fk_questioneer_user_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `question_date` datetime NOT NULL,
  `status` enum('draft','pending','published','rejected') NOT NULL DEFAULT 'draft',
  `answer` text NOT NULL,
  `answer_date` datetime NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_interview_items`
--

LOCK TABLES `plugin_interview_items` WRITE;
/*!40000 ALTER TABLE `plugin_interview_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_interview_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll`
--

DROP TABLE IF EXISTS `plugin_poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll` (
  `poll_nr` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_poll_nr` int(11) NOT NULL,
  `is_extended` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `date_begin` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `nr_of_answers` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `votes_per_user` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `nr_of_votes` int(10) unsigned NOT NULL,
  `nr_of_votes_overall` int(10) unsigned NOT NULL,
  `percentage_of_votes_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`poll_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll`
--

LOCK TABLES `plugin_poll` WRITE;
/*!40000 ALTER TABLE `plugin_poll` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll_answer`
--

DROP TABLE IF EXISTS `plugin_poll_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll_answer` (
  `fk_poll_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `nr_answer` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `answer` varchar(255) NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL DEFAULT '0',
  `percentage` float unsigned NOT NULL,
  `percentage_overall` float unsigned NOT NULL,
  `value` int(11) NOT NULL,
  `average_value` float NOT NULL,
  `on_hitlist` tinyint(4) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `NrPoll` (`fk_poll_nr`,`fk_language_id`,`nr_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll_answer`
--

LOCK TABLES `plugin_poll_answer` WRITE;
/*!40000 ALTER TABLE `plugin_poll_answer` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll_answer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll_article`
--

DROP TABLE IF EXISTS `plugin_poll_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll_article` (
  `fk_poll_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_article_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_article_language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_poll_nr`,`fk_article_nr`,`fk_article_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll_article`
--

LOCK TABLES `plugin_poll_article` WRITE;
/*!40000 ALTER TABLE `plugin_poll_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll_issue`
--

DROP TABLE IF EXISTS `plugin_poll_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll_issue` (
  `fk_poll_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_issue_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_issue_language_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_poll_nr`,`fk_issue_nr`,`fk_issue_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll_issue`
--

LOCK TABLES `plugin_poll_issue` WRITE;
/*!40000 ALTER TABLE `plugin_poll_issue` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll_issue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll_publication`
--

DROP TABLE IF EXISTS `plugin_poll_publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll_publication` (
  `fk_poll_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_poll_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll_publication`
--

LOCK TABLES `plugin_poll_publication` WRITE;
/*!40000 ALTER TABLE `plugin_poll_publication` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll_publication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_poll_section`
--

DROP TABLE IF EXISTS `plugin_poll_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_poll_section` (
  `fk_poll_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_section_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_section_language_id` int(10) unsigned NOT NULL,
  `fk_issue_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_poll_nr`,`fk_section_nr`,`fk_section_language_id`,`fk_issue_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_poll_section`
--

LOCK TABLES `plugin_poll_section` WRITE;
/*!40000 ALTER TABLE `plugin_poll_section` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_poll_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_pollanswer_attachment`
--

DROP TABLE IF EXISTS `plugin_pollanswer_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_pollanswer_attachment` (
  `fk_poll_nr` int(11) NOT NULL,
  `fk_pollanswer_nr` int(11) NOT NULL,
  `fk_attachment_id` int(11) NOT NULL,
  PRIMARY KEY (`fk_poll_nr`,`fk_pollanswer_nr`,`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_pollanswer_attachment`
--

LOCK TABLES `plugin_pollanswer_attachment` WRITE;
/*!40000 ALTER TABLE `plugin_pollanswer_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_pollanswer_attachment` ENABLE KEYS */;
UNLOCK TABLES;

-- Acl roles
DROP TABLE IF EXISTS `acl_role`;
CREATE TABLE IF NOT EXISTS `acl_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Acl roles data
INSERT INTO `acl_role` (`id`) VALUES
(1),
(2),
(3),
(4),
(5),
(6);

-- Acl rules
DROP TABLE IF EXISTS `acl_rule`;
CREATE TABLE IF NOT EXISTS `acl_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `role_id` int(10) NOT NULL,
  `resource` varchar(80) NOT NULL DEFAULT '',
  `action` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Acl rules data
INSERT INTO `acl_rule` (`id`, `type`, `role_id`, `resource`, `action`) VALUES
(1, 'allow', 1, '', '');


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-01-17 14:49:45
