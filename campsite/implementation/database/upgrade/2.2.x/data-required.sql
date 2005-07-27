BEGIN;

UPDATE UserPerm SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';
UPDATE UserTypes SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';

UPDATE Articles, ArticlePublish SET Articles.PublishDate=ArticlePublish.ActionTime WHERE Articles.Number=ArticlePublish.NrArticle AND Articles.IdLanguage=ArticlePublish.IdLanguage;
UPDATE Articles SET PublishDate=UploadDate WHERE PublishDate=0;
UPDATE ArticlePublish SET Completed='Y' WHERE ActionTime <= NOW();

UPDATE IssuePublish SET Completed='Y' WHERE ActionTime <= NOW();

COMMIT;
