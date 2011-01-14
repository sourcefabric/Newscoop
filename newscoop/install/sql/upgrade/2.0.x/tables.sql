--
-- CAMPSITE is a Unicode-enabled multilingual web content
-- management system for news publications.
-- CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
-- Copyright (C)2000,2001  Media Development Loan Fund
-- contact: contact@campware.org - http://www.campware.org
-- Campware encourages further development. Please let us know.
--
-- This program is free software; you can redistribute it and/or
-- modify it under the terms of the GNU General Public License
-- as published by the Free Software Foundation; either version 2
-- of the License, or (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
--

-- alter Articles table
alter table Articles change Name  Name varchar(140) DEFAULT '' NOT NULL;
alter table Articles change Type Type varchar(70) DEFAULT '' NOT NULL;
alter table Articles change Keywords Keywords varchar(255) DEFAULT '' NOT NULL;

-- alter AutoId table
alter table AutoId add column TopicId int(10) unsigned DEFAULT '0' NOT NULL;

-- alter Classes table
alter table Classes change Name Name varchar(140) DEFAULT '' NOT NULL;

-- alter Countries table
alter table Countries change Name Name varchar(140) DEFAULT '' NOT NULL;

-- alter Dictionary table
alter table Dictionary change Keyword Keyword varchar(140) DEFAULT '' NOT NULL;

-- alter Events table
alter table Events add column IdLanguage int(10) unsigned DEFAULT '0' NOT NULL;
alter table Events drop primary key;
alter table Events add primary key(Id, IdLanguage);
alter table Events change Name Name varchar(140) DEFAULT '' NOT NULL;

--alter Images table
alter table Images change Description Description varchar(255) DEFAULT '' NOT NULL;
alter table Images change Photographer Photographer varchar(140) DEFAULT '' NOT NULL;
alter table Images change Place Place varchar(140) DEFAULT '' NOT NULL;

-- alter Issues table
alter table Issues change Name Name varchar(140) DEFAULT '' NOT NULL;

-- alter KeywordIndex table
alter table KeywordIndex change Keyword Keyword varchar(70) DEFAULT '' NOT NULL;

-- alter Languages table
alter table Languages change Name Name varchar(140) DEFAULT '' NOT NULL;
alter table Languages change CodePage CodePage varchar(140) DEFAULT '' NOT NULL;
alter table Languages change OrigName OrigName varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Code Code char(21) DEFAULT '' NOT NULL;
alter table Languages change Month1 Month1 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month2 Month2 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month3 Month3 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month4 Month4 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month5 Month5 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month6 Month6 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month7 Month7 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month8 Month8 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month9 Month9 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month10 Month10 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month11 Month11 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change Month12 Month12 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay1 WDay1 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay2 WDay2 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay3 WDay3 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay4 WDay4 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay5 WDay5 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay6 WDay6 varchar(140) DEFAULT '' NOT NULL;
alter table Languages change WDay7 WDay7 varchar(140) DEFAULT '' NOT NULL;

-- alter Log table
alter table Log change User User varchar(70) DEFAULT '' NOT NULL;

-- alter Publications table
alter table Publications change Name Name varchar(255) DEFAULT '' NOT NULL;
alter table Publications change Site Site varchar(255) DEFAULT '' NOT NULL;
alter table Publications change Currency Currency varchar(140) DEFAULT '' NOT NULL;
alter table Publications add column TrialTime int(10) unsigned DEFAULT '0' NOT NULL;
alter table Publications add column PaidTime int(10) unsigned DEFAULT '0' NOT NULL;

-- alter Sections table
alter table Sections change Name Name varchar(255) DEFAULT '' NOT NULL;

-- alter SubsDefTime table
alter table SubsDefTime change CountryCode CountryCode char(21) DEFAULT '' NOT NULL;

-- alter Subscriptions table
alter table Subscriptions change Currency Currency varchar(70) DEFAULT '' NOT NULL;

-- alter TimeUnits table
alter table TimeUnits change Name Name varchar(70) DEFAULT '' NOT NULL;

-- alter UserPerm table
alter table UserPerm add column ManageLocalizer enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserPerm add column ManageIndexer enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserPerm add column Publish enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserPerm add column ManageTopics enum('N','Y') DEFAULT 'N' NOT NULL;

-- alter UserTypes table
alter table UserTypes change Name Name varchar(140) DEFAULT '' NOT NULL;
alter table UserTypes add column ManageLocalizer enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserTypes add column ManageIndexer enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserTypes add column Publish enum('N','Y') DEFAULT 'N' NOT NULL;
alter table UserTypes add column ManageTopics enum('N','Y') DEFAULT 'N' NOT NULL;

-- alter Users table
alter table Users change Name Name varchar(255) DEFAULT '' NOT NULL;
alter table Users change UName UName varchar(70) DEFAULT '' NOT NULL;
alter table Users change EMail EMail varchar(255) DEFAULT '' NOT NULL;
alter table Users change City City varchar(100) DEFAULT '' NOT NULL;
alter table Users change CountryCode CountryCode char(21) DEFAULT '' NOT NULL;
alter table Users change PostalCode PostalCode varchar(70) DEFAULT '' NOT NULL;
alter table Users change Employer Employer varchar(140) DEFAULT '' NOT NULL;
alter table Users change EmployerType EmployerType varchar(140) DEFAULT '' NOT NULL;
alter table Users change Position Position varchar(70) DEFAULT '' NOT NULL;
alter table Users change How How varchar(255) DEFAULT '' NOT NULL;

--
-- Table structure for table 'ArticleTopics'
--

CREATE TABLE ArticleTopics (
  NrArticle int(10) unsigned NOT NULL default '0',
  TopicId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (NrArticle,TopicId)
) TYPE=MyISAM;

--
-- Table structure for table 'Topics'
--

CREATE TABLE Topics (
  Id int(10) unsigned NOT NULL default '0',
  LanguageId int(10) unsigned NOT NULL default '0',
  Name varchar(100) NOT NULL default '',
  ParentId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id, LanguageId),
  UNIQUE KEY Name (LanguageId, Name)
) TYPE=MyISAM;
