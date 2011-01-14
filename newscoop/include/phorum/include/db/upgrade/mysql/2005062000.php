<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD `reverse_threading` tinyint(1) NOT NULL default '0'";

?>
