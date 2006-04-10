-- MySQL dump 9.10
--
-- Host: localhost    Database: campsite
-- ------------------------------------------------------
-- Server version	4.0.18

--
-- Table structure for table `Aliases`
--

DROP TABLE IF EXISTS `Aliases`;
CREATE TABLE `Aliases` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(128) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Aliases`
--


--
-- Table structure for table `ArticleAttachments`
--

DROP TABLE IF EXISTS `ArticleAttachments`;
CREATE TABLE `ArticleAttachments` (
  `fk_article_number` int(10) unsigned NOT NULL default '0',
  `fk_attachment_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `article_attachment_index` (`fk_article_number`,`fk_attachment_id`),
  KEY `fk_article_number` (`fk_article_number`),
  KEY `fk_attachment_id` (`fk_attachment_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `ArticleAttachments`
--


--
-- Table structure for table `ArticleImages`
--

DROP TABLE IF EXISTS `ArticleImages`;
CREATE TABLE `ArticleImages` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdImage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`IdImage`),
  UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`),
  KEY `IdImage` (`IdImage`)
) TYPE=MyISAM;

--
-- Dumping data for table `ArticleImages`
--


--
-- Table structure for table `ArticleIndex`
--

DROP TABLE IF EXISTS `ArticleIndex`;
CREATE TABLE `ArticleIndex` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `IdKeyword` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `NrArticle` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPublication`,`IdLanguage`,`IdKeyword`,`NrIssue`,`NrSection`,`NrArticle`)
) TYPE=MyISAM;

--
-- Dumping data for table `ArticleIndex`
--


--
-- Table structure for table `ArticlePublish`
--

DROP TABLE IF EXISTS `ArticlePublish`;
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
) TYPE=MyISAM;

--
-- Dumping data for table `ArticlePublish`
--


--
-- Table structure for table `ArticleTopics`
--

DROP TABLE IF EXISTS `ArticleTopics`;
CREATE TABLE `ArticleTopics` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `TopicId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`TopicId`)
) TYPE=MyISAM;

--
-- Dumping data for table `ArticleTopics`
--


--
-- Table structure for table `Articles`
--

DROP TABLE IF EXISTS `Articles`;
CREATE TABLE `Articles` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `NrSection` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Type` varchar(70) NOT NULL default '',
  `IdUser` int(10) unsigned NOT NULL default '0',
  `OnFrontPage` enum('N','Y') NOT NULL default 'N',
  `OnSection` enum('N','Y') NOT NULL default 'N',
  `Published` enum('N','S','Y') NOT NULL default 'N',
  `PublishDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `UploadDate` date NOT NULL default '0000-00-00',
  `Keywords` varchar(255) NOT NULL default '',
  `Public` enum('N','Y') NOT NULL default 'N',
  `IsIndexed` enum('N','Y') NOT NULL default 'N',
  `LockUser` int(10) unsigned NOT NULL default '0',
  `LockTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ShortName` varchar(32) NOT NULL default '',
  `ArticleOrder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`NrSection`,`Number`,`IdLanguage`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Name`),
  UNIQUE KEY `Number` (`Number`,`IdLanguage`),
  UNIQUE KEY `other_key` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`Number`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`NrSection`,`IdLanguage`,`ShortName`),
  KEY `Type` (`Type`),
  KEY `ArticleOrderIdx` (`ArticleOrder`)
) TYPE=MyISAM;

--
-- Dumping data for table `Articles`
--


--
-- Table structure for table `Attachments`
--

DROP TABLE IF EXISTS `Attachments`;
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
  `last_modified` timestamp(14) NOT NULL,
  `time_created` timestamp(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Attachments`
--


--
-- Table structure for table `AutoId`
--

DROP TABLE IF EXISTS `AutoId`;
CREATE TABLE `AutoId` (
  `ArticleId` int(10) unsigned NOT NULL default '0',
  `LogTStamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `TopicId` int(10) unsigned NOT NULL default '0',
  `translation_phrase_id` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

--
-- Dumping data for table `AutoId`
--

INSERT INTO `AutoId` (`ArticleId`, `LogTStamp`, `TopicId`, `translation_phrase_id`) VALUES (0,'0000-00-00 00:00:00',0,0);

--
-- Table structure for table `Classes`
--

DROP TABLE IF EXISTS `Classes`;
CREATE TABLE `Classes` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Classes`
--


--
-- Table structure for table `Countries`
--

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
  `Code` char(2) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`Code`,`IdLanguage`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`,`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Countries`
--

INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AR',1,'Argentina'),('AG',1,'Antigua and Barbuda'),('AQ',1,'Antarctica'),('AI',1,'Anguilla'),('AO',1,'Angola'),('AD',1,'Andorra'),('AS',1,'American Samoa'),('DZ',1,'Algeria'),('AL',1,'Albania'),('AF',1,'Afghanistan'),('AM',1,'Armenia'),('AW',1,'Aruba'),('AU',1,'Australia'),('AT',1,'Austria'),('AZ',1,'Azerbaijan'),('BS',1,'Bahamas'),('BH',1,'Bahrain'),('BD',1,'Bangladesh'),('BB',1,'Barbados'),('BY',1,'Belarus'),('BE',1,'Belgium'),('BZ',1,'Belize'),('BJ',1,'Benin'),('BM',1,'Bermuda'),('BT',1,'Bhutan'),('BO',1,'Bolivia'),('BA',1,'Bosnia and Herzegovina'),('BW',1,'Botswana'),('BV',1,'Bouvet Island'),('BR',1,'Brazil'),('IO',1,'British Indian Ocean Territory'),('BN',1,'Brunei Darussalam'),('BG',1,'Bulgaria'),('BF',1,'Burkina Faso'),('BI',1,'Burundi'),('KH',1,'Cambodia'),('CM',1,'Cameroon'),('CA',1,'Canada'),('CV',1,'Cape Verde'),('KY',1,'Cayman Islands'),('CF',1,'Central African Republic'),('TD',1,'Chad'),('CL',1,'Chile'),('CN',1,'China'),('CX',1,'Christmas Island'),('CC',1,'Cocos (Keeling) Islands'),('CO',1,'Colombia'),('KM',1,'Comoros'),('CG',1,'Congo'),('CD',1,'Congo, The Democratic Republic Of The'),('CK',1,'Cook Islands'),('CR',1,'Costa Rica'),('CI',1,'Côte d\'Ivoire'),('HR',1,'Croatia'),('CU',1,'Cuba'),('CY',1,'Cyprus'),('CZ',1,'Czech Republic'),('DK',1,'Denmark'),('DJ',1,'Djibouti'),('DM',1,'Dominica'),('DO',1,'Dominican Republic'),('TP',1,'Timor-Leste'),('EC',1,'Ecuador'),('EG',1,'Egypt'),('SV',1,'El Salvador'),('GQ',1,'Equatorial Guinea'),('ER',1,'Eritrea'),('EE',1,'Estonia'),('ET',1,'Ethiopia'),('FK',1,'Falkland Islands (Malvinas)'),('FO',1,'Faroe Islands'),('FJ',1,'Fiji'),('FI',1,'Finland'),('FR',1,'France'),('FX',1,'France, Metropolitan'),('GF',1,'French Guiana'),('PF',1,'French Polynesia'),('TF',1,'French Southern Territories'),('GA',1,'Gabon'),('GM',1,'Gambia'),('GE',1,'Georgia'),('DE',1,'Germany'),('GH',1,'Ghana'),('GI',1,'Gibraltar'),('GR',1,'Greece'),('GL',1,'Greenland'),('GD',1,'Grenada'),('GP',1,'Guadeloupe'),('GU',1,'Guam'),('GT',1,'Guatemala'),('GN',1,'Guinea'),('GW',1,'Guinea-bissau'),('GY',1,'Guyana'),('HT',1,'Haiti'),('HM',1,'Heard Island and Mcdonald Islands'),('VA',1,'Holy See (Vatican City State)'),('HN',1,'Honduras'),('HK',1,'Hong Kong'),('HU',1,'Hungary'),('IS',1,'Iceland'),('IN',1,'India'),('ID',1,'Indonesia'),('IR',1,'Iran, Islamic Republic of'),('IQ',1,'Iraq'),('IE',1,'Ireland'),('IL',1,'Israel'),('IT',1,'Italy'),('JM',1,'Jamaica'),('JP',1,'Japan'),('JO',1,'Jordan'),('KZ',1,'Kazakstan'),('KE',1,'Kenya'),('KI',1,'Kiribati'),('KP',1,'Korea, Democratic Peoples Republic of'),('KR',1,'Korea, Republic of'),('KW',1,'Kuwait'),('KG',1,'Kyrgyzstan'),('LA',1,'Lao People\'s Democratic Republic'),('LV',1,'Latvia'),('LB',1,'Lebanon'),('LS',1,'Lesotho'),('LR',1,'Liberia'),('LY',1,'Libyan Arab Jamahiriya'),('LI',1,'Liechtenstein'),('LT',1,'Lithuania'),('LU',1,'Luxembourg'),('MO',1,'Macau'),('MK',1,'Macedonia, The Former Yugoslav Republic of'),('MG',1,'Madagascar'),('MW',1,'Malawi'),('MY',1,'Malaysia'),('MV',1,'Maldives'),('ML',1,'Mali'),('MT',1,'Malta'),('MH',1,'Marshall Islands'),('MQ',1,'Martinique'),('MR',1,'Mauritania'),('MU',1,'Mauritius'),('YT',1,'Mayotte'),('MX',1,'Mexico'),('FM',1,'Micronesia, Federated States of'),('MD',1,'Moldova, Republic of'),('MC',1,'Monaco'),('MN',1,'Mongolia'),('MS',1,'Montserrat'),('MA',1,'Morocco'),('MZ',1,'Mozambique'),('MM',1,'Myanmar'),('NA',1,'Namibia'),('NR',1,'Nauru'),('NP',1,'Nepal'),('NL',1,'Netherlands'),('AN',1,'Netherlands Antilles'),('NC',1,'New Caledonia'),('NZ',1,'New Zealand'),('NI',1,'Nicaragua'),('NE',1,'Niger'),('NG',1,'Nigeria'),('NU',1,'Niue'),('NF',1,'Norfolk Island'),('MP',1,'Northern Mariana Islands'),('NO',1,'Norway'),('OM',1,'Oman'),('PK',1,'Pakistan'),('PW',1,'Palau'),('PS',1,'Palestinian Territory, Occupied'),('PA',1,'Panama'),('PG',1,'Papua New Guinea'),('PY',1,'Paraguay'),('PE',1,'Peru'),('PH',1,'Philippines'),('PN',1,'Pitcairn'),('PL',1,'Poland'),('PT',1,'Portugal'),('PR',1,'Puerto Rico'),('QA',1,'Qatar'),('RE',1,'Réunion'),('RO',1,'Romania'),('RU',1,'Russian Federation'),('RW',1,'Rwanda'),('SH',1,'Saint Helena'),('KN',1,'Saint Kitts and Nevis'),('LC',1,'Saint Lucia'),('PM',1,'Saint Pierre and Miquelon'),('VC',1,'Saint Vincent and The Grenadines'),('WS',1,'Samoa'),('SM',1,'San Marino'),('ST',1,'Sao Tome and Principe'),('SA',1,'Saudi Arabia'),('SN',1,'Senegal'),('CS',1,'Serbia and Montenegro'),('SC',1,'Seychelles'),('SL',1,'Sierra Leone'),('SG',1,'Singapore'),('SK',1,'Slovakia'),('SI',1,'Slovenia'),('SB',1,'Solomon Islands'),('SO',1,'Somalia'),('ZA',1,'South Africa'),('GS',1,'South Georgia and The South Sandwich Islands'),('ES',1,'Spain'),('LK',1,'Sri Lanka'),('SD',1,'Sudan'),('SR',1,'Suriname'),('SJ',1,'Svalbard and Jan Mayen'),('SZ',1,'Swaziland'),('SE',1,'Sweden'),('CH',1,'Switzerland'),('SY',1,'Syrian Arab Republic'),('TW',1,'Taiwan, Province Of China'),('TJ',1,'Tajikistan'),('TZ',1,'Tanzania, United Republic of'),('TH',1,'Thailand'),('TG',1,'Togo'),('TK',1,'Tokelau'),('TO',1,'Tonga'),('TT',1,'Trinidad and Tobago'),('TN',1,'Tunisia'),('TR',1,'Turkey'),('TM',1,'Turkmenistan'),('TC',1,'Turks and Caicos Islands'),('TV',1,'Tuvalu'),('UG',1,'Uganda'),('UA',1,'Ukraine'),('AE',1,'United Arab Emirates'),('GB',1,'United Kingdom'),('US',1,'United States'),('UM',1,'United States Minor Outlying Islands'),('UY',1,'Uruguay'),('UZ',1,'Uzbekistan'),('VU',1,'Vanuatu'),('VE',1,'Venezuela'),('VN',1,'Vietnam'),('VG',1,'Virgin Islands, British'),('VI',1,'Virgin Islands, U.S.'),('WF',1,'Wallis And Futuna'),('EH',1,'Western Sahara'),('YE',1,'Yemen'),('ZM',1,'Zambia'),('ZW',1,'Zimbabwe'),('AX',1,'Åland Islands');

--
-- Table structure for table `Dictionary`
--

DROP TABLE IF EXISTS `Dictionary`;
CREATE TABLE `Dictionary` (
  `Id` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Keyword` varchar(140) NOT NULL default '',
  PRIMARY KEY  (`IdLanguage`,`Keyword`),
  UNIQUE KEY `Id` (`Id`,`IdLanguage`)
) TYPE=MyISAM;

--
-- Dumping data for table `Dictionary`
--


--
-- Table structure for table `Errors`
--

DROP TABLE IF EXISTS `Errors`;
CREATE TABLE `Errors` (
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Message` char(255) NOT NULL default '',
  PRIMARY KEY  (`Number`,`IdLanguage`)
) TYPE=MyISAM;

--
-- Dumping data for table `Errors`
--

INSERT INTO `Errors` (`Number`, `IdLanguage`, `Message`) VALUES (4000,1,'Internal error.'),(4001,1,'Username not specified.'),(4002,1,'Invalid username.'),(4003,1,'Password not specified.'),(4004,1,'Invalid password.'),(2000,1,'Internal error'),(2001,1,'Username is not specified. Please fill out login name field.'),(2002,1,'You are not a reader.'),(2003,1,'Publication not specified.'),(2004,1,'There are other subscriptions not payed.'),(2005,1,'Time unit not specified.'),(3000,1,'Internal error.'),(3001,1,'Username already exists.'),(3002,1,'Name is not specified. Please fill out name field.'),(3003,1,'Username is not specified. Please fill out login name field.'),(3004,1,'Password is not specified. Please fill out password field.'),(3005,1,'EMail is not specified. Please fill out EMail field.'),(3006,1,'EMail address already exists. Please try to login with your old account.'),(3007,1,'Invalid user identifier'),(3008,1,'No country specified. Please select a country.'),(3009,1,'Password (again) is not specified. Please fill out password (again) field.'),(3010,1,'Passwords do not match. Please fill out the same password to both password fields.'),(3011,1,'Password is too simple. Please choose a better password (at least 6 characters).');

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
  `Id` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `Notify` enum('N','Y') NOT NULL default 'N',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`IdLanguage`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Events`
--

INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (1,'Add Publication','N',1),(2,'Delete Publication','N',1),(11,'Add Issue','N',1),(12,'Delete Issue','N',1),(13,'Change Issue Template','N',1),(14,'Change issue status','N',1),(15,'Add Issue Translation','N',1),(21,'Add Section','N',1),(22,'Delete section','N',1),(31,'Add Article','Y',1),(32,'Delete article','N',1),(33,'Change article field','N',1),(34,'Change article properties','N',1),(35,'Change article status','Y',1),(41,'Add Image','Y',1),(42,'Delete image','N',1),(43,'Change image properties','N',1),(51,'Add User','N',1),(52,'Delete User','N',1),(53,'Changes Own Password','N',1),(54,'Change User Password','N',1),(55,'Change User Permissions','N',1),(56,'Change user information','N',1),(61,'Add article type','N',1),(62,'Delete article type','N',1),(71,'Add article type field','N',1),(72,'Delete article type field','N',1),(81,'Add dictionary class','N',1),(82,'Delete dictionary class','N',1),(91,'Add dictionary keyword','N',1),(92,'Delete dictionary keyword','N',1),(101,'Add language','N',1),(102,'Delete language','N',1),(103,'Modify language','N',1),(112,'Delete templates','N',1),(111,'Add templates','N',1),(121,'Add user type','N',1),(122,'Delete user type','N',1),(123,'Change user type','N',1),(3,'Change publication information','N',1),(36,'Change article template','N',1),(57,'Add IP Group','N',1),(58,'Delete IP Group','N',1),(131,'Add country','N',1),(132,'Add country translation','N',1),(133,'Change country name','N',1),(134,'Delete country','N',1),(4,'Add default subscription time','N',1),(5,'Delete default subscription time','N',1),(6,'Change default subscription time','N',1),(113,'Edit template','N',1),(114,'Create template','N',1),(115,'Duplicate template','N',1),(141,'Add topic','N',1),(142,'Delete topic','N',1),(143,'Update topic','N',1),(144,'Add topic to article','N',1),(145,'Delete topic from article','N',1),(151,'Add alias','N',1),(152,'Delete alias','N',1),(153,'Update alias','N',1),(154,'Duplicate section','N',1),(155,'Duplicate article','N',1);

--
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;
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
  `LastModified` timestamp(14) NOT NULL,
  `TimeCreated` timestamp(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`Id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Images`
--


--
-- Table structure for table `IssuePublish`
--

DROP TABLE IF EXISTS `IssuePublish`;
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
) TYPE=MyISAM;

--
-- Dumping data for table `IssuePublish`
--


--
-- Table structure for table `Issues`
--

DROP TABLE IF EXISTS `Issues`;
CREATE TABLE `Issues` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(140) NOT NULL default '',
  `PublicationDate` date NOT NULL default '0000-00-00',
  `Published` enum('N','Y') NOT NULL default 'N',
  `IssueTplId` int(10) unsigned default NULL,
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  `ShortName` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`IdPublication`,`Number`,`IdLanguage`),
  UNIQUE KEY `ShortName` (`IdPublication`,`IdLanguage`,`ShortName`)
) TYPE=MyISAM;

--
-- Dumping data for table `Issues`
--


--
-- Table structure for table `KeywordClasses`
--

DROP TABLE IF EXISTS `KeywordClasses`;
CREATE TABLE `KeywordClasses` (
  `IdDictionary` int(10) unsigned NOT NULL default '0',
  `IdClasses` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Definition` mediumblob NOT NULL,
  PRIMARY KEY  (`IdDictionary`,`IdClasses`,`IdLanguage`),
  KEY `IdClasses` (`IdClasses`)
) TYPE=MyISAM;

--
-- Dumping data for table `KeywordClasses`
--


--
-- Table structure for table `KeywordIndex`
--

DROP TABLE IF EXISTS `KeywordIndex`;
CREATE TABLE `KeywordIndex` (
  `Keyword` varchar(70) NOT NULL default '',
  `Id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Keyword`)
) TYPE=MyISAM;

--
-- Dumping data for table `KeywordIndex`
--


--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
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
) TYPE=MyISAM;

--
-- Dumping data for table `Languages`
--

INSERT INTO `Languages` VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
INSERT INTO `Languages` VALUES (5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO `Languages` VALUES (9,'Portuguese','ISO_8859-1','Português','pt','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado');
INSERT INTO `Languages` VALUES (12,'French','ISO_8859-1','Français','fr','Janvier','Février','Mars','Avril','Peut','Juin','Juli','Août','Septembre','Octobre','Novembre','Décembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
INSERT INTO `Languages` VALUES (13,'Spanish','ISO_8859-1','Español','es','Enero','Febrero','Marcha','Abril','Puede','Junio','Juli','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
INSERT INTO `Languages` VALUES (2,'Romanian','ISO_8859-2','Română','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă');
INSERT INTO `Languages` VALUES (7,'Croatian','ISO_8859-2','Hrvatski','hr','Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota');
INSERT INTO `Languages` VALUES (8,'Czech','ISO_8859-2','Český','cz','Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec','Neděle','Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota');
INSERT INTO `Languages` VALUES (11,'Serbo-Croatian','ISO_8859-2','Srpskohrvatski','sh','januar','februar','mart','april','maj','jun','jul','avgust','septembar','oktobar','novembar','decembar','nedelja','ponedeljak','utorak','sreda','četvrtak','petak','subota');
INSERT INTO `Languages` VALUES (10,'Serbian (Cyrillic)','ISO_8859-5','Српски (Ћирилица)','sr','јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','недеља','понедељак','уторак','среда','четвртак','петак','субота');
INSERT INTO `Languages` VALUES (15,'Russian','ISO_8859-5','Русский','ru','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','воскресенье','понедельник','вторник','среда','четверг','пятница','суббота');
INSERT INTO `Languages` VALUES (18,'Swedish','','Svenska','sv','januari','februari','mars','april','maj','juni','juli','augusti','september','oktober','november','december','söndag','måndag','tisdag','onsdag','torsdag','fredag','lördag');
INSERT INTO `Languages` VALUES (16,'Chinese','UTF-8','中文','zh','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','星期','星期','星期','星期','星期','星期','星期');
INSERT INTO `Languages` VALUES (17,'Arabic','UTF-8','عربي','ar','كانون الثاني','شباط','آذار','نيسان','آيار','حزيران','تموز','آب','أيلول','تشرين أول','تشرين الثاني','كانون أول','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت');

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
CREATE TABLE `Log` (
  `time_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `fk_event_id` int(10) unsigned NOT NULL default '0',
  `fk_user_id` int(10) unsigned default NULL,
  `text` varchar(255) NOT NULL default '',
  KEY `IdEvent` (`fk_event_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Log`
--


--
-- Table structure for table `Publications`
--

DROP TABLE IF EXISTS `Publications`;
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
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Alias` (`IdDefaultAlias`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Publications`
--


--
-- Table structure for table `Sections`
--

DROP TABLE IF EXISTS `Sections`;
CREATE TABLE `Sections` (
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ShortName` varchar(32) NOT NULL default '',
  `SectionTplId` int(10) unsigned default NULL,
  `ArticleTplId` int(10) unsigned default NULL,
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`IdLanguage`,`Number`),
  UNIQUE KEY `IdPublication` (`IdPublication`,`NrIssue`,`IdLanguage`,`Name`),
  UNIQUE KEY `ShortName` (`IdPublication`,`NrIssue`,`IdLanguage`,`ShortName`)
) TYPE=MyISAM;

--
-- Dumping data for table `Sections`
--


--
-- Table structure for table `SubsByIP`
--

DROP TABLE IF EXISTS `SubsByIP`;
CREATE TABLE `SubsByIP` (
  `IdUser` int(10) unsigned NOT NULL default '0',
  `StartIP` int(10) unsigned NOT NULL default '0',
  `Addresses` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdUser`,`StartIP`)
) TYPE=MyISAM;

--
-- Dumping data for table `SubsByIP`
--


--
-- Table structure for table `SubsDefTime`
--

DROP TABLE IF EXISTS `SubsDefTime`;
CREATE TABLE `SubsDefTime` (
  `CountryCode` char(21) NOT NULL default '',
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `TrialTime` int(10) unsigned NOT NULL default '0',
  `PaidTime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`CountryCode`,`IdPublication`)
) TYPE=MyISAM;

--
-- Dumping data for table `SubsDefTime`
--


--
-- Table structure for table `SubsSections`
--

DROP TABLE IF EXISTS `SubsSections`;
CREATE TABLE `SubsSections` (
  `IdSubscription` int(10) unsigned NOT NULL default '0',
  `SectionNumber` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) NOT NULL default '0',
  `StartDate` date NOT NULL default '0000-00-00',
  `Days` int(10) unsigned NOT NULL default '0',
  `PaidDays` int(10) unsigned NOT NULL default '0',
  `NoticeSent` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdSubscription`,`SectionNumber`,`IdLanguage`)
) TYPE=MyISAM;

--
-- Dumping data for table `SubsSections`
--


--
-- Table structure for table `Subscriptions`
--

DROP TABLE IF EXISTS `Subscriptions`;
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
) TYPE=MyISAM;

--
-- Dumping data for table `Subscriptions`
--


--
-- Table structure for table `TemplateTypes`
--

DROP TABLE IF EXISTS `TemplateTypes`;
CREATE TABLE `TemplateTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(20) NOT NULL default '',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `TemplateTypes`
--

INSERT INTO `TemplateTypes` (`Id`, `Name`) VALUES (1,'default'),(2,'issue'),(3,'section'),(4,'article');

--
-- Table structure for table `Templates`
--

DROP TABLE IF EXISTS `Templates`;
CREATE TABLE `Templates` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` char(255) NOT NULL default '',
  `Type` int(10) unsigned NOT NULL default '1',
  `Level` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Templates`
--


--
-- Table structure for table `TimeUnits`
--

DROP TABLE IF EXISTS `TimeUnits`;
CREATE TABLE `TimeUnits` (
  `Unit` char(1) NOT NULL default '',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `Name` varchar(70) NOT NULL default '',
  PRIMARY KEY  (`Unit`,`IdLanguage`)
) TYPE=MyISAM;

--
-- Dumping data for table `TimeUnits`
--

INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('D',1,'days'),('W',1,'weeks'),('M',1,'months'),('Y',1,'years'),('D',18,'dagar'),('W',18,'veckor'),('M',18,'månader'),('Y',18,'år');

--
-- Table structure for table `TopicFields`
--

DROP TABLE IF EXISTS `TopicFields`;
CREATE TABLE `TopicFields` (
  `ArticleType` varchar(250) NOT NULL default '',
  `FieldName` varchar(250) NOT NULL default '',
  `RootTopicId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ArticleType`,`FieldName`)
) TYPE=MyISAM;

--
-- Dumping data for table `TopicFields`
--


--
-- Table structure for table `Topics`
--

DROP TABLE IF EXISTS `Topics`;
CREATE TABLE `Topics` (
  `Id` int(10) unsigned NOT NULL default '0',
  `LanguageId` int(10) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `ParentId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`,`LanguageId`),
  UNIQUE KEY `Name` (`LanguageId`,`Name`),
  KEY `topic_id` (`Id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Topics`
--


--
-- Table structure for table `Translations`
--

DROP TABLE IF EXISTS `Translations`;
CREATE TABLE `Translations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `phrase_id` int(10) unsigned NOT NULL default '0',
  `fk_language_id` int(10) unsigned NOT NULL default '0',
  `translation_text` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `phrase_language_index` (`phrase_id`,`fk_language_id`),
  KEY `phrase_id` (`phrase_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `Translations`
--


--
-- Table structure for table `URLTypes`
--

DROP TABLE IF EXISTS `URLTypes`;
CREATE TABLE `URLTypes` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(15) NOT NULL default '',
  `Description` mediumblob NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `URLTypes`
--

INSERT INTO `URLTypes` (`Id`, `Name`, `Description`) VALUES (1,'template path',''),(2,'short names','');

--
-- Table structure for table `UserConfig`
--

DROP TABLE IF EXISTS `UserConfig`;
CREATE TABLE `UserConfig` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_user_id` int(10) unsigned NOT NULL default '0',
  `varname` varchar(100) NOT NULL default '',
  `value` varchar(100) default NULL,
  `last_modified` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_var_name_index` (`fk_user_id`,`varname`),
  KEY `fk_user_id` (`fk_user_id`)
) TYPE=MyISAM;

--
-- Dumping data for table `UserConfig`
--

INSERT INTO `UserConfig` (`id`, `fk_user_id`, `varname`, `value`, `last_modified`) VALUES (1,1,'ManagePub','Y',20051213155700),(2,1,'DeletePub','Y',20051213155700),(3,1,'ManageIssue','Y',20051213155700),(4,1,'DeleteIssue','Y',20051213155700),(5,1,'ManageSection','Y',20051213155700),(6,1,'DeleteSection','Y',20051213155700),(7,1,'AddArticle','Y',20051213155700),(8,1,'ChangeArticle','Y',20051213155700),(9,1,'DeleteArticle','Y',20051213155700),(10,1,'AddImage','Y',20051213155700),(11,1,'AddFile','Y',20051213155700),(12,1,'ChangeImage','Y',20051213155700),(13,1,'ChangeFile','Y',20051213155700),(14,1,'DeleteImage','Y',20051213155700),(15,1,'DeleteFile','Y',20051213155700),(16,1,'ManageTempl','Y',20051213155700),(17,1,'DeleteTempl','Y',20051213155700),(18,1,'ManageUsers','Y',20051213155700),(19,1,'ManageSubscriptions','Y',20051213155700),(20,1,'DeleteUsers','Y',20051213155700),(21,1,'ManageUserTypes','Y',20051213155700),(22,1,'ManageArticleTypes','Y',20051213155700),(23,1,'DeleteArticleTypes','Y',20051213155700),(24,1,'ManageLanguages','Y',20051213155700),(25,1,'DeleteLanguages','Y',20051213155700),(26,1,'ManageCountries','Y',20051213155700),(27,1,'DeleteCountries','Y',20051213155700),(28,1,'MailNotify','N',20051213155700),(29,1,'ViewLogs','Y',20051213155700),(30,1,'ManageLocalizer','Y',20051213155700),(31,1,'ManageIndexer','N',20060306150855),(32,1,'Publish','Y',20051213155700),(33,1,'ManageTopics','Y',20051213155700),(34,1,'EditorImage','Y',20051213155700),(35,1,'EditorTextAlignment','Y',20051213155700),(36,1,'EditorFontColor','Y',20051213155700),(37,1,'EditorFontSize','Y',20051213155700),(38,1,'EditorFontFace','Y',20051213155700),(39,1,'EditorTable','Y',20051213155700),(40,1,'EditorSuperscript','Y',20051213155700),(41,1,'EditorSubscript','Y',20051213155700),(42,1,'EditorStrikethrough','Y',20051213155700),(43,1,'EditorIndent','Y',20051213155700),(44,1,'EditorListBullet','Y',20051213155700),(45,1,'EditorListNumber','Y',20051213155700),(46,1,'EditorHorizontalRule','Y',20051213155700),(47,1,'EditorSourceView','Y',20051213155700),(48,1,'EditorEnlarge','Y',20051213155700),(49,1,'EditorTextDirection','Y',20051213155700),(50,1,'EditorLink','Y',20051213155700),(51,1,'EditorSubhead','Y',20051213155700),(52,1,'EditorBold','Y',20051213155700),(53,1,'EditorItalic','Y',20051213155700),(54,1,'EditorUnderline','Y',20051213155700),(55,1,'EditorUndoRedo','Y',20051213155700),(56,1,'EditorCopyCutPaste','Y',20051213155700),(57,1,'ManageReaders','Y',20051213155700),(58,1,'InitializeTemplateEngine','Y',20051213155700),(59,0,'KeywordSeparator',',',20060306143350),(60,1,'MoveArticle','Y',20060306143350),(61,1,'TranslateArticle','Y',20060306143350),(62,1,'AttachImageToArticle','Y',20060306143350),(63,1,'ChangeSystemPreferences','Y',20060306143350),(64,1,'AttachTopicToArticle','Y',20060306143350),(65,1,'EditorFindReplace','Y',20060306143350),(66,1,'EditorCharacterMap','Y',20060306143350);

--
-- Table structure for table `UserTypes`
--

DROP TABLE IF EXISTS `UserTypes`;
CREATE TABLE `UserTypes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_type_name` varchar(140) NOT NULL default '',
  `varname` varchar(100) NOT NULL default '',
  `value` varchar(100) default NULL,
  `last_modified` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_var_name_index` (`user_type_name`,`varname`),
  KEY `user_type_name` (`user_type_name`)
) TYPE=MyISAM;

--
-- Dumping data for table `UserTypes`
--

INSERT INTO `UserTypes` (`id`, `user_type_name`, `varname`, `value`, `last_modified`) VALUES (1,'Administrator','ManagePub','Y',20051213155700),(2,'Administrator','DeletePub','Y',20051213155700),(3,'Administrator','ManageIssue','Y',20051213155700),(4,'Administrator','DeleteIssue','Y',20051213155700),(5,'Administrator','ManageSection','Y',20051213155700),(6,'Administrator','DeleteSection','Y',20051213155700),(7,'Administrator','AddArticle','Y',20051213155700),(8,'Administrator','ChangeArticle','Y',20051213155700),(9,'Administrator','DeleteArticle','Y',20051213155700),(10,'Administrator','AddImage','Y',20051213155700),(11,'Administrator','AddFile','Y',20051213155700),(12,'Administrator','ChangeImage','Y',20051213155700),(13,'Administrator','ChangeFile','Y',20051213155700),(14,'Administrator','DeleteImage','Y',20051213155700),(15,'Administrator','DeleteFile','Y',20051213155700),(16,'Administrator','ManageTempl','Y',20051213155700),(17,'Administrator','DeleteTempl','Y',20051213155700),(18,'Administrator','ManageUsers','Y',20051213155700),(19,'Administrator','ManageSubscriptions','Y',20051213155700),(20,'Administrator','DeleteUsers','Y',20051213155700),(21,'Administrator','ManageUserTypes','Y',20051213155700),(22,'Administrator','ManageArticleTypes','Y',20051213155700),(23,'Administrator','DeleteArticleTypes','Y',20051213155700),(24,'Administrator','ManageLanguages','Y',20051213155700),(25,'Administrator','DeleteLanguages','Y',20051213155700),(26,'Administrator','ManageCountries','Y',20051213155700),(27,'Administrator','DeleteCountries','Y',20051213155700),(28,'Administrator','MailNotify','N',20051213155700),(29,'Administrator','ViewLogs','Y',20051213155700),(30,'Administrator','ManageLocalizer','Y',20051213155700),(31,'Administrator','ManageIndexer','N',20060104002155),(32,'Administrator','Publish','Y',20051213155700),(33,'Administrator','ManageTopics','Y',20051213155700),(34,'Administrator','EditorImage','Y',20051213155700),(35,'Administrator','EditorTextAlignment','Y',20051213155700),(36,'Administrator','EditorFontColor','Y',20051213155700),(37,'Administrator','EditorFontSize','Y',20051213155700),(38,'Administrator','EditorFontFace','Y',20051213155700),(39,'Administrator','EditorTable','Y',20051213155700),(40,'Administrator','EditorSuperscript','Y',20051213155700),(41,'Administrator','EditorSubscript','Y',20051213155700),(42,'Administrator','EditorStrikethrough','Y',20051213155700),(43,'Administrator','EditorIndent','Y',20051213155700),(44,'Administrator','EditorListBullet','Y',20051213155700),(45,'Administrator','EditorListNumber','Y',20051213155700),(46,'Administrator','EditorHorizontalRule','Y',20051213155700),(47,'Administrator','EditorSourceView','Y',20051213155700),(48,'Administrator','EditorEnlarge','Y',20051213155700),(49,'Administrator','EditorTextDirection','Y',20051213155700),(50,'Administrator','EditorLink','Y',20051213155700),(51,'Administrator','EditorSubhead','Y',20051213155700),(52,'Administrator','EditorBold','Y',20051213155700),(53,'Administrator','EditorItalic','Y',20051213155700),(54,'Administrator','EditorUnderline','Y',20051213155700),(55,'Administrator','EditorUndoRedo','Y',20051213155700),(56,'Administrator','EditorCopyCutPaste','Y',20051213155700),(57,'Administrator','ManageReaders','Y',20051213155700),(58,'Administrator','InitializeTemplateEngine','Y',20060104002155),(59,'Editor','ManagePub','N',20051213155700),(60,'Editor','DeletePub','N',20051213155700),(61,'Editor','ManageIssue','N',20051213155700),(62,'Editor','DeleteIssue','N',20051213155700),(63,'Editor','ManageSection','N',20051213155700),(64,'Editor','DeleteSection','N',20051213155700),(65,'Editor','AddArticle','Y',20051213155700),(66,'Editor','ChangeArticle','Y',20051213155700),(67,'Editor','DeleteArticle','Y',20051213155700),(68,'Editor','AddImage','Y',20051213155700),(69,'Editor','AddFile','Y',20051213155700),(70,'Editor','ChangeImage','Y',20051213155700),(71,'Editor','ChangeFile','Y',20051213155700),(72,'Editor','DeleteImage','Y',20051213155700),(73,'Editor','DeleteFile','Y',20051213155700),(74,'Editor','ManageTempl','N',20051213155700),(75,'Editor','DeleteTempl','N',20051213155700),(76,'Editor','ManageUsers','N',20051213155700),(77,'Editor','ManageSubscriptions','N',20051213155700),(78,'Editor','DeleteUsers','N',20051213155700),(79,'Editor','ManageUserTypes','N',20051213155700),(80,'Editor','ManageArticleTypes','N',20051213155700),(81,'Editor','DeleteArticleTypes','N',20051213155700),(82,'Editor','ManageLanguages','N',20051213155700),(83,'Editor','DeleteLanguages','N',20051213155700),(84,'Editor','ManageCountries','N',20051213155700),(85,'Editor','DeleteCountries','N',20051213155700),(86,'Editor','MailNotify','Y',20051213155700),(87,'Editor','ViewLogs','N',20051213155700),(88,'Editor','ManageLocalizer','N',20051213155700),(89,'Editor','ManageIndexer','N',20051213155700),(90,'Editor','Publish','N',20051213155700),(91,'Editor','ManageTopics','N',20051213155700),(92,'Editor','EditorImage','Y',20051213155700),(93,'Editor','EditorTextAlignment','Y',20051213155700),(94,'Editor','EditorFontColor','N',20051213155700),(95,'Editor','EditorFontSize','N',20051213155700),(96,'Editor','EditorFontFace','N',20051213155700),(97,'Editor','EditorTable','Y',20051213155700),(98,'Editor','EditorSuperscript','N',20060306153426),(99,'Editor','EditorSubscript','N',20060306153426),(100,'Editor','EditorStrikethrough','N',20051213155700),(101,'Editor','EditorIndent','Y',20051213155700),(102,'Editor','EditorListBullet','Y',20051213155700),(103,'Editor','EditorListNumber','Y',20051213155700),(104,'Editor','EditorHorizontalRule','N',20051213155700),(105,'Editor','EditorSourceView','N',20051213155700),(106,'Editor','EditorEnlarge','Y',20051213155700),(107,'Editor','EditorTextDirection','Y',20051213155700),(108,'Editor','EditorLink','Y',20051213155700),(109,'Editor','EditorSubhead','Y',20051213155700),(110,'Editor','EditorBold','Y',20051213155700),(111,'Editor','EditorItalic','Y',20051213155700),(112,'Editor','EditorUnderline','Y',20051213155700),(113,'Editor','EditorUndoRedo','Y',20051213155700),(114,'Editor','EditorCopyCutPaste','Y',20051213155700),(115,'Editor','ManageReaders','N',20051213155700),(116,'Editor','InitializeTemplateEngine','N',20051213155700),(117,'Chief Editor','ManagePub','N',20051213155700),(118,'Chief Editor','DeletePub','N',20051213155700),(119,'Chief Editor','ManageIssue','Y',20051213155700),(120,'Chief Editor','DeleteIssue','Y',20051213155700),(121,'Chief Editor','ManageSection','Y',20051213155700),(122,'Chief Editor','DeleteSection','Y',20051213155700),(123,'Chief Editor','AddArticle','Y',20051213155700),(124,'Chief Editor','ChangeArticle','Y',20051213155700),(125,'Chief Editor','DeleteArticle','Y',20051213155700),(126,'Chief Editor','AddImage','Y',20051213155700),(127,'Chief Editor','AddFile','Y',20051213155700),(128,'Chief Editor','ChangeImage','Y',20051213155700),(129,'Chief Editor','ChangeFile','Y',20051213155700),(130,'Chief Editor','DeleteImage','Y',20051213155700),(131,'Chief Editor','DeleteFile','Y',20051213155700),(132,'Chief Editor','ManageTempl','N',20060306153540),(133,'Chief Editor','DeleteTempl','N',20060306153540),(134,'Chief Editor','ManageUsers','Y',20060306153540),(135,'Chief Editor','ManageSubscriptions','N',20051213155700),(136,'Chief Editor','DeleteUsers','Y',20060306153540),(137,'Chief Editor','ManageUserTypes','N',20051213155700),(138,'Chief Editor','ManageArticleTypes','Y',20051213155700),(139,'Chief Editor','DeleteArticleTypes','Y',20051213155700),(140,'Chief Editor','ManageLanguages','N',20051213155700),(141,'Chief Editor','DeleteLanguages','N',20051213155700),(142,'Chief Editor','ManageCountries','N',20051213155700),(143,'Chief Editor','DeleteCountries','N',20051213155700),(144,'Chief Editor','MailNotify','N',20051213155700),(145,'Chief Editor','ViewLogs','Y',20051213155700),(146,'Chief Editor','ManageLocalizer','Y',20051213155700),(147,'Chief Editor','ManageIndexer','N',20051213155700),(148,'Chief Editor','Publish','Y',20051213155700),(149,'Chief Editor','ManageTopics','Y',20051213155700),(150,'Chief Editor','EditorImage','Y',20051213155700),(151,'Chief Editor','EditorTextAlignment','N',20051213155700),(152,'Chief Editor','EditorFontColor','Y',20051213155700),(153,'Chief Editor','EditorFontSize','N',20051213155700),(154,'Chief Editor','EditorFontFace','N',20051213155700),(155,'Chief Editor','EditorTable','Y',20051213155700),(156,'Chief Editor','EditorSuperscript','Y',20051213155700),(157,'Chief Editor','EditorSubscript','Y',20051213155700),(158,'Chief Editor','EditorStrikethrough','Y',20051213155700),(159,'Chief Editor','EditorIndent','Y',20051213155700),(160,'Chief Editor','EditorListBullet','Y',20051213155700),(161,'Chief Editor','EditorListNumber','Y',20051213155700),(162,'Chief Editor','EditorHorizontalRule','N',20051213155700),(163,'Chief Editor','EditorSourceView','N',20051213155700),(164,'Chief Editor','EditorEnlarge','Y',20051213155700),(165,'Chief Editor','EditorTextDirection','Y',20051213155700),(166,'Chief Editor','EditorLink','Y',20051213155700),(167,'Chief Editor','EditorSubhead','Y',20051213155700),(168,'Chief Editor','EditorBold','Y',20051213155700),(169,'Chief Editor','EditorItalic','Y',20051213155700),(170,'Chief Editor','EditorUnderline','Y',20051213155700),(171,'Chief Editor','EditorUndoRedo','Y',20051213155700),(172,'Chief Editor','EditorCopyCutPaste','Y',20051213155700),(173,'Chief Editor','ManageReaders','Y',20051213155700),(174,'Chief Editor','InitializeTemplateEngine','N',20051213155700),(175,'Journalist','ManagePub','N',20060103231030),(176,'Journalist','DeletePub','N',20060103231030),(177,'Journalist','ManageIssue','N',20060103231030),(178,'Journalist','DeleteIssue','N',20060103231030),(179,'Journalist','ManageSection','N',20060103231030),(180,'Journalist','DeleteSection','N',20060103231030),(181,'Journalist','AddArticle','Y',20060103231030),(182,'Journalist','ChangeArticle','N',20060103231030),(183,'Journalist','DeleteArticle','N',20060103231030),(184,'Journalist','AddImage','Y',20060103231030),(185,'Journalist','ChangeImage','Y',20060103231030),(186,'Journalist','DeleteImage','N',20060103231030),(187,'Journalist','ManageTempl','N',20060103231030),(188,'Journalist','DeleteTempl','N',20060103231030),(189,'Journalist','ManageUsers','N',20060103231030),(190,'Journalist','ManageReaders','N',20060103231030),(191,'Journalist','ManageSubscriptions','N',20060103231030),(192,'Journalist','DeleteUsers','N',20060103231030),(193,'Journalist','ManageUserTypes','N',20060103231030),(194,'Journalist','ManageArticleTypes','N',20060103231030),(195,'Journalist','DeleteArticleTypes','N',20060103231030),(196,'Journalist','ManageLanguages','N',20060103231030),(197,'Journalist','DeleteLanguages','N',20060103231030),(198,'Journalist','MailNotify','N',20060103231030),(199,'Journalist','ManageCountries','N',20060103231030),(200,'Journalist','DeleteCountries','N',20060103231030),(201,'Journalist','ViewLogs','N',20060103231030),(202,'Journalist','ManageLocalizer','N',20060103231030),(203,'Journalist','ManageIndexer','N',20060103231030),(204,'Journalist','Publish','N',20060103231030),(205,'Journalist','ManageTopics','N',20060103231030),(206,'Journalist','EditorBold','Y',20060103231030),(207,'Journalist','EditorItalic','Y',20060103231030),(208,'Journalist','EditorUnderline','Y',20060103231030),(209,'Journalist','EditorUndoRedo','Y',20060306153035),(210,'Journalist','EditorCopyCutPaste','Y',20060103231030),(211,'Journalist','EditorImage','Y',20060103231030),(212,'Journalist','EditorTextAlignment','N',20060306153035),(213,'Journalist','EditorFontColor','N',20060306153035),(214,'Journalist','EditorFontSize','N',20060103231030),(215,'Journalist','EditorFontFace','N',20060103231030),(216,'Journalist','EditorTable','N',20060306153248),(217,'Journalist','EditorSuperscript','N',20060103231030),(218,'Journalist','EditorSubscript','N',20060103231030),(219,'Journalist','EditorStrikethrough','N',20060306153035),(220,'Journalist','EditorIndent','N',20060306153035),(221,'Journalist','EditorListBullet','Y',20060103231030),(222,'Journalist','EditorListNumber','Y',20060103231030),(223,'Journalist','EditorHorizontalRule','N',20060103231030),(224,'Journalist','EditorSourceView','N',20060103231030),(225,'Journalist','EditorEnlarge','Y',20060103231030),(226,'Journalist','EditorTextDirection','N',20060306153035),(227,'Journalist','EditorLink','Y',20060103231030),(228,'Journalist','EditorSubhead','Y',20060103231030),(229,'Journalist','InitializeTemplateEngine','N',20060103231030),(230,'Journalist','AddFile','Y',20060103231030),(231,'Journalist','ChangeFile','Y',20060103231030),(232,'Journalist','DeleteFile','N',20060103231030),(233,'Administrator','MoveArticle','Y',20060306143350),(234,'Administrator','TranslateArticle','Y',20060306143350),(235,'Administrator','AttachImageToArticle','Y',20060306143350),(236,'Administrator','ChangeSystemPreferences','Y',20060306143350),(237,'Administrator','AttachTopicToArticle','Y',20060306143350),(238,'Administrator','EditorFindReplace','Y',20060306143350),(239,'Administrator','EditorCharacterMap','Y',20060306143350),(240,'Chief Editor','MoveArticle','Y',20060306143350),(241,'Chief Editor','TranslateArticle','Y',20060306143350),(242,'Chief Editor','AttachImageToArticle','Y',20060306143350),(243,'Chief Editor','ChangeSystemPreferences','N',20060306143350),(244,'Chief Editor','AttachTopicToArticle','Y',20060306143350),(245,'Chief Editor','EditorFindReplace','Y',20060306143350),(246,'Chief Editor','EditorCharacterMap','Y',20060306143350),(247,'Editor','MoveArticle','Y',20060306143350),(248,'Editor','TranslateArticle','Y',20060306143350),(249,'Editor','AttachImageToArticle','Y',20060306143350),(250,'Editor','ChangeSystemPreferences','N',20060306143350),(251,'Editor','AttachTopicToArticle','Y',20060306143350),(252,'Editor','EditorFindReplace','Y',20060306143350),(253,'Editor','EditorCharacterMap','Y',20060306143350),(254,'Journalist','MoveArticle','N',20060306143350),(255,'Journalist','TranslateArticle','Y',20060306143350),(256,'Journalist','AttachImageToArticle','Y',20060306143350),(257,'Journalist','ChangeSystemPreferences','N',20060306143350),(258,'Journalist','AttachTopicToArticle','Y',20060306143350),(259,'Journalist','EditorFindReplace','Y',20060306143350),(260,'Journalist','EditorCharacterMap','Y',20060306143350),(261,'Subscription manager','ManagePub','N',20060306153624),(262,'Subscription manager','DeletePub','N',20060306153624),(263,'Subscription manager','ManageIssue','N',20060306153624),(264,'Subscription manager','DeleteIssue','N',20060306153624),(265,'Subscription manager','ManageSection','N',20060306153624),(266,'Subscription manager','DeleteSection','N',20060306153624),(267,'Subscription manager','AddArticle','N',20060306153624),(268,'Subscription manager','ChangeArticle','N',20060306153624),(269,'Subscription manager','MoveArticle','N',20060306153624),(270,'Subscription manager','TranslateArticle','N',20060306153624),(271,'Subscription manager','DeleteArticle','N',20060306153624),(272,'Subscription manager','AttachImageToArticle','N',20060306153624),(273,'Subscription manager','AttachTopicToArticle','N',20060306153624),(274,'Subscription manager','AddImage','N',20060306153624),(275,'Subscription manager','ChangeImage','N',20060306153624),(276,'Subscription manager','DeleteImage','N',20060306153624),(277,'Subscription manager','ManageTempl','N',20060306153624),(278,'Subscription manager','DeleteTempl','N',20060306153624),(279,'Subscription manager','ManageUsers','N',20060306153624),(280,'Subscription manager','ManageReaders','Y',20060306153624),(281,'Subscription manager','ManageSubscriptions','Y',20060306153624),(282,'Subscription manager','DeleteUsers','N',20060306153624),(283,'Subscription manager','ManageUserTypes','N',20060306153624),(284,'Subscription manager','ManageArticleTypes','N',20060306153624),(285,'Subscription manager','DeleteArticleTypes','N',20060306153624),(286,'Subscription manager','ManageLanguages','N',20060306153624),(287,'Subscription manager','DeleteLanguages','N',20060306153624),(288,'Subscription manager','MailNotify','N',20060306153624),(289,'Subscription manager','ManageCountries','N',20060306153624),(290,'Subscription manager','DeleteCountries','N',20060306153624),(291,'Subscription manager','ViewLogs','N',20060306153624),(292,'Subscription manager','ManageLocalizer','N',20060306153624),(293,'Subscription manager','ManageIndexer','N',20060306153624),(294,'Subscription manager','Publish','N',20060306153624),(295,'Subscription manager','ManageTopics','N',20060306153624),(296,'Subscription manager','EditorBold','N',20060306153624),(297,'Subscription manager','EditorItalic','N',20060306153624),(298,'Subscription manager','EditorUnderline','N',20060306153624),(299,'Subscription manager','EditorUndoRedo','N',20060306153624),(300,'Subscription manager','EditorCopyCutPaste','N',20060306153624),(301,'Subscription manager','EditorFindReplace','N',20060306153624),(302,'Subscription manager','EditorCharacterMap','N',20060306153624),(303,'Subscription manager','EditorImage','N',20060306153624),(304,'Subscription manager','EditorTextAlignment','N',20060306153624),(305,'Subscription manager','EditorFontColor','N',20060306153624),(306,'Subscription manager','EditorFontSize','N',20060306153624),(307,'Subscription manager','EditorFontFace','N',20060306153624),(308,'Subscription manager','EditorTable','N',20060306153624),(309,'Subscription manager','EditorSuperscript','N',20060306153624),(310,'Subscription manager','EditorSubscript','N',20060306153624),(311,'Subscription manager','EditorStrikethrough','N',20060306153624),(312,'Subscription manager','EditorIndent','N',20060306153624),(313,'Subscription manager','EditorListBullet','N',20060306153624),(314,'Subscription manager','EditorListNumber','N',20060306153624),(315,'Subscription manager','EditorHorizontalRule','N',20060306153624),(316,'Subscription manager','EditorSourceView','N',20060306153624),(317,'Subscription manager','EditorEnlarge','N',20060306153624),(318,'Subscription manager','EditorTextDirection','N',20060306153624),(319,'Subscription manager','EditorLink','N',20060306153624),(320,'Subscription manager','EditorSubhead','N',20060306153624),(321,'Subscription manager','InitializeTemplateEngine','N',20060306153624),(322,'Subscription manager','ChangeSystemPreferences','N',20060306153624),(323,'Subscription manager','AddFile','N',20060306153624),(324,'Subscription manager','ChangeFile','N',20060306153624),(325,'Subscription manager','DeleteFile','N',20060306153624);

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `KeyId` int(10) unsigned default NULL,
  `Name` varchar(255) NOT NULL default '',
  `UName` varchar(70) NOT NULL default '',
  `Password` varchar(64) NOT NULL default '',
  `EMail` varchar(255) NOT NULL default '',
  `Reader` enum('Y','N') NOT NULL default 'Y',
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
  `time_updated` timestamp(14) NOT NULL,
  `time_created` timestamp(14) NOT NULL default '00000000000000',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `UName` (`UName`)
) TYPE=MyISAM;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`Id`, `KeyId`, `Name`, `UName`, `Password`, `EMail`, `Reader`, `City`, `StrAddress`, `State`, `CountryCode`, `Phone`, `Fax`, `Contact`, `Phone2`, `Title`, `Gender`, `Age`, `PostalCode`, `Employer`, `EmployerType`, `Position`, `Interests`, `How`, `Languages`, `Improvements`, `Pref1`, `Pref2`, `Pref3`, `Pref4`, `Field1`, `Field2`, `Field3`, `Field4`, `Field5`, `Text1`, `Text2`, `Text3`, `time_updated`, `time_created`) VALUES (1,NULL,'Administrator','admin','b2d716fb2328a246e8285f47b1500ebcb349c187','','N','','','','AD','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','',20060306164323,00000000000000);

