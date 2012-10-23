ALTER TABLE  `Images` ADD  `is_updated_storage` TINYINT( 1 ) NOT NULL DEFAULT  '0',
ADD INDEX (  `is_updated_storage`, `Location`, `ImageFileName` );
