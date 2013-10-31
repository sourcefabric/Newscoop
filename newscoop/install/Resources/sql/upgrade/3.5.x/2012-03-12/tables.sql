
DROP TABLE IF EXISTS `Versions`;
CREATE TABLE `Versions` (
    `id` integer unsigned NOT NULL auto_increment,
    `ver_name` varchar(255) NOT NULL,
    `ver_value` varchar(255) NOT NULL default '',
    `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ver_name` (`ver_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `article_datetimes` ADD COLUMN `event_comment` TEXT;

