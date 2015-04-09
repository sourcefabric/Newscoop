DROP TABLE IF EXISTS `Snippets`;
CREATE TABLE IF NOT EXISTS `Snippets` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Enabled` tinyint(1) NOT NULL,
  `Created` datetime NOT NULL,
  `Modified` datetime NOT NULL,
  `TemplateId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_1457978AF846113F` (`TemplateId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `SnippetTemplates`;
CREATE TABLE IF NOT EXISTS SnippetTemplates (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TemplateCode` longtext COLLATE utf8_unicode_ci NOT NULL,
  `Favourite` tinyint(1) DEFAULT NULL,
  `Enabled` tinyint(1) NOT NULL,
  `IconInactive` longtext COLLATE utf8_unicode_ci,
  `IconActive` longtext COLLATE utf8_unicode_ci,
  `Created` datetime NOT NULL,
  `Modified` datetime NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Snippets ADD CONSTRAINT SnippetTemplate FOREIGN KEY (TemplateId) REFERENCES SnippetTemplates (Id);
DROP TABLE IF EXISTS `ArticleSnippets`;
CREATE TABLE ArticleSnippets (
  ArticleId INT NOT NULL,
  SnippetId INT NOT NULL,
  INDEX IDX_5080CDE7C53224D (ArticleId),
  INDEX IDX_5080CDEB00DA91C (SnippetId),
  PRIMARY KEY(ArticleId, SnippetId)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;