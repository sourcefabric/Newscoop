<?php
if(!defined("PHORUM_ADMIN")) return;
// altering the tables for the mixed mode
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} CHANGE threaded threaded_list TINYINT( 4 ) DEFAULT '0' NOT NULL, ADD threaded_read TINYINT( 4 ) DEFAULT '0' NOT NULL";
$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} CHANGE threaded threaded_list TINYINT( 4 ) DEFAULT '0' NOT NULL, ADD threaded_read TINYINT( 4 ) DEFAULT '0' NOT NULL";
?>
