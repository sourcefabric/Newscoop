<?php
if(!defined("PHORUM_ADMIN")) return;
$upgrade_queries[]="ALTER TABLE {$PHORUM['forums_table']} ADD template_settings text NOT NULL";
?>
