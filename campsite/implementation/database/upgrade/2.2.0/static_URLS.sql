-- URLTypes
CREATE TABLE URLTypes (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(15) UNIQUE NOT NULL,
    Description mediumblob NOT NULL
);
INSERT INTO URLTypes (Id, Name, Description) VALUES(1, 'template path', '');
INSERT INTO URLTypes (Id, Name, Description) VALUES(2, 'short names', '');

-- Templates
CREATE TABLE Templates (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(255) NOT NULL UNIQUE,
    Type int(10) unsigned NOT NULL DEFAULT 1,
    Level int(10) unsigned NOT NULL DEFAULT 0
);

CREATE TABLE TemplateTypes (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(20) NOT NULL UNIQUE
);
INSERT INTO TemplateTypes (Id, Name) VALUES (1, 'default');
INSERT INTO TemplateTypes (Id, Name) VALUES (2, 'issue');
INSERT INTO TemplateTypes (Id, Name) VALUES (3, 'section');
INSERT INTO TemplateTypes (Id, Name) VALUES (4, 'article');

CREATE TABLE Aliases (
    Id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Name char(128) NOT NULL UNIQUE,
    IdPublication int(10) unsigned NOT NULL
);


-- Retrieve publications info into a duplicate table having the new structure
INSERT INTO Aliases (Name, IdPublication) SELECT Site, Publications.Id FROM Publications;
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


-- Retrieve issues info into a duplicate table having the new structure
CREATE TABLE IssuesDup (
    IdPublication int(10) unsigned NOT NULL default '0',
    Number int(10) unsigned NOT NULL default '0',
    IdLanguage int(10) unsigned NOT NULL default '0',
    Name varchar(140) NOT NULL default '',
    PublicationDate date NOT NULL default '0000-00-00',
    Published enum('N','Y') NOT NULL default 'N',
    FrontPage varchar(128) NOT NULL default '',
    SingleArticle varchar(128) NOT NULL default '',
    ShortName varchar(32) NOT NULL default '',
    PRIMARY KEY  (IdPublication,Number,IdLanguage),
    UNIQUE KEY ShortName (IdPublication, IdLanguage, ShortName)
);
INSERT INTO IssuesDup SELECT IdPublication, Number, IdLanguage, Name, PublicationDate, Published, FrontPage, SingleArticle, Number FROM Issues;
DROP TABLE Issues;
ALTER TABLE IssuesDup RENAME TO Issues;


-- Retrieve sections info into a duplicate table having the new structure
CREATE TABLE SectionsDup (
    IdPublication int(10) unsigned NOT NULL default '0',
    NrIssue int(10) unsigned NOT NULL default '0',
    IdLanguage int(10) unsigned NOT NULL default '0',
    Number int(10) unsigned NOT NULL default '0',
    Name varchar(255) NOT NULL default '',
    ShortName varchar(32) NOT NULL default '',
    PRIMARY KEY  (IdPublication,NrIssue,IdLanguage,Number),
    UNIQUE KEY IdPublication (IdPublication,NrIssue,IdLanguage,Name),
    UNIQUE KEY ShortName (IdPublication, NrIssue, IdLanguage, ShortName)
);
INSERT INTO SectionsDup SELECT IdPublication, NrIssue, IdLanguage, Number, Name, Number FROM Sections;
DROP TABLE Sections;
ALTER TABLE SectionsDup RENAME TO Sections;


-- Retrieve articles info into a duplicate table having the new structure
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
    PRIMARY KEY  (IdPublication,NrIssue,NrSection,Number,IdLanguage),
    UNIQUE KEY IdPublication (IdPublication,NrIssue,NrSection,IdLanguage,Name),
    UNIQUE KEY Number (Number,IdLanguage),
    UNIQUE KEY other_key (IdPublication,NrIssue,NrSection,IdLanguage,Number),
    KEY Type (Type),
    UNIQUE KEY ShortName (IdPublication, NrIssue, NrSection, IdLanguage, ShortName)
);
INSERT INTO ArticlesDup SELECT IdPublication, NrIssue, NrSection, Number, IdLanguage, Name, Type, IdUser, OnFrontPage, OnSection, Published, UploadDate, Keywords, Public, IsIndexed, LockUser, LockTime, Number FROM Articles;
DROP TABLE Articles;
ALTER TABLE ArticlesDup RENAME TO Articles;


-- Add new events to Events table
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(151, 'Add alias', 'N', 1);
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(152, 'Delete alias', 'N', 1);
INSERT INTO Events (Id, Name, Notify, IdLanguage) VALUES(153, 'Update alias', 'N', 1);
 
