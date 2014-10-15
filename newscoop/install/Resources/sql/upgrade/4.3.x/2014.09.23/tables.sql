ALTER TABLE `community_ticker_event` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1';
UPDATE `SystemPreferences` SET `value` = 'public/videos/' WHERE `varname` = 'FlashDirectory';