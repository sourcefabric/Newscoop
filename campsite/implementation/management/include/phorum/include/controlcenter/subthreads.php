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

// remove threads fromlist
if(isset($_POST["delthreads"])){
    foreach($_POST["delthreads"] as $thread){
        phorum_user_unsubscribe( $PHORUM['user']['user_id'], $thread );
    }
}

// change any email settings
if(isset($_POST["sub_type"])){
    foreach($_POST["sub_type"] as $thread=>$type){
        if($type!=$_POST["old_sub_type"][$thread]){
            phorum_user_unsubscribe( $PHORUM['user']['user_id'], $thread );
            phorum_user_subscribe( $PHORUM['user']['user_id'], $_POST["thread_forum_id"][$thread], $thread, $type );
        }
    }
}

// the number of days to show
if (isset($_POST['subdays']) && is_numeric($_POST['subdays'])) {
    $subdays = $_POST['subdays'];
} elseif(isset($PHORUM['args']['subdays']) && !empty($PHORUM["args"]['subdays']) && is_numeric($PHORUM["args"]['subdays'])) {
    $subdays = $PHORUM['args']['subdays'];
} else {
    $subdays = 2;
} 

$PHORUM['DATA']['SELECTED'] = $subdays; 

// reading all subscriptions to messages
$subscr_array = phorum_db_get_message_subscriptions($PHORUM['user']['user_id'], $subdays); 

// reading all forums
$forum_ids = $subscr_array['forum_ids'];
unset($subscr_array['forum_ids']);
$forums_arr = phorum_db_get_forums($forum_ids,-1,$PHORUM['vroot']);
$subscr_array_final = array();
foreach($subscr_array as $dummy => $data) {
    if ($data['forum_id'] == 0) {
        $data['forum'] = $PHORUM['DATA']['LANG']['Announcement'];
    } else {
        $data['forum'] = $forums_arr[$data['forum_id']]['name'];
    } 

    $data['datestamp'] = phorum_date($PHORUM["short_date"], $data["modifystamp"]);
    $data['readurl'] = phorum_get_url(PHORUM_FOREIGN_READ_URL, $data["forum_id"], $data["thread"]);

    if(!empty($data["user_id"])) {
        $data["profile_url"] = phorum_get_url(PHORUM_PROFILE_URL, $data["user_id"]);
        // we don't normally put HTML in this code, but this makes it easier on template builders
        $data["linked_author"] = "<a href=\"".$data["profile_url"]."\">".htmlspecialchars($data["author"])."</a>";
    } elseif(!empty($data["email"])) {
        $data["email_url"] = phorum_html_encode("mailto:$data[email]");
        // we don't normally put HTML in this code, but this makes it easier on template builders
        $data["linked_author"] = "<a href=\"".$data["email_url"]."\">".htmlspecialchars($data["author"])."</a>";
    } else {
        $data["linked_author"] = htmlspecialchars($data["author"]);
    }

    $data["subject"]=htmlspecialchars($data["subject"]);

    $subscr_array_final[] = $data;
}

$PHORUM['DATA']['subscriptions'] = $subscr_array_final;
$template = "cc_subscriptions";

?>
