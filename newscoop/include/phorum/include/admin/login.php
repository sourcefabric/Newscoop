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

    // don't allow this page to be loaded directly
    if(!defined("PHORUM_ADMIN")) exit();

    if(isset($_POST["username"]) && isset($_POST["password"])){
        if(phorum_user_check_login($_POST["username"], $_POST["password"])!=0){
            if($PHORUM["user"]["admin"]){
                phorum_user_create_session(PHORUM_SESSION_ADMIN);
                if(!empty($_POST["target"])){
                    phorum_redirect_by_url($_POST['target']);
                } else {
                    phorum_redirect_by_url($_SERVER['PHP_SELF']);
                }
                exit();
            }
        }
    }

    include_once "./include/admin/PhorumInputForm.php";

    $frm = new PhorumInputForm ("", "post");

    if(count($_REQUEST)){

        $frm->hidden("target", $_SERVER["REQUEST_URI"]);
    }

    $frm->addrow("Username", $frm->text_box("username", "", 30));

    $frm->addrow("Password", $frm->text_box("password", "", 30, 0, true));

    $frm->show();

?>
