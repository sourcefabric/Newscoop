CREATE TABLE IF NOT EXISTS `ArticleImageCaptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdLanguage` int(11) NOT NULL,
  `IdImage` int(11) NOT NULL,
  `NrArticle` int(11) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `articleImage_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `imageId` (`IdImage`,`NrArticle`,`IdLanguage`),
  KEY `IDX_1E9BFCA410F3034D6CB384EF` (`IdImage`,`NrArticle`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;