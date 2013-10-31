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

-- Change fields to proper data type, add new fk_type_id and change primary key to include the new field as part of it
ALTER TABLE ArticleAuthors DROP PRIMARY KEY;
ALTER TABLE `ArticleAuthors` CHANGE `fk_article_number` `fk_article_number` INT(10) UNSIGNED NULL DEFAULT '0', CHANGE `fk_language_id` `fk_language_id` INT(10) UNSIGNED NULL DEFAULT '0', CHANGE `fk_author_id` `fk_author_id` INT(10) UNSIGNED NULL DEFAULT '0', ADD `fk_type_id` INT(10) UNSIGNED NULL DEFAULT '0', ADD PRIMARY KEY(`fk_article_number`,`fk_language_id`,`fk_author_id`,`fk_type_id`);

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

--
-- Table structure for table `CityLocations`
--

DROP TABLE IF EXISTS `CityLocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CityLocations` (
  `id` int(10) unsigned NOT NULL,
  `city_type` varchar(10) DEFAULT NULL,
  `population` int(10) unsigned NOT NULL,
  `position` point NOT NULL,
  `elevation` int(11) DEFAULT NULL,
  `country_code` char(2) NOT NULL,
  `time_zone` varchar(1023) NOT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CityNames`
--

DROP TABLE IF EXISTS `CityNames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CityNames` (
  `fk_citylocations_id` int(10) NOT NULL,
  `city_name` varchar(1024) NOT NULL,
  `name_type` varchar(10) NOT NULL,
  KEY (`fk_citylocations_id`),
  KEY (`city_name`(32))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


-- Create table for widget context - widget relation
DROP TABLE IF EXISTS `WidgetContext_Widget`;
CREATE TABLE IF NOT EXISTS `WidgetContext_Widget` (
  `id` varchar(13) NOT NULL,
  `fk_widgetcontext_id` smallint(3) unsigned NOT NULL,
  `fk_widget_id` mediumint(8) unsigned NOT NULL,
  `fk_user_id` int(10) unsigned NOT NULL,
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `settings` TEXT(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`, `fk_user_id`),
  INDEX (`fk_user_id`, `fk_widgetcontext_id`, `order`)
);



-- it goes from Articles into Maps (one Map at a single Article),
--   and then from Maps via MapLocations into Locations
-- NOTE: the Maps-Locations relationships is M:N because of COW,
--   otherwise a location would be at a single map

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
    MapName VARCHAR(1023) NOT NULL,

--  management related things
    IdUser int(10) unsigned NOT NULL DEFAULT 0,
    time_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY maps_article_number (fk_article_number),
    KEY maps_map_name (MapName(64))
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
    KEY map_locations_map_id (fk_map_id)
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
    KEY map_location_languages_content_id (fk_content_id)
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
    IdUser int(10) unsigned NOT NULL DEFAULT 0,
    time_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    SPATIAL INDEX locations_poi_location (poi_location)
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
    IdUser int(10) unsigned NOT NULL DEFAULT 0,
    time_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

--  specifying the rows by unique way
    PRIMARY KEY (id),

    KEY location_contents_poi_name (poi_name(64))

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

--  other options, e.g. for a player
    options VARCHAR(1023) NOT NULL DEFAULT "",

--  management related things
    IdUser int(10) unsigned NOT NULL DEFAULT 0,
    time_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    KEY multimedia_media_type (media_type(32)),
    KEY multimedia_media_src (media_src(64)),

    PRIMARY KEY (id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE MapLocationMultimedia (

    id int(10) unsigned NOT NULL AUTO_INCREMENT,

--  description belongs to a map-location point and a language
    fk_maplocation_id int(10) unsigned NOT NULL DEFAULT 0,

--  description belongs to a map-location point and a language
    fk_multimedia_id int(10) unsigned NOT NULL DEFAULT 0,

    PRIMARY KEY (id),

    KEY maplocationmultimedia_maplocation_id (fk_maplocation_id),
    KEY maplocationmultimedia_multimedia_id (fk_multimedia_id)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- Topics table refactoring
ALTER TABLE Topics RENAME TopicsOld;

CREATE TABLE Topics (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    node_left int(10) unsigned NOT NULL,
    node_right int(10) unsigned NOT NULL,
    PRIMARY KEY (id),
    INDEX(node_left),
    INDEX(node_right)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE TopicNames (
    fk_topic_id int(10) unsigned NOT NULL,
    fk_language_id int(10) unsigned NOT NULL,
    name varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (fk_topic_id, fk_language_id),
    UNIQUE KEY (fk_language_id, name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
