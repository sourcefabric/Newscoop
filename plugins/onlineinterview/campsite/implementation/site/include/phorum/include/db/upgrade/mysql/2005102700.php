<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['search_table']} ADD COLUMN forum_id int(10) UNSIGNED NOT NULL DEFAULT '0', ADD KEY forum_id (forum_id)";
$upgrade_queries[]="UPDATE {$PHORUM['search_table']}, {$PHORUM['message_table']} set {$PHORUM['search_table']}.forum_id={$PHORUM['message_table']}.forum_id where {$PHORUM['search_table']}.message_id={$PHORUM['message_table']}.message_id";

?>
