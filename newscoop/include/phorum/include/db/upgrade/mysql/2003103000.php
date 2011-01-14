<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} drop allow_html";

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} add registration_control tinyint(1) NOT NULL default '0'";

$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} change sec_public pub_perms int(10) unsigned NOT NULL default '0'";
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} change sec_reg reg_perms int(10) unsigned NOT NULL default '0'";

?>
