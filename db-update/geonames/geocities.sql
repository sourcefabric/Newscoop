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

