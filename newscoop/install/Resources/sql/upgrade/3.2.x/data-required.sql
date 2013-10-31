-- add new system preferences for cache engine and tinymce editor image shrinking ratio
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('CacheEngine', 'APC');
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('EditorImageRatio', '100');
-- add/enable default template filters
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateFilter', '.*, CVS');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('ImagecacheLifetime', '86400');
-- add new log events for article files
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (38, 'Add file to article', 'N', 1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (39, 'Delete file from article', 'N', 1);
