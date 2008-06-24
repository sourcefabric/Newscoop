<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="alter table {$PHORUM['message_table']} add viewcount int(10) unsigned NOT NULL default '0'";
$upgrade_queries[]="alter table {$PHORUM['forums_table']} add count_views tinyint(1) unsigned NOT NULL default '0'";
?>
