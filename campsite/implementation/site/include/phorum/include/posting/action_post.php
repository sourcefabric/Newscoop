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

// For phorum_update_thread_info().
include_once("./include/thread_info.php");

// For phorum_email_moderators() and phorum_email_notice().
include_once("./include/email_functions.php");

// Set some values.
$message["moderator_post"] = $PHORUM["DATA"]["MODERATOR"] ? 1 : 0;
$message["sort"] = PHORUM_SORT_DEFAULT;
$message["closed"] = $message["allow_reply"] ? 0 : 1;

// Determine and set the user's IP address.
$user_ip = $_SERVER["REMOTE_ADDR"];
if ($PHORUM["dns_lookup"]) {
    $resolved = @gethostbyaddr($_SERVER["REMOTE_ADDR"]); 
    if (!empty($resolved)) {
        $user_ip = $resolved;
    }
}
$message["ip"] = $user_ip;

// For replies, inherit the closed parameter of our top parent.
// Only for rare race conditions, since you cannot reply to
// closed threads.
if ($mode == "reply") {
    $message["closed"] = $top_parent["closed"];
    $message["allow_reply"] = ! $top_parent["closed"];
}

// Check if allow_reply can be set.
if ($mode == "post" && ! $PHORUM["DATA"]["OPTION_ALLOWED"]["allow_reply"]) {
    $message["closed"] = 0;
    $message["allow_reply"] = 1;
}

// For sticky and announcement theads set the sort parameter
// for replies to the correct value, so threaded views will work.
if ($mode == "reply")
{
    if ($top_parent["sort"] == PHORUM_SORT_STICKY) {
        $message["sort"] = PHORUM_SORT_STICKY;
    } elseif ($top_parent["sort"] == PHORUM_SORT_ANNOUNCEMENT) {
        $message["sort"] = PHORUM_SORT_ANNOUNCEMENT;
        $message["forum_id"] = $top_parent["forum_id"];
    }
}

// Do specific actions for new threads with a "special" flag.
if ($mode == "post" && isset($message["special"]))
{
    if ($message["special"]=="sticky" && $PHORUM["DATA"]["OPTION_ALLOWED"]["sticky"]) {
        $message["sort"] = PHORUM_SORT_STICKY;
    } elseif ($message["special"] == "announcement" && $PHORUM["DATA"]["OPTION_ALLOWED"]["announcement"]) {
        $message["sort"] = PHORUM_SORT_ANNOUNCEMENT;
        $message["forum_id"]= $PHORUM["vroot"] ? $PHORUM["vroot"] : 0;
    }
}

if ($PHORUM["DATA"]["LOGGEDIN"] && $message["show_signature"]) {
    $message["meta"]["show_signature"] = 1;
}

// Put messages on hold in case the forum is moderated.
if ($PHORUM["DATA"]["MODERATED"]) {
    $message["status"] = PHORUM_STATUS_HOLD;
} else {
    $message["status"] = PHORUM_STATUS_APPROVED;
}

// Create a unique message id.
$suffix = preg_replace("/[^a-z0-9]/i", "", $PHORUM["name"]);
$message["msgid"] = md5(uniqid(rand())) . ".$suffix";

// Run pre post mods.
$message = phorum_hook("pre_post", $message);

// Add attachments to meta data.
$message["meta"]["attachments"] = array();
foreach ($message["attachments"] as $info) {
    if ($info["keep"]) {
        $message["meta"]["attachments"][] = array(
            "file_id"   => $info["file_id"],
            "name"      => $info["name"],
            "size"      => $info["size"],
        );
    }
}
if (!count($message["meta"]["attachments"])) {
    unset($message["meta"]["attachments"]);
}

// Keep a copy of the message we have got now.
$message_copy = $message;

// Store the message in the database.
$success = phorum_db_post_message($message);

if ($success)
{
	// Relate the message posted to Campsite
	phorum_db_relate_message_to_campsite($message);

    // Handle linking and deleting of attachments to synchronize
    // the message attachments with the working copy list
    // of attachments.
    foreach ($message_copy["attachments"] as $info) {
        if ($info["keep"]) {
            phorum_db_file_link(
                $info["file_id"],
                $message["message_id"],
                PHORUM_LINK_MESSAGE
            );
        } else {
            phorum_db_file_delete($info["file_id"]);
        }
    }

    // Retrieve the message again to have it in the correct
    // format (otherwise it's a bit messed up in the
    // post-function). Do merge back data which is not
    // stored in the database, but which we might need later on.
    $message = phorum_db_get_message($message["message_id"]);
    foreach ($message_copy as $key => $val) {
        if (! isset($message[$key])) {
            $message[$key] = $val;
        }
    }

    phorum_update_thread_info($message["thread"]);

    // Subscribe user to the thread if requested.
    if ($message["email_notify"] && $message["user_id"]) {
        phorum_user_subscribe(
            $message["user_id"], $PHORUM["forum_id"],
            $message["thread"], PHORUM_SUBSCRIPTION_MESSAGE
        );
    }

    // Mark own message read.
    if ($PHORUM["DATA"]["LOGGEDIN"]) {
        phorum_db_newflag_add_read(array(0=>array(
            "id"    => $message["message_id"],
            "forum" => $message["forum_id"],
        )));
        phorum_user_addpost();
    }

    // Actions for messages which are approved.
    if ($message["status"] > 0)
    {
        // Update forum statistics.
        phorum_db_update_forum_stats(false, 1, $message["datestamp"]);

        // Mail subscribed users.
        phorum_email_notice($message);
    }

    // Mail moderators.
    if ($PHORUM["email_moderators"] == PHORUM_EMAIL_MODERATOR_ON) {
        phorum_email_moderators($message);
    }

    // Run after post mods.
    $message = phorum_hook("post_post", $message);

    // Posting is completed. Take the user back to the forum.
    if ($PHORUM["redirect_after_post"] == "read")
    {
        // To the end of the thread.
        if (isset($top_parent)) { // not set for top parents themselves.
            $readlen = $PHORUM["read_length"];
            $pages = ceil(($top_parent["thread_count"]+1) / $readlen);

            if ($pages > 1) {
                $redir_url = phorum_get_url(
                    PHORUM_READ_URL, $message["thread"],
                    $message["message_id"], "page=$pages"
                );
            } else {
                $redir_url = phorum_get_url(
                    PHORUM_READ_URL, $message["thread"],
                    $message["message_id"]
                );
            }

            // wrap redirect because of IE
            $redir_url = phorum_get_url(PHORUM_REDIRECT_URL, 'phorum_redirect_to=' . urlencode($redir_url));

        } else {

            $redir_url = phorum_get_url( PHORUM_READ_URL, $message["thread"] );
        }

    }
    else
    {
        $redir_url = phorum_get_url(PHORUM_LIST_URL);
    }

    phorum_redirect_by_url($redir_url);

    return;
}

// If we get here, the posting was not successful.
// Restore message and setup the data for displaying an error to the user.
$message = $message_copy;
$PHORUM["DATA"]["ERROR"] = $PHORUM["DATA"]["LANG"]["PostErrorOccured"];
$error_flag = true;

?>
