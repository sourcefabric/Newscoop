################################################################################
#
# CAMPSITE is a Unicode-enabled multilingual web content
# management system for news publications.
# CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
# Copyright (C)2000,2001  Media Development Loan Fund
# contact: contact@campware.org - http://www.campware.org
# Campware encourages further development. Please let us know.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
################################################################################

#
# Table structure for table 'ArticleIndex'
#

CREATE TABLE ArticleIndex (
  IdPublication int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  IdKeyword int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  NrArticle int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdPublication,IdLanguage,IdKeyword,NrIssue,NrSection,NrArticle)
) TYPE=MyISAM;

#
# Dumping data for table 'ArticleIndex'
#


#
# Table structure for table 'ArticleTopics'
#

CREATE TABLE ArticleTopics (
  NrArticle int(10) unsigned NOT NULL default '0',
  TopicId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (NrArticle,TopicId)
) TYPE=MyISAM;

#
# Dumping data for table 'ArticleTopics'
#


#
# Table structure for table 'Articles'
#

CREATE TABLE Articles (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  Type varchar(70) NOT NULL default '',
  IdUser int(10) unsigned NOT NULL default '0',
  OnFrontPage enum('N','Y') NOT NULL default 'N',
  OnSection enum('N','Y') NOT NULL default 'N',
  Published enum('N','S','Y') NOT NULL default 'N',
  UploadDate date NOT NULL default '0000-00-00',
  Keywords varchar(255) NOT NULL default '',
  Public enum('N','Y') NOT NULL default 'N',
  IsIndexed enum('N','Y') NOT NULL default 'N',
  LockUser int(10) unsigned NOT NULL default '0',
  LockTime datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (IdPublication,NrIssue,NrSection,Number,IdLanguage),
  UNIQUE KEY other_key (IdPublication,NrIssue,NrSection,IdLanguage,Number),
  UNIQUE KEY Number (Number,IdLanguage),
  UNIQUE KEY IdPublication (IdPublication,NrIssue,NrSection,IdLanguage,Name),
  KEY Type (Type)
) TYPE=MyISAM;

#
# Dumping data for table 'Articles'
#


#
# Table structure for table 'AutoId'
#

CREATE TABLE AutoId (
  DictionaryId int(10) unsigned NOT NULL default '0',
  ClassId int(10) unsigned NOT NULL default '0',
  ArticleId int(10) unsigned NOT NULL default '0',
  KeywordId int(10) unsigned NOT NULL default '0',
  LogTStamp datetime NOT NULL default '0000-00-00 00:00:00',
  TopicId int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'AutoId'
#

INSERT INTO AutoId VALUES (0,0,0,0,'0000-00-00 00:00:00',0);

#
# Table structure for table 'Classes'
#

CREATE TABLE Classes (
  Id int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PRIMARY KEY  (Id,IdLanguage),
  UNIQUE KEY IdLanguage (IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Classes'
#


#
# Table structure for table 'Countries'
#

CREATE TABLE Countries (
  Code char(2) NOT NULL default '',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PRIMARY KEY  (Code,IdLanguage),
  UNIQUE KEY IdLanguage (IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Countries'
#

INSERT INTO Countries VALUES ('AR',1,'Argentina');
INSERT INTO Countries VALUES ('AG',1,'Antigua And Barbuda');
INSERT INTO Countries VALUES ('AQ',1,'Antarctica');
INSERT INTO Countries VALUES ('AI',1,'Anguilla');
INSERT INTO Countries VALUES ('AO',1,'Angola');
INSERT INTO Countries VALUES ('AD',1,'Andorra');
INSERT INTO Countries VALUES ('DE',5,'Deutschland');
INSERT INTO Countries VALUES ('AS',1,'American Samoa');
INSERT INTO Countries VALUES ('AT',6,'Österreich');
INSERT INTO Countries VALUES ('DZ',1,'Algeria');
INSERT INTO Countries VALUES ('IT',14,'Italia');
INSERT INTO Countries VALUES ('AL',1,'Albania');
INSERT INTO Countries VALUES ('FR',12,'France');
INSERT INTO Countries VALUES ('PT',9,'Portugal');
INSERT INTO Countries VALUES ('AF',1,'Afghanistan');
INSERT INTO Countries VALUES ('ES',13,'España');
INSERT INTO Countries VALUES ('AM',1,'Armenia');
INSERT INTO Countries VALUES ('AW',1,'Aruba');
INSERT INTO Countries VALUES ('AU',1,'Australia');
INSERT INTO Countries VALUES ('AT',1,'Austria');
INSERT INTO Countries VALUES ('AZ',1,'Azerbaijan');
INSERT INTO Countries VALUES ('BS',1,'Bahamas');
INSERT INTO Countries VALUES ('BH',1,'Bahrain');
INSERT INTO Countries VALUES ('BD',1,'Bangladesh');
INSERT INTO Countries VALUES ('BB',1,'Barbados');
INSERT INTO Countries VALUES ('BY',1,'Belarus');
INSERT INTO Countries VALUES ('BE',1,'Belgium');
INSERT INTO Countries VALUES ('BZ',1,'Belize');
INSERT INTO Countries VALUES ('BJ',1,'Benin');
INSERT INTO Countries VALUES ('BM',1,'Bermuda');
INSERT INTO Countries VALUES ('BT',1,'Bhutan');
INSERT INTO Countries VALUES ('BO',1,'Bolivia');
INSERT INTO Countries VALUES ('BA',1,'Bosnia And Herzegovina');
INSERT INTO Countries VALUES ('BW',1,'Botswana');
INSERT INTO Countries VALUES ('BV',1,'Bouvet Island');
INSERT INTO Countries VALUES ('BR',1,'Brazil');
INSERT INTO Countries VALUES ('IO',1,'British Indian Ocean Territory');
INSERT INTO Countries VALUES ('BN',1,'Brunei Darussalam');
INSERT INTO Countries VALUES ('BG',1,'Bulgaria');
INSERT INTO Countries VALUES ('BF',1,'Burkina Faso');
INSERT INTO Countries VALUES ('BI',1,'Burundi');
INSERT INTO Countries VALUES ('KH',1,'Cambodia');
INSERT INTO Countries VALUES ('CM',1,'Cameroon');
INSERT INTO Countries VALUES ('CA',1,'Canada');
INSERT INTO Countries VALUES ('CV',1,'Cape Verde');
INSERT INTO Countries VALUES ('KY',1,'Cayman Islands');
INSERT INTO Countries VALUES ('CF',1,'Central African Republic');
INSERT INTO Countries VALUES ('TD',1,'Chad');
INSERT INTO Countries VALUES ('CL',1,'Chile');
INSERT INTO Countries VALUES ('CN',1,'China');
INSERT INTO Countries VALUES ('CX',1,'Christmas Island');
INSERT INTO Countries VALUES ('CC',1,'Cocos (Keeling) Islands');
INSERT INTO Countries VALUES ('CO',1,'Colombia');
INSERT INTO Countries VALUES ('KM',1,'Comoros');
INSERT INTO Countries VALUES ('CG',1,'Congo');
INSERT INTO Countries VALUES ('CD',1,'Congo, The Democratic Republic Of The');
INSERT INTO Countries VALUES ('CK',1,'Cook Islands');
INSERT INTO Countries VALUES ('CR',1,'Costa Rica');
INSERT INTO Countries VALUES ('CI',1,'Cote Divoire');
INSERT INTO Countries VALUES ('HR',1,'Croatia');
INSERT INTO Countries VALUES ('CU',1,'Cuba');
INSERT INTO Countries VALUES ('CY',1,'Cyprus');
INSERT INTO Countries VALUES ('CZ',1,'Czech Republic');
INSERT INTO Countries VALUES ('DK',1,'Denmark');
INSERT INTO Countries VALUES ('DJ',1,'Djibouti');
INSERT INTO Countries VALUES ('DM',1,'Dominica');
INSERT INTO Countries VALUES ('DO',1,'Dominican Republic');
INSERT INTO Countries VALUES ('TP',1,'East Timor');
INSERT INTO Countries VALUES ('EC',1,'Ecuador');
INSERT INTO Countries VALUES ('EG',1,'Egypt');
INSERT INTO Countries VALUES ('SV',1,'El Salvador');
INSERT INTO Countries VALUES ('GQ',1,'Equatorial Guinea');
INSERT INTO Countries VALUES ('ER',1,'Eritrea');
INSERT INTO Countries VALUES ('EE',1,'Estonia');
INSERT INTO Countries VALUES ('ET',1,'Ethiopia');
INSERT INTO Countries VALUES ('FK',1,'Falkland Islands (Malvinas)');
INSERT INTO Countries VALUES ('FO',1,'Faroe Islands');
INSERT INTO Countries VALUES ('FJ',1,'Fiji');
INSERT INTO Countries VALUES ('FI',1,'Finland');
INSERT INTO Countries VALUES ('FR',1,'France');
INSERT INTO Countries VALUES ('FX',1,'France, Metropolitan');
INSERT INTO Countries VALUES ('GF',1,'French Guiana');
INSERT INTO Countries VALUES ('PF',1,'French Polynesia');
INSERT INTO Countries VALUES ('TF',1,'French Southern Territories');
INSERT INTO Countries VALUES ('GA',1,'Gabon');
INSERT INTO Countries VALUES ('GM',1,'Gambia');
INSERT INTO Countries VALUES ('GE',1,'Georgia');
INSERT INTO Countries VALUES ('DE',1,'Germany');
INSERT INTO Countries VALUES ('GH',1,'Ghana');
INSERT INTO Countries VALUES ('GI',1,'Gibraltar');
INSERT INTO Countries VALUES ('GR',1,'Greece');
INSERT INTO Countries VALUES ('GL',1,'Greenland');
INSERT INTO Countries VALUES ('GD',1,'Grenada');
INSERT INTO Countries VALUES ('GP',1,'Guadeloupe');
INSERT INTO Countries VALUES ('GU',1,'Guam');
INSERT INTO Countries VALUES ('GT',1,'Guatemala');
INSERT INTO Countries VALUES ('GN',1,'Guinea');
INSERT INTO Countries VALUES ('GW',1,'Guinea-bissau');
INSERT INTO Countries VALUES ('GY',1,'Guyana');
INSERT INTO Countries VALUES ('HT',1,'Haiti');
INSERT INTO Countries VALUES ('HM',1,'Heard Island And Mcdonald Islands');
INSERT INTO Countries VALUES ('VA',1,'Holy See (Vatican City State)');
INSERT INTO Countries VALUES ('HN',1,'Honduras');
INSERT INTO Countries VALUES ('HK',1,'Hong Kong');
INSERT INTO Countries VALUES ('HU',1,'Hungary');
INSERT INTO Countries VALUES ('IS',1,'Iceland');
INSERT INTO Countries VALUES ('IN',1,'India');
INSERT INTO Countries VALUES ('ID',1,'Indonesia');
INSERT INTO Countries VALUES ('IR',1,'Iran, Islamic Republic Of');
INSERT INTO Countries VALUES ('IQ',1,'Iraq');
INSERT INTO Countries VALUES ('IE',1,'Ireland');
INSERT INTO Countries VALUES ('IL',1,'Israel');
INSERT INTO Countries VALUES ('IT',1,'Italy');
INSERT INTO Countries VALUES ('JM',1,'Jamaica');
INSERT INTO Countries VALUES ('JP',1,'Japan');
INSERT INTO Countries VALUES ('JO',1,'Jordan');
INSERT INTO Countries VALUES ('KZ',1,'Kazakstan');
INSERT INTO Countries VALUES ('KE',1,'Kenya');
INSERT INTO Countries VALUES ('KI',1,'Kiribati');
INSERT INTO Countries VALUES ('KP',1,'Korea, Democratic Peoples Republic Of');
INSERT INTO Countries VALUES ('KR',1,'Korea, Republic Of');
INSERT INTO Countries VALUES ('KW',1,'Kuwait');
INSERT INTO Countries VALUES ('KG',1,'Kyrgyzstan');
INSERT INTO Countries VALUES ('LA',1,'Lao Peoples Democratic Republic');
INSERT INTO Countries VALUES ('LV',1,'Latvia');
INSERT INTO Countries VALUES ('LB',1,'Lebanon');
INSERT INTO Countries VALUES ('LS',1,'Lesotho');
INSERT INTO Countries VALUES ('LR',1,'Liberia');
INSERT INTO Countries VALUES ('LY',1,'Libyan Arab Jamahiriya');
INSERT INTO Countries VALUES ('LI',1,'Liechtenstein');
INSERT INTO Countries VALUES ('LT',1,'Lithuania');
INSERT INTO Countries VALUES ('LU',1,'Luxembourg');
INSERT INTO Countries VALUES ('MO',1,'Macau');
INSERT INTO Countries VALUES ('MK',1,'Macedonia, The Former Yugoslav Republic Of');
INSERT INTO Countries VALUES ('MG',1,'Madagascar');
INSERT INTO Countries VALUES ('MW',1,'Malawi');
INSERT INTO Countries VALUES ('MY',1,'Malaysia');
INSERT INTO Countries VALUES ('MV',1,'Maldives');
INSERT INTO Countries VALUES ('ML',1,'Mali');
INSERT INTO Countries VALUES ('MT',1,'Malta');
INSERT INTO Countries VALUES ('MH',1,'Marshall Islands');
INSERT INTO Countries VALUES ('MQ',1,'Martinique');
INSERT INTO Countries VALUES ('MR',1,'Mauritania');
INSERT INTO Countries VALUES ('MU',1,'Mauritius');
INSERT INTO Countries VALUES ('YT',1,'Mayotte');
INSERT INTO Countries VALUES ('MX',1,'Mexico');
INSERT INTO Countries VALUES ('FM',1,'Micronesia, Federated States Of');
INSERT INTO Countries VALUES ('MD',1,'Moldova, Republic Of');
INSERT INTO Countries VALUES ('MC',1,'Monaco');
INSERT INTO Countries VALUES ('MN',1,'Mongolia');
INSERT INTO Countries VALUES ('MS',1,'Montserrat');
INSERT INTO Countries VALUES ('MA',1,'Morocco');
INSERT INTO Countries VALUES ('MZ',1,'Mozambique');
INSERT INTO Countries VALUES ('MM',1,'Myanmar');
INSERT INTO Countries VALUES ('NA',1,'Namibia');
INSERT INTO Countries VALUES ('NR',1,'Nauru');
INSERT INTO Countries VALUES ('NP',1,'Nepal');
INSERT INTO Countries VALUES ('NL',1,'Netherlands');
INSERT INTO Countries VALUES ('AN',1,'Netherlands Antilles');
INSERT INTO Countries VALUES ('NC',1,'New Caledonia');
INSERT INTO Countries VALUES ('NZ',1,'New Zealand');
INSERT INTO Countries VALUES ('NI',1,'Nicaragua');
INSERT INTO Countries VALUES ('NE',1,'Niger');
INSERT INTO Countries VALUES ('NG',1,'Nigeria');
INSERT INTO Countries VALUES ('NU',1,'Niue');
INSERT INTO Countries VALUES ('NF',1,'Norfolk Island');
INSERT INTO Countries VALUES ('MP',1,'Northern Mariana Islands');
INSERT INTO Countries VALUES ('NO',1,'Norway');
INSERT INTO Countries VALUES ('OM',1,'Oman');
INSERT INTO Countries VALUES ('PK',1,'Pakistan');
INSERT INTO Countries VALUES ('PW',1,'Palau');
INSERT INTO Countries VALUES ('PS',1,'Palestinian Territory, Occupied');
INSERT INTO Countries VALUES ('PA',1,'Panama');
INSERT INTO Countries VALUES ('PG',1,'Papua New Guinea');
INSERT INTO Countries VALUES ('PY',1,'Paraguay');
INSERT INTO Countries VALUES ('PE',1,'Peru');
INSERT INTO Countries VALUES ('PH',1,'Philippines');
INSERT INTO Countries VALUES ('PN',1,'Pitcairn');
INSERT INTO Countries VALUES ('PL',1,'Poland');
INSERT INTO Countries VALUES ('PT',1,'Portugal');
INSERT INTO Countries VALUES ('PR',1,'Puerto Rico');
INSERT INTO Countries VALUES ('QA',1,'Qatar');
INSERT INTO Countries VALUES ('RE',1,'Reunion');
INSERT INTO Countries VALUES ('RO',1,'Romania');
INSERT INTO Countries VALUES ('RU',1,'Russian Federation');
INSERT INTO Countries VALUES ('RW',1,'Rwanda');
INSERT INTO Countries VALUES ('SH',1,'Saint Helena');
INSERT INTO Countries VALUES ('KN',1,'Saint Kitts And Nevis');
INSERT INTO Countries VALUES ('LC',1,'Saint Lucia');
INSERT INTO Countries VALUES ('PM',1,'Saint Pierre And Miquelon');
INSERT INTO Countries VALUES ('VC',1,'Saint Vincent And The Grenadines');
INSERT INTO Countries VALUES ('WS',1,'Samoa');
INSERT INTO Countries VALUES ('SM',1,'San Marino');
INSERT INTO Countries VALUES ('ST',1,'Sao Tome And Principe');
INSERT INTO Countries VALUES ('SA',1,'Saudi Arabia');
INSERT INTO Countries VALUES ('SN',1,'Senegal');
INSERT INTO Countries VALUES ('SC',1,'Seychelles');
INSERT INTO Countries VALUES ('SL',1,'Sierra Leone');
INSERT INTO Countries VALUES ('SG',1,'Singapore');
INSERT INTO Countries VALUES ('SK',1,'Slovakia');
INSERT INTO Countries VALUES ('SI',1,'Slovenia');
INSERT INTO Countries VALUES ('SB',1,'Solomon Islands');
INSERT INTO Countries VALUES ('SO',1,'Somalia');
INSERT INTO Countries VALUES ('ZA',1,'South Africa');
INSERT INTO Countries VALUES ('GS',1,'South Georgia And The South Sandwich Islands');
INSERT INTO Countries VALUES ('ES',1,'Spain');
INSERT INTO Countries VALUES ('LK',1,'Sri Lanka');
INSERT INTO Countries VALUES ('SD',1,'Sudan');
INSERT INTO Countries VALUES ('SR',1,'Suriname');
INSERT INTO Countries VALUES ('SJ',1,'Svalbard And Jan Mayen');
INSERT INTO Countries VALUES ('SZ',1,'Swaziland');
INSERT INTO Countries VALUES ('SE',1,'Sweden');
INSERT INTO Countries VALUES ('CH',1,'Switzerland');
INSERT INTO Countries VALUES ('SY',1,'Syrian Arab Republic');
INSERT INTO Countries VALUES ('TW',1,'Taiwan, Province Of China');
INSERT INTO Countries VALUES ('TJ',1,'Tajikistan');
INSERT INTO Countries VALUES ('TZ',1,'Tanzania, United Republic Of');
INSERT INTO Countries VALUES ('TH',1,'Thailand');
INSERT INTO Countries VALUES ('TG',1,'Togo');
INSERT INTO Countries VALUES ('TK',1,'Tokelau');
INSERT INTO Countries VALUES ('TO',1,'Tonga');
INSERT INTO Countries VALUES ('TT',1,'Trinidad And Tobago');
INSERT INTO Countries VALUES ('TN',1,'Tunisia');
INSERT INTO Countries VALUES ('TR',1,'Turkey');
INSERT INTO Countries VALUES ('TM',1,'Turkmenistan');
INSERT INTO Countries VALUES ('TC',1,'Turks And Caicos Islands');
INSERT INTO Countries VALUES ('TV',1,'Tuvalu');
INSERT INTO Countries VALUES ('UG',1,'Uganda');
INSERT INTO Countries VALUES ('UA',1,'Ukraine');
INSERT INTO Countries VALUES ('AE',1,'United Arab Emirates');
INSERT INTO Countries VALUES ('GB',1,'United Kingdom');
INSERT INTO Countries VALUES ('US',1,'United States');
INSERT INTO Countries VALUES ('UM',1,'United States Minor Outlying Islands');
INSERT INTO Countries VALUES ('UY',1,'Uruguay');
INSERT INTO Countries VALUES ('UZ',1,'Uzbekistan');
INSERT INTO Countries VALUES ('VU',1,'Vanuatu');
INSERT INTO Countries VALUES ('VE',1,'Venezuela');
INSERT INTO Countries VALUES ('VN',1,'Vietnam');
INSERT INTO Countries VALUES ('VG',1,'Virgin Islands, British');
INSERT INTO Countries VALUES ('VI',1,'Virgin Islands, U.S.');
INSERT INTO Countries VALUES ('WF',1,'Wallis And Futuna');
INSERT INTO Countries VALUES ('EH',1,'Western Sahara');
INSERT INTO Countries VALUES ('YE',1,'Yemen');
INSERT INTO Countries VALUES ('YU',1,'Yugoslavia');
INSERT INTO Countries VALUES ('ZM',1,'Zambia');
INSERT INTO Countries VALUES ('ZW',1,'Zimbabwe');
INSERT INTO Countries VALUES ('GB',2,'Marea Britanie');
INSERT INTO Countries VALUES ('RO',2,'România');
INSERT INTO Countries VALUES ('CZ',2,'Republica Cehă');
INSERT INTO Countries VALUES ('YU',4,'Jugoslavija');

#
# Table structure for table 'Dictionary'
#

CREATE TABLE Dictionary (
  Id int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Keyword varchar(140) NOT NULL default '',
  PRIMARY KEY  (IdLanguage,Keyword),
  UNIQUE KEY Id (Id,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Dictionary'
#


#
# Table structure for table 'Errors'
#

CREATE TABLE Errors (
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Message char(255) NOT NULL default '',
  PRIMARY KEY  (Number,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Errors'
#

INSERT INTO Errors VALUES (4000,1,'Internal error.');
INSERT INTO Errors VALUES (4001,1,'Username not specified.');
INSERT INTO Errors VALUES (4002,1,'Invalid username.');
INSERT INTO Errors VALUES (4003,1,'Password not specified.');
INSERT INTO Errors VALUES (4004,1,'Invalid password.');
INSERT INTO Errors VALUES (2000,1,'Internal error');
INSERT INTO Errors VALUES (2001,1,'Username is not specified. Please fill out login name field.');
INSERT INTO Errors VALUES (2002,1,'You are not a reader.');
INSERT INTO Errors VALUES (2003,1,'Publication not specified.');
INSERT INTO Errors VALUES (2004,1,'There are other subscriptions not payed.');
INSERT INTO Errors VALUES (2005,1,'Time unit not specified.');
INSERT INTO Errors VALUES (3000,1,'Internal error.');
INSERT INTO Errors VALUES (3001,1,'Username already exists.');
INSERT INTO Errors VALUES (3002,1,'Name is not specified. Please fill out name field.');
INSERT INTO Errors VALUES (3003,1,'Username is not specified. Please fill out login name field.');
INSERT INTO Errors VALUES (3004,1,'Password is not specified. Please fill out password field.');
INSERT INTO Errors VALUES (3005,1,'EMail is not specified. Please fill out EMail field.');
INSERT INTO Errors VALUES (3006,1,'EMail address already exists. Please try to login with your old account.');
INSERT INTO Errors VALUES (3007,1,'Invalid user identifier');
INSERT INTO Errors VALUES (3008,1,'No country specified. Please select a country.');
INSERT INTO Errors VALUES (3009,1,'Password (again) is not specified. Please fill out password (again) field.');
INSERT INTO Errors VALUES (3010,1,'Passwords do not match. Please fill out the same password to both password fields.');
INSERT INTO Errors VALUES (3011,1,'Password is too simple. Please choose a better password (at least 6 characters).');

#
# Table structure for table 'Events'
#

CREATE TABLE Events (
  Id int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  Notify enum('N','Y') NOT NULL default 'N',
  IdLanguage int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id,IdLanguage),
  UNIQUE KEY Name (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Events'
#

INSERT INTO Events VALUES (1,'Add Publication','N',1);
INSERT INTO Events VALUES (2,'Delete Publication','N',1);
INSERT INTO Events VALUES (11,'Add Issue','N',1);
INSERT INTO Events VALUES (12,'Delete Issue','N',1);
INSERT INTO Events VALUES (13,'Change Issue Template','N',1);
INSERT INTO Events VALUES (14,'Change issue status','N',1);
INSERT INTO Events VALUES (15,'Add Issue Translation','N',1);
INSERT INTO Events VALUES (21,'Add Section','N',1);
INSERT INTO Events VALUES (22,'Delete section','N',1);
INSERT INTO Events VALUES (31,'Add Article','Y',1);
INSERT INTO Events VALUES (32,'Delete article','N',1);
INSERT INTO Events VALUES (33,'Change article field','N',1);
INSERT INTO Events VALUES (34,'Change article properties','N',1);
INSERT INTO Events VALUES (35,'Change article status','Y',1);
INSERT INTO Events VALUES (41,'Add Image','Y',1);
INSERT INTO Events VALUES (42,'Delete image','N',1);
INSERT INTO Events VALUES (43,'Change image properties','N',1);
INSERT INTO Events VALUES (51,'Add User','N',1);
INSERT INTO Events VALUES (52,'Delete User','N',1);
INSERT INTO Events VALUES (53,'Changes Own Password','N',1);
INSERT INTO Events VALUES (54,'Change User Password','N',1);
INSERT INTO Events VALUES (55,'Change User Permissions','N',1);
INSERT INTO Events VALUES (56,'Change user information','N',1);
INSERT INTO Events VALUES (61,'Add article type','N',1);
INSERT INTO Events VALUES (62,'Delete article type','N',1);
INSERT INTO Events VALUES (71,'Add article type field','N',1);
INSERT INTO Events VALUES (72,'Delete article type field','N',1);
INSERT INTO Events VALUES (81,'Add dictionary class','N',1);
INSERT INTO Events VALUES (82,'Delete dictionary class','N',1);
INSERT INTO Events VALUES (91,'Add dictionary keyword','N',1);
INSERT INTO Events VALUES (92,'Delete dictionary keyword','N',1);
INSERT INTO Events VALUES (101,'Add language','N',1);
INSERT INTO Events VALUES (102,'Delete language','N',1);
INSERT INTO Events VALUES (103,'Modify language','N',1);
INSERT INTO Events VALUES (112,'Delete templates','N',1);
INSERT INTO Events VALUES (111,'Add templates','N',1);
INSERT INTO Events VALUES (121,'Add user type','N',1);
INSERT INTO Events VALUES (122,'Delete user type','N',1);
INSERT INTO Events VALUES (123,'Change user type','N',1);
INSERT INTO Events VALUES (3,'Change publication information','N',1);
INSERT INTO Events VALUES (36,'Change article template','N',1);
INSERT INTO Events VALUES (57,'Add IP Group','N',1);
INSERT INTO Events VALUES (58,'Delete IP Group','N',1);
INSERT INTO Events VALUES (131,'Add country','N',1);
INSERT INTO Events VALUES (132,'Add country translation','N',1);
INSERT INTO Events VALUES (133,'Change country name','N',1);
INSERT INTO Events VALUES (134,'Delete country','N',1);
INSERT INTO Events VALUES (4,'Add default subscription time','N',1);
INSERT INTO Events VALUES (5,'Delete default subscription time','N',1);
INSERT INTO Events VALUES (6,'Change default subscription time','N',1);
INSERT INTO Events VALUES (113,'Edit template','N',1);
INSERT INTO Events VALUES (114,'Create template','N',1);
INSERT INTO Events VALUES (115,'Duplicate template','N',1);
INSERT INTO Events VALUES (141,'Add topic','N',1);
INSERT INTO Events VALUES (142,'Delete topic','N',1);
INSERT INTO Events VALUES (143,'Update topic','N',1);
INSERT INTO Events VALUES (144,'Add topic to article','N',1);
INSERT INTO Events VALUES (145,'Delete topic from article','N',1);

#
# Table structure for table 'Images'
#

CREATE TABLE Images (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  NrArticle int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  Description varchar(255) NOT NULL default '',
  Photographer varchar(140) NOT NULL default '',
  Place varchar(140) NOT NULL default '',
  Date date NOT NULL default '0000-00-00',
  ContentType varchar(64) NOT NULL default '',
  Image mediumblob NOT NULL,
  PRIMARY KEY  (IdPublication,NrIssue,NrSection,NrArticle,Number)
) TYPE=MyISAM;

#
# Dumping data for table 'Images'
#


#
# Table structure for table 'Issues'
#

CREATE TABLE Issues (
  IdPublication int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PublicationDate date NOT NULL default '0000-00-00',
  Published enum('N','Y') NOT NULL default 'N',
  FrontPage varchar(128) NOT NULL default '',
  SingleArticle varchar(128) NOT NULL default '',
  PRIMARY KEY  (IdPublication,Number,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Issues'
#


#
# Table structure for table 'KeywordClasses'
#

CREATE TABLE KeywordClasses (
  IdDictionary int(10) unsigned NOT NULL default '0',
  IdClasses int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Definition mediumblob NOT NULL,
  PRIMARY KEY  (IdDictionary,IdClasses,IdLanguage),
  KEY IdClasses (IdClasses)
) TYPE=MyISAM;

#
# Dumping data for table 'KeywordClasses'
#


#
# Table structure for table 'KeywordIndex'
#

CREATE TABLE KeywordIndex (
  Keyword varchar(70) NOT NULL default '',
  Id int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Keyword)
) TYPE=MyISAM;

#
# Dumping data for table 'KeywordIndex'
#


#
# Table structure for table 'Languages'
#

CREATE TABLE Languages (
  Id int(10) unsigned NOT NULL auto_increment,
  Name varchar(140) NOT NULL default '',
  CodePage varchar(140) NOT NULL default '',
  OrigName varchar(140) NOT NULL default '',
  Code varchar(21) NOT NULL default '',
  Month1 varchar(140) NOT NULL default '',
  Month2 varchar(140) NOT NULL default '',
  Month3 varchar(140) NOT NULL default '',
  Month4 varchar(140) NOT NULL default '',
  Month5 varchar(140) NOT NULL default '',
  Month6 varchar(140) NOT NULL default '',
  Month7 varchar(140) NOT NULL default '',
  Month8 varchar(140) NOT NULL default '',
  Month9 varchar(140) NOT NULL default '',
  Month10 varchar(140) NOT NULL default '',
  Month11 varchar(140) NOT NULL default '',
  Month12 varchar(140) NOT NULL default '',
  WDay1 varchar(140) NOT NULL default '',
  WDay2 varchar(140) NOT NULL default '',
  WDay3 varchar(140) NOT NULL default '',
  WDay4 varchar(140) NOT NULL default '',
  WDay5 varchar(140) NOT NULL default '',
  WDay6 varchar(140) NOT NULL default '',
  WDay7 varchar(140) NOT NULL default '',
  PRIMARY KEY  (Id),
  UNIQUE KEY Name (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Languages'
#

INSERT INTO Languages VALUES (1,'English','ISO_8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
INSERT INTO Languages VALUES (5,'German','ISO_8859-1','Deutsch','de','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (6,'Austrian','IS0_8859-1','Deutsch (Österreich)','at','Jänner','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (9,'Portuguese','ISO_8859-1','Português','pt','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado');
INSERT INTO Languages VALUES (12,'French','ISO_8859-1','Français','fr','Janvier','Février','Mars','Avril','Peut','Juin','Juli','Août','Septembre','Octobre','Novembre','Décembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
INSERT INTO Languages VALUES (13,'Spanish','ISO_8859-1','Español','es','Enero','Febrero','Marcha','Abril','Puede','Junio','Juli','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
INSERT INTO Languages VALUES (14,'Italian','ISO_8859-1','Italiano','it','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre','Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');
INSERT INTO Languages VALUES (2,'Romanian','ISO_8859-2','Română','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminică','Luni','Marţi','Miercuri','Joi','Vineri','Sâmbătă');
INSERT INTO Languages VALUES (7,'Croatian','ISO_8859-2','Hrvatski','hr','Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota');
INSERT INTO Languages VALUES (8,'Czech','ISO_8859-2','Český','cz','Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec','Neděle','Pondělí','Úterý','Středa','Čtvrtek','Pátek','Sobota');
INSERT INTO Languages VALUES (11,'Bosnian','ISO_8859-2','Bosanski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota');
INSERT INTO Languages VALUES (10,'Sebian (Cyrillic)','ISO_8859-5','Српски (Ћирилица)','sr','јануар','фебруар','март','април','мај','јун','јул','август','септембар','октобар','новембар','децембар','Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота');
INSERT INTO Languages VALUES (15,'Russian','ISO_8859-5','Русский','ru','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь','воскресенье','понедельник','вторник','среда','четверг','пятница','суббота');
INSERT INTO Languages VALUES (3,'Hebrew','ISO_8859-9','øàè÷øàè','he','÷øà èâëéçâëòç ëòéç','ëòéç ëòé','çëòéçëòéå456','÷øàè÷øàèøà','ãëğãâëé','ñòéâëòé','âëòé','âëòéã÷øòùãâòùã','âùãâùã/\'ø÷øé','âëòéëòéç','éöéòúêçìêóçì','âëòéçéìòéç','åïíôåïíàèå','ïàèåïéçêúîöúõîöúî','äğîáäğîäğá','æñáäãùãâë','ëòéëòéçòéìêì','éçìêéçíåèïíè','÷øàè÷øàè');

#
# Table structure for table 'Log'
#

CREATE TABLE Log (
  TStamp datetime NOT NULL default '0000-00-00 00:00:00',
  IdEvent int(10) unsigned NOT NULL default '0',
  User varchar(70) NOT NULL default '',
  Text varchar(255) NOT NULL default '',
  KEY IdEvent (IdEvent)
) TYPE=MyISAM;

#
# Dumping data for table 'Log'
#


#
# Table structure for table 'Publications'
#

CREATE TABLE Publications (
  Id int(10) unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  Site varchar(255) NOT NULL default '',
  IdDefaultLanguage int(10) unsigned NOT NULL default '0',
  PayTime int(10) unsigned NOT NULL default '0',
  TimeUnit enum('D','W','M','Y') NOT NULL default 'D',
  UnitCost float(10,2) unsigned NOT NULL default '0.00',
  Currency varchar(140) NOT NULL default '',
  TrialTime int(10) unsigned NOT NULL default '0',
  PaidTime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id),
  UNIQUE KEY Name (Name),
  UNIQUE KEY Site (Site)
) TYPE=MyISAM;

#
# Dumping data for table 'Publications'
#


#
# Table structure for table 'Sections'
#

CREATE TABLE Sections (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  PRIMARY KEY  (IdPublication,NrIssue,IdLanguage,Number),
  UNIQUE KEY IdPublication (IdPublication,NrIssue,IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Sections'
#


#
# Table structure for table 'SubsByIP'
#

CREATE TABLE SubsByIP (
  IdUser int(10) unsigned NOT NULL default '0',
  StartIP int(10) unsigned NOT NULL default '0',
  Addresses int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdUser,StartIP)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsByIP'
#


#
# Table structure for table 'SubsDefTime'
#

CREATE TABLE SubsDefTime (
  CountryCode char(21) NOT NULL default '',
  IdPublication int(10) unsigned NOT NULL default '0',
  TrialTime int(10) unsigned NOT NULL default '0',
  PaidTime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (CountryCode,IdPublication)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsDefTime'
#


#
# Table structure for table 'SubsSections'
#

CREATE TABLE SubsSections (
  IdSubscription int(10) unsigned NOT NULL default '0',
  SectionNumber int(10) unsigned NOT NULL default '0',
  StartDate date NOT NULL default '0000-00-00',
  Days int(10) unsigned NOT NULL default '0',
  PaidDays int(10) unsigned NOT NULL default '0',
  NoticeSent enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (IdSubscription,SectionNumber)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsSections'
#


#
# Table structure for table 'Subscriptions'
#

CREATE TABLE Subscriptions (
  Id int(10) unsigned NOT NULL auto_increment,
  IdUser int(10) unsigned NOT NULL default '0',
  IdPublication int(10) unsigned NOT NULL default '0',
  Active enum('Y','N') NOT NULL default 'Y',
  ToPay float(10,2) unsigned NOT NULL default '0.00',
  Currency varchar(70) NOT NULL default '',
  Type enum('T','P') NOT NULL default 'T',
  PRIMARY KEY  (Id),
  UNIQUE KEY IdUser (IdUser,IdPublication)
) TYPE=MyISAM;

#
# Dumping data for table 'Subscriptions'
#


#
# Table structure for table 'TimeUnits'
#

CREATE TABLE TimeUnits (
  Unit char(1) NOT NULL default '',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(70) NOT NULL default '',
  PRIMARY KEY  (Unit,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'TimeUnits'
#

INSERT INTO TimeUnits VALUES ('D',1,'days');
INSERT INTO TimeUnits VALUES ('W',1,'weeks');
INSERT INTO TimeUnits VALUES ('M',1,'months');
INSERT INTO TimeUnits VALUES ('Y',1,'years');

#
# Table structure for table 'Topics'
#

CREATE TABLE Topics (
  Id int(10) unsigned NOT NULL default '0',
  LanguageId int(10) unsigned NOT NULL default '0',
  Name varchar(100) NOT NULL default '',
  ParentId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id,LanguageId),
  UNIQUE KEY Name (LanguageId,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Topics'
#


#
# Table structure for table 'UserPerm'
#

CREATE TABLE UserPerm (
  IdUser int(10) unsigned NOT NULL default '0',
  ManagePub enum('N','Y') NOT NULL default 'N',
  DeletePub enum('N','Y') NOT NULL default 'N',
  ManageIssue enum('N','Y') NOT NULL default 'N',
  DeleteIssue enum('N','Y') NOT NULL default 'N',
  ManageSection enum('N','Y') NOT NULL default 'N',
  DeleteSection enum('N','Y') NOT NULL default 'N',
  AddArticle enum('N','Y') NOT NULL default 'N',
  ChangeArticle enum('N','Y') NOT NULL default 'N',
  DeleteArticle enum('N','Y') NOT NULL default 'N',
  AddImage enum('N','Y') NOT NULL default 'N',
  ChangeImage enum('N','Y') NOT NULL default 'N',
  DeleteImage enum('N','Y') NOT NULL default 'N',
  ManageTempl enum('N','Y') NOT NULL default 'N',
  DeleteTempl enum('N','Y') NOT NULL default 'N',
  ManageUsers enum('N','Y') NOT NULL default 'N',
  ManageSubscriptions enum('N','Y') NOT NULL default 'N',
  DeleteUsers enum('N','Y') NOT NULL default 'N',
  ManageUserTypes enum('N','Y') NOT NULL default 'N',
  ManageArticleTypes enum('N','Y') NOT NULL default 'N',
  DeleteArticleTypes enum('N','Y') NOT NULL default 'N',
  ManageLanguages enum('N','Y') NOT NULL default 'N',
  DeleteLanguages enum('N','Y') NOT NULL default 'N',
  ManageDictionary enum('N','Y') NOT NULL default 'N',
  DeleteDictionary enum('N','Y') NOT NULL default 'N',
  ManageCountries enum('N','Y') NOT NULL default 'N',
  DeleteCountries enum('N','Y') NOT NULL default 'N',
  ManageClasses enum('N','Y') NOT NULL default 'N',
  MailNotify enum('N','Y') NOT NULL default 'N',
  ViewLogs enum('N','Y') NOT NULL default 'N',
  ManageLocalizer enum('N','Y') NOT NULL default 'N',
  ManageIndexer enum('N','Y') NOT NULL default 'N',
  Publish enum('N','Y') NOT NULL default 'N',
  ManageTopics enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (IdUser)
) TYPE=MyISAM;

#
# Dumping data for table 'UserPerm'
#

INSERT INTO UserPerm VALUES (1,'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','Y','Y','Y','Y','Y','Y');

#
# Table structure for table 'UserTypes'
#

CREATE TABLE UserTypes (
  Name varchar(140) NOT NULL default '',
  Reader enum('N','Y') NOT NULL default 'N',
  ManagePub enum('N','Y') NOT NULL default 'N',
  DeletePub enum('N','Y') NOT NULL default 'N',
  ManageIssue enum('N','Y') NOT NULL default 'N',
  DeleteIssue enum('N','Y') NOT NULL default 'N',
  ManageSection enum('N','Y') NOT NULL default 'N',
  DeleteSection enum('N','Y') NOT NULL default 'N',
  AddArticle enum('N','Y') NOT NULL default 'N',
  ChangeArticle enum('N','Y') NOT NULL default 'N',
  DeleteArticle enum('N','Y') NOT NULL default 'N',
  AddImage enum('N','Y') NOT NULL default 'N',
  ChangeImage enum('N','Y') NOT NULL default 'N',
  DeleteImage enum('N','Y') NOT NULL default 'N',
  ManageTempl enum('N','Y') NOT NULL default 'N',
  DeleteTempl enum('N','Y') NOT NULL default 'N',
  ManageUsers enum('N','Y') NOT NULL default 'N',
  ManageSubscriptions enum('N','Y') NOT NULL default 'N',
  DeleteUsers enum('N','Y') NOT NULL default 'N',
  ManageUserTypes enum('N','Y') NOT NULL default 'N',
  ManageArticleTypes enum('N','Y') NOT NULL default 'N',
  DeleteArticleTypes enum('N','Y') NOT NULL default 'N',
  ManageLanguages enum('N','Y') NOT NULL default 'N',
  DeleteLanguages enum('N','Y') NOT NULL default 'N',
  ManageDictionary enum('N','Y') NOT NULL default 'N',
  DeleteDictionary enum('N','Y') NOT NULL default 'N',
  ManageCountries enum('N','Y') NOT NULL default 'N',
  DeleteCountries enum('N','Y') NOT NULL default 'N',
  ManageClasses enum('N','Y') NOT NULL default 'N',
  MailNotify enum('N','Y') NOT NULL default 'N',
  ViewLogs enum('N','Y') NOT NULL default 'N',
  ManageLocalizer enum('N','Y') NOT NULL default 'N',
  ManageIndexer enum('N','Y') NOT NULL default 'N',
  Publish enum('N','Y') NOT NULL default 'N',
  ManageTopics enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'UserTypes'
#

INSERT INTO UserTypes VALUES ('Reader','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N');
INSERT INTO UserTypes VALUES ('Administrator','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','N','Y','Y','N','Y','Y');
INSERT INTO UserTypes VALUES ('Editor','N','N','N','N','N','N','N','Y','Y','Y','Y','Y','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','Y','N','N','N','N','N');
INSERT INTO UserTypes VALUES ('Chief Editor','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N','Y','Y','N','N','N','N','N','N','N','N','Y','Y','Y','N','Y');

#
# Table structure for table 'Users'
#

CREATE TABLE Users (
  Id int(10) unsigned NOT NULL auto_increment,
  KeyId int(10) unsigned default NULL,
  Name varchar(255) NOT NULL default '',
  UName varchar(70) NOT NULL default '',
  Password varchar(32) NOT NULL default '',
  EMail varchar(255) NOT NULL default '',
  Reader enum('Y','N') NOT NULL default 'Y',
  City varchar(100) NOT NULL default '',
  StrAddress varchar(255) NOT NULL default '',
  State varchar(32) NOT NULL default '',
  CountryCode varchar(21) NOT NULL default '',
  Phone varchar(20) NOT NULL default '',
  Fax varchar(20) NOT NULL default '',
  Contact varchar(64) NOT NULL default '',
  Phone2 varchar(20) NOT NULL default '',
  Title enum('Mr.','Mrs.','Ms.','Dr.') NOT NULL default 'Mr.',
  Gender enum('M','F') NOT NULL default 'M',
  Age enum('0-17','18-24','25-39','40-49','50-65','65-') NOT NULL default '0-17',
  PostalCode varchar(70) NOT NULL default '',
  Employer varchar(140) NOT NULL default '',
  EmployerType varchar(140) NOT NULL default '',
  Position varchar(70) NOT NULL default '',
  Interests mediumblob NOT NULL,
  How varchar(255) NOT NULL default '',
  Languages varchar(100) NOT NULL default '',
  Improvements mediumblob NOT NULL,
  Pref1 enum('N','Y') NOT NULL default 'N',
  Pref2 enum('N','Y') NOT NULL default 'N',
  Pref3 enum('N','Y') NOT NULL default 'N',
  Pref4 enum('N','Y') NOT NULL default 'N',
  Field1 varchar(150) NOT NULL default '',
  Field2 varchar(150) NOT NULL default '',
  Field3 varchar(150) NOT NULL default '',
  Field4 varchar(150) NOT NULL default '',
  Field5 varchar(150) NOT NULL default '',
  Text1 mediumblob NOT NULL,
  Text2 mediumblob NOT NULL,
  Text3 mediumblob NOT NULL,
  PRIMARY KEY  (Id),
  UNIQUE KEY UName (UName)
) TYPE=MyISAM;

#
# Dumping data for table 'Users'
#

INSERT INTO Users VALUES (1,849663625,'Administrator','admin','2c380f066e0e45d1','','N','','','','','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','');
