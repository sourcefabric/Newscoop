-- Create table for template cache db handler
CREATE TABLE IF NOT EXISTS `Cache` (
  `language` int(11) default NULL,
  `publication` int(11) default NULL,
  `issue` int(11) default NULL,
  `section` int(11) default NULL,
  `article` int(11) default NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`template`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



ALTER TABLE `Authors` ADD `type` INT NULL ,
ADD `skype` VARCHAR( 255 ) NULL ,
ADD `jabber` VARCHAR( 255 ) NULL ,
ADD `aim` VARCHAR( 255 ) NULL ,
ADD `biography` TEXT NULL ,
ADD `image` INT NULL;
DROP TABLE IF EXISTS `Authorsaliases`;
CREATE TABLE `Authorsaliases` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`IdAuthor` INT NOT NULL ,
`alias` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;
DROP TABLE IF EXISTS `Authorbiography`;
CREATE TABLE `Authorbiography` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`IdAuthor` INT NOT NULL ,
`IdLanguage` INT NOT NULL ,
`biography` TEXT NOT NULL,
`first_name` VARCHAR( 255 ) NULL ,
`last_name` VARCHAR( 255 ) NULL
) ENGINE = MYISAM ;


ALTER TABLE `ArticleAuthors` CHANGE `fk_article_number` `fk_article_number` INT( 10 ) UNSIGNED NULL ,
CHANGE `fk_language_id` `fk_language_id` INT( 10 ) UNSIGNED NULL ,
CHANGE `fk_author_id` `fk_author_id` INT( 10 ) UNSIGNED NULL ;

INSERT INTO `Events` (
`Id` ,
`Name` ,
`Notify` ,
`IdLanguage`
)
VALUES (
'174', 'Delete Author', 'N', '01'
);

INSERT INTO `Events` (
`Id` ,
`Name` , 
`Notify` ,
`IdLanguage`
)
VALUES (
'172', 'Add Author', 'N', '1'
), (
'173', 'Edit Author', 'N', '1'
);


INSERT INTO `liveuser_rights` (
`right_id` ,
`area_id` ,
`right_define_name` ,
`has_implied`
)
VALUES (
'97', '0', 'EditAuthors', '1'
);

ALTER TABLE `liveuser_users` ADD COLUMN `password_reset_token` VARCHAR(85) NULL  AFTER `isActive` ;