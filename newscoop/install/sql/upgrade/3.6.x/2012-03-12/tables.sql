
DROP TABLE IF EXISTS `Versions`;
CREATE TABLE `Versions` (
    name varchar(255),
    version varchar(255)
);

ALTER TABLE `article_datetimes` ADD COLUMN `event_comment` TEXT;

