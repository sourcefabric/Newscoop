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
define('phorum_page','script');
define('PHORUM_SCRIPT', 1);

include_once("./common.php");

// if we are running in the webserver, bail out
if (isset($_SERVER["REMOTE_ADDR"])) {
    echo $PHORUM["DATA"]["LANG"]["CannotBeRunFromBrowser"];
    return;
}

if (! isset($_SERVER["argv"][1])) {
    phorum_script_usage();
}

// figure out what module we are trying to run
if (strpos($_SERVER["argv"][1], "--module=") === 0) {
    $module = substr(strstr($_SERVER["argv"][1], "="), 1);
    if (strlen($module) > 0) {
        $args = $_SERVER["argv"];
        unset($args[0]);
        $args[1] = $module;
        phorum_hook("external", $args);
    }
    else {
        phorum_script_usage();
    }
}
elseif ($argv[1] == "--scheduled") {
    phorum_hook("scheduled");
}
else {
    phorum_script_usage();
}

function phorum_script_usage() {
    $PHORUM=$GLOBALS["PHORUM"];
    echo $PHORUM["DATA"]["LANG"]["ScriptUsage"];
    exit(1);
}
?>
