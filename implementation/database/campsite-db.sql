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

# MySQL dump 8.8
#
# Host: localhost    Database: campsite
#--------------------------------------------------------
# Server version	3.23.23-beta-log

#
# Table structure for table 'ArticleIndex'
#

CREATE TABLE ArticleIndex (
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  IdKeyword int(10) unsigned DEFAULT '0' NOT NULL,
  NrIssue int(10) unsigned DEFAULT '0' NOT NULL,
  NrSection int(10) unsigned DEFAULT '0' NOT NULL,
  NrArticle int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdPublication,IdLanguage,IdKeyword,NrIssue,NrSection,NrArticle)
);

#
# Dumping data for table 'ArticleIndex'
#


#
# Table structure for table 'Articles'
#

CREATE TABLE Articles (
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  NrIssue int(10) unsigned DEFAULT '0' NOT NULL,
  NrSection int(10) unsigned DEFAULT '0' NOT NULL,
  Number int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(64) DEFAULT '' NOT NULL,
  Type char(15) DEFAULT '' NOT NULL,
  IdUser int(10) unsigned DEFAULT '0' NOT NULL,
  OnFrontPage enum('N','Y') DEFAULT 'N' NOT NULL,
  OnSection enum('N','Y') DEFAULT 'N' NOT NULL,
  Published enum('N','S','Y') DEFAULT 'N' NOT NULL,
  UploadDate date DEFAULT '0000-00-00' NOT NULL,
  Keywords char(255) DEFAULT '' NOT NULL,
  Public enum('N','Y') DEFAULT 'N' NOT NULL,
  IsIndexed enum('N','Y') DEFAULT 'N' NOT NULL,
  LockUser int(10) unsigned DEFAULT '0' NOT NULL,
  LockTime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY (IdPublication,NrIssue,NrSection,Number,IdLanguage),
  KEY Type (Type),
  UNIQUE other_key (IdPublication,NrIssue,NrSection,IdLanguage,Number),
  UNIQUE Number (Number,IdLanguage),
  UNIQUE IdPublication (IdPublication,NrIssue,NrSection,IdLanguage,Name)
);

#
# Dumping data for table 'Articles'
#


#
# Table structure for table 'AutoId'
#

CREATE TABLE AutoId (
  DictionaryId int(10) unsigned DEFAULT '0' NOT NULL,
  ClassId int(10) unsigned DEFAULT '0' NOT NULL,
  ArticleId int(10) unsigned DEFAULT '0' NOT NULL,
  KeywordId int(10) unsigned DEFAULT '0' NOT NULL,
  LogTStamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL
);

#
# Dumping data for table 'AutoId'
#

INSERT INTO AutoId VALUES (0,0,0,0,'0000-00-00 00:00:00');

#
# Table structure for table 'Classes'
#

CREATE TABLE Classes (
  Id int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(64) DEFAULT '' NOT NULL,
  PRIMARY KEY (Id,IdLanguage),
  UNIQUE IdLanguage (IdLanguage,Name)
);

#
# Dumping data for table 'Classes'
#


#
# Table structure for table 'Countries'
#

CREATE TABLE Countries (
  Code char(2) DEFAULT '' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(64) DEFAULT '' NOT NULL,
  PRIMARY KEY (Code,IdLanguage),
  UNIQUE IdLanguage (IdLanguage,Name)
);

#
# Dumping data for table 'Countries'
#

INSERT INTO Countries VALUES ('CZ',1,'Czech Republic');
INSERT INTO Countries VALUES ('US',1,'United States Of America');
INSERT INTO Countries VALUES ('GB',1,'Great Britain');
INSERT INTO Countries VALUES ('RO',1,'Romania');
INSERT INTO Countries VALUES ('GB',2,'Marea Britanie');
INSERT INTO Countries VALUES ('RO',2,'Rom‚nia');
INSERT INTO Countries VALUES ('CZ',2,'Republica Ceh„');
INSERT INTO Countries VALUES ('UA',1,'Ukraine');
INSERT INTO Countries VALUES ('YU',1,'Yugoslavia');
INSERT INTO Countries VALUES ('YU',4,'Jugoslavija');
INSERT INTO Countries VALUES ('DE',5,'Deutschland');
INSERT INTO Countries VALUES ('DE',1,'Germany');
INSERT INTO Countries VALUES ('AT',1,'Austria');
INSERT INTO Countries VALUES ('AT',6,'÷sterreich');
INSERT INTO Countries VALUES ('IT',1,'Italy');
INSERT INTO Countries VALUES ('IT',14,'Italia');
INSERT INTO Countries VALUES ('FR',1,'France');
INSERT INTO Countries VALUES ('FR',12,'France');
INSERT INTO Countries VALUES ('PT',1,'Portugal');
INSERT INTO Countries VALUES ('PT',9,'Portugal');
INSERT INTO Countries VALUES ('ES',1,'Spain');
INSERT INTO Countries VALUES ('ES',13,'EspaÒa');

#
# Table structure for table 'Dictionary'
#

CREATE TABLE Dictionary (
  Id int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Keyword char(64) DEFAULT '' NOT NULL,
  UNIQUE Id (Id,IdLanguage),
  PRIMARY KEY (IdLanguage,Keyword)
);

#
# Dumping data for table 'Dictionary'
#


#
# Table structure for table 'Errors'
#

CREATE TABLE Errors (
  Number int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Message char(255) DEFAULT '' NOT NULL,
  PRIMARY KEY (Number,IdLanguage)
);

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
  Id int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(32) DEFAULT '' NOT NULL,
  Notify enum('N','Y') DEFAULT 'N' NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE Name (Name)
);

#
# Dumping data for table 'Events'
#

INSERT INTO Events VALUES (1,'Add Publication','N');
INSERT INTO Events VALUES (2,'Delete Publication','N');
INSERT INTO Events VALUES (11,'Add Issue','N');
INSERT INTO Events VALUES (12,'Delete Issue','N');
INSERT INTO Events VALUES (13,'Change Issue Template','N');
INSERT INTO Events VALUES (14,'Change issue status','N');
INSERT INTO Events VALUES (15,'Add Issue Translation','N');
INSERT INTO Events VALUES (21,'Add Section','N');
INSERT INTO Events VALUES (22,'Delete section','N');
INSERT INTO Events VALUES (31,'Add Article','Y');
INSERT INTO Events VALUES (32,'Delete article','N');
INSERT INTO Events VALUES (33,'Change article field','N');
INSERT INTO Events VALUES (34,'Change article properties','N');
INSERT INTO Events VALUES (35,'Change article status','Y');
INSERT INTO Events VALUES (41,'Add Image','Y');
INSERT INTO Events VALUES (42,'Delete image','N');
INSERT INTO Events VALUES (43,'Change image properties','N');
INSERT INTO Events VALUES (51,'Add User','N');
INSERT INTO Events VALUES (52,'Delete User','N');
INSERT INTO Events VALUES (53,'Changes Own Password','N');
INSERT INTO Events VALUES (54,'Change User Password','N');
INSERT INTO Events VALUES (55,'Change User Permissions','N');
INSERT INTO Events VALUES (56,'Change user information','N');
INSERT INTO Events VALUES (61,'Add article type','N');
INSERT INTO Events VALUES (62,'Delete article type','N');
INSERT INTO Events VALUES (71,'Add article type field','N');
INSERT INTO Events VALUES (72,'Delete article type field','N');
INSERT INTO Events VALUES (81,'Add dictionary class','N');
INSERT INTO Events VALUES (82,'Delete dictionary class','N');
INSERT INTO Events VALUES (91,'Add dictionary keyword','N');
INSERT INTO Events VALUES (92,'Delete dictionary keyword','N');
INSERT INTO Events VALUES (101,'Add language','N');
INSERT INTO Events VALUES (102,'Delete language','N');
INSERT INTO Events VALUES (103,'Modify language','N');
INSERT INTO Events VALUES (112,'Delete templates','N');
INSERT INTO Events VALUES (111,'Add templates','N');
INSERT INTO Events VALUES (121,'Add user type','N');
INSERT INTO Events VALUES (122,'Delete user type','N');
INSERT INTO Events VALUES (123,'Change user type','N');
INSERT INTO Events VALUES (3,'Change publication information','N');
INSERT INTO Events VALUES (36,'Change article template','N');
INSERT INTO Events VALUES (57,'Add IP Group','N');
INSERT INTO Events VALUES (58,'Delete IP Group','N');
INSERT INTO Events VALUES (131,'Add country','N');
INSERT INTO Events VALUES (132,'Add country translation','N');
INSERT INTO Events VALUES (133,'Change country name','N');
INSERT INTO Events VALUES (134,'Delete country','N');
INSERT INTO Events VALUES (4,'Add default subscription time','N');
INSERT INTO Events VALUES (5,'Delete default subscription time','N');
INSERT INTO Events VALUES (6,'Change default subscription time','N');

#
# Table structure for table 'Images'
#

CREATE TABLE Images (
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  NrIssue int(10) unsigned DEFAULT '0' NOT NULL,
  NrSection int(10) unsigned DEFAULT '0' NOT NULL,
  NrArticle int(10) unsigned DEFAULT '0' NOT NULL,
  Number int(10) unsigned DEFAULT '0' NOT NULL,
  Description varchar(128) DEFAULT '' NOT NULL,
  Photographer varchar(64) DEFAULT '' NOT NULL,
  Place varchar(64) DEFAULT '' NOT NULL,
  Date date DEFAULT '0000-00-00' NOT NULL,
  ContentType varchar(64) DEFAULT '' NOT NULL,
  Image mediumblob DEFAULT '' NOT NULL,
  PRIMARY KEY (IdPublication,NrIssue,NrSection,NrArticle,Number)
);

#
# Dumping data for table 'Images'
#


#
# Table structure for table 'Issues'
#

CREATE TABLE Issues (
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  Number int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(64) DEFAULT '' NOT NULL,
  PublicationDate date DEFAULT '0000-00-00' NOT NULL,
  Published enum('N','Y') DEFAULT 'N' NOT NULL,
  FrontPage char(128) DEFAULT '' NOT NULL,
  SingleArticle char(128) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdPublication,Number,IdLanguage)
);

#
# Dumping data for table 'Issues'
#


#
# Table structure for table 'KeywordClasses'
#

CREATE TABLE KeywordClasses (
  IdDictionary int(10) unsigned DEFAULT '0' NOT NULL,
  IdClasses int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Definition mediumblob DEFAULT '' NOT NULL,
  KEY IdClasses (IdClasses),
  PRIMARY KEY (IdDictionary,IdClasses,IdLanguage)
);

#
# Dumping data for table 'KeywordClasses'
#


#
# Table structure for table 'KeywordIndex'
#

CREATE TABLE KeywordIndex (
  Keyword char(16) DEFAULT '' NOT NULL,
  Id int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (Keyword)
);

#
# Dumping data for table 'KeywordIndex'
#


#
# Table structure for table 'Languages'
#

CREATE TABLE Languages (
  Id int(10) unsigned NOT NULL auto_increment,
  Name char(32) DEFAULT '' NOT NULL,
  CodePage char(32) DEFAULT '' NOT NULL,
  OrigName char(32) DEFAULT '' NOT NULL,
  Code char(5) DEFAULT '' NOT NULL,
  Month1 char(20) DEFAULT '' NOT NULL,
  Month2 char(20) DEFAULT '' NOT NULL,
  Month3 char(20) DEFAULT '' NOT NULL,
  Month4 char(20) DEFAULT '' NOT NULL,
  Month5 char(20) DEFAULT '' NOT NULL,
  Month6 char(20) DEFAULT '' NOT NULL,
  Month7 char(20) DEFAULT '' NOT NULL,
  Month8 char(20) DEFAULT '' NOT NULL,
  Month9 char(20) DEFAULT '' NOT NULL,
  Month10 char(20) DEFAULT '' NOT NULL,
  Month11 char(20) DEFAULT '' NOT NULL,
  Month12 char(20) DEFAULT '' NOT NULL,
  WDay1 char(20) DEFAULT '' NOT NULL,
  WDay2 char(20) DEFAULT '' NOT NULL,
  WDay3 char(20) DEFAULT '' NOT NULL,
  WDay4 char(20) DEFAULT '' NOT NULL,
  WDay5 char(20) DEFAULT '' NOT NULL,
  WDay6 char(20) DEFAULT '' NOT NULL,
  WDay7 char(20) DEFAULT '' NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE Name (Name)
);

#
# Dumping data for table 'Languages'
#

INSERT INTO Languages VALUES (1,'English','ISO-8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
INSERT INTO Languages VALUES (2,'Romanian','ISO-8859-2','Rom‚n„','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminic„','Luni','Mar˛i','Miercuri','Joi','Vineri','S‚mb„t„');
INSERT INTO Languages VALUES (3,'Hebrew','ISO-8859-9','¯‡Ë˜¯‡Ë','he','˜¯‡ Ë‚ÎÈÁ‚ÎÚÁ ÎÚÈÁ','ÎÚÈÁ ÎÚÈ','ÁÎÚÈÁÎÚÈÂ456','˜¯‡Ë˜¯‡Ë¯‡','„Î„‚ÎÈ','ÒÚÈ‚ÎÚÈ','‚ÎÚÈ','‚ÎÚÈ„˜¯Ú˘„‚Ú˘„','‚˘„‚˘„/\'¯˜¯È','‚ÎÚÈÎÚÈÁ','ÈˆÈÚ˙ÍÁÏÍÛÁÏ','‚ÎÚÈÁÈÏÚÈÁ','ÂÔÌÙÂÔÌ‡ËÂ','Ô‡ËÂÔÈÁÍ˙Óˆ˙ıÓˆ˙Ó','‰Ó·‰Ó‰·','ÊÒ·‰„˘„‚Î','ÎÚÈÎÚÈÁÚÈÏÍÏ','ÈÁÏÍÈÁÌÂËÔÌË','˜¯‡Ë˜¯‡Ë');
INSERT INTO Languages VALUES (4,'Serbo-Croatian','ISO-8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedelja','Ponedeljak','Utorak','Sreda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (5,'German','ISO-8859-1','Deutsch','de','Januar','Februar','M‰rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (6,'Austrian','IS0-8859-1','Deutsch (÷sterreich)','at','J‰nner','Februar','M‰rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (7,'Croatian','ISO-8859-2','Hrvatski','hr','SijeËanj','VeljaËa','Oæujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (8,'Czech','ISO-8859-2','»esk˝','cz','Leden','⁄nor','B¯ezen','Duben','KvÏten','»erven','»ervenec','Srpen','Z·¯Ì','ÿÌjen','Listopad','Prosinec','NedÏle','PondÏlÌ','⁄ter˝','St¯eda','»tvrtek','P·tek','Sobota');
INSERT INTO Languages VALUES (9,'Portuguese','ISO-8859-1','PortuguÍs','pt','Janeiro','Fevereiro','MarÁo','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','TerÁa-feira','Quarta-feira','Quinta-feira','Sexta-feira','S·bado');
INSERT INTO Languages VALUES (10,'Sebian (Cyrillic)','ISO-8859-5','¡‡ﬂ·⁄ÿ (´ÿ‡ÿ€ÿÊ–)','sr','¯–›„–‡','‰’—‡„–‡','‹–‡‚','–ﬂ‡ÿ€','‹–¯','¯„›','¯„€','–“”„·‚','·’ﬂ‚’‹—–‡','ﬁ⁄‚ﬁ—–‡','›ﬁ“’‹—–‡','‘’Ê’‹—–‡','Ω’‘’˘–','øﬁ›’‘’˘–⁄','√‚ﬁ‡–⁄','¡‡’‘–','«’‚“‡‚–⁄','ø’‚–⁄','¡„—ﬁ‚–');
INSERT INTO Languages VALUES (11,'Bosnian','ISO-8859-2','Bosanski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (12,'French','ISO-8859-1','FranÁais','fr','Janvier','FÈvrier','Mars','Avril','Peut','Juin','Juli','Ao˚t','Septembre','Octobre','Novembre','DÈcembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
INSERT INTO Languages VALUES (13,'Spanish','ISO-8859-1','EspaÒol','es','Enero','Febrero','Marcha','Abril','Puede','Junio','Juli','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','MiÈrcoles','Jueves','Viernes','S·bado');
INSERT INTO Languages VALUES (14,'Italian','ISO-8859-1','Italiano','it','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre','Domenica','LunedÏ','MartedÏ','MercoledÏ','GiovedÏ','VenerdÏ','Sabato');
INSERT INTO Languages VALUES (15,'Russian','ISO-8859-5','¿„··⁄ÿŸ','ru','Ô›“–‡Ï','‰’“‡–€Ï','‹–‡‚','–ﬂ‡’€Ï','‹–Ÿ','ÿÓ›Ï','ÿÓ€Ï','–“”„·‚','·’›‚Ô—‡Ï','ﬁ⁄‚Ô—‡Ï','›ﬁÔ—‡Ï','‘’⁄–—‡Ï','“ﬁ·⁄‡’·’›Ï’','ﬂﬁ›’‘’€Ï›ÿ⁄','“‚ﬁ‡›ÿ⁄','·‡’‘–','Á’‚“’‡”','ﬂÔ‚›ÿÊ–','·„——ﬁ‚–');

#
# Table structure for table 'Log'
#

CREATE TABLE Log (
  TStamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  IdEvent int(10) unsigned DEFAULT '0' NOT NULL,
  User varchar(32) DEFAULT '' NOT NULL,
  Text varchar(255) DEFAULT '' NOT NULL,
  KEY IdEvent (IdEvent)
);

#
# Dumping data for table 'Log'
#


#
# Table structure for table 'Publications'
#

CREATE TABLE Publications (
  Id int(10) unsigned NOT NULL auto_increment,
  Name char(32) DEFAULT '' NOT NULL,
  Site char(128) DEFAULT '' NOT NULL,
  IdDefaultLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  PayTime int(10) unsigned DEFAULT '0' NOT NULL,
  TimeUnit enum('D','W','M','Y') DEFAULT 'D' NOT NULL,
  UnitCost float(10,2) unsigned DEFAULT '0.00' NOT NULL,
  Currency char(20) DEFAULT '' NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE Name (Name),
  UNIQUE Site (Site)
);

#
# Dumping data for table 'Publications'
#


#
# Table structure for table 'Sections'
#

CREATE TABLE Sections (
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  NrIssue int(10) unsigned DEFAULT '0' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Number int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(64) DEFAULT '' NOT NULL,
  PRIMARY KEY (IdPublication,NrIssue,IdLanguage,Number),
  UNIQUE IdPublication (IdPublication,NrIssue,IdLanguage,Name)
);

#
# Dumping data for table 'Sections'
#


#
# Table structure for table 'SubsByIP'
#

CREATE TABLE SubsByIP (
  IdUser int(10) unsigned DEFAULT '0' NOT NULL,
  StartIP int(10) unsigned DEFAULT '0' NOT NULL,
  Addresses int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (IdUser,StartIP)
);

#
# Dumping data for table 'SubsByIP'
#


#
# Table structure for table 'SubsDefTime'
#

CREATE TABLE SubsDefTime (
  CountryCode char(2) DEFAULT '' NOT NULL,
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  TrialTime int(10) unsigned DEFAULT '0' NOT NULL,
  PaidTime int(10) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (CountryCode,IdPublication)
);

#
# Dumping data for table 'SubsDefTime'
#


#
# Table structure for table 'SubsSections'
#

CREATE TABLE SubsSections (
  IdSubscription int(10) unsigned DEFAULT '0' NOT NULL,
  SectionNumber int(10) unsigned DEFAULT '0' NOT NULL,
  StartDate date DEFAULT '0000-00-00' NOT NULL,
  Days int(10) unsigned DEFAULT '0' NOT NULL,
  PaidDays int(10) unsigned DEFAULT '0' NOT NULL,
  NoticeSent enum('N','Y') DEFAULT 'N' NOT NULL,
  PRIMARY KEY (IdSubscription,SectionNumber)
);

#
# Dumping data for table 'SubsSections'
#


#
# Table structure for table 'Subscriptions'
#

CREATE TABLE Subscriptions (
  Id int(10) unsigned NOT NULL auto_increment,
  IdUser int(10) unsigned DEFAULT '0' NOT NULL,
  IdPublication int(10) unsigned DEFAULT '0' NOT NULL,
  Active enum('Y','N') DEFAULT 'Y' NOT NULL,
  ToPay float(10,2) unsigned DEFAULT '0.00' NOT NULL,
  Currency char(20) DEFAULT '' NOT NULL,
  Type enum('T','P') DEFAULT 'T' NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE IdUser (IdUser,IdPublication)
);

#
# Dumping data for table 'Subscriptions'
#


#
# Table structure for table 'TimeUnits'
#

CREATE TABLE TimeUnits (
  Unit char(1) DEFAULT '' NOT NULL,
  IdLanguage int(10) unsigned DEFAULT '0' NOT NULL,
  Name char(20) DEFAULT '' NOT NULL,
  PRIMARY KEY (Unit,IdLanguage)
);

#
# Dumping data for table 'TimeUnits'
#

INSERT INTO TimeUnits VALUES ('D',1,'days');
INSERT INTO TimeUnits VALUES ('W',1,'weeks');
INSERT INTO TimeUnits VALUES ('M',1,'months');
INSERT INTO TimeUnits VALUES ('Y',1,'years');

#
# Table structure for table 'UserPerm'
#

CREATE TABLE UserPerm (
  IdUser int(10) unsigned DEFAULT '0' NOT NULL,
  ManagePub enum('N','Y') DEFAULT 'N' NOT NULL,
  DeletePub enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageIssue enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteIssue enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageSection enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteSection enum('N','Y') DEFAULT 'N' NOT NULL,
  AddArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  ChangeArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  AddImage enum('N','Y') DEFAULT 'N' NOT NULL,
  ChangeImage enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteImage enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageTempl enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteTempl enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageUsers enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageSubscriptions enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteUsers enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageUserTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageArticleTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteArticleTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageLanguages enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteLanguages enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageDictionary enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteDictionary enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageCountries enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteCountries enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageClasses enum('N','Y') DEFAULT 'N' NOT NULL,
  MailNotify enum('N','Y') DEFAULT 'N' NOT NULL,
  ViewLogs enum('N','Y') DEFAULT 'N' NOT NULL,
  PRIMARY KEY (IdUser)
);

#
# Dumping data for table 'UserPerm'
#

INSERT INTO UserPerm VALUES (1,'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');

#
# Table structure for table 'UserTypes'
#

CREATE TABLE UserTypes (
  Name char(32) DEFAULT '' NOT NULL,
  Reader enum('N','Y') DEFAULT 'N' NOT NULL,
  ManagePub enum('N','Y') DEFAULT 'N' NOT NULL,
  DeletePub enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageIssue enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteIssue enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageSection enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteSection enum('N','Y') DEFAULT 'N' NOT NULL,
  AddArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  ChangeArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteArticle enum('N','Y') DEFAULT 'N' NOT NULL,
  AddImage enum('N','Y') DEFAULT 'N' NOT NULL,
  ChangeImage enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteImage enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageTempl enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteTempl enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageUsers enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageSubscriptions enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteUsers enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageUserTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageArticleTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteArticleTypes enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageLanguages enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteLanguages enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageDictionary enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteDictionary enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageCountries enum('N','Y') DEFAULT 'N' NOT NULL,
  DeleteCountries enum('N','Y') DEFAULT 'N' NOT NULL,
  ManageClasses enum('N','Y') DEFAULT 'N' NOT NULL,
  MailNotify enum('N','Y') DEFAULT 'N' NOT NULL,
  ViewLogs enum('N','Y') DEFAULT 'N' NOT NULL,
  PRIMARY KEY (Name)
);

#
# Dumping data for table 'UserTypes'
#

INSERT INTO UserTypes VALUES ('Reader','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N');
INSERT INTO UserTypes VALUES ('Administrator','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y');
INSERT INTO UserTypes VALUES ('Editor','N','N','N','N','N','N','N','Y','Y','Y','Y','Y','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','Y','N');

#
# Table structure for table 'Users'
#

CREATE TABLE Users (
  Id int(10) unsigned NOT NULL auto_increment,
  KeyId int(10) unsigned,
  Name varchar(64) DEFAULT '' NOT NULL,
  UName varchar(32) DEFAULT '' NOT NULL,
  Password varchar(32) DEFAULT '' NOT NULL,
  EMail varchar(128) DEFAULT '' NOT NULL,
  Reader enum('Y','N') DEFAULT 'Y' NOT NULL,
  City varchar(60) DEFAULT '' NOT NULL,
  StrAddress varchar(255) DEFAULT '' NOT NULL,
  State varchar(32) DEFAULT '' NOT NULL,
  CountryCode char(2) DEFAULT '' NOT NULL,
  Phone varchar(20) DEFAULT '' NOT NULL,
  Fax varchar(20) DEFAULT '' NOT NULL,
  Contact varchar(64) DEFAULT '' NOT NULL,
  Phone2 varchar(20) DEFAULT '' NOT NULL,
  Title enum('Mr.','Mrs.','Ms.','Dr.') DEFAULT 'Mr.' NOT NULL,
  Gender enum('M','F') DEFAULT 'M' NOT NULL,
  Age enum('0-17','18-24','25-39','40-49','50-65','65-') DEFAULT '0-17' NOT NULL,
  PostalCode varchar(10) DEFAULT '' NOT NULL,
  Employer varchar(30) DEFAULT '' NOT NULL,
  EmployerType varchar(40) DEFAULT '' NOT NULL,
  Position varchar(30) DEFAULT '' NOT NULL,
  Interests mediumblob DEFAULT '' NOT NULL,
  How varchar(100) DEFAULT '' NOT NULL,
  Languages varchar(100) DEFAULT '' NOT NULL,
  Improvements mediumblob DEFAULT '' NOT NULL,
  Pref1 enum('N','Y') DEFAULT 'N' NOT NULL,
  Pref2 enum('N','Y') DEFAULT 'N' NOT NULL,
  Pref3 enum('N','Y') DEFAULT 'N' NOT NULL,
  Pref4 enum('N','Y') DEFAULT 'N' NOT NULL,
  Field1 varchar(150) DEFAULT '' NOT NULL,
  Field2 varchar(150) DEFAULT '' NOT NULL,
  Field3 varchar(150) DEFAULT '' NOT NULL,
  Field4 varchar(150) DEFAULT '' NOT NULL,
  Field5 varchar(150) DEFAULT '' NOT NULL,
  Text1 mediumblob DEFAULT '' NOT NULL,
  Text2 mediumblob DEFAULT '' NOT NULL,
  Text3 mediumblob DEFAULT '' NOT NULL,
  PRIMARY KEY (Id),
  UNIQUE UName (UName)
);

#
# Dumping data for table 'Users'
#

INSERT INTO Users VALUES (1,NULL,'Administrator','admin','2c380f066e0e45d1','','N','','','','','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','');

