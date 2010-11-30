-- some inserted data are utf-8 encoded
set names utf8;

-- template cache
DELETE FROM `SystemPreferences` WHERE `varname` ='TemplateCacheHandler';
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('TemplateCacheHandler', NULL);
UPDATE `SystemPreferences` SET `varname` = 'DBCacheEngine', `value` = NULL WHERE `varname` ='CacheEngine';

-- add new events for the authors management
INSERT INTO `Events` (`Id`,`Name`,`Notify`,`IdLanguage`) VALUES ('172','Add Author','N','1'),('173','Edit Author','N','1'),('174','Delete Author','N','01');

-- add default author types
INSERT INTO `AuthorTypes` (`id`,`type`) VALUES (NULL,'Author'),(NULL,'Writer'),(NULL,'Photographer'),(NULL,'Editor'),(NULL,'Columnist');

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
-- map providers available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderNames', 'GoogleV3,OSM');
-- Google map provider available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderAvailableGoogleV3', '1');
-- JS script to include for Google maps api v3
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderIncludeGoogleV3', 'http://maps.google.com/maps/api/js?sensor=false');
-- OpenStreetMap map provider available to be set for articles
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderAvailableOSM', '1');
-- OpenStreetMap API is inside the OpenLayers
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderIncludeOSM', '');
-- the default map provider is Google maps api v3
INSERT INTO SystemPreferences (varname, value) VALUES ('MapProviderDefault', 'GoogleV3');

-- POI markers
-- what marker figures are available
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerDirectory', '/javascript/geocoding/markers/');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerNames', 'gold,blue,red,green');
-- file names of POI markers, with shifts of the marker figures so that its tips point correctly
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceGold', 'marker-gold.png');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetXGold', '-10');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetYGold', '-20');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceBlue', 'marker-blue.png');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetXBlue', '-10');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetYBlue', '-20');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceRed', 'marker-red.png');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetXRed', '-10');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetYRed', '-20');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceGreen', 'marker-green.png');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetXGreen', '-10');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerOffsetYGreen', '-20');
-- default marker figure to be used
INSERT INTO SystemPreferences (varname, value) VALUES ('MapMarkerSourceDefault', 'gold');

-- Pop-up setting
-- default pop-up width
INSERT INTO SystemPreferences (varname, value) VALUES ('MapPopupWidthDefault', '100');
-- default pop-up width
INSERT INTO SystemPreferences (varname, value) VALUES ('MapPopupHeightDefault', '100');

-- Pop-up multimedia content
-- video providers available
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoNames', 'YouTube,Vimeo');
-- youtube wideo setting
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoAvailableYouTube', '1');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoSourceYouTube', '<object width="%%w%%" height="%%h%%"><param name="movie" value="http://www.youtube.com/v/%%id%%"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/%%id%%" type="application/x-shockwave-flash" wmode="transparent" width="%%w%%" height="%%h%%"></embed></object>');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthYouTube', '425');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightYouTube', '350');
-- vimeo video setting
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoAvailableVimeo', '1');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoSourceVimeo', '<object width="%%w%%" height="%%h%%"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" /><embed src="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="%%w%%" height="%%h%%"></object>');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoWidthVimeo', '400');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoHeightVimeo', '225');
-- default video provider
INSERT INTO SystemPreferences (varname, value) VALUES ('MapVideoDefault', 'YouTube');
-- names of audio type available
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioNames', 'ogg,mp3,wav');
-- settings for audio types
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeOgg', 'audio/ogg');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeMp3', 'audio/mpeg');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeWav', 'audio/vnd.wave');
-- default audio type
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioTypeDefault', 'ogg');
-- setting for the audio html object
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioAutoStart', 'false');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioSite', '');
INSERT INTO SystemPreferences (varname, value) VALUES ('MapAudioObject', '<object><param name="src" value="%%site%%%%track%%"><param name="autostart" value="%%auto%%"><param name="autoplay" value="%%auto%%"><param name="controller" value="true"><embed src="%%site%%%%track%%" controller="true" autoplay="%%auto%%" autostart="%%auto%%" type="%%type%%" /></object>');

-- Geo Names
source geonames.sql


