-- MySQL dump 9.11
--
-- Host: localhost    Database: campsite
-- ------------------------------------------------------
-- Server version	4.0.23_Debian-3ubuntu2-log

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
-- Table structure for table `ArticleImages`
--

DROP TABLE IF EXISTS `ArticleImages`;
CREATE TABLE `ArticleImages` (
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdImage` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`NrArticle`,`IdImage`),
  UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`)
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
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `ActionTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Publish` enum('P','U') default NULL,
  `FrontPage` enum('S','R') default NULL,
  `SectionPage` enum('S','R') default NULL,
  `Completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`NrArticle`,`IdLanguage`,`ActionTime`)
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
-- Table structure for table `AutoId`
--

DROP TABLE IF EXISTS `AutoId`;
CREATE TABLE `AutoId` (
  `DictionaryId` int(10) unsigned NOT NULL default '0',
  `ClassId` int(10) unsigned NOT NULL default '0',
  `ArticleId` int(10) unsigned NOT NULL default '0',
  `KeywordId` int(10) unsigned NOT NULL default '0',
  `LogTStamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `TopicId` int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

--
-- Dumping data for table `AutoId`
--

INSERT INTO `AutoId` (`DictionaryId`, `ClassId`, `ArticleId`, `KeywordId`, `LogTStamp`, `TopicId`) VALUES (0,0,0,0,'0000-00-00 00:00:00',0);

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

INSERT INTO `Countries` (`Code`, `IdLanguage`, `Name`) VALUES ('AR',1,'Argentina'),('AG',1,'Antigua And Barbuda'),('AQ',1,'Antarctica'),('AI',1,'Anguilla'),('AO',1,'Angola'),('AD',1,'Andorra'),('AS',1,'American Samoa'),('DZ',1,'Algeria'),('AL',1,'Albania'),('AF',1,'Afghanistan'),('AM',1,'Armenia'),('AW',1,'Aruba'),('AU',1,'Australia'),('AT',1,'Austria'),('AZ',1,'Azerbaijan'),('BS',1,'Bahamas'),('BH',1,'Bahrain'),('BD',1,'Bangladesh'),('BB',1,'Barbados'),('BY',1,'Belarus'),('BE',1,'Belgium'),('BZ',1,'Belize'),('BJ',1,'Benin'),('BM',1,'Bermuda'),('BT',1,'Bhutan'),('BO',1,'Bolivia'),('BA',1,'Bosnia And Herzegovina'),('BW',1,'Botswana'),('BV',1,'Bouvet Island'),('BR',1,'Brazil'),('IO',1,'British Indian Ocean Territory'),('BN',1,'Brunei Darussalam'),('BG',1,'Bulgaria'),('BF',1,'Burkina Faso'),('BI',1,'Burundi'),('KH',1,'Cambodia'),('CM',1,'Cameroon'),('CA',1,'Canada'),('CV',1,'Cape Verde'),('KY',1,'Cayman Islands'),('CF',1,'Central African Republic'),('TD',1,'Chad'),('CL',1,'Chile'),('CN',1,'China'),('CX',1,'Christmas Island'),('CC',1,'Cocos (Keeling) Islands'),('CO',1,'Colombia'),('KM',1,'Comoros'),('CG',1,'Congo'),('CD',1,'Congo, The Democratic Republic Of The'),('CK',1,'Cook Islands'),('CR',1,'Costa Rica'),('CI',1,'Cote Divoire'),('HR',1,'Croatia'),('CU',1,'Cuba'),('CY',1,'Cyprus'),('CZ',1,'Czech Republic'),('DK',1,'Denmark'),('DJ',1,'Djibouti'),('DM',1,'Dominica'),('DO',1,'Dominican Republic'),('TP',1,'East Timor'),('EC',1,'Ecuador'),('EG',1,'Egypt'),('SV',1,'El Salvador'),('GQ',1,'Equatorial Guinea'),('ER',1,'Eritrea'),('EE',1,'Estonia'),('ET',1,'Ethiopia'),('FK',1,'Falkland Islands (Malvinas)'),('FO',1,'Faroe Islands'),('FJ',1,'Fiji'),('FI',1,'Finland'),('FR',1,'France'),('FX',1,'France, Metropolitan'),('GF',1,'French Guiana'),('PF',1,'French Polynesia'),('TF',1,'French Southern Territories'),('GA',1,'Gabon'),('GM',1,'Gambia'),('GE',1,'Georgia'),('DE',1,'Germany'),('GH',1,'Ghana'),('GI',1,'Gibraltar'),('GR',1,'Greece'),('GL',1,'Greenland'),('GD',1,'Grenada'),('GP',1,'Guadeloupe'),('GU',1,'Guam'),('GT',1,'Guatemala'),('GN',1,'Guinea'),('GW',1,'Guinea-bissau'),('GY',1,'Guyana'),('HT',1,'Haiti'),('HM',1,'Heard Island And Mcdonald Islands'),('VA',1,'Holy See (Vatican City State)'),('HN',1,'Honduras'),('HK',1,'Hong Kong'),('HU',1,'Hungary'),('IS',1,'Iceland'),('IN',1,'India'),('ID',1,'Indonesia'),('IR',1,'Iran, Islamic Republic Of'),('IQ',1,'Iraq'),('IE',1,'Ireland'),('IL',1,'Israel'),('IT',1,'Italy'),('JM',1,'Jamaica'),('JP',1,'Japan'),('JO',1,'Jordan'),('KZ',1,'Kazakstan'),('KE',1,'Kenya'),('KI',1,'Kiribati'),('KP',1,'Korea, Democratic Peoples Republic Of'),('KR',1,'Korea, Republic Of'),('KW',1,'Kuwait'),('KG',1,'Kyrgyzstan'),('LA',1,'Lao Peoples Democratic Republic'),('LV',1,'Latvia'),('LB',1,'Lebanon'),('LS',1,'Lesotho'),('LR',1,'Liberia'),('LY',1,'Libyan Arab Jamahiriya'),('LI',1,'Liechtenstein'),('LT',1,'Lithuania'),('LU',1,'Luxembourg'),('MO',1,'Macau'),('MK',1,'Macedonia, The Former Yugoslav Republic Of'),('MG',1,'Madagascar'),('MW',1,'Malawi'),('MY',1,'Malaysia'),('MV',1,'Maldives'),('ML',1,'Mali'),('MT',1,'Malta'),('MH',1,'Marshall Islands'),('MQ',1,'Martinique'),('MR',1,'Mauritania'),('MU',1,'Mauritius'),('YT',1,'Mayotte'),('MX',1,'Mexico'),('FM',1,'Micronesia, Federated States Of'),('MD',1,'Moldova, Republic Of'),('MC',1,'Monaco'),('MN',1,'Mongolia'),('MS',1,'Montserrat'),('MA',1,'Morocco'),('MZ',1,'Mozambique'),('MM',1,'Myanmar'),('NA',1,'Namibia'),('NR',1,'Nauru'),('NP',1,'Nepal'),('NL',1,'Netherlands'),('AN',1,'Netherlands Antilles'),('NC',1,'New Caledonia'),('NZ',1,'New Zealand'),('NI',1,'Nicaragua'),('NE',1,'Niger'),('NG',1,'Nigeria'),('NU',1,'Niue'),('NF',1,'Norfolk Island'),('MP',1,'Northern Mariana Islands'),('NO',1,'Norway'),('OM',1,'Oman'),('PK',1,'Pakistan'),('PW',1,'Palau'),('PS',1,'Palestinian Territory, Occupied'),('PA',1,'Panama'),('PG',1,'Papua New Guinea'),('PY',1,'Paraguay'),('PE',1,'Peru'),('PH',1,'Philippines'),('PN',1,'Pitcairn'),('PL',1,'Poland'),('PT',1,'Portugal'),('PR',1,'Puerto Rico'),('QA',1,'Qatar'),('RE',1,'Reunion'),('RO',1,'Romania'),('RU',1,'Russian Federation'),('RW',1,'Rwanda'),('SH',1,'Saint Helena'),('KN',1,'Saint Kitts And Nevis'),('LC',1,'Saint Lucia'),('PM',1,'Saint Pierre And Miquelon'),('VC',1,'Saint Vincent And The Grenadines'),('WS',1,'Samoa'),('SM',1,'San Marino'),('ST',1,'Sao Tome And Principe'),('SA',1,'Saudi Arabia'),('SN',1,'Senegal'),('CS',1,'Serbia and Montenegro'),('SC',1,'Seychelles'),('SL',1,'Sierra Leone'),('SG',1,'Singapore'),('SK',1,'Slovakia'),('SI',1,'Slovenia'),('SB',1,'Solomon Islands'),('SO',1,'Somalia'),('ZA',1,'South Africa'),('GS',1,'South Georgia And The South Sandwich Islands'),('ES',1,'Spain'),('LK',1,'Sri Lanka'),('SD',1,'Sudan'),('SR',1,'Suriname'),('SJ',1,'Svalbard And Jan Mayen'),('SZ',1,'Swaziland'),('SE',1,'Sweden'),('CH',1,'Switzerland'),('SY',1,'Syrian Arab Republic'),('TW',1,'Taiwan, Province Of China'),('TJ',1,'Tajikistan'),('TZ',1,'Tanzania, United Republic Of'),('TH',1,'Thailand'),('TG',1,'Togo'),('TK',1,'Tokelau'),('TO',1,'Tonga'),('TT',1,'Trinidad And Tobago'),('TN',1,'Tunisia'),('TR',1,'Turkey'),('TM',1,'Turkmenistan'),('TC',1,'Turks And Caicos Islands'),('TV',1,'Tuvalu'),('UG',1,'Uganda'),('UA',1,'Ukraine'),('AE',1,'United Arab Emirates'),('GB',1,'United Kingdom'),('US',1,'United States'),('UM',1,'United States Minor Outlying Islands'),('UY',1,'Uruguay'),('UZ',1,'Uzbekistan'),('VU',1,'Vanuatu'),('VE',1,'Venezuela'),('VN',1,'Vietnam'),('VG',1,'Virgin Islands, British'),('VI',1,'Virgin Islands, U.S.'),('WF',1,'Wallis And Futuna'),('EH',1,'Western Sahara'),('YE',1,'Yemen'),('ZM',1,'Zambia'),('ZW',1,'Zimbabwe');

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
  `IdPublication` int(10) unsigned NOT NULL default '0',
  `NrIssue` int(10) unsigned NOT NULL default '0',
  `IdLanguage` int(10) unsigned NOT NULL default '0',
  `ActionTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Action` enum('P','U') NOT NULL default 'P',
  `PublishArticles` enum('Y','N') NOT NULL default 'Y',
  `Completed` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdPublication`,`NrIssue`,`IdLanguage`,`ActionTime`)
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

INSERT INTO `Languages` (`Id`, `Name`, `CodePage`, `OrigName`, `Code`, `Month1`, `Month2`, `Month3`, `Month4`, `Month5`, `Month6`, `Month7`, `Month8`, `Month9`, `Month10`, `Month11`, `Month12`, `WDay1`, `WDay2`, `WDay3`, `WDay4`, `WDay5`, `WDay6`, `WDay7`) VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),(5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'),(6,'Austrian','IS0_8859-1','Deutsch (Österreich)','at','Jänner','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'),(9,'Portuguese','ISO_8859-1','Português','pt','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'),(12,'French','ISO_8859-1','Français','fr','Janvier','Février','Mars','Avril','Peut','Juin','Juli','Août','Septembre','Octobre','Novembre','Décembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'),(13,'Spanish','ISO_8859-1','Español','es','Enero','Febrero','Marcha','Abril','Puede','Junio','Juli','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'),(14,'Italian','ISO_8859-1','Italiano','it','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre','Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'),(2,'Romanian','ISO_8859-2','Română','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă'),(7,'Croatian','ISO_8859-2','Hrvatski','hr','Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota'),(8,'Czech','ISO_8859-2','Český','cz','Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec','Neděle','Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota'),(11,'Serbo-Croatian','ISO_8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota'),(10,'Serbian (Cyrillic)','ISO_8859-5','Српски (Ћирилица)','sr','јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота'),(15,'Russian','ISO_8859-5','Русский','ru','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'),(3,'Hebrew','ISO_8859-9','øàè÷øàè','he','÷øà èâëéçâëòç ëòéç','ëòéç ëòé','çëòéçëòéå456','÷øàè÷øàèøà','ãëğãâëé','ñòéâëòé','âëòé','âëòéã÷øòùãâòùã','âùãâùã/\'ø÷øé','âëòéëòéç','éöéòúêçìêóçì','âëòéçéìòéç','åïíôåïíàèå','ïàèåïéçêúîöúõîöúî','äğîáäğîäğá','æñáäãùãâë','ëòéëòéçòéìêì','éçìêéçíåèïíè','÷øàè÷øàè'),(16,'Chinese','UTF-8','中文','zh','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','星期','星期','星期','星期','星期','星期','星期');

--
-- Table structure for table `Log`
--

DROP TABLE IF EXISTS `Log`;
CREATE TABLE `Log` (
  `TStamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `IdEvent` int(10) unsigned NOT NULL default '0',
  `User` varchar(70) NOT NULL default '',
  `Text` varchar(255) NOT NULL default '',
  KEY `IdEvent` (`IdEvent`)
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
  `PayTime` int(10) unsigned NOT NULL default '0',
  `TimeUnit` enum('D','W','M','Y') NOT NULL default 'D',
  `UnitCost` float(10,2) unsigned NOT NULL default '0.00',
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
  `StartDate` date NOT NULL default '0000-00-00',
  `Days` int(10) unsigned NOT NULL default '0',
  `PaidDays` int(10) unsigned NOT NULL default '0',
  `NoticeSent` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdSubscription`,`SectionNumber`)
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

INSERT INTO `TimeUnits` (`Unit`, `IdLanguage`, `Name`) VALUES ('D',1,'days'),('W',1,'weeks'),('M',1,'months'),('Y',1,'years');

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
  UNIQUE KEY `Name` (`LanguageId`,`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `Topics`
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
-- Table structure for table `UserPerm`
--

DROP TABLE IF EXISTS `UserPerm`;
CREATE TABLE `UserPerm` (
  `IdUser` int(10) unsigned NOT NULL default '0',
  `ManagePub` enum('N','Y') NOT NULL default 'N',
  `DeletePub` enum('N','Y') NOT NULL default 'N',
  `ManageIssue` enum('N','Y') NOT NULL default 'N',
  `DeleteIssue` enum('N','Y') NOT NULL default 'N',
  `ManageSection` enum('N','Y') NOT NULL default 'N',
  `DeleteSection` enum('N','Y') NOT NULL default 'N',
  `AddArticle` enum('N','Y') NOT NULL default 'N',
  `ChangeArticle` enum('N','Y') NOT NULL default 'N',
  `DeleteArticle` enum('N','Y') NOT NULL default 'N',
  `AddImage` enum('N','Y') NOT NULL default 'N',
  `ChangeImage` enum('N','Y') NOT NULL default 'N',
  `DeleteImage` enum('N','Y') NOT NULL default 'N',
  `ManageTempl` enum('N','Y') NOT NULL default 'N',
  `DeleteTempl` enum('N','Y') NOT NULL default 'N',
  `ManageUsers` enum('N','Y') NOT NULL default 'N',
  `ManageSubscriptions` enum('N','Y') NOT NULL default 'N',
  `DeleteUsers` enum('N','Y') NOT NULL default 'N',
  `ManageUserTypes` enum('N','Y') NOT NULL default 'N',
  `ManageArticleTypes` enum('N','Y') NOT NULL default 'N',
  `DeleteArticleTypes` enum('N','Y') NOT NULL default 'N',
  `ManageLanguages` enum('N','Y') NOT NULL default 'N',
  `DeleteLanguages` enum('N','Y') NOT NULL default 'N',
  `ManageDictionary` enum('N','Y') NOT NULL default 'N',
  `DeleteDictionary` enum('N','Y') NOT NULL default 'N',
  `ManageCountries` enum('N','Y') NOT NULL default 'N',
  `DeleteCountries` enum('N','Y') NOT NULL default 'N',
  `ManageClasses` enum('N','Y') NOT NULL default 'N',
  `MailNotify` enum('N','Y') NOT NULL default 'N',
  `ViewLogs` enum('N','Y') NOT NULL default 'N',
  `ManageLocalizer` enum('N','Y') NOT NULL default 'N',
  `ManageIndexer` enum('N','Y') NOT NULL default 'N',
  `Publish` enum('N','Y') NOT NULL default 'N',
  `ManageTopics` enum('N','Y') NOT NULL default 'N',
  `EditorImage` enum('N','Y') NOT NULL default 'N',
  `EditorTextAlignment` enum('N','Y') NOT NULL default 'N',
  `EditorFontColor` enum('N','Y') NOT NULL default 'N',
  `EditorFontSize` enum('N','Y') NOT NULL default 'N',
  `EditorFontFace` enum('N','Y') NOT NULL default 'N',
  `EditorTable` enum('N','Y') NOT NULL default 'N',
  `EditorSuperscript` enum('N','Y') NOT NULL default 'N',
  `EditorSubscript` enum('N','Y') NOT NULL default 'N',
  `EditorStrikethrough` enum('N','Y') NOT NULL default 'N',
  `EditorIndent` enum('N','Y') NOT NULL default 'N',
  `EditorListBullet` enum('N','Y') NOT NULL default 'N',
  `EditorListNumber` enum('N','Y') NOT NULL default 'N',
  `EditorHorizontalRule` enum('N','Y') NOT NULL default 'N',
  `EditorSourceView` enum('N','Y') NOT NULL default 'N',
  `EditorEnlarge` enum('N','Y') NOT NULL default 'N',
  `EditorTextDirection` enum('N','Y') NOT NULL default 'N',
  `EditorLink` enum('N','Y') NOT NULL default 'N',
  `EditorSubhead` enum('N','Y') NOT NULL default 'N',
  `EditorBold` enum('N','Y') NOT NULL default 'N',
  `EditorItalic` enum('N','Y') NOT NULL default 'N',
  `EditorUnderline` enum('N','Y') NOT NULL default 'N',
  `EditorUndoRedo` enum('N','Y') NOT NULL default 'N',
  `EditorCopyCutPaste` enum('N','Y') NOT NULL default 'N',
  `ManageReaders` enum('N','Y') NOT NULL default 'N',
  `InitializeTemplateEngine` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`IdUser`)
) TYPE=MyISAM;

--
-- Dumping data for table `UserPerm`
--

INSERT INTO `UserPerm` (`IdUser`, `ManagePub`, `DeletePub`, `ManageIssue`, `DeleteIssue`, `ManageSection`, `DeleteSection`, `AddArticle`, `ChangeArticle`, `DeleteArticle`, `AddImage`, `ChangeImage`, `DeleteImage`, `ManageTempl`, `DeleteTempl`, `ManageUsers`, `ManageSubscriptions`, `DeleteUsers`, `ManageUserTypes`, `ManageArticleTypes`, `DeleteArticleTypes`, `ManageLanguages`, `DeleteLanguages`, `ManageDictionary`, `DeleteDictionary`, `ManageCountries`, `DeleteCountries`, `ManageClasses`, `MailNotify`, `ViewLogs`, `ManageLocalizer`, `ManageIndexer`, `Publish`, `ManageTopics`, `EditorImage`, `EditorTextAlignment`, `EditorFontColor`, `EditorFontSize`, `EditorFontFace`, `EditorTable`, `EditorSuperscript`, `EditorSubscript`, `EditorStrikethrough`, `EditorIndent`, `EditorListBullet`, `EditorListNumber`, `EditorHorizontalRule`, `EditorSourceView`, `EditorEnlarge`, `EditorTextDirection`, `EditorLink`, `EditorSubhead`, `EditorBold`, `EditorItalic`, `EditorUnderline`, `EditorUndoRedo`, `EditorCopyCutPaste`, `ManageReaders`, `InitializeTemplateEngine`) VALUES (1,'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');

--
-- Table structure for table `UserTypes`
--

DROP TABLE IF EXISTS `UserTypes`;
CREATE TABLE `UserTypes` (
  `Name` varchar(140) NOT NULL default '',
  `Reader` enum('N','Y') NOT NULL default 'N',
  `ManagePub` enum('N','Y') NOT NULL default 'N',
  `DeletePub` enum('N','Y') NOT NULL default 'N',
  `ManageIssue` enum('N','Y') NOT NULL default 'N',
  `DeleteIssue` enum('N','Y') NOT NULL default 'N',
  `ManageSection` enum('N','Y') NOT NULL default 'N',
  `DeleteSection` enum('N','Y') NOT NULL default 'N',
  `AddArticle` enum('N','Y') NOT NULL default 'N',
  `ChangeArticle` enum('N','Y') NOT NULL default 'N',
  `DeleteArticle` enum('N','Y') NOT NULL default 'N',
  `AddImage` enum('N','Y') NOT NULL default 'N',
  `ChangeImage` enum('N','Y') NOT NULL default 'N',
  `DeleteImage` enum('N','Y') NOT NULL default 'N',
  `ManageTempl` enum('N','Y') NOT NULL default 'N',
  `DeleteTempl` enum('N','Y') NOT NULL default 'N',
  `ManageUsers` enum('N','Y') NOT NULL default 'N',
  `ManageSubscriptions` enum('N','Y') NOT NULL default 'N',
  `DeleteUsers` enum('N','Y') NOT NULL default 'N',
  `ManageUserTypes` enum('N','Y') NOT NULL default 'N',
  `ManageArticleTypes` enum('N','Y') NOT NULL default 'N',
  `DeleteArticleTypes` enum('N','Y') NOT NULL default 'N',
  `ManageLanguages` enum('N','Y') NOT NULL default 'N',
  `DeleteLanguages` enum('N','Y') NOT NULL default 'N',
  `ManageDictionary` enum('N','Y') NOT NULL default 'N',
  `DeleteDictionary` enum('N','Y') NOT NULL default 'N',
  `ManageCountries` enum('N','Y') NOT NULL default 'N',
  `DeleteCountries` enum('N','Y') NOT NULL default 'N',
  `ManageClasses` enum('N','Y') NOT NULL default 'N',
  `MailNotify` enum('N','Y') NOT NULL default 'N',
  `ViewLogs` enum('N','Y') NOT NULL default 'N',
  `ManageLocalizer` enum('N','Y') NOT NULL default 'N',
  `ManageIndexer` enum('N','Y') NOT NULL default 'N',
  `Publish` enum('N','Y') NOT NULL default 'N',
  `ManageTopics` enum('N','Y') NOT NULL default 'N',
  `EditorImage` enum('N','Y') NOT NULL default 'N',
  `EditorTextAlignment` enum('N','Y') NOT NULL default 'N',
  `EditorFontColor` enum('N','Y') NOT NULL default 'N',
  `EditorFontSize` enum('N','Y') NOT NULL default 'N',
  `EditorFontFace` enum('N','Y') NOT NULL default 'N',
  `EditorTable` enum('N','Y') NOT NULL default 'N',
  `EditorSuperscript` enum('N','Y') NOT NULL default 'N',
  `EditorSubscript` enum('N','Y') NOT NULL default 'N',
  `EditorStrikethrough` enum('N','Y') NOT NULL default 'N',
  `EditorIndent` enum('N','Y') NOT NULL default 'N',
  `EditorListBullet` enum('N','Y') NOT NULL default 'N',
  `EditorListNumber` enum('N','Y') NOT NULL default 'N',
  `EditorHorizontalRule` enum('N','Y') NOT NULL default 'N',
  `EditorSourceView` enum('N','Y') NOT NULL default 'N',
  `EditorEnlarge` enum('N','Y') NOT NULL default 'N',
  `EditorTextDirection` enum('N','Y') NOT NULL default 'N',
  `EditorLink` enum('N','Y') NOT NULL default 'N',
  `EditorSubhead` enum('N','Y') NOT NULL default 'N',
  `EditorBold` enum('N','Y') NOT NULL default 'N',
  `EditorItalic` enum('N','Y') NOT NULL default 'N',
  `EditorUnderline` enum('N','Y') NOT NULL default 'N',
  `EditorUndoRedo` enum('N','Y') NOT NULL default 'N',
  `EditorCopyCutPaste` enum('N','Y') NOT NULL default 'N',
  `ManageReaders` enum('N','Y') NOT NULL default 'N',
  `InitializeTemplateEngine` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`Name`)
) TYPE=MyISAM;

--
-- Dumping data for table `UserTypes`
--

INSERT INTO `UserTypes` (`Name`, `Reader`, `ManagePub`, `DeletePub`, `ManageIssue`, `DeleteIssue`, `ManageSection`, `DeleteSection`, `AddArticle`, `ChangeArticle`, `DeleteArticle`, `AddImage`, `ChangeImage`, `DeleteImage`, `ManageTempl`, `DeleteTempl`, `ManageUsers`, `ManageSubscriptions`, `DeleteUsers`, `ManageUserTypes`, `ManageArticleTypes`, `DeleteArticleTypes`, `ManageLanguages`, `DeleteLanguages`, `ManageDictionary`, `DeleteDictionary`, `ManageCountries`, `DeleteCountries`, `ManageClasses`, `MailNotify`, `ViewLogs`, `ManageLocalizer`, `ManageIndexer`, `Publish`, `ManageTopics`, `EditorImage`, `EditorTextAlignment`, `EditorFontColor`, `EditorFontSize`, `EditorFontFace`, `EditorTable`, `EditorSuperscript`, `EditorSubscript`, `EditorStrikethrough`, `EditorIndent`, `EditorListBullet`, `EditorListNumber`, `EditorHorizontalRule`, `EditorSourceView`, `EditorEnlarge`, `EditorTextDirection`, `EditorLink`, `EditorSubhead`, `EditorBold`, `EditorItalic`, `EditorUnderline`, `EditorUndoRedo`, `EditorCopyCutPaste`, `ManageReaders`, `InitializeTemplateEngine`) VALUES ('Reader','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N'),('Administrator','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y'),('Editor','N','N','N','N','N','N','N','Y','Y','Y','Y','Y','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','Y','N','N','N','N','N','Y','Y','N','N','N','Y','Y','Y','N','Y','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N'),('Chief Editor','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N','Y','Y','N','N','N','N','N','N','N','N','Y','Y','N','Y','Y','Y','N','Y','N','N','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N');

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
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `UName` (`UName`)
) TYPE=MyISAM;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`Id`, `KeyId`, `Name`, `UName`, `Password`, `EMail`, `Reader`, `City`, `StrAddress`, `State`, `CountryCode`, `Phone`, `Fax`, `Contact`, `Phone2`, `Title`, `Gender`, `Age`, `PostalCode`, `Employer`, `EmployerType`, `Position`, `Interests`, `How`, `Languages`, `Improvements`, `Pref1`, `Pref2`, `Pref3`, `Pref4`, `Field1`, `Field2`, `Field3`, `Field4`, `Field5`, `Text1`, `Text2`, `Text3`) VALUES (1,NULL,'Administrator','admin','2c380f066e0e45d1','','N','','','','','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','');
UPDATE `Users` SET Password = password('admn00');
