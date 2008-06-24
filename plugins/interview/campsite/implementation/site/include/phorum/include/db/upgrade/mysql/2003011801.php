<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['message_table']} ADD edit_count TINYINT unsigned NOT NULL DEFAULT 0, ADD edit_date datetime NOT NULL default '0000-00-00 00:00:00'";
?>
