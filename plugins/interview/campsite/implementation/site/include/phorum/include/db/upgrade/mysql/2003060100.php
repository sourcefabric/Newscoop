<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD password_temp varchar(50) NOT NULL default ''";
?>
