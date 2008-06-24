<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="UPDATE {$PHORUM['forums_table']} set active=1";

?>
