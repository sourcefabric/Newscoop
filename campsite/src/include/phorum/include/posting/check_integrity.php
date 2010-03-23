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

// For phorum_valid_email()
include_once("./include/email_functions.php");

$error = false;

// Post and reply checks for unregistered users.
if (! $PHORUM["DATA"]["LOGGEDIN"] &&
    ($mode == 'post' || $mode == 'reply'))
{
    if (empty($message["author"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrAuthor"];
    } elseif ((!defined('PHORUM_ENFORCE_UNREGISTERED_NAMES') || (defined('PHORUM_ENFORCE_UNREGISTERED_NAMES') && PHORUM_ENFORCE_UNREGISTERED_NAMES == true)) && phorum_user_check_username($message["author"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrRegisterdName"];
    } elseif (!empty($message["email"]) &&
              phorum_user_check_email($message["email"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrRegisterdEmail"];
    }
}

// A hook entry for checking the data from a module.
if (! $error) {
    list($message, $error) =
        phorum_hook("check_post", array($message, $error));
}

// Data integrity checks for all messages.
if (! $error)
{
    if (empty($message["subject"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrSubject"];
    } elseif (empty($message["body"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrBody"];
    } elseif (!empty($message["email"]) &&
              !phorum_valid_email($message["email"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrEmail"];
    } elseif (strlen($message["body"]) > 64000) {
        $error = $PHORUM["DATA"]["LANG"]["ErrBodyTooLarge"];
    }
}

if ($error) {
    $PHORUM["DATA"]["ERROR"] = $error;
    $error_flag = true;
}

?>
