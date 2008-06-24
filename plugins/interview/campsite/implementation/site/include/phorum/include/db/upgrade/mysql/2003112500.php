<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]="alter table {$PHORUM['forums_table']} drop email_outgoing_address, drop email_incoming_address, drop email_subject_tag";

?>
