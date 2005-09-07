BEGIN;

UPDATE UserPerm SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';
UPDATE UserTypes SET ManageClasses = 'N', ManageDictionary = 'N', DeleteDictionary = 'N';

UPDATE Articles SET PublishDate = UploadDate;
UPDATE ArticlePublish SET Completed='Y' WHERE ActionTime <= NOW();

UPDATE IssuePublish SET Completed='Y' WHERE ActionTime <= NOW();

COMMIT;
