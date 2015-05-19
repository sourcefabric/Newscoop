ALTER TABLE playlist_article DROP INDEX id_playlist;
ALTER TABLE `playlist_article` ADD KEY `IDX_BD05197C8759FDB8` (`id_playlist`), ADD KEY `IDX_BD05197CAA07C9D3813385DE` (`article_no`,`article_language`);
ALTER TABLE `playlist_article` ADD `article_language` INT(11) NOT NULL;
UPDATE `playlist_article` AS pa LEFT JOIN Articles AS a ON pa.`article_no` = a.`Number` SET pa.`article_language` = a.`IdLanguage`;