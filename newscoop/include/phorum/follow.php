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
define('phorum_page','subscribe');

include_once("./common.php");

phorum_require_login();

// checking read-permissions
if(!phorum_check_read_common()) {
  return;
}

// somehow we got to a folder
if($PHORUM["folder_flag"] || empty($PHORUM["forum_id"])){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL, $PHORUM["forum_id"]);
    phorum_redirect_by_url($dest_url);
    exit();
}

if(isset($PHORUM["args"][1])){
    $thread=$PHORUM["args"][1];
} elseif(isset($_POST["thread"])){
    $thread=$_POST["thread"];
}

if(empty($thread)) {
    phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
    exit();
}

$message=phorum_db_get_message($thread);

if(isset($PHORUM["args"]["remove"])){
    // we are removing a message from the follow list
    phorum_user_unsubscribe( $PHORUM['user']['user_id'], $thread );
    $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["RemoveFollowed"];
    $PHORUM["DATA"]["URL"]["REDIRECT"]=phorum_get_url(PHORUM_FOREIGN_READ_URL, $message["forum_id"], $thread);
    $PHORUM["DATA"]["BACKMSG"]=$PHORUM["DATA"]["LANG"]["BackToThread"];
    $template="message";
} elseif(isset($PHORUM["args"]["noemail"])){
    // we are stopping emails for this thread
    phorum_user_unsubscribe( $PHORUM['user']['user_id'], $thread );
    phorum_user_subscribe( $PHORUM['user']['user_id'], $message["forum_id"], $thread, PHORUM_SUBSCRIPTION_BOOKMARK );
    $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["NoMoreEmails"];
    $PHORUM["DATA"]["URL"]["REDIRECT"]=phorum_get_url(PHORUM_FOREIGN_READ_URL, $message["forum_id"], $thread);
    $PHORUM["DATA"]["BACKMSG"]=$PHORUM["DATA"]["LANG"]["BackToThread"];
    $template="message";
} elseif(!empty($_POST)) {
    // the user has submitted the form
    $type = (isset($_POST["send_email"])) ? PHORUM_SUBSCRIPTION_MESSAGE : PHORUM_SUBSCRIPTION_BOOKMARK;
    phorum_user_subscribe( $PHORUM['user']['user_id'], $message["forum_id"], $thread, $type );
    $PHORUM["DATA"]["URL"]["REDIRECT"]=phorum_get_url(PHORUM_FOREIGN_READ_URL, $message["forum_id"], $thread);
    $PHORUM["DATA"]["BACKMSG"]=$PHORUM["DATA"]["LANG"]["BackToThread"];
    $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["BookmarkedThread"];
    $template="message";
} else {
    // we are following a new thread
    $PHORUM["DATA"]["URL"]["ACTION"]=phorum_get_url(PHORUM_FOLLOW_ACTION_URL);
    $PHORUM["DATA"]["SUBJECT"]=htmlspecialchars($message["subject"]);
    $PHORUM["DATA"]["THREAD"]=$thread;
    $PHORUM["DATA"]["FORUM_ID"]=$PHORUM["forum_id"];
    $template="follow";
}


// set all our common URL's
phorum_build_common_urls();

include phorum_get_template("header");
phorum_hook("after_header");
include phorum_get_template($template);
phorum_hook("before_footer");
include phorum_get_template("footer");


?>
