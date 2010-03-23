<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['forums_table']} modify description text not null default ''";
$upgrade_queries[]="alter table {$PHORUM['user_newflags_table']} drop newflags";

?>
