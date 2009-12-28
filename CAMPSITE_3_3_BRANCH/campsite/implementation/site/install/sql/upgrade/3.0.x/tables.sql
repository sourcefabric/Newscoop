ALTER TABLE ArticleTypeMetadata ADD COLUMN is_content_field BOOLEAN NOT NULL DEFAULT FALSE;

ALTER TABLE Articles ADD COLUMN object_id INT;

CREATE TABLE `ObjectTypes` (
  `id` INT NOT NULL auto_increment,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `OBJECTTYPES_NAME` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Plugins` (
  `Name` varchar(255) NOT NULL,
  `Version` varchar(255) NOT NULL,
  `Enabled` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Sessions` (
  `id` VARCHAR(255)  NOT NULL,
  `start_time` DATETIME  NOT NULL,
  `user_id` INT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Requests` (
  `session_id` VARCHAR(255) NOT NULL,
  `object_id` INT NOT NULL,
  `request_count` INT NOT NULL,
  `last_request_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RequestObjects` (
  `object_id` INT  NOT NULL auto_increment,
  `object_type_id` INT  NOT NULL,
  `request_count` INT  NOT NULL,
  `last_update_time` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
