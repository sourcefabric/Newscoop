-- add new system preferences options
INSERT INTO SystemPreferences (id, varname, value, last_modified) VALUES (15,'SiteOnline','Y','2007-10-07 01:49:11'),(16,'SiteCharset','utf-8','2007-07-26 04:49:32'),(17,'SiteLocale','en-US','2007-07-26 04:49:56'),(18,'SiteCacheEnabled','N','2007-07-26 04:50:19'),(22,'SiteMetaKeywords','Campsite, MDLF, Campware, CMS, OpenSource, Media','2007-10-05 01:31:36'),(19,'SiteSecretKey','4b506c2968184be185f6282f5dcac832','2007-10-04 20:51:41'),(20,'SiteSessionLifeTime','1400','2007-10-04 20:51:51'),(21,'SiteTitle','Campsite 3.0','2007-10-07 01:39:13'),(23,'SiteMetaDescription','Campsite 3.0 site, try it out!','2007-10-07 01:36:18'),(24,'SMTPHost','localhost','2007-10-26 01:30:45'),(25,'SMTPPort','25','2007-10-26 01:30:45');

-- add Spanish translation for time units
INSERT INTO TimeUnits (Unit, IdLanguage, Name) VALUES ('D',13,'dÃ­as'),('W',13,'semanas'),('M',13,'meses'),('Y',13,'aÃ±os');

-- fix month names for Spanish translation
UPDATE Languages SET Month3 = 'Marzo', Month5 = 'Mayo', Month7 = 'Julio' WHERE Name = 'Spanish';

-- Upgrade Campsite users to LiveUser
system php ./upgrade_liveuser.php
