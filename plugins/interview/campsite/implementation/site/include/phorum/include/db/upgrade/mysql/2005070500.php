<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['user_table']} add column moderator_data text not null default '';";

?>
