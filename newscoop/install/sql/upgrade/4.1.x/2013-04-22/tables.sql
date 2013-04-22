ALTER TABLE `package` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `package` CHANGE `headline` `headline` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `package` CHANGE `description` `description` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `package` CHANGE `slug` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
