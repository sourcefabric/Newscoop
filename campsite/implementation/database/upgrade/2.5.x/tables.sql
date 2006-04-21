-- For new Article type features - ability
-- to rename, translate, reorder, and hide them.
CREATE TABLE ArticleTypeMetadata (
    type_name VARCHAR(255) NOT NULL,
    field_name VARCHAR(255),
    field_weight INT,
    is_hidden INT DEFAULT 1,
    fk_phrase_id INT UNSIGNED,
    field_type VARCHAR(255),
    field_type_param VARCHAR(255)
);

-- Change article creation time so we know when
-- it was created, down to the second.
ALTER TABLE `Articles` CHANGE `UploadDate` `UploadDate` DATETIME NOT NULL DEFAULT '0000-00-00';
