<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} add column sticky_count int(10) unsigned NOT NULL default '0'";
?>
