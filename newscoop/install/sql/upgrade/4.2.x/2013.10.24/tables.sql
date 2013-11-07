ALTER TABLE `Images` CHANGE `Description` `Description` text COLLATE 'utf8_general_ci' NOT NULL DEFAULT '';
ALTER TABLE `package_item` CHANGE `caption` `caption` text COLLATE 'utf8_general_ci' NULL;