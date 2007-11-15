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

    $error="";

    $forums=phorum_db_get_forums();
    $forum_list=array(0=>"All Forums");

    foreach($forums as $forum_idn=>$forum){
        if($forum['folder_flag'] == 0) 
            $forum_list[$forum_idn]=$forum["name"];
    }      
    
    if(count($_POST)){

        if($_POST["days"] > 0){
          $ret=phorum_db_prune_oldThreads(time()-(86400*$_POST['days']), intval($_POST['forumid']), $_POST['mode']);
          // updating forum-stats
          if($_POST['forumid']) {
            $PHORUM['forum_id']=$_POST['forumid'];
            phorum_db_update_forum_stats(true);
          } else {
            foreach($forum_list as $fid => $fname) {
              $PHORUM['forum_id']=$fid;
              phorum_db_update_forum_stats(true);            
            }
          }
          // prune messages
        }

        if(!$ret){
            $error="No messages deleted.<br />";
        } else {
            echo "$ret Messages deleted.<br />";
        }
    }

    if($error){
        phorum_admin_error($error);
    }

    include_once "./include/admin/PhorumInputForm.php";
        

    $frm =& new PhorumInputForm ("", "post", "Delete messages");

    $frm->hidden("module", "message_prune");

    $frm->addbreak("Pruning old threads ...");
    $frm->addmessage("ATTENTION!<br />This script deletes quickly A LOT of messages. Use it on your own risk.<br />There is no further confirmation message after sending this form!");

    $frm->addrow("older than (days from today)",$frm->text_box("days", "365", 10));
    $frm->addrow("in Forum", $frm->select_tag("forumid", $forum_list,0));
    $frm->addrow("Check for", $frm->select_tag("mode", array(1=>"When the thread was started",2=>"When the last answer to the thread was posted"),0));    

    $frm->show();


?>
