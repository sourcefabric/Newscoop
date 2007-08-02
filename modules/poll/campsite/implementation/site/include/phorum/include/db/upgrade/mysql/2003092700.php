<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="CREATE TABLE {$PHORUM['banlist_table']} ( id int(11) NOT NULL auto_increment, forum_id int(11) NOT NULL default '0', type tinyint(4) NOT NULL default '0', pcre tinyint(4) NOT NULL default '0', string varchar(255) NOT NULL default '', PRIMARY KEY  (id), KEY forum_id (forum_id)) TYPE=MyISAM";
?>
