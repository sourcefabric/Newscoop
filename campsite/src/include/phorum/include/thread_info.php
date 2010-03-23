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

/**
 * This is the callback-function for removing hidden messages from an array of messages
 */
 
function phorum_remove_hidden($val) 
{
    return ($val['status'] > 0);
}

/**
 * This function sets the stats for a thread like count, timestamp, etc.
 */

function phorum_update_thread_info($thread)
{
    $PHORUM = $GLOBALS["PHORUM"];
    
    $messages=phorum_db_get_messages($thread);
    //these are not needed here
    unset($messages['users']);
    
    // remove hidden/unapproved messages from the array
    $filtered_messages=array_filter($messages, "phorum_remove_hidden");    
    
    $thread_count=count($filtered_messages);

    if($thread_count>0){

        $message_ids=array_keys($filtered_messages);
    
        $parent_message=$filtered_messages[$thread];
    
        if (isset($PHORUM["reverse_threading"]) && $PHORUM["reverse_threading"]) {
            reset($filtered_messages);
            $recent_message=current($filtered_messages);
        } else {
            $recent_message=end($filtered_messages);
        }
        
        // prep the message to save
        $message["thread_count"]=$thread_count;
        $message["modifystamp"]=$recent_message["datestamp"];
        $message["meta"]=$parent_message["meta"];
        $message["meta"]["recent_post"]["user_id"]=$recent_message["user_id"];
        $message["meta"]["recent_post"]["author"]=$recent_message["author"];
        $message["meta"]["recent_post"]["message_id"]=$recent_message["message_id"];
        $message["meta"]["message_ids"]=$message_ids;
        // used only for mods
        $message["meta"]["message_ids_moderator"]=array_keys($messages);

        phorum_db_update_message($thread, $message);
        
    }

}


?>
