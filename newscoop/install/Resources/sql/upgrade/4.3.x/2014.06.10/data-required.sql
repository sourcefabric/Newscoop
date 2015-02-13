INSERT INTO `SystemPreferences` (`varname`, `value`, `last_modified`) VALUES ('SmartyUseProtocol', 'Y', NULL);
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
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(2, 'Youtube', NULL, '<iframe width=\"{{ width }}\" height=\"{{ height }}\" src=\"//www.youtube.com/embed/{{ ID }}\" frameborder=\"0\" allowfullscreen></iframe>', 1, 1, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(18, 'ID', 'text', 'frontend', 2, 1),
	(19, 'width', 'integer', 'frontend', 2, 0),
	(20, 'height', 'integer', 'frontend', 2, 0);
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(3, 'Vimeo', NULL, '<iframe src=\"//player.vimeo.com/video/{{ ID }}\" width=\"{{ width }}\" height=\"{{ height }}\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 1, 1, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(21, 'ID', 'text', 'frontend', 3, 1),
	(22, 'width', 'integer', 'frontend', 3, 0),
	(23, 'height', 'integer', 'frontend', 3, 0);
INSERT INTO `SnippetTemplates` (`Id`, `Name`, `Controller`, `TemplateCode`, `Favourite`, `Enabled`, `IconInactive`, `IconActive`, `Created`, `Modified`)
VALUES
	(4, 'Generic', NULL, '{{Generic | raw}}', 0, 1, NULL, NULL, '2014-06-10 14:15:49', '2014-06-10 14:15:49');

INSERT INTO `SnippetTemplateField` (`Id`, `Name`, `Type`, `Scope`, `TemplateId`, `Required`)
VALUES
	(24, 'Generic', 'textarea', 'frontend', 4, 1);
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
