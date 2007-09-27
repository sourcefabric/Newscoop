<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "CREATE TABLE {$PHORUM["pm_buddies_table"]} ( 
    pm_buddy_id int(10) unsigned NOT NULL auto_increment, 
    user_id int(10) unsigned NOT NULL, 
    buddy_user_id int(10) unsigned NOT NULL, 
    PRIMARY KEY pm_buddy_id (pm_buddy_id), 
    UNIQUE KEY userids (user_id, buddy_user_id)
) TYPE=MyISAM";

?>
