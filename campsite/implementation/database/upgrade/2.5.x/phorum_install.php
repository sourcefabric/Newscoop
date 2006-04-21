<?php 

define( "PHORUM", "5.1-dev" );

// our internal version in format of year-month-day-serial
define( "PHORUMINTERNAL", "2006032300" );

include_once("phorum_constants.php");
include_once("phorum_config.php");
include_once("phorum_mysql.php");

phorum_db_create_tables();

// setup vars for initial settings
$tmp_dir = (substr(__FILE__, 0, 1)=="/") ? "/tmp" : "C:\\Windows\\Temp";

$default_forum_options=array(
	'forum_id'=>0,
	'moderation'=>0,
	'email_moderators'=>0,
	'pub_perms'=>1,
	'reg_perms'=>15,
	'display_fixed'=>0,
	'template'=>'default',
	'language'=>'english',
	'threaded_list'=>0,
	'threaded_read'=>0,
	'reverse_threading'=>0,
	'float_to_top'=>1,
	'list_length_flat'=>30,
	'list_length_threaded'=>15,
	'read_length'=>30,
	'display_ip_address'=>0,
	'allow_email_notify'=>0,
	'check_duplicate'=>1,
	'count_views'=>2,
	'max_attachments'=>0,
	'allow_attachment_types'=>'',
	'max_attachment_size'=>0,
	'max_totalattachment_size'=>0,
	'vroot'=>0,
	);

// insert the default module settings
// hooks

$hooks_initial=array(
	'format'=>array(
	        'mods'=>array('smileys','bbcode'),
	        'funcs'=>array('phorum_mod_smileys','phorum_bb_code')
	        )
	);

$mods_initial=array(
    'html'   =>0,
    'replace'=>0,
    'smileys'=>1,
    'bbcode' =>1
);

// set initial settings
$settings=array(
	"title" => "Phorum 5",
	"cache" => "$tmp_dir",
	"session_timeout" => "30",
	"short_session_timeout" => "60",
	"tight_security" => "0",
	"session_path" => "/",
	"session_domain" => "",
	"admin_session_salt" => microtime(),
	"cache_users" => "0",
	"register_email_confirm" => "0",
	"default_template" => "default",
	"default_language" => "english",
	"use_cookies" => "1",
	"use_bcc" => "1",
	"use_rss" => "1",
	"internal_version" => "" . PHORUMINTERNAL . "",
	"PROFILE_FIELDS" => array(array('name'=>"real_name",'length'=> 255, 'html_disabled'=>1)),
	"enable_pm" => "0",
	"user_edit_timelimit" => "0",
	"enable_new_pm_count" => "0",
	"enable_dropdown_userlist" => "1",
	"enable_moderator_notifications" => "1",
	"show_new_on_index" => "1",
	"dns_lookup" => "1",
	"tz_offset" => "0",
	"user_time_zone" => "1",
	"user_template" => "0",
	"registration_control" => "1",
	"file_uploads" => "0",
	"file_types" => "",
	"max_file_size" => "",
	"file_space_quota" => "",
	"file_offsite" => "0",
	"system_email_from_name" => "",
	"hide_forums" => "1",
	"enable_new_pm_count" => "1",
	"track_user_activity" => "86400",
	"html_title" => "Phorum",
	"head_tags" => "",
	"cache_users" => 0,
	"redirect_after_post" => "list",
	"reply_on_read_page" => 1,
	"status" => "normal",
	"use_new_folder_style" => 1,
	"default_forum_options" => $default_forum_options,
	"hooks"=> $hooks_initial,
	"mods" => $mods_initial	
	);

phorum_db_update_settings($settings);

?>