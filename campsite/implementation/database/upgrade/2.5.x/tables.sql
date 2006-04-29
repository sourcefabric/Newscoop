-- For new Article type features - ability
-- to rename, translate, reorder, and hide them.
--CREATE TABLE ArticleTypeMetadata (
--    type_name VARCHAR(255) NOT NULL,
--    field_name VARCHAR(255) NOT NULL DEFAULT 'NULL',
--    field_weight INT,
--    is_hidden INT DEFAULT 0,
--    fk_phrase_id INT UNSIGNED,
--    field_type VARCHAR(255),
--    field_type_param VARCHAR(255),
--    PRIMARY KEY (`type_name`,`field_name`)
--);

-- Change article creation time so we know when
-- it was created, down to the second.
ALTER TABLE `Articles` CHANGE `UploadDate` `UploadDate` DATETIME NOT NULL DEFAULT '0000-00-00';

-- Change Issue publish time so we know hour, minute, second.
ALTER TABLE `Issues` CHANGE `PublicationDate` `PublicationDate` DATETIME NOT NULL DEFAULT '0000-00-00';

-- Add a "last-modified" field to article table
ALTER TABLE `Articles` ADD `time_updated` TIMESTAMP NOT NULL ;


--
-- Article Comments
--

-- The table to map articles to comment threads and vice versa.
CREATE TABLE `ArticleComments` (
`fk_article_number` INT UNSIGNED NOT NULL ,
`fk_language_id` INT UNSIGNED NOT NULL ,
`fk_comment_thread_id` INT UNSIGNED NOT NULL ,
PRIMARY KEY ( `fk_article_number` , `fk_language_id` ) ,
INDEX ( `fk_comment_thread_id` )
) ENGINE = MYISAM ;


-- The forum ID for article comments.
ALTER TABLE `Publications` ADD `fk_forum_id` INT NULL ;


-- As always, there are more user permissions!
-- Run the user permission upgrade script
-- system php ./upgrade_user_perms.php

system php ./upgrade_article_types.php
