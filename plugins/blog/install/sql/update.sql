ALTER TABLE `plugin_blog_blog` CHANGE `published` `date` DATETIME NOT NULL;
ALTER TABLE `plugin_blog_entry` CHANGE `published` `date` DATETIME NOT NULL;
ALTER TABLE `plugin_blog_comment` CHANGE `published` `date` DATETIME NOT NULL;