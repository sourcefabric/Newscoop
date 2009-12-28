<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['user_table']} CHANGE email_temp email_temp VARCHAR( 110 )";

?>
