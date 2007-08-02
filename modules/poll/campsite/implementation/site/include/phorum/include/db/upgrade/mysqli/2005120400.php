<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "alter table {$PHORUM["user_table"]} add moderation_email tinyint(2) unsigned not null default 1";

?>
