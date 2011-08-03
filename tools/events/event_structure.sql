
--
-- Dumping data for table `Articles`
--

LOCK TABLES `Articles` WRITE;
/*!40000 ALTER TABLE `Articles` DISABLE KEYS */;
INSERT INTO `Articles` (`IdPublication`, `NrIssue`, `NrSection`, `Number`, `IdLanguage`, `Name`, `Type`, `IdUser`, `fk_default_author_id`, `OnFrontPage`, `OnSection`, `Published`, `PublishDate`, `UploadDate`, `Keywords`, `Public`, `IsIndexed`, `LockUser`, `LockTime`, `ShortName`, `ArticleOrder`, `comments_enabled`, `comments_locked`, `time_updated`, `object_id`) VALUES (2,13,30,101,1,'Test Event','event_type',1,NULL,'N','N','N','0000-00-00 00:00:00','2011-08-11 06:30:29','','Y','Y',0,'0000-00-00 00:00:00','101',1,1,0,'2011-08-03 14:16:52',0);
/*!40000 ALTER TABLE `Articles` ENABLE KEYS */;
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
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('news','NULL',NULL,0,1,NULL,NULL,NULL,0,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('news','deck',2,0,0,NULL,'body',NULL,0,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('news','full_text',3,0,0,NULL,'body',NULL,1,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('news','highlight',1,0,0,NULL,'switch',NULL,0,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('page','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('page','full_text',1,0,0,NULL,'body',NULL,1,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('link','NULL',NULL,0,0,NULL,NULL,NULL,0,NULL);
-- INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('link','url',1,0,0,NULL,'text',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','NULL',NULL,0,0,10,NULL,NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_provider',1,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_name',2,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_description',3,0,0,NULL,'body','editor_size=250',1,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_date',4,0,0,NULL,'date',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_time',6,0,0,NULL,'topic',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_type',7,0,0,NULL,'topic',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_visible',9,0,0,NULL,'switch',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_price',10,0,0,NULL,'numeric','precision=2',0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','location_town',14,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','event_country',12,0,0,NULL,'topic',NULL,0,NULL);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','location_street',15,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','location_zip_code',13,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','web_link',16,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','email_address',17,0,0,NULL,'text',NULL,0,0);
INSERT INTO `ArticleTypeMetadata` (`type_name`, `field_name`, `field_weight`, `is_hidden`, `comments_enabled`, `fk_phrase_id`, `field_type`, `field_type_param`, `is_content_field`, `max_size`) VALUES ('event_type','phone_number',18,0,0,NULL,'text',NULL,0,0);
/*!40000 ALTER TABLE `ArticleTypeMetadata` ENABLE KEYS */;
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
-- INSERT INTO `TopicFields` (`ArticleType`, `FieldName`, `RootTopicId`) VALUES ('test_type','event_time',16);
INSERT INTO `TopicFields` (`ArticleType`, `FieldName`, `RootTopicId`) VALUES ('event_type','event_type',8);
INSERT INTO `TopicFields` (`ArticleType`, `FieldName`, `RootTopicId`) VALUES ('event_type','event_time',98);
INSERT INTO `TopicFields` (`ArticleType`, `FieldName`, `RootTopicId`) VALUES ('event_type','event_country',105);
/*!40000 ALTER TABLE `TopicFields` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Dumping data for table `Topics`
--

LOCK TABLES `Topics` WRITE;
/*!40000 ALTER TABLE `Topics` DISABLE KEYS */;
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (98,11,24);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (99,12,19);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (100,20,21);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (101,22,23);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (102,13,14);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (103,15,16);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (104,17,18);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (105,1,10);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (106,2,3);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (107,4,5);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (108,6,7);
INSERT INTO `Topics` (`id`, `node_left`, `node_right`) VALUES (109,8,9);
/*!40000 ALTER TABLE `Topics` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Dumping data for table `TopicNames`
--

LOCK TABLES `TopicNames` WRITE;
/*!40000 ALTER TABLE `TopicNames` DISABLE KEYS */;
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (98,1,'Time');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (99,1,'1AM');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (100,1,'2AM');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (101,1,'3AM');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (102,1,'1:00');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (103,1,'1:15');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (104,1,'1:30');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (105,1,'Countries');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (106,1,'CH');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (107,1,'DE');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (108,1,'FR');
INSERT INTO `TopicNames` (`fk_topic_id`, `fk_language_id`, `name`) VALUES (109,1,'IT');
/*!40000 ALTER TABLE `TopicNames` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Dumping data for table `Translations`
--

LOCK TABLES `Translations` WRITE;
/*!40000 ALTER TABLE `Translations` DISABLE KEYS */;
-- INSERT INTO `Translations` (`id`, `phrase_id`, `fk_language_id`, `translation_text`) VALUES (1,1,1,'article');
INSERT INTO `Translations` (`id`, `phrase_id`, `fk_language_id`, `translation_text`) VALUES (13,10,1,'event type');
/*!40000 ALTER TABLE `Translations` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `Xevent_type`
--

DROP TABLE IF EXISTS `Xevent_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Xevent_type` (
  `NrArticle` int(10) unsigned NOT NULL,
  `IdLanguage` int(10) unsigned NOT NULL,
  `Fevent_provider` varchar(255) NOT NULL,
  `Fevent_name` varchar(255) NOT NULL,
  `Fevent_description` mediumblob NOT NULL,
  `Fevent_date` date NOT NULL,
  `Fevent_type` int(10) unsigned NOT NULL,
  `Fevent_visible` tinyint(1) NOT NULL,
  `Fevent_price` decimal(65,2) NOT NULL,
  `Fevent_time` int(10) unsigned NOT NULL,
  `Flocation_town` varchar(255) NOT NULL,
  `Flocation_street` varchar(255) NOT NULL,
  `Flocation_zip_code` varchar(255) NOT NULL,
  `Fevent_country` int(10) unsigned NOT NULL,
  `Fweb_link` varchar(255) NOT NULL,
  `Femail_address` varchar(255) NOT NULL,
  `Fphone_number` varchar(255) NOT NULL,
  PRIMARY KEY (`NrArticle`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Xevent_type`
--

LOCK TABLES `Xevent_type` WRITE;
/*!40000 ALTER TABLE `Xevent_type` DISABLE KEYS */;
INSERT INTO `Xevent_type` (`NrArticle`, `IdLanguage`, `Fevent_provider`, `Fevent_name`, `Fevent_description`, `Fevent_date`, `Fevent_type`, `Fevent_visible`, `Fevent_price`, `Fevent_time`, `Flocation_town`, `Flocation_street`, `Flocation_zip_code`, `Fevent_country`, `Fweb_link`, `Femail_address`, `Fphone_number`) VALUES (101,1,'Anatomisches Museum der UniversitÃ¤t Basel','Die verschiedenen Gesichter des Gesichts','<p>Das Gesicht ist ein Abbild der Seele (Cicero)ï»¿</p>\r\n<p>Die Sonderausstellung zeigt, wie ...ï»¿</p>\r\n<p>Â </p>\r\n<p>Any other info, like plain text written time schedule, way directions, etc. to put probably here.</p>\r\n<p>Â </p>\r\n<p>What about IDs of events, actions, towns, locations? They could help to identify repeated events, and to take coordinates of events of the same locations.</p>\r\n<p>And event coordinates are just as map\'s POI coordinates?</p>\r\n<p>And if an event is for a year, will we display it 365 times, even in search results?</p>','2011-08-13',37,1,'5.00',104,'Basel','Postfach 911','123 456',106,'http://www.anatomie.unibas.ch/museum','Museum-Anatomie@unibas.ch','061 267 3535');
/*!40000 ALTER TABLE `Xevent_type` ENABLE KEYS */;
UNLOCK TABLES;



