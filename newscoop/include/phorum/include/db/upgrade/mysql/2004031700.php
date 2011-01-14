<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="alter table {$PHORUM['user_table']} add `date_added` int(10) unsigned NOT NULL default '0'";
$upgrade_queries[]="alter table {$PHORUM['user_table']} add `date_last_active` int(10) unsigned NOT NULL default '0'";
$upgrade_queries[]="alter table {$PHORUM['user_table']} add `hide_activity` tinyint(1) NOT NULL default '0'";

$upgrade_queries[]="alter table {$PHORUM['user_table']} add KEY `activity` (`date_last_active`,`hide_activity`)";
$upgrade_queries[]="alter table {$PHORUM['user_table']} add KEY `date_added` (`date_added`)";
 
$upgrade_queries[]="update {$PHORUM['user_table']} set date_last_active=UNIX_TIMESTAMP(NOW()), date_added=UNIX_TIMESTAMP(NOW())";

?>
