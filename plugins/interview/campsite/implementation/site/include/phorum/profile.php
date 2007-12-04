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
define('phorum_page','profile');

include_once("./common.php");
include_once("./include/email_functions.php");
include_once("./include/format_functions.php");

// set all our URL's
phorum_build_common_urls();

$template = "profile";
$error = "";

// redirect if no profile id passed
if(!empty($PHORUM["args"][1])){
    $profile_id = (int)$PHORUM["args"][1];
}

if(empty($PHORUM["args"][1]) || empty($profile_id)){
    phorum_redirect_by_url(phorum_get_url(PHORUM_INDEX_URL));
    exit();
}

include_once("./include/users.php");

$user = phorum_user_get($profile_id);

if(!is_array($user) || $user["active"]==0) {
    $PHORUM["DATA"]["ERROR"]=$PHORUM["DATA"]["LANG"]["UnknownUser"];
    $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_LIST_URL);
    $PHORUM['DATA']["BACKMSG"]=$PHORUM["DATA"]["LANG"]["BackToList"];

    // have to include the header here for the Redirect
    include phorum_get_template("header");
    phorum_hook("after_header");
    include phorum_get_template("message");
    phorum_hook("before_footer");
    include phorum_get_template("footer");
    return;
}

// security messures
unset($user["password"]);
unset($user["permissions"]);

// set any custom profile fields that are not present.
if (!empty($PHORUM["PROFILE_FIELDS"])) {
    foreach($PHORUM["PROFILE_FIELDS"] as $field) {
        if (!isset($user[$field['name']])) $user[$field['name']] = "";
    }
}

$PHORUM["DATA"]["PROFILE"] = $user;
$PHORUM["DATA"]["PROFILE"]["forum_id"] = $PHORUM["forum_id"];

$PHORUM["DATA"]["PROFILE"]["date_added"]=phorum_date( $PHORUM['short_date'], $PHORUM["DATA"]["PROFILE"]["date_added"]);

if( !empty($PHORUM["user"]["admin"]) ||
    (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES) && PHORUM_MOD_EMAIL_VIEW) ||
    (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_USERS) && PHORUM_MOD_EMAIL_VIEW) ||
    !$user["hide_email"]){

    $PHORUM["DATA"]["PROFILE"]["email"]=phorum_html_encode($user["email"]);
} else {
    $PHORUM["DATA"]["PROFILE"]["email"] = $PHORUM["DATA"]["LANG"]["Hidden"];
}

if( $PHORUM["track_user_activity"] && 
    (!empty($PHORUM["user"]["admin"]) ||
     (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) ||
     (phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_USERS)) ||
     !$user["hide_activity"])){

    $PHORUM["DATA"]["PROFILE"]["date_last_active"]=phorum_date( $PHORUM['short_date'], $PHORUM["DATA"]["PROFILE"]["date_last_active"]);
} else {
    unset($PHORUM["DATA"]["PROFILE"]["date_last_active"]);
}

$PHORUM["DATA"]["PROFILE"]["posts"]=number_format($PHORUM["DATA"]["PROFILE"]["posts"]);

$PHORUM["DATA"]["PROFILE"]["pm_url"] = phorum_get_url(PHORUM_PM_URL, "page=send", "to_id=".urlencode($user["user_id"]));
$PHORUM["DATA"]["PROFILE"]["pm_addbuddy_url"] = phorum_get_url(PHORUM_PM_URL, "page=buddies", "action=addbuddy", "addbuddy_id=".urlencode($user["user_id"]));
$PHORUM["DATA"]["PROFILE"]["is_buddy"] = phorum_db_pm_is_buddy($user["user_id"]);
// unset($PHORUM["DATA"]["PROFILE"]["signature"]);

$PHORUM["DATA"]["PROFILE"]["username"] = htmlspecialchars($PHORUM["DATA"]["PROFILE"]["username"]);

$PHORUM["DATA"]["PROFILE"] = phorum_hook("profile", $PHORUM["DATA"]["PROFILE"]);

// set all our URL's
phorum_build_common_urls();

include phorum_get_template("header");
phorum_hook("after_header");
include phorum_get_template("profile");
phorum_hook("before_footer");
include phorum_get_template("footer");

?>
