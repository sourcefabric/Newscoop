<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD edit_post TINYINT unsigned NOT NULL DEFAULT 1";
?>
