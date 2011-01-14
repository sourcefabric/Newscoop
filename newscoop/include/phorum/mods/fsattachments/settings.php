<?php

if(!defined("PHORUM_ADMIN")) return;

// save settings
if(count($_POST)){
    $PHORUM["fsattachments"]["path"]=$_POST["path"];

    if(!phorum_db_update_settings(array("fsattachments"=>$PHORUM["fsattachments"]))){
        $error="Database error while updating settings.";
    }
    else {
        echo "Settings Updated<br />";
    }
}

include_once "./include/admin/PhorumInputForm.php";

$frm = new PhorumInputForm ("", "post", "Save");

$frm->hidden("module", "modsettings");
$frm->hidden("mod", "fsattachments"); // this is the directory name that the Settings file lives in

if (!empty($error)){
    echo "$error<br />";
}

$frm->addbreak("Edit settings for the Filesystem Attachment Storage module");

$frm->addmessage("This is the directory where files will be stored on disk.  You need to enter a full path to the directory.  It is up to you to ensure that the web server daemon can write to this directory.");

$frm->addrow("Directory where files are kept: ", $frm->text_box("path", $PHORUM["fsattachments"]["path"], 50));

$frm->show();

?>