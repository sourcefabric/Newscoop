<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['files_table']} add column link varchar(10) NOT NULL DEFAULT ''";
$upgrade_queries[]="drop index message_id on {$PHORUM['files_table']}";
$upgrade_queries[]="create index message_id_link on {$PHORUM['files_table']} (message_id, link)";
$upgrade_queries[]="update {$PHORUM['files_table']} set link='message' where message_id != 0";
$upgrade_queries[]="update {$PHORUM['files_table']} set link='user' where message_id = 0";

?>
