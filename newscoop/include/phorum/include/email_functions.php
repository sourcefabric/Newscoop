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

function phorum_valid_email($email){
    $PHORUM = $GLOBALS["PHORUM"];

    $ret = false;

    $email = trim($email);

    if(preg_match('/^([a-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]+(\.[a-z0-9\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]+)*)@(((([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))\.)*((([-a-z0-9]*[a-z0-9])?)|(#[0-9]+)|(\[((([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\.){3}(([01]?[0-9]{0,2})|(2(([0-4][0-9])|(5[0-5]))))\]))$/i', $email)){
        if(!$PHORUM["dns_lookup"]){
            // format is valid
            // don't look up mail server
            $ret = true;
        } elseif(function_exists('checkdnsrr')) {

            // check if a mailserver exists for the domain
            if(checkdnsrr($fulldomain, "MX")) {
                $ret = true;
            }

            // some hosts don't have an MX record, but accept mail themselves
            if(!$ret){
                // default timeout of 60 seconds makes the user way too long
                // in case of problems.
                ini_set('default_socket_timeout', 10);
                if(@fsockopen($fulldomain, 25)){
                    $ret = true;
                }
            }
        } else {
            // bah, you must be using windows and we can't run real checks
            $ret = true;
        }
    }

    return $ret;
}

/**
 * function for sending email to users, gets addresses-array and data-array
 */
function phorum_email_user($addresses, $data)
{
    $PHORUM = $GLOBALS['PHORUM'];

    $mailmessage = $data['mailmessage'];
    unset($data['mailmessage']);
    $mailsubject = $data['mailsubject'];
    unset($data['mailsubject']);

    if(is_array($data) && count($data)) {
        foreach(array_keys($data) as $key){
            $mailmessage = str_replace("%$key%", $data[$key], $mailmessage);
            $mailsubject = str_replace("%$key%", $data[$key], $mailsubject);
        }
    }

    $num_addresses = count($addresses);
    $from_address = "\"".$PHORUM['system_email_from_name']."\" <".$PHORUM['system_email_from_address'].">";

    $hook_data = array(
        'addresses'  => $addresses,
        'from'       => $from_address,
        'subject'    => $mailsubject,
        'body'       => $mailmessage,
        'bcc'        => $PHORUM['use_bcc']
    );

    $send_messages = phorum_hook("send_mail", $hook_data);

    if(isset($data["msgid"])){
        $msgid="\nMessage-ID: {$data['msgid']}";
    } else {
        $msgid="";
    }

    if($send_messages != 0 && $num_addresses > 0){
        $phorum_major_version = substr(PHORUM, 0, strpos(PHORUM, '.'));
        $mailer = "Phorum" . $phorum_major_version;
        $mailheader ="Content-Type: text/plain; charset={$PHORUM["DATA"]["CHARSET"]}\nContent-Transfer-Encoding: {$PHORUM["DATA"]["MAILENCODING"]}\nX-Mailer: $mailer$msgid\n";

        if(isset($PHORUM['use_bcc']) && $PHORUM['use_bcc'] && $num_addresses > 3){
            mail(" ", $mailsubject, $mailmessage, $mailheader."From: $from_address\nBCC: " . implode(",", $addresses));
        } else {
            foreach($addresses as $address){
                mail($address, $mailsubject, $mailmessage, $mailheader."From: $from_address");
            }
        }
    }

    return $num_addresses;
}

function phorum_email_pm_notice($message, $langusers)
{
    $mail_data = array(
        "pm_message_id" => $message["pm_message_id"],
        "author"        => $message["from_username"],
        "subject"       => $message["subject"],
        "full_body"     => $message["message"],
        "plain_body"    => wordwrap(phorum_strip_body($message["message"]),72),
        "read_url"      => phorum_get_url(PHORUM_PM_URL, "page=read", "pm_id=" . $message["pm_message_id"]),
    );

    if (isset($_POST[PHORUM_SESSION_LONG_TERM])) {
        // strip any auth info from the read url
        $mail_data["read_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["read_url"]);
    }

    foreach ($langusers as $language => $users)
    {
        $PHORUM = $GLOBALS["PHORUM"];

        if ( file_exists( "./include/lang/$language.php" ) ) {
            include( "./include/lang/$language.php" );
        } else {
            include("./include/lang/{$PHORUM['language']}.php");
        }

        $mail_data["mailmessage"] = $PHORUM["DATA"]["LANG"]['PMNotifyMessage'];
        $mail_data["mailsubject"] = $PHORUM["DATA"]["LANG"]['PMNotifySubject'];

        $addresses = array();
        foreach ($users as $user) {
            $addresses[] = $user["email"];
        }

        phorum_email_user($addresses, $mail_data);
    }
}

function phorum_email_notice($message)
{
    $PHORUM=$GLOBALS["PHORUM"];

    // do we allow email-notification for that forum?
    if(!$PHORUM['allow_email_notify']) {
        return;
    }

    include_once("./include/format_functions.php");

    $mail_users_full = phorum_db_get_subscribed_users($PHORUM['forum_id'], $message['thread'], PHORUM_SUBSCRIPTION_MESSAGE);

    if (count($mail_users_full)) {
        $mail_data = array(
            "forumname"   => strip_tags($PHORUM["DATA"]["NAME"]),
            "forum_id"    => $PHORUM['forum_id'],
            "message_id"  => $message['message_id'],
            "author"      => $message['author'],
            "subject"     => $message['subject'],
            "full_body"   => $message['body'],
            "plain_body"  => phorum_strip_body($message['body']),
            "read_url"    => phorum_get_url(PHORUM_READ_URL, $message['thread'], $message['message_id']),
            "remove_url"  => phorum_get_url(PHORUM_FOLLOW_URL, $message['thread'], "remove=1"),
            "noemail_url" => phorum_get_url(PHORUM_FOLLOW_URL, $message['thread'], "noemail=1"),
            "followed_threads_url" => phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_SUBSCRIPTION_THREADS),
            "msgid"       => $message["msgid"]
        );
        if (isset($_POST[PHORUM_SESSION_LONG_TERM])) {
            // strip any auth info from the read url
            $mail_data["read_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["read_url"]);
            $mail_data["remove_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["remove_url"]);
            $mail_data["noemail_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["noemail_url"]);
            $mail_data["followed_threads_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["followed_threads_url"]);
        }
        // go through the user-languages and send mail with their set lang
        foreach($mail_users_full as $language => $mail_users) {
            if ( file_exists( "./include/lang/$language.php" ) ) {
                include( "./include/lang/$language.php" );
            } else {
                include("./include/lang/{$PHORUM['language']}.php");
            }
            $mail_data["mailmessage"] = $PHORUM["DATA"]["LANG"]['NewReplyMessage'];
            $mail_data["mailsubject"] = $PHORUM["DATA"]["LANG"]['NewReplySubject'];
            phorum_email_user($mail_users, $mail_data);

        }
    }
}

function phorum_email_moderators($message)
{
    $PHORUM=$GLOBALS["PHORUM"];

    $mail_users = phorum_user_get_moderators($PHORUM['forum_id'],false,true);

    if (count($mail_users)) {
        include_once("./include/format_functions.php");
        if($message["status"] > 0) { // just notification of a new message
            $mailtext = $PHORUM["DATA"]["LANG"]['NewUnModeratedMessage'];
        } else { // posts needing approval
            $mailtext = $PHORUM["DATA"]["LANG"]['NewModeratedMessage'];
        }
        $mail_data = array(
            "mailmessage" => $mailtext,
            "mailsubject" => $PHORUM["DATA"]["LANG"]['NewModeratedSubject'],
            "forumname"   => strip_tags($PHORUM["DATA"]["NAME"]),
            "forum_id"    => $PHORUM['forum_id'],
            "message_id"  => $message['message_id'],
            "author"      => $message['author'],
            "subject"     => $message['subject'],
            "full_body"   => $message['body'],
            "plain_body"  => phorum_strip_body($message['body']),
            "approve_url" => phorum_get_url(PHORUM_PREPOST_URL),
            "read_url"    => phorum_get_url(PHORUM_READ_URL, $message['thread'], $message['message_id'])
        );
        if (isset($_POST[PHORUM_SESSION_LONG_TERM])) {
            // strip any auth info from the read url
            $mail_data["read_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["read_url"]);
            $mail_data["approve_url"] = preg_replace("!,{0,1}" . PHORUM_SESSION_LONG_TERM . "=" . urlencode($_POST[PHORUM_SESSION_LONG_TERM]) . "!", "", $mail_data["approve_url"]);
        }
        phorum_email_user($mail_users, $mail_data);
    }
}

?>
