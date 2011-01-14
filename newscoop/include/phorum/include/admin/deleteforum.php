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

    if($_GET["confirm"]=="Yes"){

        if($_GET["folder_flag"]){
            
            $cur_folder_id=$_GET['forum_id'];
            // handling vroots            
            $oldfolder_tmp=phorum_db_get_forums($cur_folder_id);
            $oldfolder=array_shift($oldfolder_tmp);

            if($oldfolder['parent_id'] > 0) { // is it a real folder?
                $parent_folder=phorum_db_get_forums($oldfolder['parent_id']);
                if($parent_folder[$oldfolder['parent_id']]['vroot'] > 0) { // is a vroot set?
                    // then set the vroot to the vroot of the parent-folder
                    phorum_admin_set_vroot($cur_folder_id,$parent_folder[$oldfolder['parent_id']]['vroot'],$cur_folder_id);
                }
            } else { // just default root ...
                phorum_admin_set_vroot($cur_folder_id,0,$cur_folder_id);
            }
            // done with vroots
            
            phorum_db_drop_folder($cur_folder_id);
            $msg="The folder was deleted.  All forums and folders in this folder have been moved to this folder's parent.";
        } else {
            phorum_db_drop_forum($_GET["forum_id"]);
            $msg="The forum was deleted.  All messages in that forum were deleted.";
        }

    } elseif($_GET["confirm"]=="No"){

        $msg="No action was taken.";

    } else {

        $forums=phorum_db_get_forums((int)$_GET["forum_id"]);
        $forum=array_shift($forums);

        if($forum["folder_flag"]){
            $msg="Are you sure you want to delete $forum[name]?  All forums and folders in this folder will be moved to this folder's parent.";
        } else {
            $msg="Are you sure you want to delete $forum[name]?  All messages in this forum will be deleted";
        }
        $msg.="<form action=\"$_SERVER[PHP_SELF]\" method=\"get\"><input type=\"hidden\" name=\"module\" value=\"$module\" /><input type=\"hidden\" name=\"forum_id\" value=\"$_GET[forum_id]\" /><input type=\"hidden\" name=\"folder_flag\" value=\"$forum[folder_flag]\" /><input type=\"submit\" name=\"confirm\" value=\"Yes\" />&nbsp;<input type=\"submit\" name=\"confirm\" value=\"No\" /></form>";

    }

?>
<div class="PhorumInfoMessage"><?php echo $msg; ?></div>
