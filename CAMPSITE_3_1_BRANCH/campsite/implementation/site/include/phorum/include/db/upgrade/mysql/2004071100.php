<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the message-table with an index for unapproved messages
$upgrade_queries[]="ALTER TABLE {$PHORUM['groups_table']} ADD open tinyint(3) NOT NULL default '0' AFTER name";


?>
