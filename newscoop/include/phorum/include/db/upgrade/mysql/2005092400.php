<?php
if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "alter table {$PHORUM['user_table']} add column cookie_sessid_lt varchar(50) NOT NULL default '',change column sessid sessid_st varchar(50) NOT NULL default '',add index sessid_st (sessid_st),add index cookie_sessid_lt (cookie_sessid_lt),drop index sessid";

?>
