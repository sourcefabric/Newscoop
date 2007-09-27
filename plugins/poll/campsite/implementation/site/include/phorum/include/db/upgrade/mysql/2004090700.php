<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['user_table']} ADD last_active_forum INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL AFTER date_last_active";

$upgrade_queries[]="alter table {$PHORUM['user_table']} drop key activity";

$upgrade_queries[]="alter table {$PHORUM['user_table']} add key activity (date_last_active,hide_activity,last_active_forum)";

?>
