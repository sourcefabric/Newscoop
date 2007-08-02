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

if($PHORUM["forum_id"]==0){

    $forums[0] = array(
                    "forum_id" => 0,
                    "folder_flag" => 1,
                    "vroot" => 0
                 );
} else {

    $forums = phorum_db_get_forums( $PHORUM["forum_id"] );
}

if($PHORUM["vroot"]==$PHORUM["forum_id"]){
    $more_forums = phorum_db_get_forums( 0, $PHORUM["forum_id"] );
    foreach($more_forums as $forum_id => $forum){
        if(empty($forums[$forum_id])){
            $forums[$forum_id]=$forum;
        }
    }
    $folders[$PHORUM["forum_id"]]=$PHORUM["forum_id"];
}

$PHORUM["DATA"]["FORUMS"] = array();

$forums_shown=false;

// create the top level folder

foreach( $forums as $key=>$forum ) {
    if($forum["folder_flag"] && $forum["vroot"]==$PHORUM["vroot"]){
        $folders[$key]=$forum["forum_id"];
        $forums[$key]["url"] = phorum_get_url( PHORUM_INDEX_URL, $forum["forum_id"] );

        $sub_forums = phorum_db_get_forums( 0, $forum["forum_id"] );
        foreach($sub_forums as $sub_forum){
            if(!$sub_forum["folder_flag"]){
                $folder_forums[$sub_forum["parent_id"]][]=$sub_forum;
            }
        }
    }
}


foreach( $folders as $folder_key=>$folder_id ) {

    if(!isset($folder_forums[$folder_id])) continue;

    $shown_sub_forums=array();

    foreach($folder_forums[$folder_id] as $key=>$forum){

        if($PHORUM["hide_forums"] && !phorum_user_access_allowed(PHORUM_USER_ALLOW_READ, $forum["forum_id"])){
            unset($folder_forums[$folder_id][$key]);
            continue;
        }

        $forum["url"] = phorum_get_url( PHORUM_LIST_URL, $forum["forum_id"] );
        $forum["url_markread"] = phorum_get_url( PHORUM_INDEX_URL, $forum["forum_id"], "markread" );
        if(isset($PHORUM['use_rss']) && $PHORUM['use_rss']) {
            $forum["url_rss"] = phorum_get_url( PHORUM_RSS_URL, $forum["forum_id"] );
        }


        if ( $forum["message_count"] > 0 ) {
            $forum["last_post"] = phorum_date( $PHORUM["long_date"], $forum["last_post_time"] );
        } else {
            $forum["last_post"] = "&nbsp;";
        }

        if($PHORUM["DATA"]["LOGGEDIN"] && $PHORUM["show_new_on_index"]){
            list($forum["new_messages"], $forum["new_threads"]) = phorum_db_newflag_get_unread_count($forum["forum_id"]);
        }

        $shown_sub_forums[] = $forum;

    }

    if(count($shown_sub_forums)){
        $PHORUM["DATA"]["FORUMS"][]=$forums[$folder_key];
        $PHORUM["DATA"]["FORUMS"]=array_merge($PHORUM["DATA"]["FORUMS"], $shown_sub_forums);
    }

}

// set all our URL's
phorum_build_common_urls();

if(!count($PHORUM["DATA"]["FORUMS"])){
    include phorum_get_template( "header" );
    phorum_hook("after_header");
    $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["NoRead"];
    include phorum_get_template( "message" );
    phorum_hook("before_footer");
    include phorum_get_template( "footer" );
    return;
}

// should we show the top-link?
if($PHORUM['forum_id'] == 0 || $PHORUM['vroot'] == $PHORUM['forum_id']) {
    unset($PHORUM["DATA"]["URL"]["INDEX"]);
}

$PHORUM["DATA"]["FORUMS"]=phorum_hook("index", $PHORUM["DATA"]["FORUMS"]);

include phorum_get_template( "header" );
phorum_hook("after_header");
include phorum_get_template( "index_new" );
phorum_hook("before_footer");
include phorum_get_template( "footer" );

?>
