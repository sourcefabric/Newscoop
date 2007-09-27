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

if(!defined("PHORUM")) return;

// Check if the user is allowed to post a new message or a reply.
if( ($mode == "post" && !phorum_user_access_allowed(PHORUM_USER_ALLOW_NEW_TOPIC)) ||
    ($mode == "reply" && !phorum_user_access_allowed(PHORUM_USER_ALLOW_REPLY)) ) { if ($PHORUM["DATA"]["LOGGEDIN"]) {
        // If users are logged in and can't post, they don't have rights to do so.
        $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["NoPost"];
    } else {
        // Check if they could post if logged in. If so, let them know to log in.
        if( ($mode == "reply" && $PHORUM["reg_perms"] & PHORUM_USER_ALLOW_REPLY) ||
            ($mode == "post" && $PHORUM["reg_perms"] & PHORUM_USER_ALLOW_NEW_TOPIC) ) {
            $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["PleaseLoginPost"];
        } else {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["NoPost"];
        }
    }
    $error_flag = true;
    return;

// Check that they are logged in according to the security settings in
// the admin. If they aren't then either set a message with a login link
// (when running as include) or redirect to the login page.
} elseif($PHORUM["DATA"]["LOGGEDIN"] && !$PHORUM["DATA"]["FULLY_LOGGEDIN"]){

    if (isset($PHORUM["postingargs"]["as_include"])) {

        // Generate the URL to return to after logging in.
        $args = array(PHORUM_REPLY_URL, $PHORUM["args"][1]);
        if (isset($PHORUM["args"][2])) $args[] = $PHORUM["args"][2];
        if (isset($PHORUM["args"]["quote"])) $args[] = "quote=1";
        $redir = urlencode(call_user_func_array('phorum_get_url', $args));
        $url = phorum_get_url(PHORUM_LOGIN_URL, "redir=$redir");
        
        $PHORUM["DATA"]["URL"]["REDIRECT"] = $url;
        $PHORUM["DATA"]["BACKMSG"] = $PHORUM["DATA"]["LANG"]["LogIn"];
        $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["PeriodicLogin"];
        $error_flag = true;
        return;

    } else {

        // Generate the URL to return to after logging in.
        $args = array(PHORUM_POSTING_URL);
        if (isset($PHORUM["args"][1])) $args[] = $PHORUM["args"][1];
        if (isset($PHORUM["args"][2])) $args[] = $PHORUM["args"][2];
        if (isset($PHORUM["args"]["quote"])) $args[] = "quote=1";
        $redir = urlencode(call_user_func_array('phorum_get_url', $args));

        phorum_redirect_by_url(phorum_get_url(PHORUM_LOGIN_URL,"redir=$redir"));
        exit();

    } 
}

// Put read-only user info in the message.
if ($mode == "post" || $mode == "reply")
{
    if ($PHORUM["DATA"]["LOGGEDIN"]){
        $message["user_id"] = $PHORUM["user"]["user_id"];
        $message["author"]  = $PHORUM["user"]["username"];
    } else {
        $message["user_id"] = 0;
    }
}

// On finishing up, find the original message data in case we're
// editing or replying. Put read-only data in the message to prevent
// data tampering.
if ($finish && ($mode == 'edit' || $mode == 'reply'))
{
    $id = $mode == "edit" ? "message_id" : "parent_id";
    $origmessage = phorum_db_get_message($message[$id]);
    if (! $origmessage) {
        phorum_redirect_by_url(phorum_get_url(PHORUM_INDEX_URL));
        exit();
    }

    // Copy read-only information for editing messages.
    if ($mode == "edit") {
        $message = phorum_posting_merge_db2form($message, $origmessage, READONLYFIELDS);
    // Copy read-only information for replying to messages.
    } else {
        $message["parent_id"] = $origmessage["message_id"];
        $message["thread"] = $origmessage["thread"];
    }
}

// We never store the email address in the message in case it
// was posted by a registered user.
if ($message["user_id"]) {
    $message["email"] = "";
}

// Find the startmessage for the thread.
if ($mode == "reply" || $mode == "edit") {
    $top_parent = phorum_db_get_message($message["thread"]);
}

// Do permission checks for replying to messages.
if ($mode == "reply")
{
    // Find the direct parent for this message.
    if ($message["thread"] != $message["parent_id"]) {
        $parent = phorum_db_get_message($message["parent_id"]);
    } else {
        $parent = $top_parent;
    }

    // If this thread is unapproved, then get out.
    $unapproved =
        empty($top_parent) ||
        empty($parent) ||
        $top_parent["closed"] ||
        $top_parent["status"] != PHORUM_STATUS_APPROVED ||
        $parent["status"] != PHORUM_STATUS_APPROVED;

    if ($unapproved) 
    {
        // In case we run the editor included in the read page,
        // we should not redirect to the listpage for moderators.
        // Else a moderator can never read an unapproved message.
        if (isset($PHORUM["postingargs"]["as_include"])) {
            if ($PHORUM["DATA"]["MODERATOR"]) {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["UnapprovedMessage"];
                $error_flag = true;
                return;
            }
        }

        // In other cases, redirect users that are replying to
        // unapproved messages to the message list.
        phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
        exit;
    }

}

// Do permission checks for editing messages.
if ($mode == "edit")
{
    // Check if the user is allowed to edit this post.
    $timelim = $PHORUM["user_edit_timelimit"];
    $useredit =
        $message["user_id"] == $PHORUM["user"]["user_id"] &&
        phorum_user_access_allowed(PHORUM_USER_ALLOW_EDIT) &&
        ! empty($top_parent) &&
        ! $top_parent["closed"] &&
        (! $timelim || $message["datestamp"] + ($timelim * 60) >= time());

    // Moderators are allowed to edit message, but not messages from
    // announcement threads. Announcements may only be edited by users
    // for which the option "announcement" is set as allowed.
    $moderatoredit =
        $PHORUM["DATA"]["MODERATOR"] &&
        $message["forum_id"] == $PHORUM["forum_id"] &&
        ($message["special"] != "announcement" || 
         $PHORUM["DATA"]["OPTION_ALLOWED"]["announcement"]);

    if (!$useredit && !$moderatoredit) {
        $PHORUM["DATA"]["MESSAGE"] =
            $PHORUM["DATA"]["LANG"]["EditPostForbidden"];
        $error_flag = true;
        return;
    }
}


?>
