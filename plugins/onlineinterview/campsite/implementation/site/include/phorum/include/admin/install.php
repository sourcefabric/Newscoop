<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

    if(!defined("PHORUM_ADMIN")) return;

    if(!phorum_db_check_connection()){
        echo "A database connection could not be established.  Please edit include/db/config.php.";
        return;
    }

    include_once "./include/admin/PhorumInputForm.php";

    if(empty($_POST["step"])){
        $step = 0;
    } else {
        $step = $_POST["step"];
    }

    if(count($_POST)){

        // THIS IS THE WORK STEP

        switch ($step){

            case 5:

                if(!empty($_POST["admin_user"]) && !empty($_POST["admin_pass"]) && !empty($_POST["admin_pass2"]) && !empty($_POST["admin_email"])){
                    if($_POST["admin_pass"]!=$_POST["admin_pass2"]){
                        echo "The password fields do not match<br />";
                        $step=4;
                    } elseif(phorum_user_check_login($_POST["admin_user"], $_POST["admin_pass"])){
                        if($PHORUM["user"]["admin"]){
                            echo "Admin user already exists and has permissions<br />";
                        } else {
                            echo "That user already exists but does not have admin permissions<br />";
                            $step=4;
                        }
                    } else {

                        // add the user
                        $user = array( "username"=>$_POST["admin_user"], "password"=>$_POST["admin_pass"], "email"=>$_POST["admin_email"], "active"=>1, "admin"=>1 );

                        if(!phorum_user_add($user)){

                            echo "There was an error adding the user.<br />";
                            $step=4;
                        }

                        // set the default http_path so we can continue.
                        if(!empty($_SERVER["HTTP_REFERER"])) {
                            $http_path=$_SERVER["HTTP_REFERER"];
                        } elseif(!empty($_SERVER['HTTP_HOST'])) {
                            $http_path="http://".$_SERVER['HTTP_HOST'];
                            $http_path.=$_SERVER['PHP_SELF'];
                        } else {
                            $http_path="http://".$_SERVER['SERVER_NAME'];
                            $http_path.=$_SERVER['PHP_SELF'];
                        }
                        phorum_db_update_settings(array("http_path"=>dirname($http_path)));
                        phorum_db_update_settings(array("system_email_from_address"=>$_POST["admin_email"]));


                    }
                } else {
                    echo "Please fill in all fields.<br />";
                    $step=4;
                }

                break;
        }

    }

    // THIS IS THE OUTPUT STEP

    if($PHORUM["installed"]) $step=5;

    switch ($step){

        case 0:

            $frm =& new PhorumInputForm ("", "post", "Continue ->");
            $frm->addbreak("Welcome to Phorum");
            $frm->addmessage("This wizard will setup Phorum on your server.  The first step is to prepare the database.  Phorum has already confirmed that it can connect to your database.  Press continue when you are ready.");
            $frm->hidden("module", "install");
            $frm->hidden("step", "2");
            $frm->show();

            break;

        case 2:
            // ok, fresh install

            $err=phorum_db_create_tables();

            if($err){
                $message="Could not create tables, database said:<blockquote>$err</blockquote>";
                $message.="Your database user will need to have create table permissions.  If you know what the error is (tables already exist, etc.) and want to continue, click the button below.";
            } else {
                $message="Tables created.  Next we will check your cache settings. Press continue when ready.";

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
                "enable_pm" => "1",
                "user_edit_timelimit" => "0",
                "enable_new_pm_count" => "1",
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

                // posting forum and test-message

                // create a test forum
                $forum=array(
                "name"=>'Test Forum',
                "active"=>1,
                "description"=>'This is a test forum.  Feel free to delete it or edit after installation.',
                "template"=>'default',
                "folder_flag"=>0,
                "parent_id"=>0,
                "list_length_flat"=>30,
                "list_length_threaded"=>15,
                "read_length"=>20,
                "moderation"=>0,
                "threaded_list"=>0,
                "threaded_read"=>0,
                "float_to_top"=>1,
                "display_ip_address"=>0,
                "allow_email_notify"=>1,
                "language"=>'english',
                "email_moderators"=>0,
                "display_order"=>0,
                "edit_post"=>1,
                "pub_perms" =>  1,
                "reg_perms" =>  15
                );

                $GLOBALS["PHORUM"]['forum_id']=phorum_db_add_forum($forum);
                $GLOBALS["PHORUM"]['vroot']=0;

                // create a test post
                $test_message=array(
                "forum_id" => $GLOBALS['PHORUM']["forum_id"],
                "thread" => 0,
                "parent_id" => 0,
                "author" => 'Phorum Installer',
                "subject" => 'Test Message',
                "email" => '',
                "ip" => '127.0.0.1',
                "user_id" => 0,
                "moderator_post" => 0,
                "closed" => 0,
                "status" => PHORUM_STATUS_APPROVED,
                "sort" => PHORUM_SORT_DEFAULT,
                "msgid" => '',
                "body" => "This is a test message.  You can delete it after install using the admin.\n\nPhorum 5 Team"
                );

                phorum_db_post_message($test_message);

                include_once ("./include/thread_info.php");

                phorum_update_thread_info($test_message["thread"]);

                phorum_db_update_forum_stats(true);

            }

            $frm =& new PhorumInputForm ("", "post", "Continue ->");
            $frm->addbreak("Creating tables....");
            $frm->addmessage($message);
            $frm->hidden("step", "6");
            $frm->hidden("module", "install");
            $frm->show();

            break;

        case 4:

            $frm =& new PhorumInputForm ("", "post");
            $frm->hidden("step", "5");
            $frm->hidden("module", "install");
            $frm->addbreak("Creating An Administrator");
            $frm->addmessage("Please enter the following information.  This can be your user information or you can create an administrator that is separate from yourself.<br /><br />Note: If you are using a pre-existing authentication database, please enter the username and password of the admin user that already exists.");
            $admin_user = isset($_POST["admin_user"]) ? $_POST["admin_user"] : "";
            $admin_email = isset($_POST["admin_email"]) ? $_POST["admin_email"] : "";
            $frm->addrow("Admin User Name", $frm->text_box("admin_user", $admin_user, 30));
            $frm->addrow("Admin Email Address", $frm->text_box("admin_email", $admin_email, 30));
            $frm->addrow("Admin Password", $frm->text_box("admin_pass", "", 30, 0, true));
            $frm->addrow("(again)", $frm->text_box("admin_pass2", "", 30, 0, true));
            $frm->show();

            break;

        case 5:

            phorum_db_update_settings( array("installed"=>1) );
            echo "The setup is complete.  You can now go to <a href=\"$_SERVER[PHP_SELF]\">the admin</a> and start making Phorum all your own.<br /><br /><strong>Here are some things you will want to look at:</strong><br /><br /><a href=\"$_SERVER[PHP_SELF]?module=settings\">The General Settings page</a><br /><br /><a href=\"$_SERVER[PHP_SELF]?module=mods\">Pre-installed modules</a><br /><br /><a href=\"docs/faq.txt\">The FAQ</a><br /><br /><a href=\"docs/performance.txt\">How to get peak performance from Phorum</a><br /><br /><strong>For developers:</strong><br /><br /><a href=\"docs/creating_mods.txt\">Module Creation</a><br /><br /><a href=\"docs/permissions.txt\">How Phorum permisssions work</a><br /><br /><a href=\"docs/CODING-STANDARDS\">The Phorum Team's codings standards</a>";

            break;

        case 6:
            // try to figure out if we can write to the cache directory
            $message = "";
            error_reporting(0);
            $err = false;
            if ($fp = fopen($PHORUM["cache"] . "/phorum-install-test", "w+")) {
                unlink($PHORUM["cache"] . "/phorum-install-test");
            }
            else {
                // in this case the normal setting is wrong, so try ./cache
                $PHORUM["cache"] = "./cache";
                $settings = array("cache" => $PHORUM["cache"]);
                if (!phorum_db_update_settings($settings)) {
                    $message .= "Database error updating settings.<br />";
                    $err = true;
                }
                elseif ($fp = fopen($PHORUM["cache"] . "/phorum-install-test", "w+")) {
                    unlink($PHORUM["cache"] . "/phorum-install-test");
                }
                else {
                    $err = true;
                }

            }
            error_reporting(E_WARN);
            if ($message == "") {
                if($err){
                    $message.="Your cache directory is not writable. Please change the permissions on '/cache' inside the Phorum directory to allow writing. In Unix, you may have to use this command: chmod 777 cache<br /><br />If you want to continue anyway and set a cache directory manually, press continue. Note that you must do this, Phorum will not work without a valid cache.";
                } else {
                    $message.="Cache directory set.  Next we will create a user with administrator privileges.  Press continue when ready.";
                }
            }

            $frm =& new PhorumInputForm ("", "post", "Continue ->");
            $frm->hidden("module", "install");
            $frm->addbreak("Checking cache....");
            $frm->addmessage($message);
            $frm->hidden("step", "4");
            $frm->show();

            break;
    }

?>
