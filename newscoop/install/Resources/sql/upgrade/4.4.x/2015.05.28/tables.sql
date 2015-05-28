CREATE TABLE IF NOT EXISTS `TopicFields` (
  `ArticleType` varchar(166) NOT NULL DEFAULT '',
  `FieldName` varchar(166) NOT NULL DEFAULT '',
  `RootTopicId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ArticleType`,`FieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;