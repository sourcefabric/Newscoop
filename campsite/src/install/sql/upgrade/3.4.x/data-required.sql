-- template cache
DELETE FROM `SystemPreferences` WHERE `varname` ='TemplateCacheHandler';
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('TemplateCacheHandler', NULL);
UPDATE `SystemPreferences` SET `varname` = 'DBCacheEngine', `value` = NULL WHERE `varname` ='CacheEngine';

-- add new events for the authors management
INSERT INTO `Events` (`Id`,`Name`,`Notify`,`IdLanguage`) VALUES ('172','Add Author','N','1'),('173','Edit Author','N','1'),('174','Delete Author','N','01');

-- add default author types
INSERT INTO `AuthorTypes` (`id`,`type`) VALUES (NULL,'Author'),(NULL,'Writer'),(NULL,'Photographer'),(NULL,'Editor'),(NULL,'Columnist');

-- add system setting for password recovery
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('PasswordRecovery','Y');

-- call additional db upgrade script
system php ./update_rights.php;

