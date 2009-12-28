<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="CREATE TABLE {$PHORUM['private_message_table']} ( private_message_id int(10) unsigned NOT NULL auto_increment, from_username varchar(50) NOT NULL default '', to_username varchar(50) NOT NULL default '', from_user_id int(10) unsigned NOT NULL default '0', to_user_id int(10) unsigned NOT NULL default '0', subject varchar(100) NOT NULL default '', message text NOT NULL, datestamp int(10) unsigned NOT NULL default '0', read_flag tinyint(1) NOT NULL default '0', reply_flag tinyint(1) NOT NULL default '0', to_del_flag tinyint(1) NOT NULL default '0', from_del_flag tinyint(1) NOT NULL default '0', PRIMARY KEY (private_message_id), KEY to_user_id (to_user_id,to_del_flag,datestamp), KEY from_user_id (from_user_id,from_del_flag,datestamp), KEY to_del_flag (to_del_flag,from_del_flag) ) TYPE=MyISAM";
$upgrade_queries[]="alter table {$PHORUM['user_table']} modify active tinyint(1) NOT NULL default '0'";

?>
