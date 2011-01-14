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
    if((isset($_POST["password"]) && !empty($_POST["password"]) && $_POST["password"] != $_POST["password2"]) || !isset($_POST["password"]) || empty($_POST['password'])) {
            $error = $PHORUM["DATA"]["LANG"]["ErrPassword"];
    } else {
            $_POST['password_temp']=$_POST['password'];
            list($error,$okmsg) = phorum_controlcenter_user_save($panel);
    }
}

$PHORUM["DATA"]["PROFILE"]["block_title"] = $PHORUM["DATA"]["LANG"]["ChangePassword"];
$PHORUM['DATA']['PROFILE']['CHANGEPASSWORD'] = 1;
$template = "cc_usersettings";
        
?>
