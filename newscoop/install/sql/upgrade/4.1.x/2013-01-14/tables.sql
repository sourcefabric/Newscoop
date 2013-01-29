CREATE TABLE IF NOT EXISTS `user_topic` (
  `user_id` int(11) unsigned NOT NULL,
  `topic_id` int(11) unsigned NOT NULL,
  `topic_language` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`,`topic_language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user_topic` DROP PRIMARY KEY;
ALTER TABLE `user_topic` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `user_topic` ADD UNIQUE `user_topic` (`user_id`, `topic_id`, `topic_language`);
