<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="alter table {$PHORUM['user_group_xref_table']} add status tinyint(3) NOT NULL default '1'";
?>
