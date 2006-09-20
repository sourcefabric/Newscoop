<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
// Copyright (C) 2006  Phorum Development Team                                //
// http://www.phorum.org                                                      //
//                                                                            //
// This program is free software. You can redistribute it and/or modify       //
// it under the terms of either the current Phorum License (viewable at       //
// phorum.org) or the Phorum License that was distributed with this file      //
//                                                                            //
// This program is distributed in the hope that it will be useful,            //
// but WITHOUT ANY WARRANTY, without even the implied warranty of             //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                       //
//                                                                            //
// You should have received a copy of the Phorum License                      //
// along with this program.                                                   //
////////////////////////////////////////////////////////////////////////////////
define('phorum_page','control');

include_once("./common.php");

phorum_require_login();

include_once("./include/email_functions.php");
include_once("./include/format_functions.php");

define("PHORUM_CONTROL_CENTER", 1);

// A user has to be logged in to use his control-center.
if (!$PHORUM["DATA"]["LOGGEDIN"]) {
    phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
    exit();
}

// If the user is not fully logged in, send him to the login page.
if(!$PHORUM["DATA"]["FULLY_LOGGEDIN"]){
    phorum_redirect_by_url(phorum_get_url(PHORUM_LOGIN_URL, "redir=".PHORUM_CONTROLCENTER_URL));
    exit();
}

$error_msg = false;

// Generating the panel id of the page to use.
$panel = (!isset($PHORUM['args']['panel']) || empty($PHORUM["args"]['panel']))
       ? PHORUM_CC_SUMMARY : $PHORUM["args"]['panel'];

// Sometimes we set the panel id from a post-form.
if (isset($_POST['panel'])) {
    $panel = $_POST['panel'];
}

// Set all our URLs.
phorum_build_common_urls();

// Generate the control panel URLs.
$PHORUM['DATA']['URL']['CC0'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_SUMMARY);
$PHORUM['DATA']['URL']['CC1'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_SUBSCRIPTION_THREADS);
$PHORUM['DATA']['URL']['CC2'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_SUBSCRIPTION_FORUMS);
$PHORUM['DATA']['URL']['CC3'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_USERINFO);
$PHORUM['DATA']['URL']['CC4'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_SIGNATURE);
$PHORUM['DATA']['URL']['CC5'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_MAIL);
$PHORUM['DATA']['URL']['CC6'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_BOARD);
$PHORUM['DATA']['URL']['CC7'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_PASSWORD);
$PHORUM['DATA']['URL']['CC8'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_UNAPPROVED);
$PHORUM['DATA']['URL']['CC9'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_FILES);
$PHORUM['DATA']['URL']['CC10'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_USERS);
$PHORUM['DATA']['URL']['CC14'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_PRIVACY);
$PHORUM['DATA']['URL']['CC15'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_GROUP_MODERATION);
$PHORUM['DATA']['URL']['CC16'] = phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_GROUP_MEMBERSHIP);

// Determine if the user files functionality is available.
$PHORUM["DATA"]["MYFILES"] = ($PHORUM["file_uploads"] || $PHORUM["user"]["admin"]);

// Determine if the user is a moderator.
$PHORUM["DATA"]["MESSAGE_MODERATOR"] = (count(phorum_user_access_list(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) > 0);
$PHORUM["DATA"]["USER_MODERATOR"] = phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_USERS);
$PHORUM["DATA"]["GROUP_MODERATOR"] = phorum_user_allow_moderate_group();
$PHORUM["DATA"]["MODERATOR"] = ($PHORUM["DATA"]["USER_MODERATOR"] + $PHORUM["DATA"]["MESSAGE_MODERATOR"] + $PHORUM["DATA"]["GROUP_MODERATOR"]) > 0;

// The form action for the common form.
$PHORUM["DATA"]["URL"]["ACTION"] = phorum_get_url(PHORUM_CONTROLCENTER_ACTION_URL);

$user = $PHORUM['user'];

// Security messures.
unset($user["password"]);
unset($user["password_temp"]);
unset($user["permissions"]);

// Format the user signature using standard message body formatting
// or  HTML escape it
$user["signature"] = htmlspecialchars($user["signature"]);

// Fake a message here so we can run the sig through format_message.
$fake_messages = array(array("author"=>"", "email"=>"", "subject"=>"", "body"=>$user["signature"]));
$fake_messages = phorum_format_messages( $fake_messages );
$user["signature_formatted"] = $fake_messages[0]["body"];

// Initialize any custom profile fields that are not present.
if (!empty($PHORUM["PROFILE_FIELDS"])) {
    foreach($PHORUM["PROFILE_FIELDS"] as $field) {
        if (!isset($user[$field['name']])) $user[$field['name']] = "";
    }
}

// Setup template data.
$PHORUM["DATA"]["PROFILE"] = $user;
$PHORUM["DATA"]["PROFILE"]["forum_id"] = isset($PHORUM["forum_id"]) ? $PHORUM['forum_id'] : 0;
$PHORUM["DATA"]["PROFILE"]["PANEL"] = $panel;

// Set the back-URL and -message.
if ($PHORUM['forum_id'] > 0 && $PHORUM['folder_flag'] == 0) {
    $PHORUM['DATA']['URL']['BACK'] = phorum_get_url(PHORUM_LIST_URL);
    $PHORUM['DATA']['URL']['BACKTITLE'] = $PHORUM['DATA']['LANG']['BacktoForum'];
} else {
    if(isset($PHORUM['forum_id'])) {
        $PHORUM['DATA']['URL']['BACK'] = phorum_get_url(PHORUM_INDEX_URL,$PHORUM['forum_id']);
    } else {
        $PHORUM['DATA']['URL']['BACK'] = phorum_get_url(PHORUM_INDEX_URL);
    }
    $PHORUM['DATA']['URL']['BACKTITLE'] = $PHORUM['DATA']['LANG']['BackToForumList'];
}

// Load the include file for the current panel.
$panel = basename($panel);
if (file_exists("./include/controlcenter/$panel.php")) {
    include "./include/controlcenter/$panel.php";
} else {
    include "./include/controlcenter/summary.php";
}

// The include file can set the template we have to use for
// displaying the main part of the control panel screen
// in the $template variable.
if (isset($template)) {
    $PHORUM['DATA']['content_template'] = $template;
}

// The include file can also set an error message to show
// in the $error variable and a success message in $okmsg.
if (isset($error) && !empty($error)) $PHORUM['DATA']['ERROR'] = $error;
if (isset($okmsg) && !empty($okmsg)) $PHORUM['DATA']['OKMSG'] = $okmsg;

// Display the control panel page.
include phorum_get_template("header");
phorum_hook("after_header");
if ($error_msg) { // Possibly set from the panel include file.
    include phorum_get_template("message");
} else {
    include phorum_get_template("cc_index");
}
phorum_hook("before_footer");
include phorum_get_template("footer");

// ============================================================================

/**
 * A common function which is used to save the userdata from the post-data.
 * @param panel - The panel for which to save data.
 * @return array - An array containing $error and $okmsg.
 */
function phorum_controlcenter_user_save($panel)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $error = "";
    $okmsg = "";

    // Setup the default userdata fields that may be changed
    // from the control panel interface.
    $userdata = array(
        'signature'       => NULL,
        'hide_email'      => NULL,
        'hide_activity'   => NULL,
        'password'        => NULL,
        'tz_offset'       => NULL,
        'is_dst'          => NULL,
        'user_language'   => NULL,
        'threaded_list'   => NULL,
        'threaded_read'   => NULL,
        'email_notify'    => NULL,
        'show_signature'  => NULL,
        'pm_email_notify' => NULL,
        'email'           => NULL,
        'email_temp'      => NULL,
        'user_template'   => NULL,
        'moderation_email'=> NULL,
    );
    // Add custom profile fields as acceptable fields.
    foreach ($PHORUM["PROFILE_FIELDS"] as $field) {
        $userdata[$field["name"]] = NULL;
    }
    // Update userdata with $_POST information.
    foreach ($_POST as $key => $val) {
       if (array_key_exists($key, $userdata)) {
           $userdata[$key] = $val;
       }
    }
    // Remove unused profile fields.
    foreach ($userdata as $key => $val) {
        if (is_null($val)) {
            unset($userdata[$key]);
        }
    }

    // Set static userdata.
    $userdata["user_id"] = $PHORUM["user"]["user_id"];
    $userdata["fk_campsite_user_id"] = $PHORUM["user"]["fk_campsite_user_id"];

    // Run a hook, so module writers can update and check the userdata.
    $userdata = phorum_hook("cc_save_user", $userdata);

    // Set $error, in case the before_register hook did set an error.
    if (isset($userdata['error'])) {
        $error=$userdata['error'];
        unset($userdata['error']);
    // Try to update the userdata in the database.
    } elseif (!phorum_user_save($userdata)) {
        // Updating the user failed.
        $error = $PHORUM["DATA"]["LANG"]["ErrUserAddUpdate"];
    } else {
	// Sync the campsite user
	require_once('../../classes/Language.php');
	require_once('../../classes/User.php');
	$campsiteUser = new User($userdata["fk_campsite_user_id"]);
	if ($campsiteUser->exists()) {
		if (array_key_exists('password', $userdata)) {
			$campsiteUser->setPassword($userdata["password"]);
		} elseif (array_key_exists('email', $userdata)) {
			$campsiteUser->setProperty('EMail', $userdata["email"]);
		}
	}

        // Updating the user was successful.
        $okmsg = $PHORUM["DATA"]["LANG"]["ProfileUpdatedOk"];

        // Let the userdata be reloaded.
        phorum_user_set_current_user($userdata["user_id"]);

        // If a new password was set, let's create a new session.
        if (isset($userdata["password"]) && !empty($userdata["password"])) {
            phorum_user_create_session();
        }

        // Copy data from the updated user back into the template data.
        // Leave PANEL and forum_id alone (these are injected into the
        // userdata in the template from this script).
        foreach ($GLOBALS["PHORUM"]["DATA"]["PROFILE"] as $key => $val) {
            if ($key == "PANEL" || $key == "forum_id") continue;
            if (isset($GLOBALS["PHORUM"]["user"][$key])) {
                $GLOBALS["PHORUM"]["DATA"]["PROFILE"][$key] = $GLOBALS["PHORUM"]["user"][$key];
            } else {
                $GLOBALS["PHORUM"]["DATA"]["PROFILE"][$key] = "";
            }
        }
    }

    return array($error, $okmsg);
}

?>
