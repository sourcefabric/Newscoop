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

$template = "cc_start";
$PHORUM['DATA']['UserPerms'] = phorum_readable_permissions();
$PHORUM['DATA']['PROFILE']['date_added'] = phorum_date( $PHORUM['short_date'], $PHORUM['DATA']['PROFILE']['date_added']);
if( $PHORUM["track_user_activity"] && 
    (!empty($PHORUM["user"]["admin"]) ||
     (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) ||
     (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_USERS)) ||
     !$user["hide_activity"])){

    $PHORUM["DATA"]["PROFILE"]["date_last_active"]=phorum_date( $PHORUM['short_date'], $PHORUM["DATA"]["PROFILE"]["date_last_active"]);
} else {
    unset($PHORUM["DATA"]["PROFILE"]["date_last_active"]);
}

$PHORUM["DATA"]["PROFILE"]["username"] = htmlspecialchars($PHORUM["DATA"]["PROFILE"]["username"]);

$PHORUM["DATA"]["PROFILE"] = phorum_hook("profile", $PHORUM["DATA"]["PROFILE"]);
/* --------------------------------------------------------------- */

function phorum_readable_permissions()
{
    $PHORUM = $GLOBALS['PHORUM'];
    $newperms = array();

    if (isset($PHORUM["user"]["permissions"])) {
        $forums = phorum_db_get_forums(array_keys($PHORUM["user"]["permissions"]));

        foreach($PHORUM["user"]["permissions"] as $forum => $perms) {
            if(isset($forums[$forum])) {
                if($perms & PHORUM_USER_ALLOW_MODERATE_MESSAGES){
                    $newperms[] = array('forum' => $forums[$forum]["name"], 'perm' => $PHORUM['DATA']['LANG']['PermModerator']);
                }

                if($perms & PHORUM_USER_ALLOW_READ){
                    $newperms[] = array('forum' => $forums[$forum]["name"], 'perm' => $PHORUM['DATA']['LANG']['PermAllowRead']);
                }

                if($perms & PHORUM_USER_ALLOW_REPLY){
                    $newperms[] = array('forum' => $forums[$forum]["name"], 'perm' => $PHORUM['DATA']['LANG']['PermAllowReply']);
                }

                if($perms & PHORUM_USER_ALLOW_NEW_TOPIC){
                    $newperms[] = array('forum' => $forums[$forum]["name"], 'perm' => $PHORUM['DATA']['LANG']['PermAllowPost']);
                }
            }
        }
    }

    return $newperms;
}
?>
