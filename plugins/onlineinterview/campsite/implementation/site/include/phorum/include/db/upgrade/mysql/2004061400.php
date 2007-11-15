<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the user tables for email-changes
$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD email_temp VARCHAR( 101 ) NOT NULL AFTER email" ;
$upgrade_queries[]="ALTER TABLE {$PHORUM['user_table']} ADD INDEX ( email_temp ) ";
?>
