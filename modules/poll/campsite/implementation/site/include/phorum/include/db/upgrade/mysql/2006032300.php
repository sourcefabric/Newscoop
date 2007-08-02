<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "ALTER TABLE {$PHORUM["message_table"]} ADD INDEX ( `user_id` ) ";

?>
