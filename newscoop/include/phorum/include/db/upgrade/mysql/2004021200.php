<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="ALTER TABLE {$PHORUM['files_table']} add message_id int unsigned not null default 0";
$upgrade_queries[]="ALTER TABLE {$PHORUM['files_table']} add key (message_id)";

?>
