<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} add closed tinyint(4) NOT NULL default '0'";

$upgrade_queries[]="update {$PHORUM['message_table']} set closed=1, status=2 where status=1";

$upgrade_queries[]="alter table {$PHORUM['message_table']} add key dup_check (forum_id,author,subject,datestamp)";

$upgrade_queries[]="alter table {$PHORUM['message_table']} drop key message_id";

$upgrade_queries[]="alter table {$PHORUM['message_table']} add key forum_max_message (forum_id, message_id)";

$upgrade_queries[]="alter table {$PHORUM['message_table']} add key last_post_time (forum_id, status, modifystamp)";


?>
