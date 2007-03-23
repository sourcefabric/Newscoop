<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} drop attachments";
  
$upgrade_queries[]="CREATE TABLE {$PHORUM['files_table']} ( file_id int(11) NOT NULL auto_increment, user_id int(11) NOT NULL default '0', filename varchar(255) NOT NULL default '', filesize int(11) NOT NULL default '0', file_data mediumtext NOT NULL, add_datetime int(10) unsigned NOT NULL default '0', PRIMARY KEY  (file_id), KEY add_datetime (add_datetime) ) TYPE=MyISAM";
$upgrade_queries[]="CREATE TABLE {$PHORUM['message_files_xref_table']} ( message_id int(11) NOT NULL default '0', file_id int(11) NOT NULL default '0', PRIMARY KEY  (message_id, file_id) ) TYPE=MyISAM";


?>
