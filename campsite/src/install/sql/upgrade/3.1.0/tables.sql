ALTER TABLE `ArticleIndex` ADD INDEX `keyword_idx`(`IdKeyword`);
ALTER TABLE `ArticleIndex` ADD UNIQUE INDEX `article_keyword_idx`(`NrArticle`, `IdLanguage`, `IdKeyword`);
