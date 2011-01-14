<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD `sessid` varchar(50) NOT NULL default '',add key sessid (sessid)";

?>
