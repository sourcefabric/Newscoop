CREATE TABLE IF NOT EXISTS SnippetTemplates (Id INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) NOT NULL, Controller VARCHAR(255) DEFAULT NULL, TemplateCode LONGTEXT NOT NULL, Favourite TINYINT(1) DEFAULT NULL, Enabled TINYINT(1) NOT NULL, IconInactive LONGTEXT DEFAULT NULL, IconActive LONGTEXT DEFAULT NULL, Created DATETIME NOT NULL, Modified DATETIME NOT NULL, PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS SnippetTemplateField (Id INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) NOT NULL, Type VARCHAR(255) NOT NULL, Scope VARCHAR(255) NOT NULL, Required TINYINT(1) NOT NULL, TemplateId INT NOT NULL, INDEX IDX_2060662F846113F (TemplateId), PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS SnippetFields (Id INT AUTO_INCREMENT NOT NULL, Data LONGTEXT DEFAULT NULL, Name VARCHAR(255) NOT NULL, SnippetId INT NOT NULL, TemplateFieldId INT NOT NULL, INDEX IDX_1F835121B00DA91C (SnippetId), INDEX IDX_1F835121EBCA9337 (TemplateFieldId), PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS Snippets (Id INT AUTO_INCREMENT NOT NULL, Name VARCHAR(255) NOT NULL, Enabled TINYINT(1) NOT NULL, Created DATETIME NOT NULL, Modified DATETIME NOT NULL, TemplateId INT DEFAULT NULL, INDEX IDX_1457978AF846113F (TemplateId), PRIMARY KEY(Id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE IF NOT EXISTS ArticleSnippets (ArticleNr INT NOT NULL, SnippetId INT NOT NULL, INDEX IDX_5080CDEC7C601DB (ArticleNr), INDEX IDX_5080CDEB00DA91C (SnippetId), PRIMARY KEY(ArticleNr, SnippetId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE SnippetTemplateField ADD CONSTRAINT FK_2060662F846113F FOREIGN KEY (TemplateId) REFERENCES SnippetTemplates (Id);
ALTER TABLE SnippetFields ADD CONSTRAINT FK_1F835121B00DA91C FOREIGN KEY (SnippetId) REFERENCES Snippets (Id);
ALTER TABLE SnippetFields ADD CONSTRAINT FK_1F835121EBCA9337 FOREIGN KEY (TemplateFieldId) REFERENCES SnippetTemplateField (Id);
ALTER TABLE Snippets ADD CONSTRAINT FK_1457978AF846113F FOREIGN KEY (TemplateId) REFERENCES SnippetTemplates (Id);
ALTER TABLE ArticleSnippets ADD CONSTRAINT FK_5080CDEC7C601DB FOREIGN KEY (ArticleNr) REFERENCES Articles (Number);
ALTER TABLE ArticleSnippets ADD CONSTRAINT FK_5080CDEB00DA91C FOREIGN KEY (SnippetId) REFERENCES Snippets (Id);

--- Start of Embed.ly Snippet Template
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES 
	(1, 'Embed.ly', 'Newscoop\\NewscoopBundle\\Controller\\EmbedlyController', '<a class=\"embedly-card\" href=\"{{ URL }}\">{{ title }}</a><script>!function(a){var b=\"embedly-platform\",c=\"script\";if(!a.getElementById(b)){var d=a.createElement(c);d.id=b,d.src=(\"https:\"===document.location.protocol?\"https\":\"http\")+\"://cdn.embedly.com/widgets/platform.js\";var e=document.getElementsByTagName(c)[0];e.parentNode.insertBefore(d,e)}}(document);</script>', 0, 1, NULL, NULL, '2014-05-12 13:19:43', '2014-05-12 13:19:43');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `Required`, `TemplateId`)
VALUES
	(1, 'URL', 'url', 'frontend', 1, 1),
	(2, 'Endpoint', 'text', 'frontend', 0, 1),
	(3, 'maxwidth', 'integer', 'frontend', 0, 1),
	(4, 'provider_url', 'url', 'backend', 0, 1),
	(5, 'description', 'textarea', 'backend', 0, 1),
	(6, 'title', 'text', 'backend', 0, 1),
	(7, 'type', 'text', 'backend', 0, 1),
	(8, 'thumbnail_width', 'integer', 'backend', 0, 1),
	(9, 'height', 'integer', 'backend', 0, 1),
	(10, 'width', 'integer', 'backend', 0, 1),
	(11, 'html', 'textarea', 'backend', 0, 1),
	(12, 'author_name', 'text', 'backend', 0, 1),
	(13, 'version', 'text', 'backend', 0, 1),
	(14, 'provider_name', 'text', 'backend', 0, 1),
	(15, 'thumbnail_url', 'url', 'backend', 0, 1),
	(16, 'thumbnail_height', 'integer', 'backend', 0, 1),
	(17, 'author_url', 'url', 'backend', 0, 1);
--- End of Embed.ly Snippet Template

--- Start of Youtube Snippet Template
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(2, 'Youtube', NULL, '<iframe width=\"{{ width }}\" height=\"{{ height }}\" src=\"//www.youtube.com/embed/{{ ID }}\" frameborder=\"0\" allowfullscreen></iframe>', 1, 1, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(18, 'ID', 'text', 'frontend', 2, 1),
	(19, 'width', 'integer', 'frontend', 2, 0),
	(20, 'height', 'integer', 'frontend', 2, 0);
--- End of Youtube Snippet Template

--- Start of Vimeo Snippet Template
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(3, 'Vimeo', NULL, '<iframe src=\"//player.vimeo.com/video/{{ ID }}\" width=\"{{ width }}\" height=\"{{ height }}\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 1, 0, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(21, 'ID', 'text', 'frontend', 3, 1),
	(22, 'width', 'integer', 'frontend', 3, 0),
	(23, 'height', 'integer', 'frontend', 3, 0);
--- Start of Vimeo Snippet Template

--- Start of Generic Snippet Template
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(4, 'Generic', NULL, '{{Generic}}', 0, 1, NULL, NULL, '2014-06-10 14:15:49', '2014-06-10 14:15:49');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(18, 'Generic', 'textarea', 'frontend', 4, 1);
--- End of Generic Snippet Template

--- Start of Embed.ly Snippet sample
INSERT INTO `Snippets` (`Id`, `Name`, `Enabled`, `Created`, `Modified`, `TemplateId`)
VALUES
	(1, 'Youtube Sourcefabric Booktype Video', 1, '2014-06-10 13:15:10', '2014-06-10 13:15:12', 1);

INSERT INTO `SnippetFields` (`Id`, `Data`, `SnippetId`, `TemplateFieldId`, `Name`)
VALUES
	(1, 'https://www.youtube.com/watch?v=AYVUPem_jaM', 1, 1, 'URL'),
	(2, NULL, 1, 2, 'Endpoint'),
	(3, '0', 1, 3, 'maxwidth'),
	(4, 'http://www.youtube.com/', 1, 4, 'provider_url'),
	(5, 'Sourcefabric builds open source software to support independent media worldwide. On February 14th, we\'ll announce our tool to help people and organisations write and publish great multi-platform books. Write and publish great books ready for iPad, Kindle, Nook or print within minutes.', 1, 5, 'description'),
	(6, 'The future of the book is in your hands', 1, 6, 'title'),
	(7, 'video', 1, 7, 'type'),
	(8, '480', 1, 8, 'thumbnail_width'),
	(9, '309', 1, 9, 'height'),
	(10, '550', 1, 10, 'width'),
	(11, '<iframe class=\"embedly-embed\" src=\"//cdn.embedly.com/widgets/media.html?url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DAYVUPem_jaM&src=http%3A%2F%2Fwww.youtube.com%2Fembed%2FAYVUPem_jaM%3Ffeature%3Doembed&image=http%3A%2F%2Fi1.ytimg.com%2Fvi%2FAYVUPem_jaM%2Fhqdefault.jpg&type=text%2Fhtml&schema=youtube\" width=\"550\" height=\"309\" scrolling=\"no\" frameborder=\"0\" allowfullscreen></iframe>', 1, 11, 'html'),
	(12, 'Sourcefabric', 1, 12, 'author_name'),
	(13, '1.0', 1, 13, 'version'),
	(14, 'YouTube', 1, 14, 'provider_name'),
	(15, 'http://i1.ytimg.com/vi/AYVUPem_jaM/hqdefault.jpg', 1, 15, 'thumbnail_url'),
	(16, '360', 1, 16, 'thumbnail_height'),
	(17, 'http://www.youtube.com/user/Sourcefabric', 1, 17, 'author_url');
--- End of Embed.ly Snippet sample