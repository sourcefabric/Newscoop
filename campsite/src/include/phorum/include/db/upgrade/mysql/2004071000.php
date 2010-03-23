<?php
if(!defined("PHORUM_ADMIN")) return;

// altering the message-table with an index for unapproved messages
$upgrade_queries[]="ALTER TABLE {$PHORUM['message_table']} ADD INDEX status_forum ( status , forum_id )";


?>
