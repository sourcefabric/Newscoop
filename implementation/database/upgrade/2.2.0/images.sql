-- Step 1: Create ImagesDup table and populate it with data from Images table
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
  `ThumbnailFileName` varchar(50) NOT NULL default '',
  `ImageFileName` varchar(50) NOT NULL default '',
  `UploadedByUser` int(11) default NULL,
  `LastModified` timestamp(14) NOT NULL,
  `TimeCreated` timestamp(14) NOT NULL default '00000000000000',
  `NrArticle` int(10) unsigned NOT NULL default '0',
  `Number` int(10) unsigned NOT NULL default '0',
  `Image` mediumblob NOT NULL,
  PRIMARY KEY  (`Id`)
) TYPE=MyISAM;
    

INSERT INTO ImagesDup (Description, Photographer, Place, Date, ContentType, Location, URL, NrArticle, Number, Image) SELECT Description, Photographer, Place, Date, ContentType, 'local', '', NrArticle, Number, Image FROM Images;


-- Step 2: Run the 'transfer_images' script now!!!


-- Step 3: Create ArticleImages table and populate it with data from ImagesDup table
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
 
