-- ticket #2220 - A new field to input section description
ALTER TABLE `Sections` ADD `Description` BLOB AFTER ShortName;

-- create the ArticleAudioclips table
CREATE TABLE ArticleAudioclips (
    fk_article_number INT(10) UNSIGNED NOT NULL DEFAULT 0,
    fk_audioclip_gunid VARCHAR(16) NOT NULL,
    PRIMARY KEY (fk_article_number, fk_audioclip_gunid)
);
