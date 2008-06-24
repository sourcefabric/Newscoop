<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD `inherit_id` int(10) unsigned default '0' not null";

?>
