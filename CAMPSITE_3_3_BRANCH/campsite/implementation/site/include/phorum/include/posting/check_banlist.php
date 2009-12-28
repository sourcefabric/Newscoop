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

// For phorum_check_ban_lists().
include_once("./include/profile_functions.php");

// Create a list of the bans that we want to check.
$bans = array();

// Add checks for registered users.
if ($PHORUM["DATA"]["LOGGEDIN"]) {
    $bans[] = array($PHORUM["user"]["username"], PHORUM_BAD_NAMES);
    $bans[] = array($PHORUM["user"]["email"], PHORUM_BAD_EMAILS);
    $bans[] = array($PHORUM["user"]["user_id"], PHORUM_BAD_USERID);
}
// Add checks for unregistered users.
else {
    $bans[] = array($message["author"], PHORUM_BAD_NAMES);
    $bans[] = array($message["email"], PHORUM_BAD_EMAILS);
}

// Add check for IP-address bans.
$bans[] = array(NULL, PHORUM_BAD_IPS);

// Add check for Illegal Content (SPAM) bans.
$bans[] = array($message["subject"], PHORUM_BAD_SPAM_WORDS);
$bans[] = array($message["body"], PHORUM_BAD_SPAM_WORDS);


// Run the checks.
$msg = phorum_check_bans($bans);
if (!is_null($msg)) {
    $PHORUM["DATA"]["MESSAGE"] = $msg;
    $error_flag = true;
}

?>
