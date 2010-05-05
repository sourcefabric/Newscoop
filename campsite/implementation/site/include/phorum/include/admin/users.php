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

    include('./include/format_functions.php');


    $error="";

    if(count($_POST)){


        if( isset($_POST['action']) && $_POST['action'] == "deleteUsers") {

            $count=count($_POST['deleteIds']);
            if($count > 0) {
                foreach($_POST['deleteIds'] as $id => $deluid) {
                    phorum_user_delete($deluid);
                }
                phorum_admin_okmsg("$count User(s) deleted.");
            }

        } else {

            $user_data=$_POST;

            switch( $_POST["section"] ) {


                case "forums":

                    if($_POST["new_forum"]){
                        if(!is_array($_POST["new_forum_permissions"])){
                            $permission=0;
                        } else {
                            $permission = 0;
                            foreach($_POST["new_forum_permissions"] as $perm=>$check){
                               $permission = $permission | $perm;
                            }
                        }

                        $user_data["forum_permissions"][$_POST["new_forum"]]=$permission;
                    }

                    if(isset($_POST["delforum"])){
                        foreach($_POST["delforum"] as $fid=>$val){
                            unset($user_data["forum_permissions"][$fid]);
                            unset($_POST["forums"][$fid]);
                        }
                    }

                    if(isset($_POST["forums"])){
                        foreach($_POST["forums"] as $forum_id){
                            $permission=0;

                            if(isset($user_data["forum_permissions"][$forum_id])){
                                foreach($user_data["forum_permissions"][$forum_id] as $perm=>$check){
                                    $permission = $permission | $perm;
                                }
                            }

                            $user_data["forum_permissions"][$forum_id]=$permission;
                        }
                    }

                    if(empty($user_data["forum_permissions"])) $user_data["forum_permissions"]=array();

                    unset($user_data["delforum"]);
                    unset($user_data["new_forum"]);
                    unset($user_data["new_forum_permissions"]);

                    break;

                case "groups":
                    $groupdata = array();

                    if($_POST["new_group"]){
                        // set the new group permission to approved
                        $groupdata[$_POST["new_group"]] = PHORUM_USER_GROUP_APPROVED;
                    }

                    if(isset($_POST["group_perm"])){
                        foreach($_POST["group_perm"] as $group_id=>$perm){
                            // as long as we aren't removing them from the group, accept other values
                            if ($perm != PHORUM_USER_GROUP_REMOVE){
                                $groupdata[$group_id] = $perm;
                            }
                        }
                    }

                    phorum_user_save_groups($_POST["user_id"], $groupdata);
                    break;
            }

            if(isset($_POST['password1']) && !empty($_POST['password1']) && !empty($_POST['password2']) && $_POST['password1'] != $_POST['password2']) {
                $error="Passwords don't match!";
            } elseif(!empty($_POST['password1']) && !empty($_POST['password2'])) {
                $user_data['password']=$_POST['password1'];
            }

            // clean up
            unset($user_data["module"]);
            unset($user_data["section"]);
            unset($user_data["password1"]);
            unset($user_data["password2"]);

            if(empty($error)){
                phorum_user_save($user_data);
                phorum_admin_okmsg("User Saved");
            }
        }

    }

    if ($error) {
        phorum_admin_error($error);
    }

    include_once "./include/admin/PhorumInputForm.php";
    include_once "./include/profile_functions.php";
    include_once "./include/users.php";

    if(!defined("PHORUM_ORIGINAL_USER_CODE") || PHORUM_ORIGINAL_USER_CODE!==true){
        echo "Phorum User Admin only works with the Phorum User System.";
        return;
    }

    if(!isset($_GET["edit"]) && !isset($_POST['section'])){

        if(empty($_REQUEST["user_id"])){

            $frm = new PhorumInputForm ("", "get", "Search");

            $frm->addbreak("Phorum User Admin");

            $frm->hidden("module", "users");

            $frm->addrow("Search", "Username or email contains: " . $frm->text_box("search", htmlspecialchars($_REQUEST["search"]), 30) . " &bull; <a href=\"{$_SERVER['PHP_SELF']}?module=users&search=\">Find All Users</a>");

            $frm->addrow("", "Post count " .
                $frm->select_tag("posts_op", array("gte" => ">=", "lte" => "<="), $_REQUEST["posts_op"]) .
                $frm->text_box("posts", htmlspecialchars($_REQUEST["posts"]), 5) .
                " and last active " .
                // these are flipped because we're going back in time
                $frm->select_tag("lastactive_op", array("gte" => "<=", "lte" => ">="), $_REQUEST["lastactive_op"]) .
                $frm->text_box("lastactive", htmlspecialchars($_REQUEST["lastactive"]), 5) . " days ago");
            $frm->show();
        }

?>
        <hr class=\"PhorumAdminHR\" />

        <script type="text/javascript">
        <!--
        function CheckboxControl(form, onoff) {
            for (var i = 0; i < form.elements.length; i++)
                if (form.elements[i].type == "checkbox")
                    form.elements[i].checked = onoff;
        }
        // -->
        </script>
<?php

        $search=$_REQUEST["search"];

        $url_safe_search=urlencode($_REQUEST["search"]);
        $url_safe_search.="&posts=".urlencode($_REQUEST["posts"]);
        $url_safe_search.="&posts_op=".urlencode($_REQUEST["posts_op"]);
        $url_safe_search.="&lastactive=".urlencode($_REQUEST["lastactive"]);
        $url_safe_search.="&lastactive_op=".urlencode($_REQUEST["lastactive_op"]);

        $users=phorum_db_search_users($_REQUEST["search"]);

        if (isset($_REQUEST["posts"]) && $_REQUEST["posts"] != "" && $_REQUEST["posts"] >= 0) {
            $cmpfn = phorum_admin_gen_compare($_REQUEST["posts_op"]);
            $users = phorum_admin_filter_arr($users, "posts", $_REQUEST["posts"], $cmpfn);
        }

        if(isset($_REQUEST["lastactive"]) && $_REQUEST["lastactive"] != "" && $_REQUEST["lastactive"] >= 0) {
            $time = time() - ($_REQUEST["lastactive"] * 86400);
            $cmpfn = phorum_admin_gen_compare($_REQUEST["lastactive_op"]);
            $users = phorum_admin_filter_arr($users, "date_last_active", $time, $cmpfn);
        }

        $total=count($users);

        // count active
        $total_active=0;
        $total_poster=0;
        foreach($users as $user){
          if ($user['active']==1) {
            $total_active++;
            if (intval($user['posts'])) $total_poster++;
          }
        }


        settype($_REQUEST["start"], "integer");

        $display=30;

        $users=array_slice($users, $_REQUEST["start"], $display);

        if(count($users)) {

            $nav="";

            if($_REQUEST["start"]>0){
                $old_start=$_REQUEST["start"]-$display;
                $nav.="<a href=\"$_SERVER[PHP_SELF]?module=users&search=$url_safe_search&start=$old_start\">Previous Page</a>";
            }

            $nav.="&nbsp;&nbsp;";

            if($_REQUEST["start"]+$display<$total){
                $new_start=$_REQUEST["start"]+$display;
                $nav.="<a href=\"$_SERVER[PHP_SELF]?module=users&search=$url_safe_search&start=$new_start\">Next Page</a>";
            }

            echo <<<EOT
            <form name="UsersForm" action="{$_SERVER['PHP_SELF']}" method="post">
            <input type="hidden" name="module" value="users">
            <input type="hidden" name="action" value="deleteUsers">
            <table border="0" cellspacing="1" cellpadding="0"
                   class="PhorumAdminTable" width="100%">
            <tr>
                <td>$total users found ($total_active active, $total_poster posting)</td>
                <td colspan="3">Showing $display users at a time
                <td colspan="2" align="right">$nav</td>
            </tr>
            <tr>
                <td class="PhorumAdminTableHead">User</td>
                <td class="PhorumAdminTableHead">Email</td>
                <td class="PhorumAdminTableHead">Status</td>
                <td class="PhorumAdminTableHead">Posts</td>
                <td class="PhorumAdminTableHead">Last Activity</td>
                <td class="PhorumAdminTableHead">Delete</td>
            </tr>
EOT;

            foreach($users as $user){

                switch($user['active']){

                    case PHORUM_USER_ACTIVE:
                        $status = "Active";
                        break;

                    case PHORUM_USER_PENDING_EMAIL:
                    case PHORUM_USER_PENDING_BOTH:
                        $status = "Pending Confirmation";
                        break;

                    case PHORUM_USER_PENDING_MOD:
                        $status = "Pending Moderator Approval";

                    default:
                        $status = "Deactivated";
                }

                $posts = intval($user['posts']);

                $ta_class = "PhorumAdminTableRow".($ta_class == "PhorumAdminTableRow" ? "Alt" : "");

                echo "<tr>\n";
                echo "    <td class=\"".$ta_class."\"><a href=\"$_SERVER[PHP_SELF]?module=users&user_id={$user['user_id']}&edit=1\">".htmlspecialchars($user['username'])."</a></td>\n";
                echo "    <td class=\"".$ta_class."\">".htmlspecialchars($user['email'])."</td>\n";
                echo "    <td class=\"".$ta_class."\">{$status}</td>\n";
                echo "    <td class=\"".$ta_class."\" style=\"text-align:right\">{$posts}</td>\n";
                echo "    <td class=\"".$ta_class."\" align=\"right\">".(intval($user['date_last_active']) ? strftime($PHORUM['short_date'], intval($user['date_last_active'])) : "&nbsp;")."</td>\n";
                echo "    <td class=\"".$ta_class."\"><input type=\"checkbox\" name=\"deleteIds[]\" value=\"{$user['user_id']}\"></td>\n";
                echo "</tr>\n";
            }

            echo <<<EOT
            <tr>
              <td colspan="6" align="right">
              <input type="button" value="Check All"
               onClick="CheckboxControl(this.form, true);">
              <input type="button" value="Clear All"
               onClick="CheckboxControl(this.form, false);">
              <input type="submit" name="submit" value="Delete Selected Users"
               onClick="return confirm('Really delete the selected user(s)?')">
              </td>
            </tr>
            </table>
            </form>
EOT;

        } else {

            echo "No Users Found.";

        }

    }

    // display edit form
    if(isset($_REQUEST["user_id"])){

        $user=phorum_user_get($_REQUEST["user_id"]);

        if(count($user)){

            $frm = new PhorumInputForm ("", "post", "Update");

            $frm->hidden("module", "users");

            $frm->hidden("section", "main");

            $frm->hidden("user_id", $_REQUEST["user_id"]);

            $frm->hidden("fk_campsite_user_id", $user["fk_campsite_user_id"]);

            $frm->addbreak("Edit User");

            $frm->addrow("User Name", htmlspecialchars($user["username"])."&nbsp;&nbsp;<a href=\"#forums\">Edit Forum Permissions</a>&nbsp;&nbsp;<a href=\"#groups\">Edit Groups</a>");

            $frm->addrow("Email", $frm->text_box("email", $user["email"], 50));
            $frm->addrow("Password (Enter to change)", $frm->text_box("password1",""));
            $frm->addrow("Password (Confirmation)", $frm->text_box("password2",""));


            $frm->addrow("Signature", $frm->textarea("signature", htmlspecialchars($user["signature"])));

            $frm->addrow("Active", $frm->select_tag("active", array("No", "Yes"), $user["active"]));

            $frm->addrow("Administrator", $frm->select_tag("admin", array("No", "Yes"), $user["admin"]));

            $frm->addrow("Registration Date", phorum_date("%m/%d/%Y %I:%M%p",$user['date_added']));

            $row=$frm->addrow("Date last active", phorum_date("%m/%d/%Y %I:%M%p",$user['date_last_active']));

            $frm->addhelp($row, "Date last active", "This shows the date, when the user was last seen in the forum. Check your setting on \"Track user usage\" in the \"General Settings\". As long as this setting is not enabled, the activity will not be tracked.");


            $frm->show();

            echo "<br /><hr class=\"PhorumAdminHR\" /><br /><a name=\"forums\"></a>";

            $frm = new PhorumInputForm ("", "post", "Update");

            $frm->hidden("user_id", $_REQUEST["user_id"]);

            $frm->hidden("module", "users");

            $frm->hidden("section", "forums");

            $row=$frm->addbreak("Edit Forum Permissions");

            $frm->addhelp($row, "Forum Permissions", "These are permissions set exclusively for this user.  You need to grant all permisssions you want the user to have for a forum here.  No permissions from groups or a forum's properties will be used once the user has specific permissions for a forum.");

            $forums=phorum_db_get_forums();

            $perm_frm = $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_READ."]", 1, "Read")."&nbsp;&nbsp;".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_REPLY."]", 1, "Reply")."&nbsp;&nbsp;".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_NEW_TOPIC."]", 1, "Create&nbsp;New&nbsp;Topics")."&nbsp;&nbsp;".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_EDIT."]", 1, "Edit&nbsp;Their&nbsp;Posts")."<br />".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_ATTACH."]", 1, "Attach&nbsp;Files")."<br />".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_MODERATE_MESSAGES."]", 1, "Moderate Messages")."&nbsp;&nbsp;".
                        $frm->checkbox("new_forum_permissions[".PHORUM_USER_ALLOW_MODERATE_USERS."]", 1, "Moderate Users")."&nbsp;&nbsp;";

            $arr[]="Add A Forum...";
            foreach($forums as $forum_id=>$forum){
                if(!isset($user["forum_permissions"][$forum_id]))
                    $arr[$forum_id]=$forum["name"];
            }

            if(count($arr)>1)
                $frm->addrow($frm->select_tag("new_forum", $arr), $perm_frm);


            if(is_array($user["forum_permissions"])){
                foreach($user["forum_permissions"] as $forum_id=>$perms){
                    $perm_frm = $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_READ."]", 1, "Read", ($perms & PHORUM_USER_ALLOW_READ))."&nbsp;&nbsp;".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_REPLY."]", 1, "Reply", ($perms & PHORUM_USER_ALLOW_REPLY))."&nbsp;&nbsp;".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_NEW_TOPIC."]", 1, "Create&nbsp;New&nbsp;Topics", ($perms & PHORUM_USER_ALLOW_NEW_TOPIC))."&nbsp;&nbsp;".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_EDIT."]", 1, "Edit&nbsp;Their&nbsp;Posts", ($perms & PHORUM_USER_ALLOW_EDIT))."<br />".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_ATTACH."]", 1, "Attach&nbsp;Files", ($perms & PHORUM_USER_ALLOW_ATTACH))."<br />".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_MODERATE_MESSAGES."]", 1, "Moderate Messages", ($perms & PHORUM_USER_ALLOW_MODERATE_MESSAGES))."&nbsp;&nbsp;".
                                $frm->checkbox("forum_permissions[$forum_id][".PHORUM_USER_ALLOW_MODERATE_USERS."]", 1, "Moderate Users", ($perms & PHORUM_USER_ALLOW_MODERATE_USERS))."&nbsp;&nbsp;".

                    $frm->hidden("forums[$forum_id]", $forum_id);

                    $row=$frm->addrow($forums[$forum_id]["name"]."<br />".$frm->checkbox("delforum[$forum_id]", 1, "Delete"), $perm_frm);

                }
            }

            $frm->show();

            echo "<br /><hr class=\"PhorumAdminHR\" /><br /><a name=\"groups\"></a>";

            $frm = new PhorumInputForm ("", "post", "Update");

            $frm->hidden("user_id", $_REQUEST["user_id"]);

            $frm->hidden("module", "users");

            $frm->hidden("section", "groups");

            $extra_opts = "";
            // if its an admin, let the user know that the admin will be able to act as a moderator no matter what
            if ($user["admin"]){
                $row=$frm->addbreak("Edit Groups (Admins can act as a moderator of every group, regardless of these values)");
            }
            else{
                $row=$frm->addbreak("Edit Groups");
            }

            $groups= phorum_db_get_groups();
            $usergroups = phorum_user_get_groups($_REQUEST["user_id"]);

            $arr=array("Add A Group...");
            foreach($groups as $group_id=>$group){
                if(!isset($usergroups[$group_id]))
                    $arr[$group_id]=$group["name"];
            }

            if(count($arr)>1)
                $frm->addrow("Add A Group", $frm->select_tag("new_group", $arr));

            if(is_array($usergroups)){
                $group_options = array(PHORUM_USER_GROUP_REMOVE => "< Remove User From Group >",
                        PHORUM_USER_GROUP_SUSPENDED => "Suspended",
                        PHORUM_USER_GROUP_UNAPPROVED => "Unapproved",
                        PHORUM_USER_GROUP_APPROVED => "Approved",
                        PHORUM_USER_GROUP_MODERATOR => "Group Moderator");
                foreach($usergroups as $group_id => $group_perm){
                    $group_info = phorum_db_get_groups($group_id);
                    $frm->hidden("groups[$group_id]", "$group_id");
                    $frm->addrow($group_info[$group_id]["name"], $frm->select_tag("group_perm[$group_id]", $group_options, $group_perm, $extra_opts));
                }
            }

            $frm->show();

        } else {

            echo "User Not Found.";

        }

    }

?>
