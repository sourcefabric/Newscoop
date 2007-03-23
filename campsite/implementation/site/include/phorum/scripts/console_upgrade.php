<?php

///////////////////////////////////////////////////////////////////////////////
//                                                                           //
// Copyright (C) 2006  Phorum Development Team                               //
// http://www.phorum.org                                                     //
//                                                                           //
// This program is free software. You can redistribute it and/or modify      //
// it under the terms of either the current Phorum License (viewable at      //
// phorum.org) or the Phorum License that was distributed with this file     //
//                                                                           //
// This program is distributed in the hope that it will be useful,           //
// but WITHOUT ANY WARRANTY, without even the implied warranty of            //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      //
//                                                                           //
// You should have received a copy of the Phorum License                     //
// along with this program.                                                  //
///////////////////////////////////////////////////////////////////////////////
define("phorum_page", "console_upgrade");

// I guess the phorum-directory is one level up. if you move the script to
// somewhere else you'll need to change that.
$PHORUM_DIRECTORY="../";

// change directory to the main-dir so we can use common.php
if(file_exists($PHORUM_DIRECTORY."/common.php")) {
    chdir($PHORUM_DIRECTORY);
} else {
    echo "Can't find common.php in the given directory. Please check the \$PHORUM_DIRECTORY -setting in console_upgrade.php\n";
    exit();
}

// include required files
include_once './common.php';
include_once './include/users.php';

// if we are running in the webserver, bail out
if (isset($_SERVER["REMOTE_ADDR"])) {
   echo $PHORUM["DATA"]["LANG"]["CannotBeRunFromBrowser"];
   return;
}

// no database connection?
if(!phorum_db_check_connection()){
    echo "A database connection could not be established.  Please edit include/db/config.php.\n";
    return;
} else {
    echo "Database connection confirmed, we will start the upgrade.\n";
    flush();
}

// no need for upgrade
if(isset($PHORUM['internal_version']) && $PHORUM['internal_version'] == PHORUMINTERNAL){
    echo "Your install is already up-to-date. No database-upgrade needed.\n";
    return;
}

if (! ini_get('safe_mode')) {
    echo "Trying to reset the timeout and rise the memory-limit ...\n";
    set_time_limit(0);
    ini_set("memory_limit","64M");
}

$fromversion=$PHORUM['internal_version'];

$upgradepath="./include/db/upgrade/{$PHORUM['DBCONFIG']['type']}/";

// read in all existing files
$dh=opendir($upgradepath);
$upgradefiles=array();
while ($file = readdir ($dh)) {
    if (substr($file,-4,4) == ".php") {
        $upgradefiles[]=$file;
    }
}
unset($file);
closedir($dh);

// sorting by number
sort($upgradefiles,SORT_NUMERIC);
reset($upgradefiles);

// advance to current version
while(list($key,$val)=each($upgradefiles)) {
    if($val == $fromversion.".php")
    break;
}


while(list($dump,$file) = each($upgradefiles)) {

    // extract the pure version, needed as internal version
    $pure_version = basename($file,".php");

    if(empty($pure_version)){
        die("Something is wrong with the upgrade script.  Please contact the Phorum Dev Team. ($fromversion,$pure_version)");
    }


    $upgradefile=$upgradepath.$file;

    if(file_exists($upgradefile)) {
        echo "Upgrading from db-version $fromversion to $pure_version ... \n";
        flush();

        if (! is_readable($upgradefile))
        die("$upgradefile is not readable. Make sure the file has got the neccessary permissions and try again.");


        $upgrade_queries=array();
        include($upgradefile);
        $err=phorum_db_run_queries($upgrade_queries);
        if($err){
            echo "an error occured: $err ... try to continue.\n";
        } else {
            echo "done.\n";
        }
        $GLOBALS["PHORUM"]["internal_version"]=$pure_version;
        phorum_db_update_settings(array("internal_version"=>$pure_version));
    } else {
        echo "Ooops, the upgradefile is missing. How could this happen?\n";
    }

    $fromversion=$pure_version;

}
?>
