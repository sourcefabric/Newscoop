<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY parent_thread";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY modifystamp_thread";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY parent_status_mod";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY parent_status_thread";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY message_status_thread";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY message_status_mod";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY parent_id";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop KEY parent_status_forum";
  
$upgrade_queries[]="alter table {$PHORUM['message_table']} add KEY list_page_flat (thread,forum_id,status,parent_id,sort)";
$upgrade_queries[]="alter table {$PHORUM['message_table']} add KEY special_threads (sort,forum_id)";
$upgrade_queries[]="alter table {$PHORUM['message_table']} add KEY list_page_float (modifystamp,forum_id,status,parent_id,sort)";

?>
