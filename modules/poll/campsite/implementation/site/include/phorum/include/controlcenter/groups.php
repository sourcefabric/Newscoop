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

// if we have a request to join a group, try and do it
if (isset($_POST["joingroup"]) && $_POST["joingroup"] > 0){
    // get the group, and the group list of the user trying to join
    $group = phorum_db_get_groups($_POST["joingroup"]);
    $usergroup = phorum_user_get_groups($PHORUM["user"]["user_id"]);
    
    // the user can't already be a member of the group, and the group must allow join requests
    if (!isset($usergroup[$_POST["joingroup"]])){
        if ($group[$_POST["joingroup"]]["open"] == PHORUM_GROUP_OPEN){
            $usergroup[$_POST["joingroup"]] = PHORUM_USER_GROUP_APPROVED;
            phorum_user_save_groups($PHORUM["user"]["user_id"], $usergroup);
            $PHORUM['DATA']['Message'] = $PHORUM['DATA']['LANG']['GroupJoinSuccess'];
        }
        elseif ($group[$_POST["joingroup"]]["open"] == PHORUM_GROUP_REQUIRE_APPROVAL){
            $usergroup[$_POST["joingroup"]] = PHORUM_USER_GROUP_UNAPPROVED;
            phorum_user_save_groups($PHORUM["user"]["user_id"], $usergroup);
            $PHORUM['DATA']['Message'] = $PHORUM['DATA']['LANG']['GroupJoinSuccessModerated'];
        }
        else
        {
            $PHORUM['DATA']['Message'] = $PHORUM['DATA']['LANG']['GroupJoinFail'];
        }
    }
    else{
        $PHORUM['DATA']['Message'] = $PHORUM['DATA']['LANG']['GroupJoinFail'];
    }
}

$template = "cc_groups";
$PHORUM['DATA']['Groups'] = phorum_readable_groups();
$PHORUM['DATA']['JOINGROUP'] = phorum_joinable_groups();
$PHORUM["DATA"]["GROUP"]["url"] = phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL, "panel=" . PHORUM_CC_GROUP_MEMBERSHIP);

/* --------------------------------------------------------------- */

function phorum_readable_groups()
{
    $PHORUM=$GLOBALS['PHORUM'];
    $readablegroups = array();
    $groups = phorum_user_get_groups($PHORUM["user"]["user_id"]);
   // print_r(array_keys($groups));
   // print_r(array_values($groups));
    foreach($groups as $groupid => $perm){
        $group = phorum_db_get_groups($groupid);
        switch ($perm){
            case PHORUM_USER_GROUP_SUSPENDED:
                $readablegroups[] = array('groupname' => $group[$groupid]["name"], 'perm' => $PHORUM['DATA']['LANG']['Suspended']);
                break;
            case PHORUM_USER_GROUP_UNAPPROVED:
                $readablegroups[] = array('groupname' => $group[$groupid]["name"], 'perm' => $PHORUM['DATA']['LANG']['Unapproved']);
                break;

            case PHORUM_USER_GROUP_APPROVED:
                $readablegroups[] = array('groupname' => $group[$groupid]["name"], 'perm' => $PHORUM['DATA']['LANG']['Approved']);
                break;

            case PHORUM_USER_GROUP_MODERATOR:
                $readablegroups[] = array('groupname' => $group[$groupid]["name"], 'perm' => $PHORUM['DATA']['LANG']['PermGroupModerator']);
                break;

              // something weird happened
            default:
                $readablegroups[] = array('groupname' => $group[$groupid]["name"], 'perm' => '?');
                break;
        }
    }
    return $readablegroups;
}

function phorum_joinable_groups()
{
    $PHORUM = $GLOBALS["PHORUM"];
    $joinablegroups = array();
    $groups = phorum_db_get_groups();
    $memberof = phorum_user_get_groups($PHORUM["user"]["user_id"]);
    foreach ($groups as $group){
        if (!isset($memberof[$group["group_id"]])){
            if ($group["open"] == PHORUM_GROUP_OPEN){
                $joinablegroups[] = array("group_id" => $group["group_id"], "name" => $group["name"]);
            }
            elseif ($group["open"] == PHORUM_GROUP_REQUIRE_APPROVAL){
                $joinablegroups[] = array("group_id" => $group["group_id"], "name" => $group["name"] . " (*)");
            }
        }
    }
    return $joinablegroups;
}
?>
