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
define('phorum_page','list');

include_once("./common.php");
include_once("./include/format_functions.php");

// set all our common URL's
phorum_build_common_urls();

if(!phorum_check_read_common()) {
  return;
}


if(empty($PHORUM["forum_id"])){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL);
    phorum_redirect_by_url($dest_url);
    exit();
}

// somehow we got to a folder in list.php
if($PHORUM["folder_flag"]){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL, $PHORUM["forum_id"]);
    phorum_redirect_by_url($dest_url);
    exit();
}

// check for markread
if (!empty($PHORUM["args"][1]) && $PHORUM["args"][1] == 'markread'){
    // setting all posts read
    unset($PHORUM['user']['newinfo']);
    phorum_db_newflag_allread();

    // redirect to a fresh list without markread in url
    $dest_url = phorum_get_url(PHORUM_LIST_URL);
    phorum_redirect_by_url($dest_url);
    exit();

}

if ($PHORUM["DATA"]["LOGGEDIN"]) { // reading newflags in
    $PHORUM['user']['newinfo']=phorum_db_newflag_get_flags();
}

// figure out what page we are on
if (empty($PHORUM["args"]["page"]) || !is_numeric($PHORUM["args"]["page"]) || $PHORUM["args"]["page"] < 0){
    $page=1;
} else {
    $page=intval($PHORUM["args"]["page"]);
}
$offset=$page-1;

// check the moderation-settings
$PHORUM["DATA"]["MODERATOR"] = phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES);

$build_move_url=false;
if($PHORUM["DATA"]["MODERATOR"]) {
    // find out how many forums this user can moderate
    $forums=phorum_db_get_forums(0,-1,$PHORUM['vroot']);

    $modforums=0;
    foreach($forums as $id=>$forum){
        if($forum["folder_flag"]==0 && phorum_user_moderate_allowed($id)){
            $modforums++;
        }
        if($modforums > 1) {
            $build_move_url=true;
            break;
        }
    }
}
// Get the threads
$rows = array();

// get the thread set started
$rows = phorum_db_get_thread_list($offset);

// redirect if invalid page
if(count($rows) < 1 && $offset > 0){
    $dest_url = phorum_get_url(PHORUM_LIST_URL);
    phorum_redirect_by_url($dest_url);
    exit();
}

if($PHORUM['threaded_list']) { // make it simpler :)
    $PHORUM["list_length"] = $PHORUM['list_length_threaded'];
} else {
    $PHORUM["list_length"] = $PHORUM['list_length_flat'];
}

// Figure out paging for threaded and flat mode. Sticky messages
// are in the thread_count, but because these are handled as a separate
// list (together with the announcements), they should not be included
// in the pages computation.
$pages=ceil(($PHORUM["thread_count"] - $PHORUM['sticky_count']) / $PHORUM["list_length"]);

// If we only have stickies and/of announcements, the number of pages
// will be zero. In that case, simply use one page.
if ($pages == 0) $pages = 1;

if($pages<=11){
    $page_start=1;
} elseif($pages-$page<5) {
    $page_start=$pages-10;
} elseif($pages>11 && $page>6){
    $page_start=$page-5;
} else {
    $page_start=1;
}

$pageno=1;
for($x=0;$x<11 && $x<$pages;$x++){
    $pageno=$x+$page_start;
    $PHORUM["DATA"]["PAGES"][] = array(
    "pageno"=>$pageno,
    "url"=>phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "page=$pageno")
    );
}

$PHORUM["DATA"]["CURRENTPAGE"]=$page;
$PHORUM["DATA"]["TOTALPAGES"]=$pages;

if($page_start>1){
    $PHORUM["DATA"]["URL"]["FIRSTPAGE"]=phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "page=1");
}

if($pageno<$pages){
    $PHORUM["DATA"]["URL"]["LASTPAGE"]=phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "page=$pages");
}

if($pages>$page){
    $nextpage=$page+1;
    $PHORUM["DATA"]["URL"]["NEXTPAGE"]=phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "page=$nextpage");
}
if($page>1){
    $prevpage=$page-1;
    $PHORUM["DATA"]["URL"]["PREVPAGE"]=phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "page=$prevpage");
}

$min_id=0;
if ($PHORUM["threaded_list"]){

    // loop through and read all the data in.
    foreach($rows as $key => $row){

        if($PHORUM["count_views"]) {  // show viewcount if enabled
              if($PHORUM["count_views"] == 2) { // viewcount as column
                  $PHORUM["DATA"]["VIEWCOUNT_COLUMN"]=true;
                  $rows[$key]["viewcount"]=$row['viewcount'];
              } else { // viewcount added to the subject
                  $rows[$key]["subject"]=$row["subject"]." ({$row['viewcount']} " . strtolower($PHORUM['DATA']['LANG']['Views']) . ")";
              }
        }

        $rows[$key]["datestamp"] = phorum_date($PHORUM["short_date"], $row["datestamp"]);
        $rows[$key]["lastpost"] = phorum_date($PHORUM["short_date"], $row["modifystamp"]);
        $rows[$key]["url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"], $row["message_id"]);

        if($row["message_id"] == $row["thread"]){
            $rows[$key]["threadstart"] = true;
        }else{
            $rows[$key]["threadstart"] = false;
        }

        $rows[$key]["delete_url1"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_MESSAGE, $row["message_id"]);
        $rows[$key]["delete_url2"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_TREE, $row["message_id"]);
        if($build_move_url) {
                $rows[$key]["move_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MOVE_THREAD, $row["message_id"]);
        }
        $rows[$key]["merge_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MERGE_THREAD, $row["message_id"]);

        $rows[$key]["new"] = "";
        // recognizing moved threads
        if(isset($row['meta']['moved']) && $row['meta']['moved'] == 1) {
           $rows[$key]['moved']=1;
        } elseif ($PHORUM["DATA"]["LOGGEDIN"]){

            // newflag, if its NOT in newinfo AND newer (min than min_id,
            // then its a new message

            // newflag for collapsed special threads (sticky and announcement)
            if (($rows[$key]['sort'] == PHORUM_SORT_STICKY ||
                 $rows[$key]['sort'] == PHORUM_SORT_ANNOUNCEMENT) &&
                 isset($row['meta']['message_ids']) &&
                 is_array($row['meta']['message_ids'])) {
                foreach ($row['meta']['message_ids'] as $cur_id) {
                    if(!isset($PHORUM['user']['newinfo'][$cur_id]) && $cur_id > $PHORUM['user']['newinfo']['min_id'])
                        $rows[$key]["new"] = $PHORUM["DATA"]["LANG"]["newflag"];
                }
            }
            // newflag for regular messages
            else {
                if (!isset($PHORUM['user']['newinfo'][$row['message_id']]) && $row['message_id'] > $PHORUM['user']['newinfo']['min_id']) {
                    $rows[$key]["new"]=$PHORUM["DATA"]["LANG"]["newflag"];
                }
            }
        }

        if ($row["user_id"]){
            $url = phorum_get_url(PHORUM_PROFILE_URL, $row["user_id"]);
            $rows[$key]["profile_url"] = $url;
            $rows[$key]["linked_author"] = "<a href=\"$url\">".htmlspecialchars($row['author'])."</a>";
        }else{
            $rows[$key]["profile_url"] = "";
            if(!empty($row['email'])) {
                $email_url = phorum_html_encode("mailto:$row[email]");
                // we don't normally put HTML in this code, but this makes it easier on template builders
                $rows[$key]["linked_author"] = "<a href=\"".$email_url."\">".htmlspecialchars($row["author"])."</a>";
            } else {
                $rows[$key]["linked_author"] = htmlspecialchars($row["author"]);
            }
        }
        if($min_id == 0 || $min_id > $row['message_id'])
            $min_id = $row['message_id'];
    }
    // don't move this up.  We want it to be conditional.
    include_once("./include/thread_sort.php");

    $rows = phorum_sort_threads($rows);

}else{

    // loop through and read all the data in.
    foreach($rows as $key => $row){

        $rows[$key]["lastpost"] = phorum_date($PHORUM["short_date"], $row["modifystamp"]);
        $rows[$key]["datestamp"] = phorum_date($PHORUM["short_date"], $row["datestamp"]);
        $rows[$key]["url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"]);
        $rows[$key]["newpost_url"] = phorum_get_url(PHORUM_READ_URL, $row["thread"],"gotonewpost");

        $rows[$key]["new"] = "";

        if($PHORUM["count_views"]) {  // show viewcount if enabled
              if($PHORUM["count_views"] == 2) { // viewcount as column
                  $PHORUM["DATA"]["VIEWCOUNT_COLUMN"]=true;
                  $rows[$key]["viewcount"]=$row['viewcount'];
              } else { // viewcount added to the subject
                  $rows[$key]["subject"]=$row["subject"]." ({$row['viewcount']} " . strtolower($PHORUM['DATA']['LANG']['Views']) . ")";
              }
        }

        // recognizing moved threads
        if(isset($row['meta']['moved']) && $row['meta']['moved'] == 1) {
           $rows[$key]['moved']=1;
        } else {
           $rows[$key]['moved']=0;
        }

        // default thread-count
        $thread_count=$row["thread_count"];

        if ($PHORUM["DATA"]["LOGGEDIN"]){

                    if($PHORUM["DATA"]["MODERATOR"]){
                        $rows[$key]["delete_url1"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_MESSAGE, $row["message_id"]);
                        $rows[$key]["delete_url2"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_DELETE_TREE, $row["message_id"]);
                        if($build_move_url) {
                                $rows[$key]["move_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MOVE_THREAD, $row["message_id"]);
                        }
                        $rows[$key]["merge_url"] = phorum_get_url(PHORUM_MODERATION_URL, PHORUM_MERGE_THREAD, $row["message_id"]);
                        // count could be different with hidden or unapproved posts
                        if(!$PHORUM["threaded_read"] && isset($row["meta"]["message_ids_moderator"])) {
                                $thread_count=count($row["meta"]["message_ids_moderator"]);
                        }
                    }

                    if(!$rows[$key]['moved'] && isset($row['meta']['message_ids']) && is_array($row['meta']['message_ids'])) {
                        foreach ($row['meta']['message_ids'] as $cur_id) {
                            if(!isset($PHORUM['user']['newinfo'][$cur_id]) && $cur_id > $PHORUM['user']['newinfo']['min_id'])
                                $rows[$key]["new"] = $PHORUM["DATA"]["LANG"]["newflag"];
                        }
                    }
        }

        if ($row["user_id"]){
            $url = phorum_get_url(PHORUM_PROFILE_URL, $row["user_id"]);
            $rows[$key]["profile_url"] = $url;
            $rows[$key]["linked_author"] = "<a href=\"$url\">$row[author]</a>";
        }else{
            $rows[$key]["profile_url"] = "";
            if(!empty($row['email'])) {
                $email_url = phorum_html_encode("mailto:$row[email]");
                // we don't normally put HTML in this code, but this makes it easier on template builders
                $rows[$key]["linked_author"] = "<a href=\"".$email_url."\">".htmlspecialchars($row["author"])."</a>";
            } else {
                $rows[$key]["linked_author"] = $row["author"];
            }
        }

        $pages=1;
        // thread_count computed above in moderators-section
        if(!$PHORUM["threaded_read"] && $thread_count>$PHORUM["read_length"]){

            $pages=ceil($thread_count/$PHORUM["read_length"]);

            if($pages<=5){
                $page_links="";
                for($x=1;$x<=$pages;$x++){
                    $url=phorum_get_url(PHORUM_READ_URL, $row["thread"], "page=$x");
                    $page_links[]="<a href=\"$url\">$x</a>";
                }
                $rows[$key]["pages"]=implode(",&nbsp;", $page_links);
            } else {
                $url=phorum_get_url(PHORUM_READ_URL, $row["thread"], "page=1");
                $rows[$key]["pages"]="<a href=\"$url\">1</a>&nbsp;";
                $rows[$key]["pages"].="...&nbsp;";
                $pageno=$pages-2;
                $url=phorum_get_url(PHORUM_READ_URL, $row["thread"], "page=$pageno");
                $rows[$key]["pages"].="<a href=\"$url\">$pageno</a>,&nbsp;";
                $pageno=$pages-1;
                $url=phorum_get_url(PHORUM_READ_URL, $row["thread"], "page=$pageno");
                $rows[$key]["pages"].="<a href=\"$url\">$pageno</a>,&nbsp;";
                $pageno=$pages;
                $url=phorum_get_url(PHORUM_READ_URL, $row["thread"], "page=$pageno");
                $rows[$key]["pages"].="<a href=\"$url\">$pageno</a>&nbsp;";
            }
        }
        if(isset($row['meta']['recent_post'])) {
            if($pages>1){
                $rows[$key]["last_post_url"]=phorum_get_url(PHORUM_READ_URL, $row["thread"], $row["meta"]["recent_post"]["message_id"], "page=$pages");
            } else {
                $rows[$key]["last_post_url"]=phorum_get_url(PHORUM_READ_URL, $row["thread"], $row["meta"]["recent_post"]["message_id"]);
            }

            $row['meta']['recent_post']['author'] = htmlspecialchars($row['meta']['recent_post']['author']);
            if ($row["meta"]["recent_post"]["user_id"]){
                $url = phorum_get_url(PHORUM_PROFILE_URL, $row["meta"]["recent_post"]["user_id"]);
                $rows[$key]["last_post_profile_url"] = $url;
                $rows[$key]["last_post_by"] = "<a href=\"$url\">{$row['meta']['recent_post']['author']}</a>";
            }else{
                $rows[$key]["profile_url"] = "";
                $rows[$key]["last_post_by"] = $row["meta"]["recent_post"]["author"];
            }
        } else {
            $rows[$key]["last_post_by"] = "";
        }

        if($min_id == 0 || $min_id > $row['message_id'])
            $min_id = $row['message_id'];
    }
}

// run list mods
$rows = phorum_hook("list", $rows);

// if we retrieve the body too we need to setup some more variables for the messages
// to make it a little more similar to the view in read.php
if(isset($PHORUM['TMP']['bodies_in_list']) && $PHORUM['TMP']['bodies_in_list'] == 1) {

    foreach ($rows as $id => $row) {

        // is the message unapproved?
        $row["is_unapproved"] = ($row['status'] < 0) ? 1 : 0;

        // check if its the first message in the thread
        if($row["message_id"] == $row["thread"]) {
            $row["threadstart"] = true;
        } else{
            $row["threadstart"] = false;
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

        // add the edited-message to a post if its edited
        if(isset($row['meta']['edit_count']) && $row['meta']['edit_count'] > 0) {
            $editmessage = str_replace ("%count%", $row['meta']['edit_count'], $PHORUM["DATA"]["LANG"]["EditedMessage"]);
            $editmessage = str_replace ("%lastedit%", phorum_date($PHORUM["short_date"],$row['meta']['edit_date']),  $editmessage);
            $editmessage = str_replace ("%lastuser%", $row['meta']['edit_username'],  $editmessage);
            $row["body"].="\n\n\n\n$editmessage";
        }


        if($PHORUM["max_attachments"]>0 && isset($row["meta"]["attachments"])){
            $PHORUM["DATA"]["ATTACHMENTS"]=true;
            $row["attachments"]=$row["meta"]["attachments"];
            // unset($row["meta"]["attachments"]);
            foreach($row["attachments"] as $key=>$file){
                $row["attachments"][$key]["size"]=phorum_filesize($file["size"]);
                $row["attachments"][$key]["name"]=
                htmlentities($file['name'], ENT_COMPAT,
                $PHORUM["DATA"]["CHARSET"]);
                $row["attachments"][$key]["url"]=
                phorum_get_url(PHORUM_FILE_URL, "file={$file['file_id']}");
            }
        }
        $rows[$id] = $row;
    }
}

// format messages
$rows = phorum_format_messages($rows);


// set up the data
$PHORUM["DATA"]["ROWS"] = $rows;

$PHORUM["DATA"]["URL"]["MARKREAD"] = phorum_get_url(PHORUM_LIST_URL, $PHORUM["forum_id"], "markread");
if($PHORUM["DATA"]["MODERATOR"]) {
   $PHORUM["DATA"]["URL"]["UNAPPROVED"] = phorum_get_url(PHORUM_PREPOST_URL);
}

// updating new-info for first visit (last message on first page is first new)
if ($PHORUM["DATA"]["LOGGEDIN"] && $PHORUM['user']['newinfo']['min_id'] == 0 && !isset($PHORUM['user']['newinfo'][$min_id]) && $min_id != 0){
    // setting it as min-id
    phorum_db_newflag_add_read($min_id);
}

include phorum_get_template("header");
phorum_hook("after_header");

// include the correct template
if ($PHORUM["threaded_list"]){
    include phorum_get_template("list_threads");
}else{
    include phorum_get_template("list");
}

phorum_hook("before_footer");
include phorum_get_template("footer");

?>
