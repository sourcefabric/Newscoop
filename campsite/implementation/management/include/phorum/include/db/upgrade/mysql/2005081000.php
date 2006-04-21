<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="insert into {$PHORUM['settings_table']} set name='installed', type='V', data='1'";

?>
