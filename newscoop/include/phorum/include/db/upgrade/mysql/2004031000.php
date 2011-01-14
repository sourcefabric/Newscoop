<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="ALTER TABLE {$PHORUM['message_table']} drop index `search`";

?>
