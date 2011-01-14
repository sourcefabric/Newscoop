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

if(!defined("PHORUM_CONTROL_CENTER")) return;

if(count($_POST)) {

    // these two are flipped as we store if hidden in the db, but we ask if allowed in the UI
    $_POST["hide_email"] = (isset($_POST["hide_email"]) && $_POST["hide_email"]) ? 0 : 1;
    $_POST["hide_activity"] = (isset($_POST["hide_activity"]) && $_POST["hide_activity"]) ? 0 : 1;
    
    list($error,$okmsg) = phorum_controlcenter_user_save($panel);
}


// these two are flipped as we store if hidden in the db, but we ask if allowed in the UI

if (!empty($PHORUM['DATA']['PROFILE']["hide_email"])) {
    $PHORUM["DATA"]["PROFILE"]["hide_email_checked"] = "";
} else {
    // more html stuff in the code. yuck.
    $PHORUM["DATA"]["PROFILE"]["hide_email_checked"] = " checked=\"checked\"";
} 

if (!empty($PHORUM['DATA']['PROFILE']["hide_activity"])) {
    $PHORUM["DATA"]["PROFILE"]["hide_activity_checked"] = "";
} else {
    // more html stuff in the code. yuck.
    $PHORUM["DATA"]["PROFILE"]["hide_activity_checked"] = " checked=\"checked\"";
} 

$PHORUM["DATA"]["PROFILE"]["block_title"] = $PHORUM["DATA"]["LANG"]["EditPrivacy"];

$PHORUM['DATA']['PROFILE']['PRIVACYSETTINGS'] = 1;
$template = "cc_usersettings";
        
?>
