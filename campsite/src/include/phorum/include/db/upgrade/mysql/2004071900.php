<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD display_fixed TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL";
?>
