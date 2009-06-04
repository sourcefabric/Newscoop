-- add new log event for article editing and tinymce editor image shrinking ratio
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('CacheEngine', 'APC');
INSERT INTO SystemPreferences (`varname`, `value`) VALUES ('EditorImageRatio', '100');
-- add/enable default template filters
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateFilterHidden', 'Y');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateFilterCVS', 'Y');
INSERT INTO `SystemPreferences` (`varname`, `value`) VALUES ('TemplateFilterSVN', 'Y');
