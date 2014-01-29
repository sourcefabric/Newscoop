-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: newscoop
-- ------------------------------------------------------
-- Server version 5.5.31-0ubuntu0.13.04.1

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `order` int(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`fk_article_number`,`fk_language_id`,`fk_author_id`,`fk_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ArticleImages`
--

DROP TABLE IF EXISTS `ArticleImages`;

CREATE TABLE ArticleImages (
  id INT AUTO_INCREMENT NOT NULL,
  NrArticle INT NOT NULL,
  Number INT DEFAULT NULL,
  is_default TINYINT(1) DEFAULT NULL,
  IdImage INT DEFAULT NULL,
  INDEX IDX_A9426E241D447EDE (IdImage),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

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
-- Table structure for table `ArticleRendition`
--

DROP TABLE IF EXISTS `ArticleRendition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticleRendition` (
  `image_id` int(11) NOT NULL,
  `rendition_id` varchar(255) NOT NULL,
  `articleNumber` int(11) NOT NULL,
  `imageSpecs` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`articleNumber`,`image_id`,`rendition_id`),
  KEY `IDX_794B8A6C3DA5256D` (`image_id`),
  KEY `IDX_794B8A6CFD656AA1` (`rendition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `webcode` varchar(10) DEFAULT NULL,
  `indexed` timestamp NULL DEFAULT NULL,
  `rating_enabled` tinyint(1) DEFAULT '1',
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
  `Source` enum('local','feedback') NOT NULL DEFAULT 'local',
  `Status` enum('unapproved','approved') NOT NULL DEFAULT 'approved',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `status` char(1) DEFAULT NULL,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`params`,`template`),
  KEY `expired` (`expired`),
  KEY `template` (`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;

CREATE TABLE Images (
  Id INT AUTO_INCREMENT NOT NULL,
  Location VARCHAR(255) NOT NULL,
  ImageFileName VARCHAR(80) DEFAULT NULL,
  ThumbnailFileName VARCHAR(80) DEFAULT NULL,
  TimeCreated DATETIME DEFAULT NULL,
  LastModified DATETIME DEFAULT NULL,
  URL VARCHAR(255) DEFAULT NULL,
  Description VARCHAR(255) DEFAULT NULL,
  width INT DEFAULT NULL,
  height INT DEFAULT NULL,
  Photographer VARCHAR(255) DEFAULT NULL,
  photographer_url VARCHAR(255) DEFAULT NULL,
  Place VARCHAR(255) DEFAULT NULL,
  Date VARCHAR(255) DEFAULT NULL,
  ContentType VARCHAR(255) NOT NULL,
  is_updated_storage INT NOT NULL,
  Source VARCHAR(255) DEFAULT NULL,
  Status VARCHAR(255) NOT NULL,
  UploadedByUser INT DEFAULT NULL,
  INDEX IDX_E7B3BB5C447C15B9 (UploadedByUser),
  INDEX is_updated_storage (is_updated_storage),
  INDEX Description (Description),
  INDEX Photographer (Photographer),
  INDEX Place (Place),
  PRIMARY KEY(Id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

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
-- Table structure for table `Issues`
--

DROP TABLE IF EXISTS `Issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Issues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `ShortName` (`IdPublication`,`IdLanguage`,`ShortName`),
  UNIQUE KEY `issue_unique` (`IdPublication`,`Number`,`IdLanguage`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `RFC3066bis` varchar(255) NOT NULL DEFAULT '',
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
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `priority` (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=648 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  KEY `map_locations_map_id` (`fk_map_id`),
  KEY `map_locations_poi_style_idx` (`poi_style`(64))
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `Plugins`
--

DROP TABLE IF EXISTS `Plugins`;

CREATE TABLE Plugins (
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(256) NOT NULL,
  Details LONGTEXT NOT NULL,
  type INT NOT NULL,
  installed_with INT NOT NULL,
  Description LONGTEXT NOT NULL,
  Version VARCHAR(256) NOT NULL,
  author VARCHAR(256) NOT NULL,
  license VARCHAR(256) NOT NULL,
  Enabled TINYINT(1) NOT NULL,
  installed_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY(Id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

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
  `comments_public_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_captcha_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_spam_blocking_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `comments_moderator_to` varchar(255) NOT NULL DEFAULT '',
  `comments_moderator_from` varchar(255) NOT NULL DEFAULT '',
  `url_error_tpl_id` int(10) unsigned DEFAULT NULL,
  `seo` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Alias` (`IdDefaultAlias`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `Sections`
--

DROP TABLE IF EXISTS `Sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_issue_id` int(10) unsigned NOT NULL,
  `IdPublication` int(10) unsigned NOT NULL DEFAULT '0',
  `NrIssue` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned NOT NULL DEFAULT '0',
  `Number` int(10) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '',
  `ShortName` varchar(32) NOT NULL DEFAULT '',
  `Description` blob,
  `SectionTplId` int(10) unsigned DEFAULT NULL,
  `ArticleTplId` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`IdLanguage`,`Name`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`IdLanguage`,`ShortName`),
  UNIQUE KEY `section_unique` (`IdPublication`,`NrIssue`,`IdLanguage`,`Number`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `SubsSections`
--

DROP TABLE IF EXISTS `SubsSections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SubsSections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdSubscription` int(10) unsigned NOT NULL DEFAULT '0',
  `SectionNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(10) unsigned DEFAULT NULL,
  `StartDate` date NOT NULL DEFAULT '0000-00-00',
  `Days` int(10) unsigned NOT NULL DEFAULT '0',
  `PaidDays` int(10) unsigned NOT NULL DEFAULT '0',
  `NoticeSent` enum('N','Y') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IdSubscription` (`IdSubscription`,`SectionNumber`,`IdLanguage`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Subscriptions`
--

DROP TABLE IF EXISTS `Subscriptions`;
CREATE TABLE Subscriptions (
  Id INT AUTO_INCREMENT NOT NULL,
  ToPay NUMERIC(10, 0) NOT NULL,
  Type VARCHAR(255) NOT NULL,
  Currency VARCHAR(255) NOT NULL,
  Active VARCHAR(255) NOT NULL,
  IdUser INT DEFAULT NULL,
  IdSubscription INT DEFAULT NULL,
  IdPublication INT DEFAULT NULL,
  INDEX IDX_B709C1F4F9C28DE1 (IdUser),
  INDEX IDX_B709C1F4303CB8FA (IdSubscription),
  INDEX IDX_B709C1F45C1FD3F4 (IdPublication),
  PRIMARY KEY(Id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

--
-- Table structure for table `SubscriptionArticle`
--

DROP TABLE IF EXISTS `SubscriptionArticle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE SubscriptionArticle (
  id INT AUTO_INCREMENT NOT NULL,
  article_number INT NOT NULL,
  language_id INT DEFAULT NULL,
  StartDate DATE NOT NULL,
  Days INT NOT NULL,
  PaidDays INT NOT NULL,
  NoticeSent VARCHAR(255) NOT NULL,
  IdSubscription INT DEFAULT NULL,
  INDEX IDX_DBC6BEEA303CB8FA (IdSubscription),
  INDEX IDX_DBC6BEEAFC5788D482F1BAF4 (article_number, language_id),
  INDEX IDX_DBC6BEEA82F1BAF4 (language_id), PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

--
-- Table structure for table `SubscriptionIssue`
--

DROP TABLE IF EXISTS `SubscriptionIssue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE SubscriptionIssue (
  id INT AUTO_INCREMENT NOT NULL,
  issue_number INT NOT NULL,
  language_id INT DEFAULT NULL,
  StartDate DATE NOT NULL,
  Days INT NOT NULL,
  PaidDays INT NOT NULL,
  NoticeSent VARCHAR(255) NOT NULL,
  IdSubscription INT DEFAULT NULL,
  INDEX IDX_DBC6BGGA303CB8FA (IdSubscription),
  INDEX IDX_DBC6BGGAFC5788D482F1BAF4 (issue_number, language_id),
  INDEX IDX_DBC6BGGA82F1BAF4 (language_id), PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

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
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=1660 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `Versions`
--

DROP TABLE IF EXISTS `Versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Versions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ver_name` varchar(255) NOT NULL,
  `ver_value` varchar(255) NOT NULL DEFAULT '',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ver_name` (`ver_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
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
-- Table structure for table `Xdebate`
--

DROP TABLE IF EXISTS `Xdebate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xdebate` (
  `NrArticle` int(11) NOT NULL,
  `IdLanguage` int(11) NOT NULL,
  `Fpro_title` varchar(255) DEFAULT NULL,
  `Fpro_text` mediumblob,
  `Fcontra_title` varchar(255) DEFAULT NULL,
  `Fcontra_text` mediumblob,
  `Fteaser` mediumblob,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Xlink`
--

DROP TABLE IF EXISTS `Xlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xlink` (
  `NrArticle` int(10) unsigned NOT NULL,
  `IdLanguage` int(10) unsigned NOT NULL,
  `Furl` varchar(255) NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Xnews`
--

DROP TABLE IF EXISTS `Xnews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xnews` (
  `NrArticle` int(10) unsigned NOT NULL,
  `IdLanguage` int(10) unsigned NOT NULL,
  `Fdeck` mediumblob NOT NULL,
  `Ffull_text` mediumblob NOT NULL,
  `Fhighlight` tinyint(1) NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Xpage`
--

DROP TABLE IF EXISTS `Xpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xpage` (
  `NrArticle` int(10) unsigned NOT NULL,
  `IdLanguage` int(10) unsigned NOT NULL,
  `Ffull_text` mediumblob NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Xpoll`
--

DROP TABLE IF EXISTS `Xpoll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xpoll` (
  `NrArticle` int(11) NOT NULL,
  `IdLanguage` int(11) NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_role`
--

DROP TABLE IF EXISTS `acl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `acl_rule`
--

DROP TABLE IF EXISTS `acl_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` enum('allow','deny') NOT NULL DEFAULT 'allow',
  `role_id` int(10) NOT NULL,
  `resource` varchar(80) NOT NULL DEFAULT '',
  `action` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id` (`role_id`,`resource`,`action`)
) ENGINE=MyISAM AUTO_INCREMENT=336 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `article_datetimes`
--

DROP TABLE IF EXISTS `article_datetimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_datetimes` (
  `id_article_datetime` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL COMMENT 'NULL = 00:00',
  `end_time` time DEFAULT NULL COMMENT 'NULL = 23:59',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'NULL = only 1 day',
  `recurring` enum('daily','weekly','monthly','yearly') DEFAULT NULL,
  `article_id` int(10) unsigned NOT NULL,
  `article_type` varchar(166) NOT NULL,
  `field_name` varchar(166) NOT NULL,
  `event_comment` text,
  PRIMARY KEY (`id_article_datetime`),
  KEY `article_id` (`article_id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `article_type` (`article_type`),
  KEY `field_name` (`field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_event`
--

DROP TABLE IF EXISTS `audit_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `resource_type` varchar(80) NOT NULL,
  `resource_id` varchar(80) DEFAULT NULL,
  `resource_title` varchar(255) DEFAULT NULL,
  `resource_diff` longtext,
  `action` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=506 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;

CREATE TABLE comment (
  id INT AUTO_INCREMENT NOT NULL,
  fk_parent_id INT DEFAULT NULL,
  fk_thread_id INT NOT NULL,
  subject VARCHAR(140) NOT NULL,
  message VARCHAR(255) NOT NULL,
  thread_level VARCHAR(4) NOT NULL,
  thread_order VARCHAR(4) NOT NULL,
  status VARCHAR(2) NOT NULL,
  ip VARCHAR(39) NOT NULL,
  time_created DATETIME NOT NULL,
  time_updated DATETIME NOT NULL,
  likes VARCHAR(4) NOT NULL,
  dislikes VARCHAR(4) NOT NULL,
  recommended VARCHAR(1) NOT NULL,
  indexed DATETIME DEFAULT NULL,
  source VARCHAR(60) NULL DEFAULT NULL,
  fk_comment_commenter_id INT DEFAULT NULL,
  fk_forum_id INT DEFAULT NULL,
  fk_language_id INT DEFAULT NULL,
  INDEX IDX_9474526C8A5657F3 (fk_comment_commenter_id),
  INDEX IDX_9474526C1BE4F90E (fk_forum_id),
  INDEX IDX_9474526C13231DE0 (fk_parent_id),
  INDEX IDX_9474526C83C99789 (fk_thread_id),
  INDEX IDX_9474526CEB0716C0 (fk_language_id),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE comment ADD CONSTRAINT FK_9474526C13231DE0 FOREIGN KEY (fk_parent_id) REFERENCES comment (id) ON DELETE SET NULL;

--
-- Table structure for table `comment_acceptance`
--

DROP TABLE IF EXISTS `comment_acceptance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_acceptance` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fk_forum_id` int(10) DEFAULT '0',
  `for_column` tinyint(4) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `search_type` tinyint(4) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_forum_id` (`fk_forum_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment_commenter`
--

DROP TABLE IF EXISTS `comment_commenter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_commenter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_user_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(39) NOT NULL DEFAULT '',
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `context_articles`
--

DROP TABLE IF EXISTS `context_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `context_articles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fk_context_id` int(10) NOT NULL,
  `fk_article_no` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `context_boxes`
--

DROP TABLE IF EXISTS `context_boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `context_boxes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fk_article_no` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `publication_id` int(11) DEFAULT NULL,
  `article_language` int(11) DEFAULT NULL,
  `article_number` int(11) DEFAULT NULL,
  `subject` varchar(128) DEFAULT NULL,
  `message` varchar(2048) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `url` varchar(128) NOT NULL,
  `time_created` datetime NOT NULL,
  `time_updated` datetime NOT NULL,
  `attachment_type` int(1) DEFAULT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ingest_feed`
--

DROP TABLE IF EXISTS `ingest_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingest_feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `mode` varchar(25) DEFAULT 'manual',
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ingest_feed_entry`
--

DROP TABLE IF EXISTS `ingest_feed_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingest_feed_entry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) unsigned NOT NULL,
  `date_id` varchar(20) DEFAULT NULL,
  `news_item_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `summary` text,
  `category` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `embargoed` datetime DEFAULT NULL,
  `priority` tinyint(4) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `attributes` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date_id` (`date_id`,`news_item_id`),
  KEY `status` (`status`,`updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `liveuser_users`
--

DROP TABLE IF EXISTS `liveuser_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `liveuser_users` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `KeyId` int(10) unsigned DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `UName` varchar(70) DEFAULT NULL,
  `Password` varchar(64) DEFAULT NULL,
  `EMail` varchar(255) NOT NULL,
  `Reader` enum('Y','N') NOT NULL DEFAULT 'Y',
  `fk_user_type` int(10) unsigned DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `StrAddress` varchar(255) DEFAULT NULL,
  `State` varchar(32) DEFAULT NULL,
  `CountryCode` varchar(21) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Contact` varchar(64) DEFAULT NULL,
  `Phone2` varchar(20) DEFAULT NULL,
  `Title` enum('Mr.','Mrs.','Ms.','Dr.') NOT NULL DEFAULT 'Mr.',
  `Gender` enum('M','F') DEFAULT NULL,
  `Age` enum('0-17','18-24','25-39','40-49','50-65','65-') NOT NULL DEFAULT '0-17',
  `PostalCode` varchar(70) DEFAULT NULL,
  `Employer` varchar(140) DEFAULT NULL,
  `EmployerType` varchar(140) DEFAULT NULL,
  `Position` varchar(70) DEFAULT NULL,
  `Interests` mediumblob,
  `How` varchar(255) DEFAULT NULL,
  `Languages` varchar(100) DEFAULT NULL,
  `Improvements` mediumblob,
  `Pref1` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref2` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref3` enum('N','Y') NOT NULL DEFAULT 'N',
  `Pref4` enum('N','Y') NOT NULL DEFAULT 'N',
  `Field1` varchar(150) DEFAULT NULL,
  `Field2` varchar(150) DEFAULT NULL,
  `Field3` varchar(150) DEFAULT NULL,
  `Field4` varchar(150) DEFAULT NULL,
  `Field5` varchar(150) DEFAULT NULL,
  `Text1` mediumblob,
  `Text2` mediumblob,
  `Text3` mediumblob,
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_created` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  `password_reset_token` varchar(85) DEFAULT NULL,
  `role_id` int(10) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `points` int(10) DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `subscriber` int(10) DEFAULT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `indexed` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UName` (`UName`),
  KEY `author_id` (`author_id`),
  KEY `indexed` (`indexed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `output`
--

DROP TABLE IF EXISTS `output`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `output` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `output_issue`
--

DROP TABLE IF EXISTS `output_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `output_issue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_issue_id` int(10) unsigned NOT NULL,
  `fk_theme_path_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned DEFAULT NULL,
  `fk_section_page_id` int(10) unsigned DEFAULT NULL,
  `fk_article_page_id` int(10) unsigned DEFAULT NULL,
  `fk_error_page_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_output_id` (`fk_output_id`,`fk_issue_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `output_section`
--

DROP TABLE IF EXISTS `output_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `output_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_section_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned DEFAULT NULL,
  `fk_section_page_id` int(10) unsigned DEFAULT NULL,
  `fk_article_page_id` int(10) unsigned DEFAULT NULL,
  `fk_error_page_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_output_id` (`fk_output_id`,`fk_section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `output_theme`
--

DROP TABLE IF EXISTS `output_theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `output_theme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_output_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL,
  `fk_theme_path_id` int(10) unsigned NOT NULL,
  `fk_front_page_id` int(10) unsigned NOT NULL,
  `fk_section_page_id` int(10) unsigned NOT NULL,
  `fk_article_page_id` int(10) unsigned NOT NULL,
  `fk_error_page_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_output_id` (`fk_output_id`,`fk_publication_id`,`fk_theme_path_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `package`
--

DROP TABLE IF EXISTS `package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rendition_id` varchar(255) DEFAULT NULL,
  `headline` varchar(255) NOT NULL,
  `description` longtext,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DE686795989D9B62` (`slug`),
  KEY `IDX_DE686795FD656AA1` (`rendition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `package_article`
--

DROP TABLE IF EXISTS `package_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_article` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `package_article_package`
--

DROP TABLE IF EXISTS `package_article_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_article_package` (
  `article_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  PRIMARY KEY (`article_id`,`package_id`),
  KEY `IDX_BB5F0F827294869C` (`article_id`),
  KEY `IDX_BB5F0F82F44CABFF` (`package_id`),
  CONSTRAINT `package_article_package_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `package_article` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_article_package_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `package_item`
--

DROP TABLE IF EXISTS `package_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `package_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `offset` int(11) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `coords` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A45640D6F44CABFF` (`package_id`),
  KEY `IDX_A45640D63DA5256D` (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlist` (
  `id_playlist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id_playlist`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playlist_article`
--

DROP TABLE IF EXISTS `playlist_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlist_article` (
  `id_playlist_article` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_playlist` int(10) unsigned NOT NULL,
  `article_no` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_playlist_article`),
  UNIQUE KEY `id_playlist` (`id_playlist`,`article_no`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `plugin_debate`
--

DROP TABLE IF EXISTS `plugin_debate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate` (
  `debate_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `parent_debate_nr` int(11) NOT NULL,
  `is_extended` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `question` varchar(255) NOT NULL,
  `date_begin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_unit` time NOT NULL,
  `nr_of_answers` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `allow_not_logged_in` tinyint(1) NOT NULL DEFAULT '0',
  `results_time_unit` enum('daily','weekly','monthly') NOT NULL DEFAULT 'daily',
  `votes_per_user` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `nr_of_votes` int(10) unsigned NOT NULL,
  `nr_of_votes_overall` int(10) unsigned NOT NULL,
  `percentage_of_votes_overall` float unsigned NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`debate_nr`,`fk_language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_answer`
--

DROP TABLE IF EXISTS `plugin_debate_answer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_answer` (
  `fk_debate_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(10) unsigned NOT NULL DEFAULT '0',
  `nr_answer` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `answer` varchar(255) NOT NULL,
  `nr_of_votes` int(10) unsigned NOT NULL DEFAULT '0',
  `percentage` float unsigned NOT NULL,
  `percentage_overall` float unsigned NOT NULL,
  `value` int(11) NOT NULL,
  `average_value` float NOT NULL,
  `on_hitlist` tinyint(4) NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `Nrdebate` (`fk_debate_nr`,`fk_language_id`,`nr_answer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_article`
--

DROP TABLE IF EXISTS `plugin_debate_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_article` (
  `fk_debate_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_article_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_article_language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`fk_debate_nr`,`fk_article_nr`,`fk_article_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_issue`
--

DROP TABLE IF EXISTS `plugin_debate_issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_issue` (
  `fk_debate_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_issue_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_issue_language_id` int(10) unsigned NOT NULL,
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_debate_nr`,`fk_issue_nr`,`fk_issue_language_id`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_publication`
--

DROP TABLE IF EXISTS `plugin_debate_publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_publication` (
  `fk_debate_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_debate_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_section`
--

DROP TABLE IF EXISTS `plugin_debate_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_section` (
  `fk_debate_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_section_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_section_language_id` int(10) unsigned NOT NULL,
  `fk_issue_nr` int(10) unsigned NOT NULL DEFAULT '0',
  `fk_publication_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_debate_nr`,`fk_section_nr`,`fk_section_language_id`,`fk_issue_nr`,`fk_publication_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debate_vote`
--

DROP TABLE IF EXISTS `plugin_debate_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debate_vote` (
  `id_vote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_debate_nr` int(10) unsigned NOT NULL,
  `fk_answer_nr` int(10) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL,
  `added` datetime NOT NULL,
  PRIMARY KEY (`id_vote`),
  UNIQUE KEY `fk_debate_nr` (`fk_debate_nr`,`fk_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugin_debateanswer_attachment`
--

DROP TABLE IF EXISTS `plugin_debateanswer_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_debateanswer_attachment` (
  `fk_debate_nr` int(11) NOT NULL,
  `fk_debateanswer_nr` int(11) NOT NULL,
  `fk_attachment_id` int(11) NOT NULL,
  PRIMARY KEY (`fk_debate_nr`,`fk_debateanswer_nr`,`fk_attachment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `reset_token` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`poll_nr`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `plugin_soundcloud`
--

DROP TABLE IF EXISTS `plugin_soundcloud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_soundcloud` (
  `article_id` int(10) unsigned NOT NULL DEFAULT '0',
  `track_id` int(10) unsigned NOT NULL DEFAULT '0',
  `track_data` blob,
  PRIMARY KEY (`article_id`,`track_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rating`
--

DROP TABLE IF EXISTS `rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rating` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_number` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating_score` int(10) NOT NULL DEFAULT '0',
  `time_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rendition`
--

DROP TABLE IF EXISTS `rendition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rendition` (
  `name` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `specs` varchar(255) NOT NULL,
  `offset` int(11) DEFAULT NULL,
  `label` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `path` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_attribute`
--

DROP TABLE IF EXISTS `user_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_attribute` (
  `user_id` int(11) unsigned NOT NULL,
  `attribute` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_identity`
--

DROP TABLE IF EXISTS `user_identity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_identity` (
  `provider` varchar(80) NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`provider`,`provider_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_points_index`
--

DROP TABLE IF EXISTS `user_points_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `points` int(10) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_subscription`
--

DROP TABLE IF EXISTS `user_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_subscription` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `subscription_type` int(1) DEFAULT NULL,
  `time_begin` datetime DEFAULT NULL,
  `time_end` datetime DEFAULT NULL,
  `subscription` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `user_id` int(11) unsigned NOT NULL,
  `action` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`action`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_topic`
--

DROP TABLE IF EXISTS `user_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_topic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `topic_language` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_topic` (`user_id`,`topic_id`,`topic_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcode`
--

DROP TABLE IF EXISTS `webcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcode` (
  `webcode` varchar(10) NOT NULL,
  `article_number` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`webcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE OAuthAccessToken (
  id INT AUTO_INCREMENT NOT NULL,
  client_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at INT DEFAULT NULL,
  scope VARCHAR(255) DEFAULT NULL,
  IdPublication INT DEFAULT NULL,
  UNIQUE INDEX UNIQ_DDE10DD55F37A13B (token),
  INDEX IDX_DDE10DD519EB6921 (client_id),
  INDEX IDX_DDE10DD55C1FD3F4 (IdPublication),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE OAuthAuthCode (
  id INT AUTO_INCREMENT NOT NULL,
  client_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  redirect_uri LONGTEXT NOT NULL,
  expires_at INT DEFAULT NULL,
  scope VARCHAR(255) DEFAULT NULL,
  IdPublication INT DEFAULT NULL,
  UNIQUE INDEX UNIQ_3DD60F725F37A13B (token),
  INDEX IDX_3DD60F7219EB6921 (client_id),
  INDEX IDX_3DD60F725C1FD3F4 (IdPublication),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE OAuthClient (
  id INT AUTO_INCREMENT NOT NULL,
  random_id VARCHAR(255) NOT NULL,
  redirect_uris LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
  secret VARCHAR(255) NOT NULL,
  allowed_grant_types LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
  name VARCHAR(255) NOT NULL,
  IdPublication INT DEFAULT NULL,
  INDEX IDX_4128BE95C1FD3F4 (IdPublication),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE OAuthPublicApiResources (
  id INT AUTO_INCREMENT NOT NULL,
  resource VARCHAR(255) NOT NULL,
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE OAuthRefreshToken (
  id INT AUTO_INCREMENT NOT NULL,
  client_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at INT DEFAULT NULL,
  scope VARCHAR(255) DEFAULT NULL,
  IdPublication INT DEFAULT NULL,
  UNIQUE INDEX UNIQ_4A42604C5F37A13B (token),
  INDEX IDX_4A42604C19EB6921 (client_id),
  INDEX IDX_4A42604C5C1FD3F4 (IdPublication),
  PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE OAuthAccessToken ADD CONSTRAINT FK_DDE10DD519EB6921 FOREIGN KEY (client_id) REFERENCES OAuthClient (id);
ALTER TABLE OAuthAuthCode ADD CONSTRAINT FK_3DD60F7219EB6921 FOREIGN KEY (client_id) REFERENCES OAuthClient (id);
ALTER TABLE OAuthRefreshToken ADD CONSTRAINT FK_4A42604C19EB6921 FOREIGN KEY (client_id) REFERENCES OAuthClient (id);

---
--- Snippets
---
DROP TABLE IF EXISTS `Snippets`;
CREATE TABLE Snippets (
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(255) NOT NULL,
  Parameters LONGTEXT NOT NULL,
  Snippet LONGTEXT NOT NULL,
  TemplateId INT DEFAULT NULL,
  UNIQUE INDEX UNIQ_1457978AF846113F (TemplateId),
  PRIMARY KEY(Id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

DROP TABLE IF EXISTS `SnippetTemplates`;
CREATE TABLE SnippetTemplates (
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(255) NOT NULL,
  Controller VARCHAR(255) NOT NULL,
  Parameters LONGTEXT NOT NULL,
  Template LONGTEXT NOT NULL,
  Favourite TINYINT(1) NOT NULL,
  IconInactive LONGTEXT NOT NULL,
  IconActive LONGTEXT NOT NULL,
  PRIMARY KEY(Id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE Snippets ADD CONSTRAINT SnippetTemplate FOREIGN KEY (TemplateId) REFERENCES SnippetTemplates (Id);

DROP TABLE IF EXISTS `ArticleSnippets`;
CREATE TABLE ArticleSnippets (
  ArticleId INT NOT NULL,
  SnippetId INT NOT NULL,
  INDEX IDX_5080CDE7C53224D (ArticleId),
  INDEX IDX_5080CDEB00DA91C (SnippetId),
  PRIMARY KEY(ArticleId, SnippetId)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
