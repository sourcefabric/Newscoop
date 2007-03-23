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

// Create a message which can be used by the database library.
$dbmessage = array(
    "message_id"    => $message["message_id"],
    "thread"        => $message["thread"],
    "parent_id"     => $message["parent_id"],
    "forum_id"      => $message["forum_id"],
    "author"        => $message["author"],
    "subject"       => $message["subject"],
    "email"         => $message["email"],
    "status"        => $message["status"],
    "closed"        => ! $message["allow_reply"],
    "body"          => $message["body"],
    "meta"          => $message["meta"],
);

// Update sort setting, if allowed. This can only be done 
// when editing the thread starter message.
if ( $message["parent_id"]==0 ) {

    if ($PHORUM["DATA"]["OPTION_ALLOWED"]["sticky"] && $message["special"]=="sticky") {
        $dbmessage["sort"] = PHORUM_SORT_STICKY;
    } elseif ($PHORUM["DATA"]["OPTION_ALLOWED"]["announcement"] && $message["special"] == "announcement") {
        $dbmessage["forum_id"] = $PHORUM["vroot"] ? $PHORUM["vroot"] : 0;
        $dbmessage["sort"] = PHORUM_SORT_ANNOUNCEMENT;
    } else {
        // Not allowed to edit. Keep existing sort value.
        switch ($message["special"]) {
            case "sticky": $sort = PHORUM_SORT_STICKY; break;
            case "announcement": $sort = PHORUM_SORT_ANNOUNCEMENT; break;
            default: $sort = PHORUM_SORT_DEFAULT; break;
        }
        $dbmessage["sort"] = $sort;
    }

} else {

    // set some key fields to the same values as the first message in the thread
    $dbmessage["forum_id"] = $top_parent["forum_id"];
    $dbmessage["sort"] = $top_parent["sort"];

}

// Update the editing info in the meta data.
$dbmessage["meta"]["show_signature"] = $message["show_signature"];
$dbmessage["meta"]["edit_count"] =
    isset($message["meta"]["edit_count"])
    ? $message["meta"]["edit_count"]+1 : 1;
$dbmessage["meta"]["edit_date"] = time();
$dbmessage["meta"]["edit_username"] = $PHORUM["user"]["username"];

// Update attachments in the meta data, link active attachments
// to the message and delete stale attachments.
$dbmessage["meta"]["attachments"] = array();
foreach ($message["attachments"] as $info)
{
    if ($info["keep"])
    {
        $dbmessage["meta"]["attachments"][] = array(
            "file_id" => $info["file_id"],
            "name"    => $info["name"],
            "size"    => $info["size"],
        );

        phorum_db_file_link(
            $info["file_id"],
            $message["message_id"],
            PHORUM_LINK_MESSAGE
        );
    } else {
        phorum_db_file_delete($info["file_id"]);
    }
}
if (!count($dbmessage["meta"]["attachments"])) {
    unset($dbmessage["meta"]["attachments"]);
}

// Update the data in the database and run pre and post editing hooks.
$dbmessage = phorum_hook("pre_edit", $dbmessage);
phorum_db_update_message($message["message_id"], $dbmessage);
phorum_hook("post_edit", $dbmessage);

// Update children to the same sort setting and forum_id.
// The forum_id update is needed for switching between
// announcements and other types of messages.
if (! $message["parent_id"] &&
    $origmessage["sort"] != $dbmessage["sort"])
{
    $messages = phorum_db_get_messages($message["thread"], 0);
    unset($messages["users"]);
    foreach($messages as $message_id => $msg){
        if($msg["sort"]!=$dbmessage["sort"] ||
           $msg["forum_id"] != $dbmessage["forum_id"]) {
            $msg["sort"]=$dbmessage["sort"];
            $msg["forum_id"]=$dbmessage["forum_id"];
            phorum_db_update_message($message_id, $msg);
        }
    }

    // The forum stats have to be updated. Announcements aren't
    // counted in the thread_count, so if switching to or
    // from announcement, the thread_count will change.
    phorum_db_update_forum_stats(true);
}

// Update all thread messages to the same closed setting.
if (! $message["parent_id"] &&
    $origmessage["closed"] != $dbmessage["closed"]) {
    if ($dbmessage["closed"]) {
        phorum_db_close_thread($message["thread"]);
    } else {
        phorum_db_reopen_thread($message["thread"]);
    }
}

// Update thread info.
phorum_update_thread_info($message['thread']);

// Update thread subscription or unsubscription.
if ($message["user_id"])
{
    if ($message["email_notify"])
    {
        phorum_user_subscribe(
            $message["user_id"], $PHORUM["forum_id"],
            $message["thread"], PHORUM_SUBSCRIPTION_MESSAGE
        );
    } else {
        phorum_user_unsubscribe(
            $message["user_id"],
            $message["thread"],
            $message["forum_id"]
        );
    }
}

$PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["MsgModEdited"];
$PHORUM['DATA']["BACKMSG"] = $PHORUM['DATA']["LANG"]["BackToThread"];
$PHORUM["DATA"]["URL"]["REDIRECT"] = phorum_get_url(
    PHORUM_READ_URL,
    $message["thread"],
    $message["message_id"]
);

?>
