ALTER TABLE `user_topic` DROP PRIMARY KEY;
ALTER TABLE `user_topic` ADD `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `user_topic` ADD UNIQUE `user_topic` (`user_id`, `topic_id`, `topic_language`);
