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

    if(empty($PHORUM["http_path"])){
        $PHORUM["http_path"]=dirname($_SERVER["PHP_SELF"]);
    }


    // load the default Phorum language
    if(isset($PHORUM["default_language"])){
        include_once( "./include/lang/$PHORUM[default_language].php" );
    }

    // HTTP Content-Type header with the charset from the default language
    if (isset($PHORUM["DATA"]['CHARSET'])) {
        header("Content-Type: text/html; " .
               "charset=".htmlspecialchars($PHORUM["DATA"]['CHARSET']));
    }
?>
<html>
<head>
<title>Phorum Admin</title>
<?php

// meta data with the charset from the default language
if (isset($PHORUM["DATA"]['CHARSET'])) {
    echo "<meta content=\"text/html; charset=".$PHORUM["DATA"]["CHARSET"]."\" http-equiv=\"Content-Type\">\n";
}

?>
<style type="text/css">

body
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
}

input, textarea, select, td
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
    border-color: #EEEEEE;
}

.input-form-th
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
    padding: 3px;
    background-color: #DDDDEA;
}

.input-form-td
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
    padding: 3px;
    background-color: #EEEEFA;
}

.input-form-td-break, .PhorumAdminTitle
{
    font-family: "Trebuchet MS",Verdana, Arial, Helvetica, sans-serif;
    font-size: 16px;
    font-weight: bold;
    padding: 3px;
    background-color: Navy;
    color: White;
}

.input-form-td-message
{
    font-family: "Trebuchet MS",Verdana, Arial, Helvetica, sans-serif;
    font-size: 13px;
    padding: 10px;
    background-color: White;
    color: Black;
}

.PhorumAdminMenu
{
    width: 150px;
    border: 1px solid Navy;
    font-size: 13px;
    margin-bottom: 3px;
    line-height: 18px;
    padding: 3px;
}

.PhorumAdminMenuTitle
{
    width: 150px;
    border: 1px solid Navy;
    background-color: Navy;
    color:  white;
    font-size: 14px;
    font-weight: bold;
    padding: 3px;
}

.PhorumAdminTableRow
{
    background-color: #EEEEFA;
    color: Navy;
    padding: 3px;
    font-size: 13px;
}

.PhorumAdminTableRowAlt
{
    background-color: #d6d6e0;
    color: Navy;
    padding: 3px;
    font-size: 13px;
}

.PhorumAdminTableHead
{
    background-color: Navy;
    color: White;
    padding: 3px;
    font-weight: bold;
    font-size: 13px;
}

.PhorumInfoMessage
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
    padding: 3px;
    background-color: #EEEEFA;
    width: 300px;
    align: center;
    text-align: left;
}

.PhorumAdminError
{
    background-image: url("./images/alert.gif");
    background-position: 5px 5px;
    background-repeat: no-repeat;
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 15px;
    padding: 12px 12px 12px 50px;
    color: #000000;
    border: 2px solid red;
    margin-bottom: 3px;
}

.PhorumAdminOkMsg
{
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 15px;
    padding: 12px;
    color: #000000;
    border: 2px solid darkgreen;
    margin-bottom: 3px;
}

.small
{
    font-size: 10px;
}

.help-td, .help-td a
{
    color: White;
    padding-bottom: 2px;
    text-decoration: none;
}

#phorum-status
{
    vertical-align: middle;
}

#status-form
{
    display: inline;
}

img.question
{
    padding: 0 5px 1px 5px;
    vertical-align: middle;
}

#helpdiv
{
    position: absolute;
    display: none;
    width: 400px;
    border: 2px solid Navy;
}

#helpdiv-hide
{
    float: right;
}

#helpdiv-title
{
    color: White;
    background-color: Navy;
    padding: 1px 1px 3px 1px;
}

#helpdiv-content
{
    background-color: White;
    height: 200px;
    padding: 8px;
    font-family: Lucida Sans Unicode, Lucida Grand, Verdana, Arial, Helvetica;
    font-size: 13px;
    overflow: scroll;
}

#help-title
{
    font-weight: bold;
    margin-bottom: 3px;
}

</style>
<script>

function show_help(key)
{
    if (document.all) {
        topoffset=document.body.scrollTop;
        leftoffset=document.body.scrollLeft;
        WIDTH=document.body.clientWidth;
        HEIGHT=document.body.clientHeight;
    } else {
        topoffset=pageYOffset;
        leftoffset=pageXOffset;
        WIDTH=window.innerWidth;
        HEIGHT=window.innerHeight;
    }

    if(WIDTH%2!=0) WIDTH--;
    if(HEIGHT%2!=0) HEIGHT--;

    newtop=((HEIGHT-200)/2)+topoffset;

    // IE still puts selects on top of stuff so it has to be fixed to the left some
    if (document.all) {
        newleft=150;
    } else {
        newleft=((WIDTH-400)/2)+leftoffset;
    }

    document.getElementById('helpdiv').style.left=newleft;
    document.getElementById('helpdiv').style.top=newtop;

    document.getElementById('help-title').innerHTML = help[key][0];
    document.getElementById('help-text').innerHTML = help[key][1];

    document.getElementById('helpdiv').style.display = 'block';

}

function hide_help()
{
    document.getElementById('helpdiv').style.display = 'none';
    document.getElementById('help-title').innerHTML = "";
    document.getElementById('help-text').innerHTML = "";
}

</script>
</head>
<body>
<div id="helpdiv">
<div id="helpdiv-hide"><a href="javascript:hide_help();"><img border="0" src="images/close.gif" height="16" width="16" /></a></div>
<div id="helpdiv-title">&nbsp;Phorum Admin Help</div>
<div id="helpdiv-content">
<div id="help-title"></div>
<div id="help-text"></div>
</div>
</div>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
    <td style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: Navy;">Phorum Admin<small><br />version <?php echo PHORUM; ?></small></td>
<?php if(empty($module)){ // only show the versioncheck if you are on the front page of the admin ?>
    <td style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: Navy;" align="center" valign="middle">
      <iframe scrolling="no" frameborder="0" align="top" width="400" height="35" src="versioncheck.php"></iframe>
    </td>
<?php } else {
    // Reset the cookie that is used for the version check.
    setcookie("phorum_upgrade_available", '', time()-86400,
              $PHORUM["session_path"], $PHORUM["session_domain"]);
} ?>
    <td style="border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: Navy;" align="right">

    <div id="phorum-status">
<?php if($module!="login" && $module!="install" && $module!="upgrade"){ ?>
<form id="status-form" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">
<input type="hidden" name="module" value="status" />
Phorum Status:
<select name="status" onChange="this.form.submit();">
<option value="normal" <?php if($PHORUM["status"]=="normal") echo "selected"; ?>>Normal</option>
<option value="read-only"<?php if($PHORUM["status"]=="read-only") echo "selected"; ?>>Read Only</option>
<option value="admin-only"<?php if($PHORUM["status"]=="admin-only") echo "selected"; ?>>Admin Only</option>
<option value="disabled"<?php if($PHORUM["status"]=="disabled" || !phorum_db_check_connection()) echo "selected"; ?>>Disabled</option>
</select>
</form>
<?php } ?>
</div>
<?php if(isset($PHORUM['user'])) { ?>
<small>Logged In As <?php echo $PHORUM["user"]["username"]; ?></small>
<?php } ?>
</td>
</tr>
</table><br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<?php

    if($module!="login" && $module!="install" && $module!="upgrade"){
?>
<tr>
    <td valign="top">
<?php
        include_once "./include/admin/PhorumAdminMenu.php";

        $menu = new PhorumAdminMenu("Main Menu");

        $menu->add("Admin Home", "", "Takes you to the default Admin page.");
        $menu->add("Phorum Index", "index", "Takes you to the front page of the Phorum.");
        $menu->add("Log Out", "logout", "Logs you out of the admin.");

        $menu->show();

        $menu = new PhorumAdminMenu("Global Settings");

        $menu->add("General Settings", "settings", "Edit the global settings which affect the enter installation.");
        $menu->add("Ban Lists", "banlist", "Edits the list of banned names, email addresses and IP addresses.");
        $menu->add("Censor List", "badwords", "Edit the list of words that are censored in posts.");
        $menu->add("Modules", "mods", "Administer the Phorum Modules that are installed.");

        $menu->show();

        $menu = new PhorumAdminMenu("Forums");

        $menu->add("Manage Forums", "", "Takes you to the default Admin page.");
        $menu->add("Default Settings", "forum_defaults", "Allows you to set defaults settings that can be inherited by forums.");
        $menu->add("Create Forum", "newforum", "Creates a new area for your users to post messages.");
        $menu->add("Create Folder", "newfolder", "Creates a folder which can contain other folders of forums.");

        $menu->show();

        $menu = new PhorumAdminMenu("Users/Groups");

        $menu->add("Edit Users", "users", "Allows administrator to edit users including deactivating them.");
        $menu->add("Edit Groups", "groups", "Allows administrator to edit groups and their forum permissions.");
        $menu->add("Custom Profiles", "customprofile", "Allows administrator to add fields to Phorum profile.");

        $menu->show();
        $menu = new PhorumAdminMenu("Maintenance");

        $menu->add("Check For New Version", "version", "Check for new releases.");
        $menu->add("Prune Messages", "message_prune", "Pruning old messages.");
        $menu->add("Purge Stale Files", "file_purge", "Purging stale files from the database.");
        $menu->add("System Sanity Checks", "sanity_checks", "Perform a number of sanity checks on the system to identify possible problems.");
        $menu->add("Manage Language Files", "manage_languages", "Allows administrator to create new or updated versions of language files.");

        $menu->show();

?>
<img src="<?php echo "$PHORUM[http_path]/images/trans.gif"; ?>" alt="" border="0" width="150" height="1" />
    </td>
    <td valign="top"><img src="<?php echo "$PHORUM[http_path]/images/trans.gif"; ?>" alt="" border="0" width="15" height="15" /></td>
<?php
    }
?>
    <td valign="top" width="100%">
