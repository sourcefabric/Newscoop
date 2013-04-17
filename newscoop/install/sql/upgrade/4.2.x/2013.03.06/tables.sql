ALTER TABLE `Plugins` DROP PRIMARY KEY;
ALTER TABLE `Plugins` ADD `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , ADD `Description` TEXT NOT NULL ;

UPDATE `SystemPreferences` SET `value` = 'public/videos/' WHERE `varname` = 'FlashDirectory';