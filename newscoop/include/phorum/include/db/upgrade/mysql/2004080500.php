<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} drop key list_page_float";
$upgrade_queries[]="alter table {$PHORUM['message_table']} drop key list_page_flat";

$upgrade_queries[]="alter table {$PHORUM['message_table']} add key list_page_float (forum_id, parent_id, modifystamp)";
$upgrade_queries[]="alter table {$PHORUM['message_table']} add key list_page_flat (forum_id, parent_id, thread)";


?>
