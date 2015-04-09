ALTER TABLE `Articles` CHANGE `Keywords` `Keywords` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE playlist_article ADD `order_number` INT NOT NULL;
ALTER TABLE `playlist` ADD `max_items` INT NULL DEFAULT NULL ;