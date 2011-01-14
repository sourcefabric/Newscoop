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

// This script can initially be called in multiple ways to indicate what
// type of posting mode will be used. The parameters are:
//
// 1) The forum id.
//
// 2) The mode to use. Possibilities are:
//
//    - post        Post a new message (default if no mode is issued)
//    - edit        User edit of an already posted message
//    - moderation  Moderator edit of an already posted message
//    - reply       Reply to a message
//    - quote       Reply to a message, with quoting of the original message
//
// 3) If edit, moderation or reply is used: the message id.
//
// Examples:
// http://yoursite/phorum/posting.php?10,quote,15
// http://yoursite/phorum/posting.php?10,edit,20
// http://yoursite/phorum/posting.php?10,post
//
// This script can also be included in another page (for putting the editor
// screen inline in a page), by setting up the $PHORUM["postingargs"] before
// including:
//
// $PHORUM["postingargs"]["as_include"] any true value, to flag included state
// $PHORUM["postingargs"][0] the forum id
// $PHORUM["postingargs"][1] the mode to use (post,reply,quote,edit,moderation)
// $PHORUM["postingargs"][2] the message id to work with (omit for "post")
//

// ----------------------------------------------------------------------
// Basic setup and checks
// ----------------------------------------------------------------------

if (! defined('phorum_page')) {
    define('phorum_page', 'post');
}

include_once("./common.php");
include_once("include/format_functions.php");

// Check if the Phorum is in read-only mode.
if(isset($PHORUM["status"]) && $PHORUM["status"]=="read-only"){
    phorum_build_common_urls();
    $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["ReadOnlyMessage"];
    // Only show header and footer when not included in another page.
    if (phorum_page == "post") {
        include phorum_get_template("header");
        phorum_hook("after_header");
    }
    include phorum_get_template("message");
    if (phorum_page == "post") {
        phorum_hook("before_footer");
        include phorum_get_template("footer");
    }
    return;
}

// No forum id was set. Take the user back to the index.
if(empty($PHORUM["forum_id"])){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL);
    phorum_redirect_by_url($dest_url);
    exit();
}

// Somehow we got to a folder in posting.php. Take the
// user back to the folder.
if($PHORUM["folder_flag"]){
    $dest_url = phorum_get_url(PHORUM_INDEX_URL, $PHORUM["forum_id"]);
    phorum_redirect_by_url($dest_url);
    exit();
}

// ----------------------------------------------------------------------
// Definitions
// ----------------------------------------------------------------------

// A list of valid posting modes.
$valid_modes = array(
    "post",       // Post a new message
    "reply",      // Post a reply to a message
    "quote",      // Post a reply with quoting of the message replied to
    "edit",       // Edit a message
    "moderation", // Edit a message in moderator modus
);

// Configuration that we use for fields that we use in the editor form.
// Format for the array elements:
// [0] The type of field (string, integer, boolean, array).
// [1] Whether the value must be included as a hidden form field
//     if the field is read-write flagged. So this is used for
//     identifying values which are always implemented  as a
//     hidden form fields.
// [2] Whether the field is read-only or not. Within the editing process,
//     this parameter can be changed to make the field writable.
//     (for example if a moderator is editing a message).
// [3] A default value to initialize the form field with.
//
$PHORUM["post_fields"] = array(
    "message_id"     => array("integer",  true,   true,  0),
    "user_id"        => array("integer",  true,   true,  0),
    "datestamp"      => array("string",   true,   true,  ''),
    "status"         => array("integer",  false,  true,  0),
    "author"         => array("string",   false,  true,  ''),
    "email"          => array("string",   false,  true,  ''),
    "subject"        => array("string",   false,  false, ''),
    "body"           => array("string",   false,  false, ''),
    "forum_id"       => array("integer",  true,   true,  $PHORUM["forum_id"]),
    "thread"         => array("integer",  true,   true,  0),
    "parent_id"      => array("integer",  true,   true,  0),
    "allow_reply"    => array("boolean",  false,  true,  1),
    "special"        => array("string",   false,  true,  ''),
    "email_notify"   => array("boolean",  false,  false, 0),
    "show_signature" => array("boolean",  false,  false, 0),
    "attachments"    => array("array",    true,   true,  array()),
    "meta"           => array("array",    true,   true,  array()),
    "thread_count"   => array("integer",  true,   true,  0),
    "mode"           => array("string",   true,   true,  ''),
);

// Indices for referencing the fields in $post_fields.
define("pf_TYPE",     0);
define("pf_HIDDEN",   1);
define("pf_READONLY", 2);
define("pf_INIT",     3);

// Definitions for a clear $apply_readonly parameter in
// the function phorum_posting_merge_db2form().
define("ALLFIELDS", false);
define("READONLYFIELDS", true);

// ----------------------------------------------------------------------
// Gather information about the editor state and start processing
// ----------------------------------------------------------------------

// Is this an initial request?
$initial = ! isset($_POST["message_id"]);

// Is finish, cancel of preview clicked?
$finish  = (! $initial && isset($_POST["finish"]));
$cancel  = (! $initial && isset($_POST["cancel"]));
$preview = (! $initial && isset($_POST["preview"]));

// Do we already have postingargs or do we use the global args?
if (! isset($PHORUM["postingargs"])) {
    $PHORUM["postingargs"] = $PHORUM["args"];
}

// Find out what editing mode we're running in.
if ($initial) {
    $mode = isset($PHORUM["postingargs"][1]) ? $PHORUM["postingargs"][1] : "post";

    // Quote may also be passed as a phorum parameter (quote=1).
    if ($mode == "reply" && isset($PHORUM["postingargs"]["quote"]) && $PHORUM["postingargs"]["quote"]) {
        $mode = "quote";
    }

} else {
    if (! isset($_POST["mode"])) {
        die("Missing parameter \"mode\" in request");
    }
    $mode = $_POST["mode"];
}
if (! in_array($mode, $valid_modes)) {
    die("Illegal mode issued: $mode");
}

// Find out if we are detaching an attachment.
// If we are, $do_detach will be set to the attachment's file_id.
$do_detach = false;
foreach ($_POST as $var => $val) {
    if (substr($var, 0, 7) == "detach:") {
        $do_detach = substr($var, 7);
    }
}

// Check if the user uploads an attachment.
$do_attach = false;
if (count($_FILES)) {
    foreach ($_FILES as $name => $data) {
        if ($data["size"]) {
            $do_attach = true;
            break;
        }
    }
    reset($_FILES);
}

// In case users click on post or preview, without uploading
// their attachment first, we fake an upload action.
if (count($_FILES)) {
    list($name, $data) = each($_FILES);
    if ($data["size"]) $do_attach = true;
    reset($_FILES);
}

// Set all our URL's
phorum_build_common_urls();
$PHORUM["DATA"]["URL"]["ACTION"] = phorum_get_url(PHORUM_POSTING_URL);

// Keep track of errors.
$error_flag = false;
$PHORUM["DATA"]["MESSAGE"] = null;
$PHORUM["DATA"]["ERROR"] = null;

// Do things that are specific for first time or followup requests.
if ($initial) {
    include("./include/posting/request_first.php");
} else {
    include("./include/posting/request_followup.php");
}

// Store the posting mode in the form parameters, so we can remember
// the mode throughout the editing cycle (for example to be able to
// create page titles which match the editing mode).
$PHORUM["DATA"]["MODE"] = $mode;

// ----------------------------------------------------------------------
// Permission and ability handling
// ----------------------------------------------------------------------

// Make a descision on what posting mode we're really handling, based on
// the data that we have. The posting modes "reply" and "quote" will
// both be called "reply" from here. Modes "edit" and "moderation" will
// be called "edit" from here. The exact editor behaviour for editing is
// based on the user's permissions, not on posting mode.
$mode = "post";
if ($message["message_id"]) {
    $mode = "edit";
} elseif ($message["parent_id"]) {
    $mode = "reply";
}

// Do ban list checks. Only check the bans on entering and
// on finishing up. No checking is needed on intermediate requests.
if (! $error_flag && ($initial || $finish || $preview)) {
    include("./include/posting/check_banlist.php");
}

// Determine the abilities that the current user has.
if (! $error_flag)
{
    // Is the forum running in a moderated state?
    $PHORUM["DATA"]["MODERATED"] =
        $PHORUM["moderation"] == PHORUM_MODERATE_ON &&
        !phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES);

    // Does the user have administrator permissions?
    $PHORUM["DATA"]["ADMINISTRATOR"] = $PHORUM["user"]["admin"];

    // Does the user have moderator permissions?
    $PHORUM["DATA"]["MODERATOR"] =
        phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES);

    // Ability: Do we allow attachments?
    $PHORUM["DATA"]["ATTACHMENTS"] = $PHORUM["max_attachments"] > 0 && phorum_user_access_allowed(PHORUM_USER_ALLOW_ATTACH);

    $PHORUM["DATA"]["EMAILNOTIFY"] =
    (isset($PHORUM['allow_email_notify']) && !empty($PHORUM['allow_email_notify']))? 1 : 0;

    // What special options can this user set for a message?
    $PHORUM["DATA"]["OPTION_ALLOWED"] = array(
        "sticky"        => false,   // Sticky flag for message sorting
        "announcement"  => false,   // Announcement flag for message sorting
        "allow_reply"   => false,   // Wheter replies are allowed in the thread
    );
    // For moderators and administrators.
    if (($PHORUM["DATA"]["MODERATOR"] || $PHORUM["DATA"]["ADMINISTRATOR"]) && $message["parent_id"] == 0) {
        $PHORUM["DATA"]["OPTION_ALLOWED"]["sticky"] = true;
        $PHORUM["DATA"]["OPTION_ALLOWED"]["allow_reply"] = true;
    }
    // For administrators only.
    if ($PHORUM["DATA"]["ADMINISTRATOR"]) {
        $PHORUM["DATA"]["OPTION_ALLOWED"]["announcement"] = true;
    }
}

if (! $error_flag)
{
    // A hook to allow modules to change the abilities from above.
    phorum_hook("posting_permission");

    // Show special sort options in the editor? These only are
    // honoured for the thread starter messages, so we check the
    // parent_id for that.
    $PHORUM["DATA"]["SHOW_SPECIALOPTIONS"] =
        $message["parent_id"] == 0 &&
        ($PHORUM["DATA"]["OPTION_ALLOWED"]["announcement"] ||
         $PHORUM["DATA"]["OPTION_ALLOWED"]["sticky"]);

    // Show special sort options or allow_reply in the editor?
    $PHORUM["DATA"]["SHOW_THREADOPTIONS"] =
        $PHORUM["DATA"]["SHOW_SPECIALOPTIONS"] ||
        $PHORUM["DATA"]["OPTION_ALLOWED"]["allow_reply"];
}

// Set extra writeable fields, based on the user's abilities.
if (isset($PHORUM["DATA"]["ATTACHMENTS"]) && $PHORUM["DATA"]["ATTACHMENTS"]) {
    // Keep it as a hidden field.
    $PHORUM["post_fields"]["attachments"][pf_READONLY] = false;
}
if (isset($PHORUM["DATA"]["MODERATOR"]) && $PHORUM["DATA"]["MODERATOR"]) {
    if (! $message["user_id"]) {
        $PHORUM["post_fields"]["author"][pf_READONLY] = false;
        $PHORUM["post_fields"]["email"][pf_READONLY] = false;
    }
}
if (isset($PHORUM["DATA"]["SHOW_SPECIALOPTIONS"]) && $PHORUM["DATA"]["SHOW_SPECIALOPTIONS"]) {
    $PHORUM["post_fields"]["special"][pf_READONLY] = false;
}
if (isset($PHORUM["DATA"]["OPTION_ALLOWED"]["allow_reply"]) && $PHORUM["DATA"]["OPTION_ALLOWED"]["allow_reply"]) {
    $PHORUM["post_fields"]["allow_reply"][pf_READONLY] = false;
}

// Check permissions and apply read-only data.
// Only do this on entering and on finishing up.
// No checking is needed on intermediate requests.
if (! $error_flag && ($initial || $finish)) {
    include("./include/posting/check_permissions.php");
}

// Do permission checks for attachment management.
if (! $error_flag && ($do_attach || $do_detach)) {
    if (! $PHORUM["DATA"]["ATTACHMENTS"]) {
        $PHORUM["DATA"]["MESSAGE"] =
        $PHORUM["DATA"]["LANG"]["AttachNotAllowed"];
        $error_flag = true;
    }
}

// ----------------------------------------------------------------------
// Perform actions
// ----------------------------------------------------------------------

// Only check the integrity of the data on finishing up. During the
// editing process, the user may produce garbage as much as he likes.
if (! $error_flag && $finish) {
    include("./include/posting/check_integrity.php");
}

// Handle cancel request.
if (! $error_flag && $cancel) {
    include("./include/posting/action_cancel.php");
}

// Count the number and total size of active attachments
// that we currently have.
$attach_count = 0;
$attach_totalsize = 0;
foreach ($message["attachments"] as $attachment) {
    if ($attachment["keep"]) {
        $attach_count ++;
        $attach_totalsize += $attachment["size"];
    }
}

// Attachment management. This will update the
// $attach_count and $attach_totalsize variables.
if (! $error_flag && ($do_attach || $do_detach)) {
    include("./include/posting/action_attachments.php");
}

// Handle finishing actions.
if (! $error_flag && $finish)
{
    // Posting mode
    if ($mode == "post" || $mode == "reply") {
        include("./include/posting/action_post.php");
    }
    // Editing mode.
    elseif ($mode == "edit") {
        include("./include/posting/action_edit.php");
    }
    // A little safety net.
    else {
        die("Internal error: finish action for \"$mode\" not available");
    }
}

// ----------------------------------------------------------------------
// Display the page
// ----------------------------------------------------------------------

// Make up the text which must be used on the posting form's submit button.
$button_txtid = $mode == "edit" ? "SaveChanges" : "Post";
$message["submitbutton_text"] = $PHORUM["DATA"]["LANG"][$button_txtid];

// Attachment config
if($PHORUM["max_attachments"]){

    $php_limit = ini_get('upload_max_filesize')*1024;
    $max_packetsize = phorum_db_maxpacketsize();
    if ($max_packetsize == NULL) {
        $db_limit = $php_limit;
    } else {
        $db_limit = $max_packetsize/1024*.6;
    }
    if($PHORUM["max_attachment_size"]==0) $PHORUM["max_attachment_size"]=$php_limit;
    $PHORUM["max_attachment_size"] = min($PHORUM["max_attachment_size"], $php_limit, $db_limit);
    if ($PHORUM["max_totalattachment_size"]) {
        if ($PHORUM["max_totalattachment_size"] < $PHORUM["max_attachment_size"]) {
            $PHORUM["max_attachment_size"] = $PHORUM["max_totalattachment_size"];
        }
    }

    // Data for attachment explanation.
    if ($PHORUM["allow_attachment_types"]) {
        $PHORUM["DATA"]["ATTACH_FILE_TYPES"] = str_replace(";", ", ", $PHORUM["allow_attachment_types"]);
        $PHORUM["DATA"]["EXPLAIN_ATTACH_FILE_TYPES"] = str_replace("%types%", $PHORUM["DATA"]["ATTACH_FILE_TYPES"], $PHORUM["DATA"]["LANG"]["AttachFileTypes"]);
    }
    if ($PHORUM["max_attachment_size"]) {
        $PHORUM["DATA"]["ATTACH_FILE_SIZE"] = $PHORUM["max_attachment_size"];
        $PHORUM["DATA"]["ATTACH_FORMATTED_FILE_SIZE"] = phorum_filesize($PHORUM["max_attachment_size"] * 1024);
        $PHORUM["DATA"]["EXPLAIN_ATTACH_FILE_SIZE"] = str_replace("%size%", $PHORUM["DATA"]["ATTACH_FORMATTED_FILE_SIZE"], $PHORUM["DATA"]["LANG"]["AttachFileSize"]);
    }
    if ($PHORUM["max_totalattachment_size"] && $PHORUM["max_attachments"]>1) {
        $PHORUM["DATA"]["ATTACH_TOTALFILE_SIZE"] = $PHORUM["max_totalattachment_size"];
        $PHORUM["DATA"]["ATTACH_FORMATTED_TOTALFILE_SIZE"] = phorum_filesize($PHORUM["max_totalattachment_size"] * 1024);
        $PHORUM["DATA"]["EXPLAIN_ATTACH_TOTALFILE_SIZE"] = str_replace("%size%", $PHORUM["DATA"]["ATTACH_FORMATTED_TOTALFILE_SIZE"], $PHORUM["DATA"]["LANG"]["AttachTotalFileSize"]);
    }
    if ($PHORUM["max_attachments"] && $PHORUM["max_attachments"]>1) {
        $PHORUM["DATA"]["ATTACH_MAX_ATTACHMENTS"] = $PHORUM["max_attachments"];
        $PHORUM["DATA"]["ATTACH_REMAINING_ATTACHMENTS"] = $PHORUM["max_attachments"] - $attach_count;
        $PHORUM["DATA"]["EXPLAIN_ATTACH_MAX_ATTACHMENTS"] = str_replace("%count%", $PHORUM["DATA"]["ATTACH_REMAINING_ATTACHMENTS"], $PHORUM["DATA"]["LANG"]["AttachMaxAttachments"]);
    }

    // A flag for the template building to be able to see if the
    // attachment storage space is full.
    $PHORUM["DATA"]["ATTACHMENTS_FULL"] =
        $attach_count >= $PHORUM["max_attachments"] ||
        ($PHORUM["max_totalattachment_size"] &&
        $attach_totalsize >= $PHORUM["max_totalattachment_size"]*1024);
}

// Let the templates know if we're running as an include.
$PHORUM["DATA"]["EDITOR_AS_INCLUDE"] =
    isset($PHORUM["postingargs"]["as_include"]) && $PHORUM["postingargs"]["as_include"];

// Process data for previewing.
if ($preview) {
    include("./include/posting/action_preview.php");
}

// Always put the current mode in the message, so hook
// writers can use this for identifying what we're doing.
$message["mode"] = $mode;

// Create hidden form field code. Fields which are read-only are
// all added as a hidden form fields in the form. Also the fields
// for which the pf_HIDDEN flag is set will be added to the
// hidden fields.
$hidden = "";
foreach ($PHORUM["post_fields"] as $var => $spec)
{
    if ($var == "mode") {
        $val = $mode;
    } elseif ($spec[pf_TYPE] == "array") {
        $val = htmlspecialchars(serialize($message[$var]));
    } else {
        $val = htmlentities($message[$var], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]);
    }
    if ($spec[pf_READONLY] || $spec[pf_HIDDEN]) {
        $hidden .= '<input type="hidden" name="' . $var .  '" ' .
                   'value="' . $val . "\" />\n";
    }
}
$PHORUM["DATA"]["POST_VARS"] .= $hidden;

// Process data for XSS prevention.
foreach ($message as $var => $val)
{
    // The meta information should not be used in templates, because
    // nothing is escaped here. But we might want to use the data in
    // mods which are run after this code. We continue here, so the
    // data won't be stripped from the message data later on.
    if ($var == "meta") continue;

    if ($var == "attachments") {
        if (is_array($val)) {
            foreach ($val as $nr => $data)
            {
                // Do not show attachments which are not kept.
                if (! $data["keep"]) {
                    unset($message["attachments"][$nr]);
                    continue;
                }

                $message[$var][$nr]["name"] = htmlspecialchars($data["name"]);
                $message[$var][$nr]["size"] = phorum_filesize(round($data["size"]));
            }
        }
    } else {
        if (is_scalar($val)) {
            $message[$var] = htmlspecialchars($val);
        } else {
            // Not used in the template, unless proven otherwise.
            $message[$var] = '[removed from template data]';
        }
    }
}

// A cancel button is not needed if the editor is included in a page.
// This can also be used by the before_editor hook to disable the
// cancel button in all pages.
$PHORUM["DATA"]["SHOW_CANCEL_BUTTON"] = (isset($PHORUM["postingargs"]["as_include"]) ? false : true);

// A hook to give modules a last chance to update the message data.
$message = phorum_hook("before_editor", $message);

// Make the message data available to the template engine.
$PHORUM["DATA"]["POST"] = $message;

// Set the field to focus.
$focus = "phorum_subject";
if (!empty($message["subject"])) $focus = "phorum_textarea";
$PHORUM["DATA"]["FOCUS_TO_ID"] = $focus;

// Load page header.
if (! isset($PHORUM["postingargs"]["as_include"])) {
    include phorum_get_template("header");
    phorum_hook("after_header");
}

// Load page content.
if (isset($PHORUM["DATA"]["MESSAGE"])) {
    include phorum_get_template("message");
} else {
    include phorum_get_template("posting");
}

// Load page footer.
if (! isset($PHORUM["postingargs"]["as_include"])) {
    phorum_hook("before_footer");
    include phorum_get_template("footer");
}

// ----------------------------------------------------------------------
// Functions
// ----------------------------------------------------------------------

// Merge data from a database message record into the form fields
// that we use. If $apply_readonly is set to a true value, then
// only the fields which are flagged as read-only will be copied.
function phorum_posting_merge_db2form($form, $db, $apply_readonly = false)
{
    $PHORUM = $GLOBALS['PHORUM'];

    // If we have a user linked to the current message, then get the
    // user data from the database, if it has to be applied as
    // read-only data.
    if ($PHORUM["post_fields"]["email"][pf_READONLY] || $PHORUM["post_fields"]["author"][pf_READONLY]) {
        if ($db["user_id"]) {
            $user_info = phorum_user_get($db["user_id"], false);
            $user_info["author"] = $user_info["username"];
        }
    }

    foreach ($PHORUM["post_fields"] as $key => $info)
    {
        // Skip writeable fields if we only have to apply read-only ones.
        if ($apply_readonly && ! $info[pf_READONLY]) continue;

        switch ($key) {
            case "show_signature": {
                $form[$key] = !empty($db["meta"]["show_signature"]);
                break;
            }

            case "allow_reply": {
                $form[$key] = ! $db["closed"];
                break;
            }

            case "email_notify": {
                $form[$key] = phorum_db_get_if_subscribed(
                    $db["forum_id"], $db["thread"], $db["user_id"]);
                break;
            }

            case "forum_id": {
                $form["forum_id"] = $db["forum_id"] ? $db["forum_id"] : $PHORUM["forum_id"];
                break;
            }

            case "attachments": {
                $form[$key] = array();
                if (isset($db["meta"]["attachments"])) {
                    foreach ($db["meta"]["attachments"] as $data) {
                        $data["keep"] = true;
                        $data["linked"] = true;
                        $form["attachments"][] = $data;
                    }
                }
                break;
            }

            case "author":
            case "email": {
                if ($db["user_id"]) {
                    $form[$key] = $user_info[$key];
                } else {
                    $form[$key] = $db[$key];
                }
                break;
            }

            case "special": {
                if ($db["sort"] == PHORUM_SORT_ANNOUNCEMENT) {
                    $form["special"] = "announcement";
                } elseif ($db["sort"] == PHORUM_SORT_STICKY) {
                    $form["special"] = "sticky";
                } else {
                    $form["special"] = "";
                }
                break;
            }

            case "mode": {
                // NOOP
                break;
            }

            default:
                $form[$key] = $db[$key];
        }
    }
    return $form;
}

?>
