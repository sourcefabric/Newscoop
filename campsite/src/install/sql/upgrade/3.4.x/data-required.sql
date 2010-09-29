DELETE FROM `SystemPreferences` WHERE `varname` ='TemplateCacheHandler';
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateCacheHandler', NULL);
UPDATE `SystemPreferences` SET `varname` = 'DBCacheEngine', `value` = NULL WHERE `varname` ='CacheEngine';
