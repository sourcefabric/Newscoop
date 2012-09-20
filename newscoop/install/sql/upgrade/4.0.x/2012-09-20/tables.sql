ALTER TABLE  `Images` CHANGE  `ThumbnailFileName`  `ThumbnailFileName` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '',
CHANGE  `ImageFileName`  `ImageFileName` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `Images` ADD  `is_updated_storage` TINYINT( 1 ) NOT NULL DEFAULT  '0',
ADD INDEX (  `is_updated_storage` );
