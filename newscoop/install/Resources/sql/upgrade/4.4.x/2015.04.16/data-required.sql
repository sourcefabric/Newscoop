ALTER TABLE  `playlist` CHANGE  `notes` `notes` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE  `playlist` CHANGE  `max_items` `max_items` INT( 11 ) NULL;