-- Create table for template cache db handler
CREATE TABLE IF NOT EXISTS `Cache` (
  `language` int(11) default NULL,
  `publication` int(11) default NULL,
  `issue` int(11) default NULL,
  `section` int(11) default NULL,
  `article` int(11) default NULL,
  `params` varchar(128) default NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`params`,`template`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Add CacheLifetime column for template cache handler
ALTER TABLE `Templates` ADD `CacheLifetime` INT NULL DEFAULT '0';


ALTER TABLE `Authors` ADD `type` INT NULL ,
ADD `skype` VARCHAR( 255 ) NULL ,
ADD `jabber` VARCHAR( 255 ) NULL ,
ADD `aim` VARCHAR( 255 ) NULL ,
ADD `biography` TEXT NULL ,
ADD `image` INT NULL;
DROP TABLE IF EXISTS `Authorsaliases`;
CREATE TABLE `Authorsaliases` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`IdAuthor` INT NOT NULL ,
`alias` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;
DROP TABLE IF EXISTS `Authorbiography`;
CREATE TABLE `Authorbiography` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`IdAuthor` INT NOT NULL ,
`IdLanguage` INT NOT NULL ,
`biography` TEXT NOT NULL,
`first_name` VARCHAR( 255 ) NULL ,
`last_name` VARCHAR( 255 ) NULL
) ENGINE = MYISAM ;


ALTER TABLE `ArticleAuthors` CHANGE `fk_article_number` `fk_article_number` INT( 10 ) UNSIGNED NULL ,
CHANGE `fk_language_id` `fk_language_id` INT( 10 ) UNSIGNED NULL ,
CHANGE `fk_author_id` `fk_author_id` INT( 10 ) UNSIGNED NULL,
ADD `fk_type_id` INT NULL ;

CREATE TABLE `AuthorsTypes` (
`id` INT NULL AUTO_INCREMENT PRIMARY KEY ,
`type` VARCHAR( 255 ) NULL
) ENGINE = MYISAM ;
CREATE TABLE `AuthorsAuthorsTypes` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`fk_author_id` INT NOT NULL ,
`fk_type_id` INT NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `liveuser_users` ADD COLUMN `password_reset_token` VARCHAR(85) NULL  AFTER `isActive` ;
-- for searching lon/lat of cities via their names



CREATE TABLE CityLocations (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
--  id to distinguish a particular city
    city_id int(10) unsigned NOT NULL,
--  city type from the administartive point of view
    city_type varchar(10),
--  city population, used e.g. for names sorting
    population int(10) unsigned NOT NULL,
--  the main info for city location
    position POINT NOT NULL,
--  for situations where some comparisons on just latitude necessary, not exact herein
    latitude float NOT NULL,
--  for situations where some comparisons on just longitude necessary, not exact herein
    longitude float NOT NULL,
--  elevation, or average elevation (if elevation not available), or NULL
    elevation int,
--  ISO-3166 2-letter country code
    country_code char(2) NOT NULL,
--  time zone (e.g. continent/city)
    time_zone varchar(1023) NOT NULL,
--  if we modify something by ourselves, we will set the modification date into some distant future to preserve the change
    modified timestamp NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY city_id (city_id),
    KEY city_type (city_type),
    KEY population (population),
    SPATIAL INDEX(position),
    KEY latitude (latitude),
    KEY longitude (longitude),
    KEY elevation (elevation),
    KEY country_code (country_code),
    KEY time_zone (time_zone)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CityNames (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
--  fk into CityLocations table
    city_id int(10) NOT NULL,
--  one of the possible names for a city
    city_name varchar(1023) NOT NULL,
--  main name ("main"), ascii name ("ascii"), ascii lower case name ("lower"), alternative name ("other")
    name_type varchar(10) NOT NULL,
    PRIMARY KEY (id),
    KEY city_id (city_id),
    KEY city_name (city_name),
    KEY name_type (name_type)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




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


