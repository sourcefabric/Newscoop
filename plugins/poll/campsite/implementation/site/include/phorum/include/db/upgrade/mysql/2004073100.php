<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} CHANGE email email VARCHAR( 100 ) NOT NULL";

$upgrade_queries[]="ALTER TABLE {$PHORUM['message_table']} CHANGE email email VARCHAR( 100 ) NOT NULL";
?>
