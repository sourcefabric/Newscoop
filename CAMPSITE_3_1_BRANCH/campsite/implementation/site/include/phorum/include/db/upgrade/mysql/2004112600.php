<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD `show_signature` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `email_notify` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `tz_offset` TINYINT( 2 ) DEFAULT '-99' NOT NULL,
ADD `is_dst` TINYINT( 1 ) DEFAULT '0' NOT NULL ,
ADD `user_language` VARCHAR( 100 ) NOT NULL ,
ADD `user_template` VARCHAR( 100 ) NOT NULL ";
?>
