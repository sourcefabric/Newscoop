<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD posts int(10) NOT NULL default '0'";
?>
