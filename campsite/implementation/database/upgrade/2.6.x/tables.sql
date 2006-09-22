-- ticket #2220 - A new field to input section description
ALTER TABLE `Sections` ADD `Description` BLOB AFTER ShortName;
