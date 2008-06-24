<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="alter table {$PHORUM['forums_table']} drop registration_control;";

?>
