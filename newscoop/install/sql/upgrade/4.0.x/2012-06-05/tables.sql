CREATE TABLE IF NOT EXISTS `webcode` (
  `webcode` varchar(10) NOT NULL,
  `article_number` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`webcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

system php ./populate_webcodes.php
