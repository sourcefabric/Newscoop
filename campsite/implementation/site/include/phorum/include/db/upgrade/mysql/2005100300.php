<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "alter table {$PHORUM["pm_buddies_table"]} add index buddy_user_id (buddy_user_id)";

?>
