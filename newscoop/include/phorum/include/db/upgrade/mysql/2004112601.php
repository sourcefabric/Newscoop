<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="CREATE TABLE {$PHORUM['user_custom_fields_table']} (
        user_id INT DEFAULT '0' NOT NULL ,
        type INT DEFAULT '0' NOT NULL ,
        data TEXT NOT NULL ,
        PRIMARY KEY ( user_id , type )) TYPE=MyISAM";
?>
