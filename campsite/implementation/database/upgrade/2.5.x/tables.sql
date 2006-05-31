-- For new Article type features - ability
-- to rename, translate, reorder, and hide them.
CREATE TABLE ArticleTypeMetadata (
    type_name VARCHAR(250) NOT NULL,
    field_name VARCHAR(250) NOT NULL DEFAULT 'NULL',
    field_weight INT,
    is_hidden TINYINT(1) NOT NULL DEFAULT 0,
    comments_enabled TINYINT(1) NOT NULL DEFAULT '0',
    fk_phrase_id INT UNSIGNED,
    field_type VARCHAR(255),
    field_type_param VARCHAR(255),
    PRIMARY KEY (`type_name`,`field_name`)
);

-- Initialze the new ArticleTypeMetadata table.
system php ./upgrade_article_types.php

-- Change article creation time so we know when
-- it was created, down to the second.
ALTER TABLE `Articles` CHANGE `UploadDate` `UploadDate` DATETIME NOT NULL DEFAULT '0000-00-00';

-- Change Issue publish time so we know hour, minute, second.
ALTER TABLE `Issues` CHANGE `PublicationDate` `PublicationDate` DATETIME NOT NULL DEFAULT '0000-00-00';

-- Add a "last-modified" field to article table
ALTER TABLE `Articles` ADD `time_updated` TIMESTAMP NOT NULL ;

-- To make it easier to figure out a users type
ALTER TABLE `Users` ADD `fk_user_type` VARCHAR( 140 ) NULL DEFAULT NULL AFTER `Reader` ;

-- Initialize the users type
system php ./init_user_type.php

--
-- Article Comments
--

-- The table to map articles to comment threads and vice versa.
CREATE TABLE `ArticleComments` (
  `fk_article_number` int(10) unsigned NOT NULL,
  `fk_language_id` int(10) unsigned NOT NULL,
  `fk_comment_thread_id` int(10) unsigned NOT NULL,
  `is_first` tinyint(1) NOT NULL default '0',
  KEY `fk_comment_thread_id` (`fk_comment_thread_id`),
  KEY `article_index` (`fk_article_number`,`fk_language_id`),
  KEY `first_message_index` (`fk_article_number`,`fk_language_id`,`is_first`)
) TYPE=MyISAM;

-- Comment activation for articles.
ALTER TABLE `Articles` ADD `comments_enabled` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `ArticleOrder` ;
ALTER TABLE `Articles` ADD `comments_locked` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `comments_enabled`;

-- Modification to the Publications table for article comments.
ALTER TABLE `Publications` ADD `fk_forum_id` INT NULL,
ADD `comments_enabled` TINYINT(1) NOT NULL DEFAULT '0',
ADD `comments_article_default_enabled` TINYINT(1) NOT NULL DEFAULT '0',
ADD `comments_subscribers_moderated` TINYINT(1) NOT NULL DEFAULT '0',
ADD `comments_public_moderated` TINYINT(1) NOT NULL DEFAULT '0';

-- Add system preference for number of login attempts before CAPTCHA is shown.
INSERT INTO `UserConfig` VALUES (67,0,'LoginFailedAttemptsNum','3','20060522012934');

CREATE TABLE `FailedLoginAttempts` (
	`ip_address` varchar(40) NOT NULL default '',
	`time_of_attempt` bigint(20) NOT NULL default '0',
	KEY `ip_address` (`ip_address`)
) TYPE=MyISAM;

-- Run the user permission upgrade script
system php ./upgrade_user_perms.php

--
-- Phorum tables
--

--
-- Table structure for table `phorum_banlists`
--

CREATE TABLE `phorum_banlists` (
  `id` int(11) NOT NULL auto_increment,
  `forum_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `pcre` tinyint(4) NOT NULL default '0',
  `string` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_files`
--

CREATE TABLE `phorum_files` (
  `file_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `file_data` mediumtext NOT NULL,
  `add_datetime` int(10) unsigned NOT NULL default '0',
  `message_id` int(10) unsigned NOT NULL default '0',
  `link` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`file_id`),
  KEY `add_datetime` (`add_datetime`),
  KEY `message_id_link` (`message_id`,`link`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_forum_group_xref`
--

CREATE TABLE `phorum_forum_group_xref` (
  `forum_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`,`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_forums`
--

CREATE TABLE `phorum_forums` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `description` text NOT NULL,
  `template` varchar(50) NOT NULL default '',
  `folder_flag` tinyint(1) NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `list_length_flat` int(10) unsigned NOT NULL default '0',
  `list_length_threaded` int(10) unsigned NOT NULL default '0',
  `moderation` int(10) unsigned NOT NULL default '0',
  `threaded_list` tinyint(4) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `float_to_top` tinyint(4) NOT NULL default '0',
  `check_duplicate` tinyint(4) NOT NULL default '0',
  `allow_attachment_types` varchar(100) NOT NULL default '',
  `max_attachment_size` int(10) unsigned NOT NULL default '0',
  `max_totalattachment_size` int(10) unsigned NOT NULL default '0',
  `max_attachments` int(10) unsigned NOT NULL default '0',
  `pub_perms` int(10) unsigned NOT NULL default '0',
  `reg_perms` int(10) unsigned NOT NULL default '0',
  `display_ip_address` smallint(5) unsigned NOT NULL default '1',
  `allow_email_notify` smallint(5) unsigned NOT NULL default '1',
  `language` varchar(100) NOT NULL default 'english',
  `email_moderators` tinyint(1) NOT NULL default '0',
  `message_count` int(10) unsigned NOT NULL default '0',
  `sticky_count` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `last_post_time` int(10) unsigned NOT NULL default '0',
  `display_order` int(10) unsigned NOT NULL default '0',
  `read_length` int(10) unsigned NOT NULL default '0',
  `vroot` int(10) unsigned NOT NULL default '0',
  `edit_post` tinyint(1) NOT NULL default '1',
  `template_settings` text NOT NULL,
  `count_views` tinyint(1) unsigned NOT NULL default '0',
  `display_fixed` tinyint(1) unsigned NOT NULL default '0',
  `reverse_threading` tinyint(1) NOT NULL default '0',
  `inherit_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `name` (`name`),
  KEY `active` (`active`,`parent_id`),
  KEY `group_id` (`parent_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_groups`
--

CREATE TABLE `phorum_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '0',
  `open` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_messages`
--

CREATE TABLE `phorum_messages` (
  `message_id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `author` varchar(37) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '2',
  `msgid` varchar(100) NOT NULL default '',
  `modifystamp` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `thread_count` int(10) unsigned NOT NULL default '0',
  `moderator_post` tinyint(3) unsigned NOT NULL default '0',
  `sort` tinyint(4) NOT NULL default '2',
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  `viewcount` int(10) unsigned NOT NULL default '0',
  `closed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`message_id`),
  KEY `thread_message` (`thread`,`message_id`),
  KEY `thread_forum` (`thread`,`forum_id`),
  KEY `special_threads` (`sort`,`forum_id`),
  KEY `status_forum` (`status`,`forum_id`),
  KEY `list_page_float` (`forum_id`,`parent_id`,`modifystamp`),
  KEY `list_page_flat` (`forum_id`,`parent_id`,`thread`),
  KEY `post_count` (`forum_id`,`status`,`parent_id`),
  KEY `dup_check` (`forum_id`,`author`,`subject`,`datestamp`),
  KEY `forum_max_message` (`forum_id`,`message_id`,`status`,`parent_id`),
  KEY `last_post_time` (`forum_id`,`status`,`modifystamp`),
  KEY `next_prev_thread` (`forum_id`,`status`,`thread`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- Campsite custom phorum addition:
-- How many levels down in a thread is the comment?
ALTER TABLE `phorum_messages` ADD `thread_depth` TINYINT UNSIGNED DEFAULT '0' NOT NULL ;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_pm_buddies`
--

CREATE TABLE `phorum_pm_buddies` (
  `pm_buddy_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `buddy_user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pm_buddy_id`),
  UNIQUE KEY `userids` (`user_id`,`buddy_user_id`),
  KEY `buddy_user_id` (`buddy_user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_pm_folders`
--

CREATE TABLE `phorum_pm_folders` (
  `pm_folder_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `foldername` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`pm_folder_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_pm_messages`
--

CREATE TABLE `phorum_pm_messages` (
  `pm_message_id` int(10) unsigned NOT NULL auto_increment,
  `from_user_id` int(10) unsigned NOT NULL default '0',
  `from_username` varchar(50) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `message` text NOT NULL,
  `datestamp` int(10) unsigned NOT NULL default '0',
  `meta` mediumtext NOT NULL,
  PRIMARY KEY  (`pm_message_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_pm_xref`
--

CREATE TABLE `phorum_pm_xref` (
  `pm_xref_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `pm_folder_id` int(10) unsigned NOT NULL default '0',
  `special_folder` varchar(10) default NULL,
  `pm_message_id` int(10) unsigned NOT NULL default '0',
  `read_flag` tinyint(1) NOT NULL default '0',
  `reply_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pm_xref_id`),
  KEY `xref` (`user_id`,`pm_folder_id`,`pm_message_id`),
  KEY `read_flag` (`read_flag`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_search`
--

CREATE TABLE `phorum_search` (
  `message_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `search_text` mediumtext NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `forum_id` (`forum_id`),
  FULLTEXT KEY `search_text` (`search_text`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_settings`
--

CREATE TABLE `phorum_settings` (
  `name` varchar(255) NOT NULL default '',
  `type` enum('V','S') NOT NULL default 'V',
  `data` text NOT NULL,
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

--
-- Dumping data for table `phorum_settings`
--

INSERT INTO `phorum_settings` (`name`, `type`, `data`) VALUES ('title', 'V', 'Phorum 5'),
('cache', 'V', '/tmp'),
('session_timeout', 'V', '30'),
('short_session_timeout', 'V', '60'),
('tight_security', 'V', '0'),
('session_path', 'V', '/'),
('session_domain', 'V', ''),
('admin_session_salt', 'V', '0.62629000 1146135136'),
('cache_users', 'V', '0'),
('register_email_confirm', 'V', '0'),
('default_template', 'V', 'default'),
('default_language', 'V', 'english'),
('use_cookies', 'V', '1'),
('use_bcc', 'V', '1'),
('use_rss', 'V', '1'),
('internal_version', 'V', '2006032300'),
('PROFILE_FIELDS', 'S', 'a:1:{i:0;a:3:{s:4:"name";s:9:"real_name";s:6:"length";i:255;s:13:"html_disabled";i:1;}}'),
('enable_pm', 'V', '0'),
('user_edit_timelimit', 'V', '0'),
('enable_new_pm_count', 'V', '1'),
('enable_dropdown_userlist', 'V', '1'),
('enable_moderator_notifications', 'V', '1'),
('show_new_on_index', 'V', '1'),
('dns_lookup', 'V', '1'),
('tz_offset', 'V', '0'),
('user_time_zone', 'V', '1'),
('user_template', 'V', '0'),
('registration_control', 'V', '1'),
('file_uploads', 'V', '0'),
('file_types', 'V', ''),
('max_file_size', 'V', ''),
('file_space_quota', 'V', ''),
('file_offsite', 'V', '0'),
('system_email_from_name', 'V', ''),
('hide_forums', 'V', '1'),
('track_user_activity', 'V', '86400'),
('html_title', 'V', 'Phorum'),
('head_tags', 'V', ''),
('redirect_after_post', 'V', 'list'),
('reply_on_read_page', 'V', '1'),
('status', 'V', 'normal'),
('use_new_folder_style', 'V', '1'),
('default_forum_options', 'S', 'a:24:{s:8:"forum_id";i:0;s:10:"moderation";i:0;s:16:"email_moderators";i:0;s:9:"pub_perms";i:1;s:9:"reg_perms";i:15;s:13:"display_fixed";i:0;s:8:"template";s:7:"default";s:8:"language";s:7:"english";s:13:"threaded_list";i:0;s:13:"threaded_read";i:0;s:17:"reverse_threading";i:0;s:12:"float_to_top";i:1;s:16:"list_length_flat";i:30;s:20:"list_length_threaded";i:15;s:11:"read_length";i:30;s:18:"display_ip_address";i:0;s:18:"allow_email_notify";i:0;s:15:"check_duplicate";i:1;s:11:"count_views";i:2;s:15:"max_attachments";i:0;s:22:"allow_attachment_types";s:0:"";s:19:"max_attachment_size";i:0;s:24:"max_totalattachment_size";i:0;s:5:"vroot";i:0;}'),
('hooks', 'S', 'a:1:{s:6:"format";a:2:{s:4:"mods";a:2:{i:0;s:7:"smileys";i:1;s:6:"bbcode";}s:5:"funcs";a:2:{i:0;s:18:"phorum_mod_smileys";i:1;s:14:"phorum_bb_code";}}}'),
('mods', 'S', 'a:4:{s:4:"html";i:0;s:7:"replace";i:0;s:7:"smileys";i:1;s:6:"bbcode";i:1;}');

-- --------------------------------------------------------

--
-- Table structure for table `phorum_subscribers`
--

CREATE TABLE `phorum_subscribers` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `sub_type` int(10) unsigned NOT NULL default '0',
  `thread` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`thread`),
  KEY `forum_id` (`forum_id`,`thread`,`sub_type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_user_custom_fields`
--

CREATE TABLE `phorum_user_custom_fields` (
  `user_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`user_id`,`type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_user_group_xref`
--

CREATE TABLE `phorum_user_group_xref` (
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `status` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`user_id`,`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_user_newflags`
--

CREATE TABLE `phorum_user_newflags` (
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `message_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`,`message_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_user_permissions`
--

CREATE TABLE `phorum_user_permissions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `permission` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`,`permission`)
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `phorum_users`
--

CREATE TABLE `phorum_users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `cookie_sessid_lt` varchar(50) NOT NULL default '',
  `sessid_st` varchar(50) NOT NULL default '',
  `sessid_st_timeout` int(10) unsigned NOT NULL default '0',
  `password_temp` varchar(50) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `email_temp` varchar(110) NOT NULL default '',
  `hide_email` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `user_data` text NOT NULL,
  `signature` text NOT NULL,
  `threaded_list` tinyint(4) NOT NULL default '0',
  `posts` int(10) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  `threaded_read` tinyint(4) NOT NULL default '0',
  `date_added` int(10) unsigned NOT NULL default '0',
  `date_last_active` int(10) unsigned NOT NULL default '0',
  `last_active_forum` int(10) unsigned NOT NULL default '0',
  `hide_activity` tinyint(1) NOT NULL default '0',
  `show_signature` tinyint(1) NOT NULL default '0',
  `email_notify` tinyint(1) NOT NULL default '0',
  `pm_email_notify` tinyint(1) NOT NULL default '1',
  `tz_offset` tinyint(2) NOT NULL default '-99',
  `is_dst` tinyint(1) NOT NULL default '0',
  `user_language` varchar(100) NOT NULL default '',
  `user_template` varchar(100) NOT NULL default '',
  `moderator_data` text NOT NULL,
  `moderation_email` tinyint(2) unsigned NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `active` (`active`),
  KEY `userpass` (`username`,`password`),
  KEY `sessid_st` (`sessid_st`),
  KEY `cookie_sessid_lt` (`cookie_sessid_lt`),
  KEY `activity` (`date_last_active`,`hide_activity`,`last_active_forum`),
  KEY `date_added` (`date_added`),
  KEY `email_temp` (`email_temp`)
) TYPE=MyISAM;
