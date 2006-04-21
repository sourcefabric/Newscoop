<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} change column inherit_id inherit_id int(10) unsigned default NULL";
?>