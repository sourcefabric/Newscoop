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
define('phorum_page','register');

include_once("./common.php");
include_once("./include/users.php");
include_once("./include/profile_functions.php");
include_once("./include/email_functions.php");

// set all our URL's
phorum_build_common_urls();

// The URL contains an approve argument, which means that a new user
// is confirming a new user account.
if (isset($PHORUM["args"]["approve"])) {

    // Extract registration validation code and user_id.
    $tmp_pass=substr($PHORUM["args"]["approve"], 0, 8);
    $user_id = (int)substr($PHORUM["args"]["approve"], 8);
    $user_id = phorum_user_verify($user_id, $tmp_pass);

    // Validation code correct.
    if ($user_id) {

        $user = phorum_user_get($user_id);

        $moduser=array();

        // The user has been denied by a moderator.
        if ($user["active"] == PHORUM_USER_INACTIVE) {
             $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyFailed"];
        // The user should still be approved by a moderator.
        } elseif ($user["active"] == PHORUM_USER_PENDING_MOD) {
        	// TODO: this message should be changed in 5.1 to have a unique message!!!
        	$PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyMod"];
        // The user is waiting for email and/or email+moderator confirmation.
        } else {
            // Waiting for both? Then switch to wait for moderator.
            if ($user["active"] == PHORUM_USER_PENDING_BOTH) {
                $moduser["active"] = PHORUM_USER_PENDING_MOD;
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyMod"];
            // Only email confirmation was required. Active the user.
            } else {
                $moduser["active"] = PHORUM_USER_ACTIVE;
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegAcctActive"];
            }

            // Save the new user active status.
            $moduser["user_id"] = $user_id;
            phorum_user_save($moduser);
        }

    // Validation code incorrect.
    } else {
        $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyFailed"];
    }

    include phorum_get_template("header");
    phorum_hook("after_header");
    include phorum_get_template("message");
    phorum_hook("before_footer");
    include phorum_get_template("footer");
    return;

}

$error = ''; // Init error as empty.

// Process posted form data.
if (count($_POST)) {

    // Sanitize input data.
    foreach ($_POST as $key => $val) {
        if ($key == 'username') {
            // Trim and space-collapse usernames, so people can't
            // impersonate as other users using the same username,
            // but with extra spaces in it.
            $_POST[$key] = preg_replace('/\s+/', ' ', trim($val));
        } else {
            $_POST[$key] = trim($val);
        }
    }

    // Check if all required fields are filled and valid.
    if (!isset($_POST["username"]) || empty($_POST['username'])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrUsername"];
    } elseif (!isset($_POST["email"]) || !phorum_valid_email($_POST["email"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrEmail"];
    } elseif (empty($_POST["password"]) || $_POST["password"] != $_POST["password2"]) {
        $error = $PHORUM["DATA"]["LANG"]["ErrPassword"];
    }
    // Check if the username and email address don't already exist.
    elseif(phorum_user_check_username($_POST["username"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrRegisterdName"];
    } elseif (phorum_user_check_email($_POST["email"])){
        $error = $PHORUM["DATA"]["LANG"]["ErrRegisterdEmail"];
    }

    // Check banlists.
    if (empty($error)) {
        $error = phorum_check_bans(array(
            array($_POST["username"], PHORUM_BAD_NAMES),
            array($_POST["email"],    PHORUM_BAD_EMAILS),
            array(NULL,               PHORUM_BAD_IPS),
        ));
    }

    // Create user if no errors have been encountered.
    if (empty($error)) {

        // Setup the default userdata to store.
        $userdata = array(
            'username'   => NULL,
            'password'   => NULL,
            'email'      => NULL,
        );
        // Add custom profile fields as acceptable fields.
        foreach ($PHORUM["PROFILE_FIELDS"] as $data) {
            $userdata[$data["name"]] = NULL;
        }
        // Update userdata with $_POST information.
        foreach ($_POST as $key => $val) {
           if (array_key_exists($key, $userdata)) {
               $userdata[$key] = $val;
           }
        }
        // Remove unused custom profile fields.
        foreach ($PHORUM["PROFILE_FIELDS"] as $field) {
            if (is_null($userdata[$field["name"]])) {
                unset($userdata[$field["name"]]);
            }
        }
        // Add static info.
        $userdata["date_added"]=time();
        $userdata["date_last_active"]=time();
        $userdata["hide_email"]=true;

        // Set user active status depending on the registration verification
        // setting. Generate a confirmation code for email verification.
        if ($PHORUM["registration_control"] == PHORUM_REGISTER_INSTANT_ACCESS) {
            $userdata["active"] = PHORUM_USER_ACTIVE;
        } elseif ($PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_EMAIL) {
            $userdata["active"] = PHORUM_USER_PENDING_EMAIL;
            $userdata["password_temp"]=substr(md5(microtime()), 0, 8);
        } elseif ($PHORUM["registration_control"]==PHORUM_REGISTER_VERIFY_MODERATOR) {
            $userdata["active"] = PHORUM_USER_PENDING_MOD;
        } elseif ($PHORUM["registration_control"]==PHORUM_REGISTER_VERIFY_BOTH) {
            $userdata["password_temp"]=substr(md5(microtime()), 0, 8);
            $userdata["active"] = PHORUM_USER_PENDING_BOTH;
        }

        // Run a hook, so module writers can update and check the userdata.
        $userdata = phorum_hook("before_register", $userdata);

        // Set $error, in case the before_register hook did set an error.
        if (isset($userdata['error'])) {
            $error = $userdata['error'];
            unset($userdata['error']);
        }
        // Try to add the user to the database.
        elseif ($user_id = phorum_user_add($userdata)) {

            // The user was added. Determine what message to show.
            if ($PHORUM["registration_control"] == PHORUM_REGISTER_INSTANT_ACCESS) {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegThanks"];
            } elseif($PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_EMAIL ||
                     $PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_BOTH) {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyEmail"];
            } elseif($PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_MODERATOR) {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyMod"];
            }

            // Send a message to the new user in case email verification is required.
            if ($PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_BOTH ||
                $PHORUM["registration_control"] == PHORUM_REGISTER_VERIFY_EMAIL) {
                $verify_url = phorum_get_url(PHORUM_REGISTER_URL, "approve=".$userdata["password_temp"]."$user_id");
                // make the link an anchor tag for AOL users
                if (preg_match("!aol\.com$!i", $userdata["email"])) {
                    $verify_url = "<a href=\"$verify_url\">$verify_url</a>";
                }
                $maildata["mailsubject"] = $PHORUM["DATA"]["LANG"]["VerifyRegEmailSubject"];
                $maildata["mailmessage"] = wordwrap($PHORUM["DATA"]["LANG"]["VerifyRegEmailBody1"], 72)."\n\n$verify_url\n\n".wordwrap($PHORUM["DATA"]["LANG"]["VerifyRegEmailBody2"], 72);
                phorum_email_user(array($userdata["email"]), $maildata);
            }

            $PHORUM["DATA"]["BACKMSG"] = $PHORUM["DATA"]["LANG"]["RegBack"];
            $PHORUM["DATA"]["URL"]["REDIRECT"] = phorum_get_url(PHORUM_LOGIN_URL);

            // Run a hook, so module writers can run tasks after registering.
            phorum_hook("after_register",$userdata);

            include phorum_get_template("header");
            phorum_hook("after_header");
            include phorum_get_template("message");
            phorum_hook("before_footer");
            include phorum_get_template("footer");
            return;

        // Adding the user to the database failed.
        } else {
            $error = $PHORUM["DATA"]["LANG"]["ErrUserAddUpdate"];
        }
    }

    // Some error encountered during processing? Then setup the
    // data to redisplay the registration form, including an error.
    if (!empty($error)) {
        foreach($_POST as $key => $val){
            $PHORUM["DATA"]["REGISTER"][$key] = htmlspecialchars($val);
        }
        $PHORUM["DATA"]["ERROR"] = htmlspecialchars($error);
    }

// No data posted, so this is the first request. Initialize form data.
} else {
    // Initialize fixed fields.
    $PHORUM["DATA"]["REGISTER"]["username"] = "";
    $PHORUM["DATA"]["REGISTER"]["email"] = "";
    $PHORUM["DATA"]["ERROR"] = "";

    // Initialize custom profile fields.
    foreach($PHORUM["PROFILE_FIELDS"] as $field) {
        $PHORUM["DATA"]["REGISTER"][$field["name"]] = "";
    }
}

# Setup static template data.
$PHORUM["DATA"]["URL"]["ACTION"] = phorum_get_url( PHORUM_REGISTER_ACTION_URL );
$PHORUM["DATA"]["REGISTER"]["forum_id"] = $PHORUM["forum_id"];
$PHORUM["DATA"]["REGISTER"]["block_title"] = $PHORUM["DATA"]["LANG"]["Register"];

// Display the registration page.
include phorum_get_template("header");
phorum_hook("after_header");
include phorum_get_template("register");
phorum_hook("before_footer");
include phorum_get_template("footer");

?>
