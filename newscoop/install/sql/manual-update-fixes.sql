DELETE FROM CityLocations;
DELETE FROM CityNames;
UPDATE plugin_blog_comment SET fk_user_id = 1;
UPDATE plugin_blog_entry SET fk_user_id = 1;

DELETE FROM resource;
DELETE FROM output_issue;
DELETE FROM output_theme;

UPDATE Articles SET LockUser = 0;
UPDATE Articles SET IdUser = 1;

UPDATE `Languages` SET `CodePage` = 'ko' WHERE `Name` = 'Korean';
UPDATE `Languages` SET `CodePage` = 'be' WHERE `Name` = 'Belarus';
UPDATE `Languages` SET `CodePage` = 'ka' WHERE `Name` = 'Georgian';
UPDATE `Languages` SET `CodePage` = 'cs' WHERE `Name` = 'Czech';
