<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD check_duplicate tinyint(4) NOT NULL default '0'";
?>
