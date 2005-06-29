BEGIN;

ALTER TABLE `UserPerm` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `UserTypes` ADD COLUMN `ManageReaders` ENUM('N', 'Y') NOT NULL DEFAULT 'N';
ALTER TABLE `Topics` CHANGE COLUMN `Name` `Name` varchar(255) NOT NULL default '';

COMMIT;
