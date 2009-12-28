<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the message-table with an index for unapproved messages
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} CHANGE list_length list_length_flat INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL";
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD list_length_threaded INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `list_length_flat` ";
$upgrade_queries[]="UPDATE {$PHORUM['forums_table']} SET list_length_threaded = list_length_flat/2 ";
?>
