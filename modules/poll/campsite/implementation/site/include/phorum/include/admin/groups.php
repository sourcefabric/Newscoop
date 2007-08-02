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

    if(count($_POST)){

        if( isset($_POST['action']) && $_POST['action'] == "deleteGroups") {
        
          $count=0;
          foreach($_POST['deleteIds'] as $id => $deluid) {
              phorum_db_delete_group($deluid);
              $count++;
          }
          echo "$count Group(s) deleted.<br />";          
          
        }

        switch ($_POST["section"]) {

            case "add":
                $group_id=0;
                $_POST["group_name"]=trim($_POST["group_name"]);
                if(!empty($_POST["group_name"])){
                    $group_id=phorum_db_add_group($_POST["group_name"]);
                }
                if(!$group_id){
                    echo "Error adding group<br />";
                } else {
                    echo "Group added<br />";
                }        
                break;

            case "edit":
                $group = array("group_id" => $_POST["group_id"], "name" => $_POST["name"], "open" => $_POST["open"]);

                if(phorum_db_save_group($group)){
                    echo "Group Saved";
                } else {
                    echo "Error Saving Group Name";
                }
                break;
        
            case "forums":
                $group=$_POST;
                if($_POST["new_forum"]){
                    if(!is_array($_POST["new_permissions"])){
                        $permission=0;
                    } else {
                        $permission = 0;
                        foreach($_POST["new_permissions"] as $perm=>$check){
                           $permission = $permission | $perm;
                        }
                    }

                    $group["permissions"][$_POST["new_forum"]]=$permission;
                }

                if(isset($_POST["delforum"])){
                    foreach($_POST["delforum"] as $fid=>$val){
                        unset($group["permissions"][$fid]);
                        unset($_POST["forums"][$fid]);
                    }
                }


                if(isset($_POST["forums"])){
                    foreach($_POST["forums"] as $forum_id){
                        $permission=0;
                            
                        if(isset($group["permissions"][$forum_id])){
                            foreach($group["permissions"][$forum_id] as $perm=>$check){
                                $permission = $permission | $perm;
                            }
                        }

                        $group["permissions"][$forum_id]=$permission;
                    }
                }

                unset($group["forums"]);
                unset($group["delforum"]);
                unset($group["new_forum"]);
                unset($group["new_permissions"]);

                if(phorum_db_save_group($group)){
                	// clearing user-cache if needed
                	if(isset($PHORUM['cache_users']) && $PHORUM['cache_users']) {
	                	$group_members=phorum_db_get_group_members($_POST["group_id"]);
	                	
	                	if(count($group_members)) {
	                		foreach($group_members as $user_id => $user_status) {
	                			phorum_cache_remove('user',$user_id);	
	                		}
	                	}
	                	
                	}
                	
                	
                    echo "Group Forum Permissions Saved";
                } else {
                    echo "Error Saving Group Forum Permissions";
                }


                break;

        }
        
    }

    if($error){
        phorum_admin_error($error);
    }

    include_once "./include/admin/PhorumInputForm.php";
    $groups=phorum_db_get_groups();
   
    $forums=phorum_db_get_forums();

    if(isset($_GET["edit"])){

        $group=$groups[$_GET["group_id"]];

        $frm =& new PhorumInputForm ("", "post");
    
        $frm->addbreak("Edit Group");
    
        $frm->hidden("module", "groups");
    
        $frm->hidden("section", "edit");

        $frm->hidden("group_id", $_GET["group_id"]);
    
        $open_options = array(PHORUM_GROUP_CLOSED => "No", 
                        PHORUM_GROUP_OPEN => "Yes",
                        PHORUM_GROUP_REQUIRE_APPROVAL => "Yes (require Group Moderator approval)");
        $frm->addrow("Name:", $frm->text_box("name", $group["name"], 50));
        $frm->addrow("Allow Membership Requests:", $frm->select_tag("open", $open_options, $group["open"], ""));        
        $frm->show();

        echo "<br /><hr class=\"PhorumAdminHR\" /><br />";


        $frm =& new PhorumInputForm ("", "post", "Update");

        $frm->hidden("module", "groups");
    
        $frm->hidden("section", "forums");

        $frm->hidden("group_id", $_GET["group_id"]);

        $row=$frm->addbreak("Edit Forum Permissions");

        $frm->addhelp($row, "Forum Permissions", "Permissions given to groups overwrite any permissions granted by the forum properties.  Also, if a user is granted permissions directly to a forum in the user admin, any group permissions he has for that forum will be ignored.  If the user is a member of two or more groups that have permissions in the same forum, the permissions will be combined. (eg. If group A allows read and reply and group B allows create and moderate, the user will receive all four permissions.)");
        
        $forums=phorum_db_get_forums();

        $perm_frm = $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_READ."]", 1, "Read")."&nbsp;&nbsp;".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_REPLY."]", 1, "Reply")."&nbsp;&nbsp;".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_NEW_TOPIC."]", 1, "Create&nbsp;New&nbsp;Topics")."&nbsp;&nbsp;".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_EDIT."]", 1, "Edit&nbsp;Their&nbsp;Posts")."<br />".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_ATTACH."]", 1, "Attach&nbsp;Files")."<br />".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_MODERATE_MESSAGES."]", 1, "Moderate Messages")."&nbsp;&nbsp;".
                    $frm->checkbox("new_permissions[".PHORUM_USER_ALLOW_MODERATE_USERS."]", 1, "Moderate Users")."&nbsp;&nbsp;";

    
        $arr[]="Add A Forum...";
        foreach($forums as $forum_id=>$forum){
            if(empty($group["permissions"][$forum_id]) && $forum['folder_flag'] == 0)
                $arr[$forum_id]=$forum["name"];
        }
        
        if(count($arr)>1)
            $frm->addrow($frm->select_tag("new_forum", $arr), $perm_frm);


        ksort($group["permissions"]);
        if(is_array($group["permissions"])){
            foreach($group["permissions"] as $forum_id=>$perms){
                $perm_frm = $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_READ."]", 1, "Read", $perms & PHORUM_USER_ALLOW_READ)."&nbsp;&nbsp;".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_REPLY."]", 1, "Reply", $perms & PHORUM_USER_ALLOW_REPLY)."&nbsp;&nbsp;".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_NEW_TOPIC."]", 1, "Create&nbsp;New&nbsp;Topics", $perms & PHORUM_USER_ALLOW_NEW_TOPIC)."&nbsp;&nbsp;".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_EDIT."]", 1, "Edit&nbsp;Their&nbsp;Posts", $perms & PHORUM_USER_ALLOW_EDIT)."<br />".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_ATTACH."]", 1, "Attach&nbsp;Files", $perms & PHORUM_USER_ALLOW_ATTACH)."<br />".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_MODERATE_MESSAGES."]", 1, "Moderate Messages", $perms & PHORUM_USER_ALLOW_MODERATE_MESSAGES)."&nbsp;&nbsp;".
                            $frm->checkbox("permissions[$forum_id][".PHORUM_USER_ALLOW_MODERATE_USERS."]", 1, "Moderate Users", $perms & PHORUM_USER_ALLOW_MODERATE_USERS)."&nbsp;&nbsp;".

                $frm->hidden("forums[$forum_id]", $forum_id);

                $row=$frm->addrow($forums[$forum_id]["name"]."<br />".$frm->checkbox("delforum[$forum_id]", 1, "Delete"), $perm_frm);

            }     
        }

        $frm->show();

    }

    if(empty($_REQUEST["edit"])){

        $frm =& new PhorumInputForm ("", "post");
    
        $frm->addbreak("Phorum Group Admin");
    
        $frm->hidden("module", "groups");
    
        $frm->hidden("section", "add");
    
        $frm->addrow("Add A Group:", $frm->text_box("group_name", "", 50));
    
        $frm->show();

        echo "<hr class=\"PhorumAdminHR\" />";
        echo "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">\n";
        echo "<input type=\"hidden\" name=\"module\" value=\"groups\">\n";            
        echo "<input type=\"hidden\" name=\"action\" value=\"deleteGroups\">\n";
        echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"PhorumAdminTable\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "    <td class=\"PhorumAdminTableHead\">Group</td>\n";
        echo "    <td class=\"PhorumAdminTableHead\">Delete</td>\n";     
        echo "</tr>\n";
    
        foreach($groups as $group){
            echo "<tr>\n";
            echo "    <td class=\"PhorumAdminTableRow\"><a href=\"$_SERVER[PHP_SELF]?module=groups&edit=1&group_id={$group['group_id']}\">".htmlspecialchars($group['name'])."</a></td>\n";
            echo "    <td class=\"PhorumAdminTableRow\">Delete? <input type=\"checkbox\" name=\"deleteIds[]\" value=\"{$group['group_id']}\"></td>\n";  
            echo "</tr>\n";
        }
        echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Delete Selected\"></td></tr>";
        echo "</table></form>\n";
    
    }
