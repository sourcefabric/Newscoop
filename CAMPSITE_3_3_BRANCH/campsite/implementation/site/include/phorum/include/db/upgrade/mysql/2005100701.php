<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['message_table']} ADD INDEX next_prev_thread ( forum_id , status , thread ) ";
?>