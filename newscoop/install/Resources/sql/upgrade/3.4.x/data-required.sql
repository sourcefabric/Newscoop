-- some inserted data are utf-8 encoded
set names utf8;

-- template cache
DELETE FROM `SystemPreferences` WHERE `varname` ='TemplateCacheHandler';
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('TemplateCacheHandler', NULL);
UPDATE `SystemPreferences` SET `varname` = 'DBCacheEngine', `value` = NULL WHERE `varname` ='CacheEngine';

-- add new events for the authors management
INSERT INTO `Events` (`Id`,`Name`,`Notify`,`IdLanguage`) VALUES (172,'Add Author','N',1),(173,'Edit Author','N',1),(174,'Delete Author','N',1),(175,'Add author type','N',1),(176,'Delete author type','N',1);

-- add default author types
INSERT INTO `AuthorTypes` (`id`,`type`) VALUES (NULL,'Author'),(NULL,'Writer'),(NULL,'Photographer'),(NULL,'Editor'),(NULL,'Columnist');

-- remove empty authors
CREATE TEMPORARY TABLE `EmptyAuthorsTmp` SELECT DISTINCT `id` FROM `Authors` WHERE `first_name` = '' AND `last_name` = '';
DELETE FROM `Authors` WHERE `id` IN (SELECT `id` FROM `EmptyAuthorsTmp` GROUP BY `id`);
DELETE FROM `ArticleAuthors` WHERE `fk_author_id` IN (SELECT `id` FROM `EmptyAuthorsTmp` GROUP BY `id`);
DROP TEMPORARY TABLE `EmptyAuthorsTmp`;

-- add creator as author for articles where author is not defined
UPDATE Articles SET fk_default_author_id = IdUser WHERE fk_default_author_id = 0;

-- add authors from Articles table to ArticleAuthors
INSERT IGNORE INTO `ArticleAuthors` (`fk_article_number`,`fk_language_id`,`fk_author_id`)
    SELECT `Number`, `IdLanguage`, `fk_default_author_id` FROM Articles;

-- set the default author type to "Author" for all the links and authors
SET @rid := (SELECT `id` FROM `AuthorTypes` WHERE type = 'Author');
UPDATE `ArticleAuthors` SET `fk_type_id` = @rid;
INSERT IGNORE INTO `AuthorAssignedTypes` (`fk_author_id`) SELECT `id` FROM `Authors`;
UPDATE `AuthorAssignedTypes` SET `fk_type_id` = @rid;

-- remove author column from Articles table
ALTER TABLE Articles DROP COLUMN fk_default_author_id;

-- add system setting for password recovery
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('PasswordRecovery','Y');

-- call additional db upgrade script
system php ./update_rights.php;



-- Map setting
-- initial/default center of the map view
INSERT INTO SystemPreferences (varname, value) VALUES ('MapCenterLongitudeDefault', '14.424133');
-- initial/default center of the map view
INSERT INTO SystemPreferences (varname, value) VALUES ('MapCenterLatitudeDefault', '50.089926');
-- initial/default resolution of the map view
INSERT INTO SystemPreferences (varname, value) VALUES ('MapDisplayResolutionDefault', '4');
-- sizes of the map div for article display
INSERT INTO SystemPreferences (varname, value) VALUES ('MapViewWidthDefault', '600');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapViewHeightDefault', '400');

-- map providers available to be set for articles
-- Google map provider available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderAvailableGoogleV3', '1');
-- MapQuest map provider available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderAvailableMapQuest', '1');
-- OpenStreetMap map provider available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderAvailableOSM', '1');
-- the default map provider is Google maps api v3
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderDefault', 'GoogleV3');

-- POI markers
-- what marker figures are available
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerDirectory', '/js/geocoding/markers/');
-- default marker figure to be used
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceDefault', 'marker-gold.png');


-- Pop-up setting
-- min. pop-up width
INSERT INTO SystemPreferences (varname, value) VALUES ('MapPopupWidthMin', '200');
-- min. pop-up width
INSERT INTO SystemPreferences (varname, value) VALUES ('MapPopupHeightMin', '150');

-- Pop-up multimedia content
-- youtube wideo setting
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthYouTube', '425');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightYouTube', '350');
-- vimeo video setting
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthVimeo', '400');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightVimeo', '225');

INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthFlash', '425');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightFlash', '350');

INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthFlv', '300');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightFlv', '280');

INSERT INTO SystemPreferences (varname, value) VALUES ('FlashServer', '');
INSERT INTO SystemPreferences (varname, value) VALUES ('FlashDirectory', 'videos/');


-- names of audio type available
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioNames', 'ogg,mp3,wav');
-- settings for audio types
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeOgg', 'audio/ogg');
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeMp3', 'audio/mpeg');
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeWav', 'audio/vnd.wave');
-- default audio type
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeDefault', 'ogg');
-- setting for the audio html object
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioAutoStart', 'false');
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioSite', '');
-- INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioObject', '<object><param name="src" value="%%site%%%%track%%"><param name="autostart" value="%%auto%%"><param name="autoplay" value="%%auto%%"><param name="controller" value="true"><embed src="%%site%%%%track%%" controller="true" autoplay="%%auto%%" autostart="%%auto%%" type="%%type%%" /></object>');

-- Geo Names
system php ./load_geonames_data.php

-- Topics refactoring
system php ./transfer_topics.php
