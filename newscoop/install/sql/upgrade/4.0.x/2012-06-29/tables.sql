DELETE FROM `Cache`;
ALTER TABLE `Cache` DROP INDEX `index` ,
ADD PRIMARY KEY ( `language` , `publication` , `issue` , `section` , `article` , `params` , `template` );
ALTER TABLE `Cache` ADD INDEX `template` ( `template` );
