
-- Add field for new language codes
ALTER TABLE `Languages` ADD `RFC3066bis` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `Code`;
