<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD `vroot` INT( 10 ) DEFAULT '0' NOT NULL";
?>
