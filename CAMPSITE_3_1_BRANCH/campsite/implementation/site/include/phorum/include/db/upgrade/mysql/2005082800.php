<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['forums_table']} add column max_totalattachment_size int(10) unsigned default 0";

?>
