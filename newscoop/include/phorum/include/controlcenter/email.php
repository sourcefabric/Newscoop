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

if ( !defined( "PHORUM_CONTROL_CENTER" ) ) return;
// need this for banlist-checks
include_once("./include/profile_functions.php");

// email-verification
if($PHORUM['registration_control']) {
    //$PHORUM['DATA']['PROFILE']['email_temp']="email_address@bogus.com|bla";
    if (!empty($PHORUM['DATA']['PROFILE']['email_temp'])) {
            list($PHORUM['DATA']['PROFILE']['email_temp_part'],$bogus)=explode("|",$PHORUM['DATA']['PROFILE']['email_temp']);
    }
}

if ( count( $_POST ) ) {

    if ( empty( $_POST["email"] ) ) {
        $error = $PHORUM["DATA"]["LANG"]["ErrRequired"];
    } elseif (!phorum_valid_email( $_POST["email"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrEmail"];
    } elseif ($PHORUM['user']['email'] != $_POST["email"] && phorum_user_check_email($_POST["email"])) {
        $error = $PHORUM["DATA"]["LANG"]["ErrEmailExists"];
    } elseif (!phorum_check_ban_lists($_POST["email"], PHORUM_BAD_EMAILS)) {
        $error = $PHORUM["DATA"]["LANG"]["ErrBannedEmail"];
    } elseif (isset($PHORUM['DATA']['PROFILE']['email_temp_part']) && !empty($_POST['email_verify_code']) && $PHORUM['DATA']['PROFILE']['email_temp_part']."|".$_POST['email_verify_code'] != $PHORUM['DATA']['PROFILE']['email_temp']) {
        $error = $PHORUM['DATA']['LANG']['ErrWrongMailcode'];
    } else {
        // flip this due to db vs. UI wording.
        $_POST["hide_email"] = ( isset($_POST["hide_email"]) ) ? 0 : 1;

        $_POST['moderation_email'] = ( isset($_POST['moderation_email']) && phorum_user_moderate_allowed(PHORUM_MODERATE_ALLOWED_ANYWHERE) ) ? 1 : 0;

        // Remember this for the template.
        if (isset($PHORUM['DATA']['PROFILE']['email_temp_part'])) {
            $email_temp_part = $PHORUM['DATA']['PROFILE']['email_temp_part'];
        }

        // do we need to send a confirmation-mail?
        if(isset($PHORUM['DATA']['PROFILE']['email_temp_part']) && !empty($_POST['email_verify_code']) && $PHORUM['DATA']['PROFILE']['email_temp_part']."|".$_POST['email_verify_code'] == $PHORUM['DATA']['PROFILE']['email_temp']) {
               $_POST['email']=$PHORUM['DATA']['PROFILE']['email_temp_part'];
               $_POST['email_temp']="";
               unset($email_temp_part);
        } elseif($PHORUM['registration_control'] && !empty($_POST['email']) && strtolower($_POST['email']) != strtolower($PHORUM["DATA"]["PROFILE"]['email'])) {
            // ... generate the confirmation-code ... //
            $conf_code= mt_rand ( 1000000, 9999999);
            $_POST['email_temp']=$_POST['email']."|".$conf_code;
            // ... send email ... //
            $maildata=array(
            'mailmessage'   => wordwrap($PHORUM['DATA']['LANG']['EmailVerifyBody'], 72),
            'mailsubject'   => $PHORUM['DATA']['LANG']['EmailVerifySubject'],
            'uname'         => $PHORUM['DATA']['PROFILE']['username'],
            'newmail'       => $_POST['email'],
            'mailcode'      => $conf_code,
            'cc_url'        => phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_MAIL)
            );
            phorum_email_user(array($_POST['email']),$maildata);

            // Remember this for the template.
            $email_temp_part = $_POST['email'];
            unset($_POST['email']);
        }
        list($error,$okmsg) = phorum_controlcenter_user_save( $panel );
    }
}

if (isset($email_temp_part)) {
$PHORUM['DATA']['PROFILE']['email_temp_part'] = $email_temp_part;
}

// flip this due to db vs. UI wording.
if ( !empty( $PHORUM['DATA']['PROFILE']["hide_email"] ) ) {
    $PHORUM["DATA"]["PROFILE"]["hide_email_checked"] = "";
} else {
    // more html stuff in the code. yuck.
    $PHORUM["DATA"]["PROFILE"]["hide_email_checked"] = " checked=\"checked\"";
}

if(phorum_user_moderate_allowed(PHORUM_MODERATE_ALLOWED_ANYWHERE)){
    $PHORUM["DATA"]["PROFILE"]["show_moderate_options"] = true;

    if ( !empty( $PHORUM['DATA']['PROFILE']["moderation_email"] ) ) {
        $PHORUM["DATA"]["PROFILE"]["moderation_email_checked"] = " checked=\"checked\"";
    } else {
        $PHORUM["DATA"]["PROFILE"]["moderation_email_checked"] = "";
    }
} else {
    $PHORUM["DATA"]["PROFILE"]["show_moderate_options"] = false;
}

$PHORUM["DATA"]["PROFILE"]["EMAIL_CONFIRM"]=$PHORUM["registration_control"];


$PHORUM["DATA"]["PROFILE"]["block_title"] = $PHORUM["DATA"]["LANG"]["EditMailsettings"];

$PHORUM['DATA']['PROFILE']['MAILSETTINGS'] = 1;
$template = "cc_usersettings";

?>
