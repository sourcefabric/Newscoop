<?php

if(!defined("PHORUM_ADMIN")) return;

$upgrade_queries[]= "alter table {$PHORUM["forums_table"]} modify inherit_id int(10) unsigned default '0'";

$upgrade_queries[]= "update {$PHORUM["forums_table"]} set inherit_id=NULL where inherit_id=0";

$upgrade_queries[]= "insert into {$PHORUM["settings_table"]} set name='default_forum_options', type='S', data='".mysql_escape_string('a:24:{s:8:"forum_id";s:1:"0";s:10:"moderation";s:1:"0";s:16:"email_moderators";s:1:"0";s:9:"pub_perms";i:1;s:9:"reg_perms";i:15;s:13:"display_fixed";i:0;s:8:"template";s:7:"default";s:8:"language";s:7:"english";s:13:"threaded_list";s:1:"0";s:13:"threaded_read";s:1:"0";s:17:"reverse_threading";s:1:"0";s:12:"float_to_top";s:1:"1";s:16:"list_length_flat";i:30;s:20:"list_length_threaded";i:30;s:11:"read_length";s:2:"30";s:18:"display_ip_address";s:1:"0";s:18:"allow_email_notify";s:1:"0";s:15:"check_duplicate";s:1:"1";s:11:"count_views";s:1:"2";s:15:"max_attachments";i:0;s:22:"allow_attachment_types";s:0:"";s:19:"max_attachment_size";i:0;s:24:"max_totalattachment_size";i:0;s:5:"vroot";i:0;}')."'";


?>
