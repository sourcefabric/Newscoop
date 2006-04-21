<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['user_table']} add column pm_email_notify tinyint ( 1 ) default '1' not null";

?>
