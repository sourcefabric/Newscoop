# Port some schema chnages from tageswoche to newscoop
ALTER TABLE `ArticleImages` DROP PRIMARY KEY;
ALTER TABLE `ArticleImages` ADD `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `ArticleImages` ADD UNIQUE (`NrArticle`, `IdImage`);
ALTER TABLE `Articles` CHANGE `indexed` `indexed` DATETIME NULL DEFAULT NULL;
ALTER TABLE `Articles` ADD INDEX `indexed` ( `indexed` );
ALTER TABLE `Authors` ADD `user_id` INT NOT NULL;
ALTER TABLE `Images` ADD `photographer_url` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `MapLocations` ADD INDEX `rank` ( `rank`,`id` );
ALTER TABLE `audit_event` CHANGE `resource_id` `resource_id` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `comment` CHANGE `recommended` `recommended` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `comment` ADD `indexed` DATETIME NULL;
ALTER TABLE `comment` ADD INDEX `indexed` ( `indexed` );
ALTER TABLE `liveuser_users` ADD `indexed` DATETIME NULL ;
ALTER TABLE `liveuser_users` ADD INDEX `indexed` ( `indexed` ) ;
ALTER TABLE `playlist_article` CHANGE `id_playlist_article` `id_playlist_article` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `user_attribute` CHANGE `value` `value` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
