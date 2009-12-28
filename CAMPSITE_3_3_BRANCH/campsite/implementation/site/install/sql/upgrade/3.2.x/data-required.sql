-- add new system preferences for cache engine and tinymce editor image shrinking ratio
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('CacheEngine', 'APC');
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('EditorImageRatio', '100');
-- add/enable default template filters
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateFilter', '.*, CVS');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('ImagecacheLifetime', '86400');
-- add right to enable editor spellchecker --
DELETE FROM `liveuser_rights_right_id_seq`;
INSERT INTO `liveuser_rights_right_id_seq` SELECT MAX(right_id) AS id FROM `liveuser_rights`;
INSERT INTO `liveuser_rights` VALUES ((SELECT id + 1 FROM `liveuser_rights_right_id_seq`), 0, 'EditorSpellcheckerEnabled', 1);
UPDATE `liveuser_rights_right_id_seq` set id = id + 1;
-- add new log events for article files
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (38, 'Add file to article', 'N', 1);
INSERT INTO `Events` (`Id`, `Name`, `Notify`, `IdLanguage`) VALUES (39, 'Delete file from article', 'N', 1);
