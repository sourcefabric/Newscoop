-- Create table for template cache db handler
CREATE TABLE IF NOT EXISTS `Cache` (
  `language` int(11) unsigned default NULL,
  `publication` int(11) unsigned default NULL,
  `issue` int(11) unsigned default NULL,
  `section` int(11) unsigned default NULL,
  `article` int(11) unsigned default NULL,
  `params` varchar(128) default NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`params`,`template`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Add CacheLifetime column for template cache handler
ALTER TABLE `Templates` ADD `CacheLifetime` INT NULL DEFAULT '0';

-- Create tables for authors management
CREATE TABLE IF NOT EXISTS `AuthorAliases` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_author_id` int(11) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorAssignedTypes` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_author_id`,`fk_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorBiographies` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(11) unsigned NOT NULL DEFAULT '0',
  `biography` text NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`fk_author_id`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Add new fields to store some more author data
ALTER TABLE `Authors` ADD `type` INT(10) UNSIGNED NULL, ADD `skype` VARCHAR(255) NULL, ADD `jabber` VARCHAR(255) NULL, ADD `aim` VARCHAR(255) NULL, ADD `biography` TEXT NULL, ADD `image` INT NULL;

-- Change fileds to proper data type
ALTER TABLE `ArticleAuthors` CHANGE `fk_article_number` `fk_article_number` INT(10) UNSIGNED NULL, CHANGE `fk_language_id` `fk_language_id` INT(10) UNSIGNED NULL, CHANGE `fk_author_id` `fk_author_id` INT(10) UNSIGNED NULL, ADD `fk_type_id` INT(10) UNSIGNED NULL;

-- Add new column to store the token in password recovering
ALTER TABLE `liveuser_users` ADD COLUMN `password_reset_token` VARCHAR(85) NULL AFTER `isActive`;

ALTER TABLE Images ADD FULLTEXT(Description);
ALTER TABLE Images ADD FULLTEXT(Photographer);
ALTER TABLE Images ADD FULLTEXT(Place);
ALTER TABLE Images ADD FULLTEXT(Caption);

-- Create table for widgets
DROP TABLE IF EXISTS `Widget`;
CREATE TABLE IF NOT EXISTS `Widget` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(78) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`path`, `class`)
);

-- Create table for widget context
DROP TABLE IF EXISTS `WidgetContext`;
CREATE TABLE IF NOT EXISTS `WidgetContext` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);


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


-- Create table for widget context - widget relation
DROP TABLE IF EXISTS `WidgetContext_Widget`;
CREATE TABLE IF NOT EXISTS `WidgetContext_Widget` (
  `id` varchar(13) NOT NULL,
  `fk_widgetcontext_id` smallint(3) unsigned NOT NULL,
  `fk_widget_id` mediumint(8) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL,
  `order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `settings` TEXT(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`, `fk_user_id`),
  INDEX (`fk_user_id`, `fk_widgetcontext_id`, `order`)
);



-- it goes from Articles into Maps (one Map at a single Article),
--   and then from Maps via MapLocations into Locations
-- NOTE: the Maps-Locations relationships is M:N because of COW,
--   otherwise a location would be at a single map

-- SELECT DISTINCT m.fk_article_number AS art FROM Maps AS m INNER JOIN MapLocations AS ml ON m.id = ml.fk_map_id INNER JOIN
--  Locations AS l ON ml.fk_location_id = l.id WHERE
--  MBRIntersects(GeomFromText(’Polygon((x0 y0,x0 y1,x1 y1,x1 y0,x0 y0))’),l.poi_location);

-- basic info on maps themselves
CREATE TABLE Maps
(
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  link to the respective article
    fk_article_number int(10) unsigned NOT NULL DEFAULT 0,

--  rank of the map in the article
    MapRank int unsigned NOT NULL DEFAULT 1,
--  so that it will be possible to disable the map for an article without any deletion
    MapUsage tinyint NOT NULL DEFAULT 1,

--  initial map center
    MapCenterLongitude REAL NOT NULL DEFAULT 0,
    MapCenterLatitude REAL NOT NULL DEFAULT 0,
--  initial map resolution
    MapDisplayResolution smallint NOT NULL DEFAULT 0,
--  the map to be used for readers
    MapProvider VARCHAR(255) NOT NULL DEFAULT "",
--  the map div size
    MapWidth int NOT NULL DEFAULT 0,
    MapHeight int NOT NULL DEFAULT 0,
--  the map name
    MapName VARCHAR(255) NOT NULL,

    PRIMARY KEY (id),
    KEY maps_article_number (fk_article_number),
    KEY maps_map_name (MapName)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- connecting maps and locations
-- NOTE: copy-on-write is done for positions
CREATE TABLE MapLocations
(
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  link to the respective map
    fk_map_id int(10) unsigned NOT NULL DEFAULT 0,

--  link to the respective point
    fk_location_id int(10) unsigned NOT NULL DEFAULT 0,

--  how the poi should be displayed, a style name, e.g. marker name [system default]
    poi_style VARCHAR(1023) NOT NULL,

--  display sequence rank
--  NOTE: this is shared between all languages of a single map
    rank int NOT NULL,

    PRIMARY KEY (id),
    KEY map_locations_point_id (fk_location_id),
    KEY map_locations_map_id (fk_map_id),
    KEY(rank)

-- UNIQUE KEY map_locations_map_location_orig (fk_map_id, fk_location_orig)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE MapLocationLanguages
(
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  description belongs to a map-location point and a language
    fk_maplocation_id int(10) unsigned NOT NULL DEFAULT 0,

--  the language for a POI content
    fk_language_id int(10) unsigned NOT NULL DEFAULT 0,

--  link to the content itself
    fk_content_id int(10) unsigned NOT NULL DEFAULT 0,

--  whether the POI should be displayed for readers
    poi_display tinyint NOT NULL DEFAULT 1,

    PRIMARY KEY (id),

    KEY map_location_languages_maplocation_id (fk_maplocation_id),
    KEY map_location_languages_language_id (fk_language_id),
    UNIQUE KEY map_locations_languages_maplocation_id_language (fk_maplocation_id, fk_language_id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- geographical locations by themselves
CREATE TABLE Locations (
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  geometrical location of the points of interests, [via map]
--  can be POINT, LINE, POLYGON
    poi_location GEOMETRY NOT NULL,
--  what is geometry of the poi (point, line, area) [point]
--  may be to set the type to enum('point', 'line', 'area')
    poi_type VARCHAR(40) NOT NULL,
--  spec. of visual representation of the POI, if any
    poi_type_style int NOT NULL DEFAULT 0,

--  geometry simplification
    poi_center POINT NOT NULL,
    poi_radius REAL NOT NULL DEFAULT 0,

--  management related things
    fk_user_id int(10) unsigned DEFAULT NULL,
    last_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    time_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',

    PRIMARY KEY (id),
    SPATIAL INDEX poi_location (poi_location),
    KEY poi_type (poi_type),
    KEY poi_type_style (poi_type_style),
    SPATIAL INDEX poi_center (poi_center),
    KEY poi_radius (poi_radius)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- descriptions for locations, with COW
CREATE TABLE LocationContents (
--  ordinary row id
    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  main label for the POI
    poi_name VARCHAR(1023) NOT NULL,
--  link from the POI popup window
    poi_link VARCHAR(1023) NOT NULL DEFAULT "",

--  textual content
--  short description to be shown at a side panel
    poi_perex VARCHAR(15100) NOT NULL DEFAULT "",
--  to use either the html content (0) or plain text (1)
--    for now, both of them with label_link/image/video if provided
    poi_content_type tinyint NOT NULL DEFAULT 0,
--  the whole html content for POI popup (if set to be used)
    poi_content TEXT NOT NULL DEFAULT "",
--  text at the POI popup content
    poi_text TEXT NOT NULL DEFAULT "",

--  management related things
--    fk_user_id int(10) unsigned DEFAULT NULL,
--    last_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--    time_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',

--  specifying the rows by unique way
    PRIMARY KEY (id),

    KEY poi_name (poi_name)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Multimedia (

    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  image / video / ...
    media_type VARCHAR(255) NOT NULL DEFAULT "",

--  for video: none/youtube/vimeo/flash
    media_spec VARCHAR(255) NOT NULL DEFAULT "",

--  media at the POI popup content
    media_src VARCHAR(1023) NOT NULL DEFAULT "",
--  video display size
    media_height int NOT NULL DEFAULT 0,
    media_width int NOT NULL DEFAULT 0,

    PRIMARY KEY (id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE MapLocationMultimedia (

    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  description belongs to a map-location point and a language
    fk_maplocation_id int(10) unsigned NOT NULL DEFAULT 0,

--  description belongs to a map-location point and a language
    fk_multimedia_id int(10) unsigned NOT NULL DEFAULT 0,

    PRIMARY KEY (id),

    KEY mapLocationmultimedia_maplocation_id (fk_maplocation_id),
    KEY mapLocationmultimedia_multimedia_id (fk_multimedia_id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


