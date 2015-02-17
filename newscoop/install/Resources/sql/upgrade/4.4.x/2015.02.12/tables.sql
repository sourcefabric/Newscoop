ALTER TABLE  `editorial_comments` CHANGE  `comment`  `comment` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `user_topic` DROP `topic_language`;