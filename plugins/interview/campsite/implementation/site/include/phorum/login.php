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
define('phorum_page','login');

include_once( "./common.php" );
include_once( "./include/users.php" );
include_once( "./include/email_functions.php" );

// ----------------------------------------------------------------------------
// Handle logout
// ----------------------------------------------------------------------------

if ($PHORUM['DATA']['LOGGEDIN'] && !empty($PHORUM["args"]["logout"])) {

    // killing long-term cookie
    phorum_user_clear_session(PHORUM_SESSION_LONG_TERM);
    // killing short-term (write) cookie
    phorum_user_clear_session(PHORUM_SESSION_SHORT_TERM);

    // reset the sessid if not using cookies
    if(!$PHORUM['use_cookies']) {

        $new_sessid=md5($_POST['username'].microtime().$_POST['password']);

        $user=array(
        'user_id'=>$PHORUM['user']['user_id'],
        'sessid_st'=>$new_sessid
        );
        phorum_user_save_simple($user);
    }


    // Determine the URL to redirect the user to. The hook "after_logout"
    // can be used by module writers to set a custom redirect URL.
    if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER["HTTP_REFERER"];
    } else {
        $url = phorum_get_url(PHORUM_LIST_URL);
    }

    // Strip the session id from the URL in case URI auth is in use.
    if (stristr($url, PHORUM_SESSION_LONG_TERM)){
        $url = str_replace(PHORUM_SESSION_LONG_TERM."=".urlencode($PHORUM["args"][PHORUM_SESSION_LONG_TERM]), "", $url);
    }

    $url = phorum_hook("after_logout", $url);

    phorum_redirect_by_url($url);
    exit();
}

// ----------------------------------------------------------------------------
// Handle login and password reminder
// ----------------------------------------------------------------------------

// Set all our URLs.
phorum_build_common_urls();

$template = "login";
$error = "";
$okmsg = "";
$username = "";

// Handle posted form data.
if (count($_POST) > 0) {

    // The user wants to retrieve a new password.
    if (isset($_POST["lostpass"])) {

        // Trim the email address.
        $_POST["lostpass"] = trim($_POST["lostpass"]);

        // Did the user enter an email address?
        if (empty($_POST["lostpass"])) {
            $error = $PHORUM["DATA"]["LANG"]["LostPassError"];
        }

        // Is the email address available in the database?
        elseif ($uid = phorum_user_check_email($_POST["lostpass"])) {

            // An existing user id was found for the entered email
            // address. Retrieve the user.
            $user = phorum_user_get($uid);

            $tmp_user=array();

            // User registration not yet approved by a moderator.
            if($user["active"] == PHORUM_USER_PENDING_MOD) {
                $template = "message";
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyMod"];
            // User registration still need email verification.
            } elseif ($user["active"] == PHORUM_USER_PENDING_EMAIL ||
                      $user["active"] == PHORUM_USER_PENDING_BOTH) {

                // Generate and store a new email confirmation code.
                $tmp_user["user_id"] = $uid;
                $tmp_user["password_temp"] = substr(md5(microtime()), 0, 8);
                phorum_user_save( $tmp_user );

                // Mail the new confirmation code to the user.
                $verify_url = phorum_get_url(PHORUM_REGISTER_URL, "approve=".$tmp_user["password_temp"]."$uid");
                $maildata["mailsubject"] = $PHORUM["DATA"]["LANG"]["VerifyRegEmailSubject"];
                $maildata["mailmessage"] =
                   wordwrap($PHORUM["DATA"]["LANG"]["VerifyRegEmailBody1"],72).
                   "\n\n$verify_url\n\n".
                   wordwrap($PHORUM["DATA"]["LANG"]["VerifyRegEmailBody2"],72);
                phorum_email_user(array($user["email"]), $maildata);

                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["RegVerifyEmail"];
                $template="message";

            // The user is active.
            } else {

                // Generate and store a new password for the user.
                include_once( "./include/profile_functions.php" );
                $newpass = phorum_gen_password();
                $tmp_user["user_id"] = $uid;
                $tmp_user["password_temp"] = $newpass;
                phorum_user_save($tmp_user);

                // Mail the new password.
                $user = phorum_user_get( $uid );
                $maildata = array();
                $maildata['mailmessage'] =
                   wordwrap($PHORUM["DATA"]["LANG"]["LostPassEmailBody1"],72).
                   "\n\n".
                   $PHORUM["DATA"]["LANG"]["Username"] .": $user[username]\n".
                   $PHORUM["DATA"]["LANG"]["Password"] .": $newpass".
                   "\n\n".
                   wordwrap($PHORUM["DATA"]["LANG"]["LostPassEmailBody2"],72);
                $maildata['mailsubject'] = $PHORUM["DATA"]["LANG"]["LostPassEmailSubject"];
                phorum_email_user(array( 0 => $user['email'] ), $maildata);

                $okmsg = $PHORUM["DATA"]["LANG"]["LostPassSent"];

            }
        }

        // The entered email address was not found.
        else {
            $error = $PHORUM["DATA"]["LANG"]["LostPassError"];
        }
    }

    // The user wants to login.
    else {

        // Check if the phorum_tmp_cookie was set. If not, the user's
        // browser does not support cookies.
        if($PHORUM["use_cookies"] && !isset($_COOKIE["phorum_tmp_cookie"])) {
            $PHORUM["use_cookies"] = false;
        }

        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        // Check if the login credentials are right.
        if (phorum_user_check_login($username, $password)) {

            // Destroy the temporary cookie.
            if(isset($_COOKIE["phorum_tmp_cookie"])){
                setcookie( "phorum_tmp_cookie", "", 0, $PHORUM["session_path"], $PHORUM["session_domain"] );
            }

            // Create an URI session id if cookies are not used..
            if(!$PHORUM["use_cookies"]) {
                $uri_session_id = md5($_POST['username'].microtime().$_POST['password']);
                $user = array(
                    'user_id'  => $PHORUM['user']['user_id'],
                    'sessid_st'=> $uri_session_id
                );
                phorum_user_save_simple($user);
                phorum_user_create_session(PHORUM_SESSION_LONG_TERM,true,$uri_session_id);
            // Create cookie session(s).
            } else {
                if (!$PHORUM["DATA"]["LOGGEDIN"]) {
                    phorum_user_create_session(PHORUM_SESSION_LONG_TERM, false);
                }
                if($PHORUM["tight_security"]){
                    phorum_user_create_session(PHORUM_SESSION_SHORT_TERM, true);
                }
            }

            // Determine the URL to redirect the user to.
            // If redir is a number, it is a URL constant.
            if(is_numeric($_POST["redir"])){
                $redir = phorum_get_url($_POST["redir"]);
            }

            // Redirecting to the registration or login page is a little weird,
            // so we just go to the list page if we came from one of those.
            elseif (isset($PHORUM['use_cookies']) && $PHORUM["use_cookies"] && !strstr($_POST["redir"], "register." . PHORUM_FILE_EXTENSION) && !strstr($_POST["redir"], "login." . PHORUM_FILE_EXTENSION)) {
                $redir = $_POST["redir"];

            // By default, we redirect to the list page.
            } else {
                $redir = phorum_get_url( PHORUM_LIST_URL );
            }

            // The hook "after_login" can be used by module writers to
            // set a custom redirect URL.
            $redir =phorum_hook( "after_login", $redir );

            phorum_redirect_by_url($redir);
            exit();
        }

        // Login failed.
        else {
            $error = $PHORUM["DATA"]["LANG"]["InvalidLogin"];
        }
    }
}

// No data posted, so this is the first request. Here we set
// a temporary cookie, so we can check if the user's browser
// supports cookies.
elseif($PHORUM["use_cookies"]) {
    setcookie( "phorum_tmp_cookie", "this will be destroyed once logged in", 0, $PHORUM["session_path"], $PHORUM["session_domain"] );
}

// Determine to what URL the user must be redirected after login.
if (!empty( $PHORUM["args"]["redir"])) {
    $redir = htmlspecialchars(urldecode($PHORUM["args"]["redir"]));
} elseif (!empty( $_REQUEST["redir"])) {
    $redir = htmlspecialchars($_REQUEST["redir"]);
} elseif (!empty( $_SERVER["HTTP_REFERER"])) {
    $base = strtolower(phorum_get_url(PHORUM_BASE_URL));
    $len = strlen($base);
    if (strtolower(substr($_SERVER["HTTP_REFERER"],0,$len)) == $base) {
        $redir = htmlspecialchars($_SERVER["HTTP_REFERER"]);
    }
}
if (! isset($redir)) {
    $redir = phorum_get_url(PHORUM_LIST_URL);
}

// Setup template data.
$PHORUM["DATA"]["LOGIN"]["redir"] = $redir;
$PHORUM["DATA"]["URL"]["REGISTER"] = phorum_get_url( PHORUM_REGISTER_URL );
$PHORUM["DATA"]["URL"]["ACTION"] = phorum_get_url( PHORUM_LOGIN_ACTION_URL );
$PHORUM["DATA"]["LOGIN"]["forum_id"] = ( int )$PHORUM["forum_id"];
$PHORUM["DATA"]["LOGIN"]["username"] = htmlspecialchars( $username );
$PHORUM["DATA"]["ERROR"] = htmlspecialchars( $error );
$PHORUM["DATA"]["OKMSG"] = htmlspecialchars( $okmsg );

// Set the field to set the focus to after loading.
$PHORUM["DATA"]["FOCUS_TO_ID"] = empty($username) ? "username" : "password";

// Display the page.
include phorum_get_template( "header" );
phorum_hook( "after_header" );
include phorum_get_template( $template );
phorum_hook( "before_footer" );
include phorum_get_template( "footer" );

?>
