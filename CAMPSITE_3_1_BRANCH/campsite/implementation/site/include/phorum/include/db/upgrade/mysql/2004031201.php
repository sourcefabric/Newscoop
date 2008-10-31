<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the files tables
$upgrade_queries[]="ALTER TABLE {$PHORUM['private_message_table']} add key `read_flag` (`to_user_id`,`read_flag`)";

?>
