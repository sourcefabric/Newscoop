<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} CHANGE meta meta mediumtext NOT NULL";

?>
