<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['message_table']} CHANGE ip ip VARCHAR( 255 ) NOT NULL";

?>
