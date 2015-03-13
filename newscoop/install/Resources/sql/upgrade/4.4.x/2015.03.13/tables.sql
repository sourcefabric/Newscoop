CREATE TABLE IF NOT EXISTS `editorial_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_article_number` int(11) NOT NULL,
  `fk_language_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `resolved` int(1) NOT NULL DEFAULT '0',
  `fk_parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `is_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
ALTER TABLE  `editorial_comments` CHANGE  `comment`  `comment` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;