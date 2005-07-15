BEGIN;

ALTER TABLE `UserPerm` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `UserTypes` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `Topics` MODIFY COLUMN `Name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `Users` MODIFY COLUMN `CountryCode` VARCHAR(21);
ALTER TABLE `Users` MODIFY COLUMN `Gender` ENUM('M','F');

system php ./update_article_types.php

COMMIT;
