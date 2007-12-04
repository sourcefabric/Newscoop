<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['private_message_table']} drop KEY read_flag";
$upgrade_queries[]="alter table {$PHORUM['private_message_table']} add KEY read_flag (to_user_id,read_flag,to_del_flag)";
?>
