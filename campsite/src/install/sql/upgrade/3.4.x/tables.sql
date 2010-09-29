-- Create table for template cache db handler
CREATE TABLE IF NOT EXISTS `Cache` (
  `language` int(11) NOT NULL,
  `publication` int(11) NOT NULL,
  `issue` int(11) NOT NULL,
  `section` int(11) DEFAULT NULL,
  `article` int(11) DEFAULT NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

