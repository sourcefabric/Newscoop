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



-- article info should be set during the article creation/modification
ALTER TABLE Articles ADD COLUMN MapUsage tinyint NOT NULL DEFAULT 0;
-- initial map center
ALTER TABLE Articles ADD COLUMN MapCenterLongitude REAL NOT NULL DEFAULT 0;
ALTER TABLE Articles ADD COLUMN MapCenterLatitude REAL NOT NULL DEFAULT 0;
-- initial map resolution
ALTER TABLE Articles ADD COLUMN MapDisplayResolution smallint NOT NULL DEFAULT 0;
-- the map to be used for readers
ALTER TABLE Articles ADD COLUMN MapProvider VARCHAR(1023) NOT NULL DEFAULT "";


-- locations stored at a table, alike attachments and images, but languages are dealt differently
-- one location may be used at more articles, languages, events, e.g. those with complex descriptions
-- was thinking about collections of pois (for feature sharing), but it would be hard for search and so
CREATE TABLE Locations (
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

-- geometrical location of the points of interests, [via map]
-- can be POINT, LINE, POLYGON
    poi_location GEOMETRY NOT NULL,
-- what is geometry of the poi (point, line, area) [point]
-- may be to set the type to enum('point', 'line', 'area')
    poi_type VARCHAR(40) NOT NULL,

-- how the poi should be displayed, a style name, e.g. marker name [system default]
    poi_type_style VARCHAR(1023) NOT NULL,

-- geometry simplification
    poi_center POINT NOT NULL,
    poi_radius REAL NOT NULL DEFAULT 0,

-- management related things
    fk_user_id int(10) unsigned DEFAULT NULL,
    last_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    time_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',

    PRIMARY KEY (id),
    SPATIAL INDEX poi_location (poi_location),
    KEY poi_type (poi_type),
    KEY poi_type_style (poi_type),
    SPATIAL INDEX poi_center (poi_center),
    KEY poi_radius (poi_radius)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- poi contents, together with relations among locations and articles, events
-- this would be alike articles, i.e. contents plus foreign keys to unique specifiers
CREATE TABLE LocationContents (
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
-- specifying the article by its number and language
    fk_article_number int(10) unsigned NOT NULL DEFAULT 0,
--  language of the poi, different languages shall have different texts
    fk_language_id int(10) unsigned NOT NULL DEFAULT 0,
-- specifying the location by its id
    fk_location_id int(10) unsigned NOT NULL DEFAULT 0,
-- and to put event_id here too; for now zeroed
    fk_event_id int(10) unsigned NOT NULL DEFAULT 0,

-- published date, put here too (from the Articles table) for faster search
    publish_date date,

-- usage of the POI for the article/language/event
-- i.e. whether the POI should be displayed for readers
    poi_display tinyint NOT NULL DEFAULT 1,

-- pop-up style features
-- whether the POI should have popup enaibled
    poi_popup tinyint NOT NULL DEFAULT 1,
-- popup default size, [system default]
    poi_popup_size_width int NOT NULL,
    poi_popup_size_height int NOT NULL,

-- main label for the POI
    poi_name VARCHAR(1023),

-- whether to use content of poi's popup directly instead of link, image, etc. [false]
--  to use either the (rich) content or link/text/image/video/audio
    poi_content_usage tinyint NOT NULL DEFAULT 0,
-- the whole html content for POI popup (if set to be used)
    poi_content TEXT NOT NULL DEFAULT "",

-- textual content
-- short description to be shown at a side panel
    poi_perex VARCHAR(15100),
--    poi_perex TEXT,
-- link from the POI popup window
    poi_link VARCHAR(1023),
-- text at the POI popup content
    poi_text TEXT,

-- multimedia content
-- image at the POI popup content
    poi_image_src VARCHAR(1023) NOT NULL DEFAULT "",

-- embedded video object at the POI popup content
    poi_video_id VARCHAR(1023) NOT NULL DEFAULT "",
-- type of the embedded video object, [system default]
    poi_video_type VARCHAR(255),
-- video display size, [system default]
    poi_video_height int,
    poi_video_width int,

-- embedded audio object at the POI popup content
    poi_audio_usage tinyint NOT NULL DEFAULT 0,
-- type of the embedded audio object, [system default]
    poi_audio_type VARCHAR(255),
-- audio track specification
    poi_audio_site VARCHAR(1023),
    poi_audio_track VARCHAR(1023) NOT NULL DEFAULT "",
    poi_audio_auto tinyint NOT NULL DEFAULT 0,

-- management related things
    fk_user_id int(10) unsigned DEFAULT NULL,
    last_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    time_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',

-- specifying the rows by unique way
    PRIMARY KEY (id),
    UNIQUE KEY article_location_index (fk_article_number, fk_language_id, fk_location_id, fk_event_id),
-- keys for reasonable access
    KEY fk_article_number (fk_article_number),
    KEY fk_language_id (fk_language_id),
    KEY fk_location_id (fk_location_id),
    KEY fk_event_id (fk_event_id),

    KEY publish_date (publish_date),
    KEY poi_display (poi_display),
    KEY poi_popup (poi_popup),

    KEY poi_name (poi_name)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


