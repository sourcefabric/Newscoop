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

if(!defined("PHORUM_CONTROL_CENTER")) return;

if (isset($PHORUM["args"]["group"])){
    $perm = phorum_user_allow_moderate_group($PHORUM["args"]["group"]);
}
else{
    $perm = $PHORUM["DATA"]["GROUP_MODERATOR"];
}

if (!$perm) {
    phorum_redirect_by_url(phorum_get_url(PHORUM_CONTROLCENTER_URL));
    exit();
} 

// figure out what the user is trying to do, in this case we have a group to list (and maybe some commands)
if (isset($PHORUM["args"]["group"])){
    // if adding a new user to the group
    if (isset($_REQUEST["adduser"])){
        $userid = phorum_db_user_check_field("username", $_REQUEST["adduser"]);
        // load the users groups, add the new group, then save again
        $groups = phorum_user_get_groups($userid);
        // make sure the user isn't already a member of the group
        if (!isset($groups[$PHORUM["args"]["group"]])){
            $groups[$PHORUM["args"]["group"]] = PHORUM_USER_GROUP_APPROVED;
            phorum_user_save_groups($userid, $groups);
            $PHORUM["DATA"]["Message"] = $PHORUM["DATA"]["LANG"]["UserAddedToGroup"];
        }
    }

    // if changing the existing members of the group
    if (isset($_REQUEST["status"])){
        foreach ($_REQUEST["status"] as $userid => $status){
            // load the users groups, make the change, then save again
            $groups = phorum_user_get_groups($userid);
            // we can't set someone to be a moderator from here
            if ($status != PHORUM_USER_GROUP_MODERATOR){
                $groups[$PHORUM["args"]["group"]] = $status;
            }
            if ($status == PHORUM_USER_GROUP_REMOVE){
                unset($groups[$PHORUM["args"]["group"]]);
            }
            phorum_user_save_groups($userid, $groups);
        }
        $PHORUM["DATA"]["Message"] = $PHORUM["DATA"]["LANG"]["ChangesSaved"];
    }

    $group = phorum_db_get_groups($PHORUM["args"]["group"]);
    $PHORUM["DATA"]["GROUP"]["name"] = $group[$PHORUM["args"]["group"]]["name"];        
    $PHORUM["DATA"]["USERS"] = array();
    $PHORUM["DATA"]["GROUP"]["url"] = phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"]);
        
    $PHORUM["DATA"]["FILTER"] = array();
    $PHORUM["DATA"]["FILTER"][] = array("name" => $PHORUM["DATA"]["LANG"]["None"],
        "enable" => !(!isset($PHORUM["args"]["filter"])),
        "url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"]));
    $PHORUM["DATA"]["FILTER"][] = array("name" => $PHORUM["DATA"]["LANG"]["Approved"],
        "enable" => !(isset($PHORUM["args"]["filter"]) && $PHORUM["args"]["filter"] == PHORUM_USER_GROUP_APPROVED),
        "url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"], "filter=" . PHORUM_USER_GROUP_APPROVED));
    $PHORUM["DATA"]["FILTER"][] = array("name" => $PHORUM["DATA"]["LANG"]["PermGroupModerator"], 
        "enable" => !(isset($PHORUM["args"]["filter"]) && $PHORUM["args"]["filter"] == PHORUM_USER_GROUP_MODERATOR),
        "url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"], "filter=" . PHORUM_USER_GROUP_MODERATOR));
    $PHORUM["DATA"]["FILTER"][] = array("name" => $PHORUM["DATA"]["LANG"]["Suspended"], 
        "enable" => !(isset($PHORUM["args"]["filter"]) && $PHORUM["args"]["filter"] == PHORUM_USER_GROUP_SUSPENDED),
        "url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"], "filter=" . PHORUM_USER_GROUP_SUSPENDED));
    $PHORUM["DATA"]["FILTER"][] = array("name" => $PHORUM["DATA"]["LANG"]["Unapproved"], 
        "enable" => !(isset($PHORUM["args"]["filter"]) && $PHORUM["args"]["filter"] == PHORUM_USER_GROUP_UNAPPROVED),
        "url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $PHORUM["args"]["group"], "filter=" . PHORUM_USER_GROUP_UNAPPROVED));

    $PHORUM["DATA"]["STATUS_OPTIONS"] = array();
    $PHORUM["DATA"]["STATUS_OPTIONS"][] = array("value" => PHORUM_USER_GROUP_REMOVE, "name" => "&lt; " . $PHORUM["DATA"]["LANG"]["RemoveFromGroup"] . " &gt;");
    $PHORUM["DATA"]["STATUS_OPTIONS"][] = array("value" => PHORUM_USER_GROUP_APPROVED, "name" => $PHORUM["DATA"]["LANG"]["Approved"]);
    $PHORUM["DATA"]["STATUS_OPTIONS"][] = array("value" => PHORUM_USER_GROUP_UNAPPROVED, "name" => $PHORUM["DATA"]["LANG"]["Unapproved"]);
    $PHORUM["DATA"]["STATUS_OPTIONS"][] = array("value" => PHORUM_USER_GROUP_SUSPENDED, "name" => $PHORUM["DATA"]["LANG"]["Suspended"]);

    $groupmembers = phorum_db_get_group_members($PHORUM["args"]["group"]);
    $usersingroup = array_keys($groupmembers);
    $users = phorum_user_get($usersingroup);
    $memberlist = array();
    foreach ($groupmembers as $userid => $status){
        // if we have a filter, check that the user is in it
        if (isset($PHORUM["args"]["filter"])){
            if ($PHORUM["args"]["filter"] != $status){
                continue;
            }
        }

        $disabled = false;
        $statustext = "";
        // moderators can't edit other moderators
        if ($status == PHORUM_USER_GROUP_MODERATOR){
            $disabled = true;
            $statustext = $PHORUM["DATA"]["LANG"]["PermGroupModerator"];
        }

        $PHORUM["DATA"]["USERS"][$userid] = array("userid" => $userid, 
            "name" => $users[$userid]["username"],
            "displayname" => $users[$userid]["username"],
            "status" => $status,
            "statustext" => $statustext,
            "disabled" => $disabled,
            "flag" => ($status < PHORUM_USER_GROUP_APPROVED),
            "profile" => phorum_get_url(PHORUM_PROFILE_URL, $userid)
            );
    }

    $PHORUM["DATA"]["USERS"] = phorum_hook("user_list", $PHORUM["DATA"]["USERS"]);

    // if the option to build a dropdown list is enabled, build the list of members that could be added
    if ($PHORUM["enable_dropdown_userlist"]){
        $userlist = phorum_user_get_list();
        $PHORUM["DATA"]["NEWMEMBERS"] = array();

        foreach ($userlist as $userid => $userinfo){
            if (!in_array($userid, $usersingroup)){
                $PHORUM["DATA"]["NEWMEMBERS"][] = $userinfo;
            }
        }
    }
}


// if they aren't doing anything, show them a list of groups they can moderate
else{
    $PHORUM["DATA"]["GROUPS"] = array();
    $groups = phorum_user_get_moderator_groups();
    // put these things in order so the user can read them
    asort($groups);
    foreach ($groups as $groupid => $groupname){
        // get the group members who are unapproved, so we can count them
        $members = phorum_db_get_group_members($groupid, PHORUM_USER_GROUP_UNAPPROVED);
        $PHORUM["DATA"]["GROUPS"][] = array("id" => $groupid, 
            "name" => $groupname, 
            "unapproved" => count($members),
            "unapproved_url" => phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $groupid, "filter=" . PHORUM_USER_GROUP_UNAPPROVED),
            "url" =>  phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MODERATION,  "group=" . $groupid)
            );
    }
}

$template = "cc_groupmod";
?>
