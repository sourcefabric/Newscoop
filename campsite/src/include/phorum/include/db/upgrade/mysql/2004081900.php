<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} drop key forum_max_message";

$upgrade_queries[]="alter table {$PHORUM['message_table']} add key forum_max_message (forum_id, message_id, status, parent_id)";

$upgrade_queries[]="insert into {$PHORUM['settings_table']} values ('show_new_on_index', 'V', '1')";

?>
