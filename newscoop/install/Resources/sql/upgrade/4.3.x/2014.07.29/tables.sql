ALTER TABLE  `ArticleImageCaptions` CHANGE  `caption`  `caption` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `cron_jobs` (
  `id` int AUTO_INCREMENT NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `schedule` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_debug` tinyint(1) DEFAULT NULL,
  `dateFormat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `output` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `runOnHost` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `environment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `runAs` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;