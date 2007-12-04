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
define('phorum_page','read');

include_once("./common.php");
include_once("./include/email_functions.php");
include_once("./include/format_functions.php");


// set all our URL's ... we need these earlier
phorum_build_common_urls();

// checking read-permissions
if(!phorum_check_read_common()) {
  return;
}

// somehow we got to a folder
if(empty($PHORUM["forum_id"]) || $PHORUM["folder_flag"]){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL, $PHORUM["forum_id"]);
    phorum_redirect_by_url($dest_url);
    exit();
}

if ($PHORUM["DATA"]["LOGGEDIN"]) { // reading newflags in
    $PHORUM['user']['newinfo']=phorum_db_newflag_get_flags();
}

$PHORUM["DATA"]["MODERATOR"] = phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES);

if($PHORUM["DATA"]["MODERATOR"]) {
        // find out how many forums this user can moderate
        $forums=phorum_db_get_forums(0,-1,$PHORUM['vroot']);

        $modforums=0;
        foreach($forums as $id=>$forum){
                if($forum["folder_flag"]==0 && phorum_user_moderate_allowed($id)){
                   $modforums++;
                }
        }
        if($modforums > 1) {
                $build_move_url=true;
        } else {
                $build_move_url=false;
        }
}

// setup some stuff based on the url passed
if(empty($PHORUM["args"][1])) {
    phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
    exit();
} elseif(empty($PHORUM["args"][2])) {
    $thread = (int)$PHORUM["args"][1];
    $message_id = (int)$PHORUM["args"][1];
} else{
    if(!is_numeric($PHORUM["args"][2])) {
        $dest_url="";
        $newervar=(int)$PHORUM["args"][1];

        switch($PHORUM["args"][2]) {
            case "newer":
                $thread = phorum_db_get_newer_thread($newervar);
                break;
            case "older":
                $thread = phorum_db_get_older_thread($newervar);
                break;
            case "markthreadread":
                // thread needs to be in $thread for the redirection
                $thread = (int)$PHORUM["args"][1];
                $thread_message=phorum_db_get_message($thread,'message_id');

                $mids=array();
                foreach($thread_message['meta']['message_ids'] as $mid) {
                    if(!isset($PHORUM['user']['newinfo'][$mid]) && $mid > $PHORUM['user']['newinfo']['min_id']) {
                        $mids[]=$mid;
                    }
                }

                $msg_count=count($mids);

                // any messages left to update newinfo with?
                if($msg_count > 0){
                    phorum_db_newflag_add_read($mids);
                    unset($mids);
                }
                break;
            case "gotonewpost":
                // thread needs to be in $thread for the redirection
                $thread = (int)$PHORUM["args"][1];
                $thread_message=phorum_db_get_message($thread,'message_id');
                $message_ids=$thread_message['meta']['message_ids'];

                foreach($message_ids as $mkey => $mid) {
                	// if already read, remove it from message-array
                	if(isset($PHORUM['user']['newinfo'][$mid]) || $mid <= $PHORUM['user']['newinfo']['min_id']) {
                		unset($message_ids[$mkey]);
                	}

                }

                // it could happen that they are all read
                if(count($message_ids)) {
                    asort($message_ids,SORT_NUMERIC); // make sure they are sorted


                    $new_message=array_shift($message_ids); // get the first element

                    if(!$PHORUM['threaded_read']) { // get new page
                        $new_page=ceil(phorum_db_get_message_index($thread,$new_message)/$PHORUM['read_length']);
                        $dest_url=phorum_get_url(PHORUM_READ_URL,$thread,$new_message,"page=$new_page");
                    } else { // for threaded
                        $dest_url=phorum_get_url(PHORUM_READ_URL,$thread,$new_message);
                    }
                } else {
                    // lets go back to the index if they are all read
                    $dest_url=phorum_get_url(PHORUM_LIST_URL);
                }

                break;


        }

        if(empty($dest_url)) {
            if($thread > 0) {
                $dest_url = phorum_get_url(PHORUM_READ_URL, $thread);
            } else{
                // we are either at the top or the bottom, go back to the list.
                $dest_url = phorum_get_url(PHORUM_LIST_URL);
            }
        }

        phorum_redirect_by_url($dest_url);
        exit();
    }

    $thread = (int)$PHORUM["args"][1];
    $message_id = (int)$PHORUM["args"][2];
}

// determining the page if page isn't given and message_id != thread
$page=0;
if(!$PHORUM["threaded_read"]) {
    if(isset($PHORUM['args']['page']) && is_numeric($PHORUM["args"]["page"]) && $PHORUM["args"]["page"] > 0) {
                $page=(int)$PHORUM["args"]["page"];
    } elseif($message_id != $thread) {
                $page=ceil(phorum_db_get_message_index($thread,$message_id)/$PHORUM['read_length']);
    } else {
                $page=1;
    }
    if(empty($page)) {
        $page=1;
    }
}

// Get the thread
$data = phorum_db_get_messages($thread,$page);


if(!empty($data) && isset($data[$thread]) && isset($data[$message_id])) {

    $fetch_user_ids = $data['users'];
    unset($data['users']);

    // remove the unneeded message bodies in threaded view
    // to avoid unnecessary formatting of bodies
    if ($PHORUM["threaded_read"] &&
        !(isset($PHORUM['TMP']['all_bodies_in_threaded_read']) &&
         !empty($PHORUM['TMP']['all_bodies_in_threaded_read']) ) ) {

            $remove_threaded_bodies=1;
            // the flag is used in the foreach-loop later on
    } else {
            $remove_threaded_bodies=0;
    }

    // build URL's that apply only here.
    if($PHORUM["float_to_top"]) {
        $PHORUM["DATA"]["URL"]["OLDERTHREAD"] = phorum_get_url(PHORUM_READ_URL, $data[$thread]["modifystamp"], "older");
        $PHORUM["DATA"]["URL"]["NEWERTHREAD"] = phorum_get_url(PHORUM_READ_URL, $data[$thread]["modifystamp"], "newer");
    } else{
        $PHORUM["DATA"]["URL"]["OLDERTHREAD"] = phorum_get_url(PHORUM_READ_URL, $thread, "older");
        $PHORUM["DATA"]["URL"]["NEWERTHREAD"] = phorum_get_url(PHORUM_READ_URL, $thread, "newer");
    }

    $PHORUM["DATA"]["URL"]["MARKTHREADREAD"] = phorum_get_url(PHORUM_READ_URL, $thread, "markthreadread");
    $PHORUM["DATA"]["POST"]["thread"] = $thread;
    $PHORUM["DATA"]["POST"]["parentid"] = $message_id;
    $PHORUM["DATA"]["POST"]["subject"] = $data[$message_id]["subject"];

    $thread_is_closed = (bool)$data[$thread]["closed"];
    $thread_is_announcement = ($data[$thread]["sort"]==PHORUM_SORT_ANNOUNCEMENT)?1:0;

    // we might have more messages for mods
    if($PHORUM["DATA"]["MODERATOR"] && isset($data[$thread]["meta"]["message_ids_moderator"])) {
        $threadnum=count($data[$thread]['meta']['message_ids_moderator']);
    } else {
        $threadnum=$data[$thread]['thread_count'];
    }

    if(!$PHORUM["threaded_read"] && $threadnum > $PHORUM["read_length"]){
        $pages=ceil($threadnum/$PHORUM["read_length"]);

        if($pages<=11){
            $page_start=1;
        } elseif($pages-$page<5) {
            $page_start=$pages-10;
        } elseif($pages>11 && $page>6){
            $page_start=$page-5;
        } else {
            $page_start=1;
        }

        for($x=0;$x<11 && $x<$pages;$x++){
            $pageno=$x+$page_start;
            $PHORUM["DATA"]["PAGES"][] = array(
            "pageno"=>$pageno,
            "url"=>phorum_get_url(PHORUM_READ_URL, $thread, "page=$pageno")
            );
        }

        $PHORUM["DATA"]["CURRENTPAGE"]=$page;
        $PHORUM["DATA"]["TOTALPAGES"]=$pages;

        if($page_start>1){
            $PHORUM["DATA"]["URL"]["FIRSTPAGE"]=phorum_get_url(PHORUM_READ_URL, $thread, "page=1");
        }

        if($pageno<$pages){
            $PHORUM["DATA"]["URL"]["LASTPAGE"]=phorum_get_url(PHORUM_READ_URL, $thread, "page=$pages");
        }

        if($pages>$page){
            $nextpage=$page+1;
            $PHORUM["DATA"]["URL"]["NEXTPAGE"]=phorum_get_url(PHORUM_READ_URL, $thread, "page=$nextpage");
        }
        if($page>1){
            $prevpage=$page-1;
            $PHORUM["DATA"]["URL"]["PREVPAGE"]=phorum_get_url(PHORUM_READ_URL, $thread, "page=$prevpage");
        }
    }

    // fetch_user_ids filled from phorum_db_get_messages
    if(isset($fetch_user_ids) && count($fetch_user_ids)){
        $user_info=phorum_user_get($fetch_user_ids, false);
        // hook to modify user info
        $user_info = phorum_hook("read_user_info", $user_info);
    }

    // URLS which are common for the thread
    if($PHORUM["DATA"]["MODERATOR"]) {
        if($build_move_url) {
                $URLS["move_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MOVE_THREAD, $thread);
        }
        $URLS["merge_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MERGE_THREAD, $thread);
        $URLS["close_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_CLOSE_THREAD, $thread);
        $URLS["reopen_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_REOPEN_THREAD, $thread);
    }




    // main loop for template setup
    $read_messages=array(); // needed for newinfo
    foreach($data as $key => $row) {

        // should we remove the bodies in threaded view
        if($remove_threaded_bodies) {
            if ($row["message_id"] != $message_id) {
                unset($row["body"]); // strip body
            }
        }

        // assign user data to the row
        if($row["user_id"] && isset($user_info[$row["user_id"]])){
            $row["user"]=$user_info[$row["user_id"]];
            unset($row["user"]["password"]);
            unset($row["user"]["password_tmp"]);
        }
        if(!$PHORUM["threaded_read"] && $PHORUM["DATA"]["LOGGEDIN"] && $row['message_id'] > $PHORUM['user']['newinfo']['min_id'] && !isset($PHORUM['user']['newinfo'][$row['message_id']])) { // set this message as read
            $read_messages[] = array("id"=>$row['message_id'],"forum"=>$row['forum_id']);
        }
        // is the message unapproved?
        $row["is_unapproved"] = ($row['status'] < 0) ? 1 : 0;

        // all stuff that makes only sense for moderators or admin
        if($PHORUM["DATA"]["MODERATOR"]) {

            $row["delete_url1"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_MESSAGE, $row["message_id"]);
            $row["delete_url2"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_TREE, $row["message_id"]);
            $row["edit_url"]=phorum_get_url(PHORUM_POSTING_URL, "moderation", $row["message_id"]);
            $row["split_url"]=phorum_get_url(PHORUM_MODERATION_URL, PHORUM_SPLIT_THREAD, $row["message_id"]);
            if($row['is_unapproved']) {
              $row["approve_url"]=phorum_get_url(PHORUM_MODERATION_URL, PHORUM_APPROVE_MESSAGE, $row["message_id"]);
            } else {
              $row["hide_url"]=phorum_get_url(PHORUM_MODERATION_URL, PHORUM_HIDE_POST, $row["message_id"]);
            }
            if($build_move_url) {
                $row["move_url"] = $URLS["move_url"];
            }
            $row["merge_url"] = $URLS["merge_url"];
            $row["close_url"] = $URLS["close_url"];
            $row["reopen_url"] = $URLS["reopen_url"];
        }

        // allow editing only if logged in, allowed for forum, the thread is open,
        // its the same user, and its within the time restriction
        if($PHORUM["user"]["user_id"]==$row["user_id"] && phorum_user_access_allowed(PHORUM_USER_ALLOW_EDIT) &&
            !$thread_is_closed &&($PHORUM["user_edit_timelimit"] == 0 || $row["datestamp"] + ($PHORUM["user_edit_timelimit"] * 60) >= time())) {
            $row["edit"]=1;
            if($PHORUM["DATA"]["MODERATOR"]) {
                $row["edituser_url"]=$row["edit_url"];
            } else {
                $row["edituser_url"]=phorum_get_url(PHORUM_POSTING_URL, "edit", $row["message_id"]);
            }
        }

        // this stuff is used in threaded and non threaded.
        $row["short_datestamp"] = phorum_date($PHORUM["short_date"], $row["datestamp"]);
        $row["datestamp"] = phorum_date($PHORUM["long_date"], $row["datestamp"]);
        $row["url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"], $row["message_id"]);
        $row["reply_url"] = phorum_get_url(PHORUM_REPLY_URL, $row["thread"], $row["message_id"]);
        $row["quote_url"] = phorum_get_url(PHORUM_REPLY_URL, $row["thread"], $row["message_id"], "quote=1");
        $row["report_url"] = phorum_get_url(PHORUM_REPORT_URL, $row["message_id"]);
        $row["follow_url"] = phorum_get_url(PHORUM_FOLLOW_URL, $row["thread"]);

        // can only send private replies if the author is a registered user
        if ($PHORUM["enable_pm"] && $row["user_id"]) {
            $row["private_reply_url"] = phorum_get_url(PHORUM_PM_URL, "page=send", "message_id=".$row["message_id"]);
        } else {
            $row["private_reply_url"] = false;
        }

        // check if its the first message in the thread
        if($row["message_id"] == $row["thread"]) {
            $row["threadstart"] = true;
        } else{
            $row["threadstart"] = false;
        }

        // should we show the signature?
        if(isset($row['body'])) {
            if(isset($row["user"]["signature"])
               && isset($row['meta']['show_signature']) && $row['meta']['show_signature']==1){

                   $phorum_sig=trim($row["user"]["signature"]);
                   if(!empty($phorum_sig)){
                       $row["body"].="\n\n$phorum_sig";
                   }
            }

            // add the edited-message to a post if its edited
            if(isset($row['meta']['edit_count']) && $row['meta']['edit_count'] > 0) {
                $editmessage = str_replace ("%count%", $row['meta']['edit_count'], $PHORUM["DATA"]["LANG"]["EditedMessage"]);
                $editmessage = str_replace ("%lastedit%", phorum_date($PHORUM["short_date"],$row['meta']['edit_date']),  $editmessage);
                $editmessage = str_replace ("%lastuser%", $row['meta']['edit_username'],  $editmessage);
                $row["body"].="\n\n\n\n$editmessage";
            }
        }


        if(!empty($row["user_id"])) {
            $row["profile_url"] = phorum_get_url(PHORUM_PROFILE_URL, $row["user_id"]);
            // we don't normally put HTML in this code, but this makes it easier on template builders
            $row["linked_author"] = "<a href=\"".$row["profile_url"]."\">$row[author]</a>";
        } elseif(!empty($row["email"])) {
            $row["email_url"] = phorum_html_encode("mailto:$row[email]");
            // we don't normally put HTML in this code, but this makes it easier on template builders
            $row["linked_author"] = "<a href=\"".$row["email_url"]."\">".htmlspecialchars($row["author"])."</a>";
        } else {
            $row["linked_author"] = htmlspecialchars($row["author"]);
        }

        // mask host if not a moderator
        if(empty($PHORUM["user"]["admin"]) && (empty($PHORUM["DATA"]["MODERATOR"]) || !PHORUM_MOD_IP_VIEW)){
            if($PHORUM["display_ip_address"]){
                if($row["moderator_post"]){
                    $row["ip"]=$PHORUM["DATA"]["LANG"]["Moderator"];
                } elseif(is_numeric(str_replace(".", "", $row["ip"]))){
                    $row["ip"]=substr($row["ip"],0,strrpos($row["ip"],'.')).'.---';
                } else {
                    $row["ip"]="---".strstr($row["ip"], ".");
                }

            } else {
                $row["ip"]=$PHORUM["DATA"]["LANG"]["IPLogged"];
            }
        }

        if($PHORUM["max_attachments"]>0 && isset($row["meta"]["attachments"])){
            $PHORUM["DATA"]["ATTACHMENTS"]=true;
            $row["attachments"]=$row["meta"]["attachments"];
            // unset($row["meta"]["attachments"]);
            foreach($row["attachments"] as $key=>$file){
                $row["attachments"][$key]["size"]=phorum_filesize($file["size"]);
                $row["attachments"][$key]["name"]=htmlentities($file['name'], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]); // clear all special chars from name to avoid XSS
                $row["attachments"][$key]["url"]=phorum_get_url(PHORUM_FILE_URL, "file={$file['file_id']}");
            }
        }

        // newflag, if its NOT in newinfo AND newer than min_id, then its a new message
        $row["new"]="";
        if ($PHORUM["DATA"]["LOGGEDIN"]){
            if (!isset($PHORUM['user']['newinfo'][$row['message_id']]) && $row['message_id'] > $PHORUM['user']['newinfo']['min_id']) {
                $row["new"]= $PHORUM["DATA"]["LANG"]["newflag"];
            }
        }

        $messages[$row["message_id"]]=$row;
    }

    if($PHORUM["threaded_read"]) {
        // don't move this up.  We want it to be conditional.
        include_once("./include/thread_sort.php");

        // run read-threads mods
        $messages = phorum_hook("readthreads", $messages);

        $messages = phorum_sort_threads($messages);

        if($PHORUM["DATA"]["LOGGEDIN"] && !isset($PHORUM['user']['newinfo'][$message_id]) && $message_id > $PHORUM['user']['newinfo']['min_id']) {
            $read_messages[] = array("id"=>$message_id,"forum"=>$messages[$message_id]['forum_id']);
        }

        // we have to loop again and create the urls for the Next and Previous links.
        foreach($messages as $key => $row) {

            if($PHORUM["count_views"]) {  // show viewcount if enabled
                  if($PHORUM["count_views"] == 2) { // viewcount as column
                      $PHORUM["DATA"]["VIEWCOUNT_COLUMN"]=true;
                      $messages[$key]["viewcount"]=$row['viewcount'];
                  } else { // viewcount added to the subject
                      $messages[$key]["subject"]=$row["subject"]." ({$row['viewcount']} {$PHORUM['DATA']['LANG']['Views']})";
                  }
            }


            $messages[$key]["next_url"] = $PHORUM["DATA"]["URL"]["NEWERTHREAD"];
            if(empty($last_key)) {
                $messages[$key]["prev_url"] = $PHORUM["DATA"]["URL"]["OLDERTHREAD"];
            } else{
                $messages[$key]["prev_url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"], $last_key);
                $messages[$last_key]["next_url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"], $row["message_id"]);
            }

            $last_key = $key;
        }
    }

    // run read mods
    $messages = phorum_hook("read", $messages);

    // increment viewcount if enabled
    if($PHORUM['count_views']) {
        phorum_db_viewcount_inc($message_id);
    }

    // format messages
    $messages = phorum_format_messages($messages);

    // set up the data
    $PHORUM["DATA"]["MESSAGE"] = $messages[$message_id];

    // we need to remove the thread-starter from the data if we are not on the first page
    $threadsubject = $messages[$thread]["subject"];
    if($page > 1)
        unset($messages[$thread]);

    $PHORUM["DATA"]["MESSAGES"] = $messages;


    // alter the HTML_TITLE
    if(!empty($PHORUM["DATA"]["HTML_TITLE"])){
        $PHORUM["DATA"]["HTML_TITLE"].=htmlentities(PHORUM_SEPARATOR, ENT_COMPAT, $PHORUM["DATA"]["CHARSET"] );;
    }
    // No htmlentities() needed. The subject is already escaped.
    // Strip HTML tags from the HTML title. There might be HTML in 
    // here, because of modules adding images and formatting.
    $PHORUM["DATA"]["HTML_TITLE"] .= trim(strip_tags($PHORUM["threaded_read"] ? $PHORUM["DATA"]["MESSAGE"]["subject"] : $threadsubject));

    // include the correct template

    include phorum_get_template("header");
    phorum_hook("after_header");

    if($PHORUM["threaded_read"]) {
        include phorum_get_template("read_threads");
    } else{
        include phorum_get_template("read");
    }
    if($PHORUM["DATA"]["LOGGEDIN"]) { // setting read messages really read
        phorum_db_newflag_add_read($read_messages);
    }

    // An anchor so clicking on a reply button can let the browser
    // jump to the editor or the closed thread message.
    if(isset($PHORUM["reply_on_read_page"]) && $PHORUM["reply_on_read_page"]) {
        print '<a name="REPLY"></a>';
    }

    // Never show the reply box if the message is closed.
    if($thread_is_closed) {

        // Closed announcements have their own specific message.
        $key = $thread_is_announcement ? "ThreadAnnouncement" : "ThreadClosed";
        $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"][$key];
        include phorum_get_template("message");

    } elseif (isset($PHORUM["reply_on_read_page"]) && $PHORUM["reply_on_read_page"]) {

        // Prepare the arguments for the posting.php script.
        $goto_mode = "reply";
        if (isset($PHORUM["args"]["quote"]) && $PHORUM["args"]["quote"]) {
            $goto_mode = "quote";
        }

        $PHORUM["postingargs"] = array(
            1 => $goto_mode,
            2 => $message_id,
            "as_include" => true
        );

        include("./posting.php");
    }

    phorum_hook("before_footer");
    include phorum_get_template("footer");


} elseif($toforum=phorum_check_moved_message($thread)) { // is it a moved thread?

    $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["MovedMessage"];
    $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_FOREIGN_READ_URL, $toforum, $thread);
    $PHORUM['DATA']["BACKMSG"]=$PHORUM["DATA"]["LANG"]["MovedMessageTo"];

    $PHORUM["DATA"]["HTML_TITLE"] = htmlentities( $PHORUM["DATA"]["HTML_TITLE"], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"] );
    // have to include the header here for the Redirect
    include phorum_get_template("header");
    phorum_hook("after_header");
    include phorum_get_template("message");
    phorum_hook("before_footer");
    include phorum_get_template("footer");

} else { // message not found
    $PHORUM["DATA"]["ERROR"]=$PHORUM["DATA"]["LANG"]["MessageNotFound"];
    $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
    $PHORUM['DATA']["BACKMSG"]=$PHORUM["DATA"]["LANG"]["BackToList"];

    $PHORUM["DATA"]["HTML_TITLE"] = htmlentities( $PHORUM["DATA"]["HTML_TITLE"], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"] );
    // have to include the header here for the Redirect
    include phorum_get_template("header");
    phorum_hook("after_header");
    include phorum_get_template("message");
    phorum_hook("before_footer");
    include phorum_get_template("footer");
}

// find out if the given thread has been moved to another forum
function phorum_check_moved_message($thread) {
    $forum_id=$GLOBALS['PHORUM']['forum_id'];
    $message=phorum_db_get_message($thread,'message_id',true);

    if(!empty($message) && $message['forum_id'] != $forum_id) {
        $ret=$message['forum_id'];
    } else {
        $ret=false;
    }
    return $ret;
}

?>
