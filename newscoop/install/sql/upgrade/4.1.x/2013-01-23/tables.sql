ALTER TABLE `Articles` ADD `rating_enabled` TINYINT(1) DEFAULT 1;

CREATE TABLE IF NOT EXISTS `user_identity` (
  `provider` varchar(80) NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`provider`, `provider_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
