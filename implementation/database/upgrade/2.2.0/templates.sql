-- IMPORTANT!!! Run after the static URL script!

-- Retrieve issues info into a duplicate table having the new structure with 
-- field IssueTplId replacing FrontPage and the new field SectionTplId
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
INSERT INTO IssuesDup SELECT IdPublication, Number, IdLanguage, i.Name, PublicationDate, Published, t.Id, t.Id, SingleArticle, ShortName FROM Issues as i LEFT JOIN Templates as t ON i.FrontPage = t.Name;
DROP TABLE Issues;
ALTER TABLE IssuesDup RENAME TO Issues;

-- Repeat the previous step for Single ArticleField
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
INSERT INTO IssuesDup SELECT IdPublication, Number, IdLanguage, i.Name, PublicationDate, Published, IssueTplId, SectionTplId, t.Id, ShortName FROM Issues as i LEFT JOIN Templates as t ON i.SingleArticle = t.Name;
DROP TABLE Issues;
ALTER TABLE IssuesDup RENAME TO Issues;

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
INSERT INTO SectionsDup SELECT IdPublication, NrIssue, IdLanguage, Number, Name, ShortName, NULL, NULL FROM Sections;
DROP TABLE Sections;
ALTER TABLE SectionsDup RENAME TO Sections;
