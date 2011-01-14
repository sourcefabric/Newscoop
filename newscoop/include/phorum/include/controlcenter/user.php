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
     list($error,$okmsg) = phorum_controlcenter_user_save($panel);
}

foreach($PHORUM["DATA"]["PROFILE"] as $key => $data) {
       if(!is_array($data)) {
            $PHORUM["DATA"]["PROFILE"][$key]=htmlspecialchars($data);
       }       
}

$PHORUM["DATA"]["PROFILE"]["block_title"] = $PHORUM["DATA"]["LANG"]["EditUserinfo"];
$PHORUM['DATA']['PROFILE']['USERPROFILE'] = 1;
$template = "cc_usersettings";
        
?>
