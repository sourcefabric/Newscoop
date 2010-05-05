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

    if(!defined("PHORUM_ADMIN")) return;

    if(!phorum_db_check_connection()){
        echo "A database connection could not be established.  Please edit include/db/config.php.";
        return;
    }

    include_once "./include/admin/PhorumInputForm.php";

    if(empty($_POST["step"])){
        $step = 0;
    } else {
        $step = $_POST["step"];
    }

    if(isset($PHORUM['internal_version']) && $PHORUM['internal_version'] == PHORUMINTERNAL){
        $step = 2;
    }

    switch ($step){

        case 0:

            $frm = new PhorumInputForm ("", "post", "Continue ->");
            $frm->addbreak("Phorum Upgrade");
            $frm->addmessage("This wizard will upgrade Phorum on your server.  Phorum has already confirmed that it can connect to your database.  Press continue when you are ready.");
            $frm->hidden("module", "upgrade");
            $frm->hidden("step", "1");
            $frm->show();

            break;

        case 1:
            if (! ini_get('safe_mode')) {
                set_time_limit(0);
                ini_set("memory_limit","64M");
            }

            // ok upgrading tables
            $message = phorum_upgrade_tables($PHORUM['internal_version'],PHORUMINTERNAL);

            $frm = new PhorumInputForm ("", "post", "Continue ->");

            // done or not done? ;)
            $stepsleft = PHORUMINTERNAL - $PHORUM['internal_version'];
            $frm->addbreak("Upgrading tables (multiple steps possible) ....");
            $frm->addmessage($message);
            if($stepsleft > 0) {
                $newstep = 1;
            } else {
                $newstep = 2;
            }
            $frm->hidden("step", $newstep);
            $frm->hidden("module", "upgrade");
            $frm->show();

            break;

        case 2:
            echo "The upgrade is complete.  You may want to look through the <a href=\"$_SERVER[PHP_SELF]\">the admin</a> for any new features in this version.";

            break;

    }

?>
