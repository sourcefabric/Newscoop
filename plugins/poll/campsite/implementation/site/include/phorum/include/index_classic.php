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

$forums = phorum_db_get_forums( 0, $parent_id );

$PHORUM["DATA"]["FORUMS"] = array();

$forums_shown=false;

foreach( $forums as $forum ) {

    if ( $forum["folder_flag"] ) {

        $forum["url"] = phorum_get_url( PHORUM_INDEX_URL, $forum["forum_id"] );

    } else {

        if($PHORUM["hide_forums"] && !phorum_user_access_allowed(PHORUM_USER_ALLOW_READ, $forum["forum_id"])){
            continue;
        }

        $forum["url"] = phorum_get_url( PHORUM_LIST_URL, $forum["forum_id"] );

        // if there is only one forum in Phorum, redirect to it.
        if ( $parent_id==0 && count( $forums ) < 2 ) {
            phorum_redirect_by_url($forum['url']);
            exit();
        } 

        if ( $forum["message_count"] > 0 ) {
            $forum["last_post"] = phorum_date( $PHORUM["long_date"], $forum["last_post_time"] );
        } else {
            $forum["last_post"] = "&nbsp;";
        } 

        if($PHORUM["DATA"]["LOGGEDIN"] && $PHORUM["show_new_on_index"]){
            list($forum["new_messages"], $forum["new_threads"]) = phorum_db_newflag_get_unread_count($forum["forum_id"]);
        }
    } 

    $forums_shown=true;

    $PHORUM["DATA"]["FORUMS"][] = $forum;
} 

if(!$forums_shown){
    // we did not show any forums here, show an error-message
    // set all our URL's
    phorum_build_common_urls();
    unset($PHORUM["DATA"]["URL"]["TOP"]);
    $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["NoForums"];
    
    include phorum_get_template( "header" );
    phorum_hook( "after_header" );
    include phorum_get_template( "message" );
    phorum_hook( "before_footer" );
    include phorum_get_template( "footer" );
    
} else {
    
    $PHORUM["DATA"]["FORUMS"]=phorum_hook("index", $PHORUM["DATA"]["FORUMS"]);
    
    // set all our URL's
    phorum_build_common_urls();
    
    // should we show the top-link?
    if($PHORUM['forum_id'] == 0 || $PHORUM['vroot'] == $PHORUM['forum_id']) { 
        unset($PHORUM["DATA"]["URL"]["INDEX"]);    
    }
    
    include phorum_get_template( "header" );
    phorum_hook("after_header");
    include phorum_get_template( "index_classic" );
    phorum_hook("before_footer");
    include phorum_get_template( "footer" );
}

?>
