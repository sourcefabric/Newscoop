-- Create new tables first: URLTypes, TemplateTypes, Templates, Aliases,
-- ArticlePublish, IssuePublish
-- Populate URLTypes and TemplateTypes tables with default values
DROP TABLE IF EXISTS URLTypes;
CREATE TABLE URLTypes (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(15) UNIQUE NOT NULL,
    Description mediumblob NOT NULL
);
INSERT INTO URLTypes (Id, Name, Description) VALUES(1, 'template path', '');
INSERT INTO URLTypes (Id, Name, Description) VALUES(2, 'short names', '');

DROP TABLE IF EXISTS TemplateTypes;
CREATE TABLE TemplateTypes (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(20) NOT NULL UNIQUE
);
INSERT INTO TemplateTypes (Id, Name) VALUES (1, 'default');
INSERT INTO TemplateTypes (Id, Name) VALUES (2, 'issue');
INSERT INTO TemplateTypes (Id, Name) VALUES (3, 'section');
INSERT INTO TemplateTypes (Id, Name) VALUES (4, 'article');

DROP TABLE IF EXISTS Templates;
CREATE TABLE Templates (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(255) NOT NULL UNIQUE,
    Type int(10) unsigned NOT NULL DEFAULT 1,
    Level int(10) unsigned NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS Aliases;
CREATE TABLE Aliases (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(128) NOT NULL UNIQUE,
    IdPublication int(10) unsigned NOT NULL
);

DROP TABLE IF EXISTS ArticlePublish;
CREATE TABLE ArticlePublish (
  NrArticle int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  PublishTime datetime NOT NULL default '0000-00-00 00:00:00',
  Publish enum('P','U') default NULL,
  FrontPage enum('S','R') default NULL,
  SectionPage enum('S','R') default NULL,
  PRIMARY KEY  (NrArticle,IdLanguage,PublishTime)
) TYPE=MyISAM;

DROP TABLE IF EXISTS IssuePublish;
CREATE TABLE IssuePublish (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  PublishTime datetime NOT NULL default '0000-00-00 00:00:00',
  Action enum('P','U') NOT NULL default 'P',
  PublishArticles enum('Y','N') NOT NULL default 'Y',
  PRIMARY KEY  (IdPublication,NrIssue,IdLanguage,PublishTime)
) TYPE=MyISAM;


-- Run transfer_templates.php script now!!!
system php ./transfer_templates.php

-- Verify if the script ran without errors
SELECT * FROM TransferTemplates;
DROP TABLE TransferTemplates;


-- Change Publications table structure
-- Retrieve publications info into a duplicate table having the new structure
INSERT INTO Aliases (Name, IdPublication) SELECT Site, Publications.Id FROM Publications;
DROP TABLE IF EXISTS PublicationsDup;
CREATE TABLE PublicationsDup (
    Id int(10) unsigned NOT NULL auto_increment,
    Name varchar(255) NOT NULL default '',
    IdDefaultLanguage int(10) unsigned NOT NULL default '0',
    PayTime int(10) unsigned NOT NULL default '0',
    TimeUnit enum('D','W','M','Y') NOT NULL default 'D',
    UnitCost float(10,2) unsigned NOT NULL default '0.00',
    Currency varchar(140) NOT NULL default '',
    TrialTime int(10) unsigned NOT NULL default '0',
    PaidTime int(10) unsigned NOT NULL default '0',
    IdDefaultAlias int(10) unsigned NOT NULL default '0',
    IdURLType int(10) unsigned NOT NULL default '1',
    PRIMARY KEY  (Id),
    UNIQUE KEY Alias (IdDefaultAlias),
    UNIQUE KEY Name (Name)
);
INSERT INTO PublicationsDup (Id, Name, IdDefaultLanguage, PayTime, TimeUnit, UnitCost, Currency, TrialTime, PaidTime, IdDefaultAlias) SELECT p.Id, p.Name, IdDefaultLanguage, PayTime, TimeUnit, UnitCost, Currency, TrialTime, PaidTime, a.Id from Publications as p, Aliases as a WHERE p.Id = a.IdPublication AND p.Site = a.Name;
DROP TABLE Publications;
ALTER TABLE PublicationsDup RENAME TO Publications;


-- Change Issues table structure
-- Retrieve issues info into a duplicate table having the new structure with 
-- field IssueTplId replacing FrontPage and the new fields SectionTplId
-- and ShortName
DROP TABLE IF EXISTS IssuesDup;
CREATE TABLE IssuesDup (
    IdPublication int(10) unsigned NOT NULL default '0',
    Number int(10) unsigned NOT NULL default '0',
    IdLanguage int(10) unsigned NOT NULL default '0',
    Name varchar(140) NOT NULL default '',
    PublicationDate date NOT NULL default '0000-00-00',
    Published enum('N','Y') NOT NULL default 'N',
    IssueTplId int(10) unsigned,
    SectionTplId int(10) unsigned,
    SingleArticle varchar(128) NOT NULL default '',
    ShortName varchar(32) NOT NULL default '',
    PRIMARY KEY  (IdPublication,Number,IdLanguage),
    UNIQUE KEY ShortName (IdPublication, IdLanguage, ShortName)
);
INSERT INTO IssuesDup SELECT i.IdPublication, i.Number, i.IdLanguage, i.Name, i.PublicationDate, i.Published, t.Id, t.Id, i.SingleArticle, i.Number FROM Issues as i LEFT JOIN Templates as t ON i.FrontPage = t.Name;
DROP TABLE Issues;
ALTER TABLE IssuesDup RENAME TO Issues;


-- Repeat the previous step for Single ArticleField
DROP TABLE IF EXISTS IssuesDup;
CREATE TABLE IssuesDup (
    IdPublication int(10) unsigned NOT NULL default '0',
    Number int(10) unsigned NOT NULL default '0',
    IdLanguage int(10) unsigned NOT NULL default '0',
    Name varchar(140) NOT NULL default '',
    PublicationDate date NOT NULL default '0000-00-00',
    Published enum('N','Y') NOT NULL default 'N',
    IssueTplId int(10) unsigned,
    SectionTplId int(10) unsigned,
    ArticleTplId int(10) unsigned,
    ShortName varchar(32) NOT NULL default '',
    PRIMARY KEY  (IdPublication,Number,IdLanguage),
    UNIQUE KEY ShortName (IdPublication, IdLanguage, ShortName)
);
INSERT INTO IssuesDup SELECT i.IdPublication, i.Number, i.IdLanguage, i.Name, i.PublicationDate, i.Published, i.IssueTplId, i.SectionTplId, t.Id, i.ShortName FROM Issues as i LEFT JOIN Templates as t ON i.SingleArticle = t.Name;
DROP TABLE Issues;
ALTER TABLE IssuesDup RENAME TO Issues;


-- Change Sections table structure
-- Retrieve sections info into a duplicate table having the new structure
DROP TABLE IF EXISTS SectionsDup;
CREATE TABLE SectionsDup (
    IdPublication int(10) unsigned NOT NULL default '0',
    NrIssue int(10) unsigned NOT NULL default '0',
    IdLanguage int(10) unsigned NOT NULL default '0',
    Number int(10) unsigned NOT NULL default '0',
    Name varchar(255) NOT NULL default '',
    ShortName varchar(32) NOT NULL default '',
    SectionTplId int(10) unsigned,
    ArticleTplId int(10) unsigned,
    PRIMARY KEY  (IdPublication,NrIssue,IdLanguage,Number),
    UNIQUE KEY IdPublication (IdPublication,NrIssue,IdLanguage,Name),
    UNIQUE KEY ShortName (IdPublication, NrIssue, IdLanguage, ShortName)
);
INSERT INTO SectionsDup SELECT IdPublication, NrIssue, IdLanguage, Number, Name, Number, NULL, NULL FROM Sections;
DROP TABLE Sections;
ALTER TABLE SectionsDup RENAME TO Sections;


-- Change Articles table structure
-- Retrieve articles info into a duplicate table having the new structure
DROP TABLE IF EXISTS ArticlesDup;
CREATE TABLE ArticlesDup (
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
    ShortName varchar(32) NOT NULL default '',
    ArticleOrder int(10) unsigned NOT NULL default '0',
    PRIMARY KEY  (IdPublication,NrIssue,NrSection,Number,IdLanguage),
    UNIQUE KEY IdPublication (IdPublication,NrIssue,NrSection,IdLanguage,Name),
    UNIQUE KEY Number (Number,IdLanguage),
    UNIQUE KEY other_key (IdPublication,NrIssue,NrSection,IdLanguage,Number),
    KEY Type (Type),
    UNIQUE KEY ShortName (IdPublication, NrIssue, NrSection, IdLanguage, ShortName),
    INDEX ArticleOrderIdx (ArticleOrder)
);
INSERT INTO ArticlesDup SELECT IdPublication, NrIssue, NrSection, Number, IdLanguage, Name, Type, IdUser, OnFrontPage, OnSection, Published, UploadDate, Keywords, Public, IsIndexed, LockUser, LockTime, Number, Number FROM Articles;
DROP TABLE Articles;
ALTER TABLE ArticlesDup RENAME TO Articles;


-- Add new events to Events table
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(151, 'Add alias', 'N', 1);
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(152, 'Delete alias', 'N', 1);
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(153, 'Update alias', 'N', 1);


-- Step 1: Create ImagesDup table and populate it with data from Images table
DROP TABLE IF EXISTS `ImagesDup`;
CREATE TABLE `ImagesDup` (
    `Id` int(10) unsigned NOT NULL auto_increment,
    `Description` varchar(255) NOT NULL default '',
    `Photographer` varchar(255) NOT NULL default '',
    `Place` varchar(255) NOT NULL default '',
    `Caption` varchar(255) NOT NULL default '',
    `Date` date NOT NULL default '0000-00-00',
    `ContentType` varchar(64) NOT NULL default '',
    `Location` enum('local','remote') NOT NULL default 'local',
    `URL` varchar(255) NOT NULL default '',
    `NrArticle` int(10) unsigned NOT NULL default '0',
    `Number` int(10) unsigned NOT NULL default '0',
    `Image` mediumblob NOT NULL,
    `ThumbnailFileName` varchar(50) NOT NULL default '',
    `ImageFileName` varchar(50) NOT NULL default '',
    `UploadedByUser` int(11) default NULL,
    `LastModified` timestamp(14) NOT NULL,
    `TimeCreated` timestamp(14) NOT NULL default '00000000000000',
    PRIMARY KEY (`Id`)
);
INSERT INTO ImagesDup (Description, Photographer, Place, Date, ContentType, Location, URL, NrArticle, Number, Image) SELECT Description, Photographer, Place, Date, ContentType, 'local', '', NrArticle, Number, Image FROM Images;


-- Step 2: Run the 'transfer_images' script now!!!
system php ./transfer_images.php

-- Verify if the script ran without errors
SELECT * FROM TransferImages;
DROP TABLE TransferImages;


-- Step 3: Create ArticleImages table and populate it with data from ImagesDup table
DROP TABLE IF EXISTS ArticleImages;
CREATE TABLE `ArticleImages` (
    `NrArticle` int(10) unsigned NOT NULL default '0',
    `IdImage` int(10) unsigned NOT NULL default '0',
    `Number` int(10) unsigned NOT NULL default '0',
    PRIMARY KEY  (`NrArticle`,`IdImage`),
    UNIQUE KEY `ArticleImage` (`NrArticle`,`Number`)
);
INSERT INTO ArticleImages (NrArticle, IdImage, Number) SELECT NrArticle, Id, Number FROM ImagesDup;


-- Step 4: Drop Images table, clean table ImagesDup and rename it to Images
DROP TABLE Images;
ALTER TABLE ImagesDup DROP COLUMN Image;
ALTER TABLE ImagesDup DROP COLUMN Number;
ALTER TABLE ImagesDup DROP COLUMN NrArticle;
ALTER TABLE ImagesDup RENAME TO Images;

ALTER TABLE `UserPerm` ADD `EditorImage` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTextAlignment` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontColor` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontSize` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontFace` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTable` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSuperscript` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSubscript` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorStrikethrough` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorIndent` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorListBullet` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorListNumber` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorHorizontalRule` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSourceView` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorEnlarge` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTextDirection` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorLink` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSubhead` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL,
ADD `EditorBold` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorItalic` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorUnderline` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorUndoRedo` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorCopyCutPaste` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ;

ALTER TABLE `UserTypes` ADD `EditorImage` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTextAlignment` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontColor` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontSize` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorFontFace` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTable` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSuperscript` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSubscript` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorStrikethrough` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorIndent` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorListBullet` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorListNumber` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorHorizontalRule` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSourceView` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorEnlarge` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorTextDirection` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorLink` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorSubhead` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL,
ADD `EditorBold` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorItalic` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorUnderline` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorUndoRedo` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ,
ADD `EditorCopyCutPaste` ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL ;
