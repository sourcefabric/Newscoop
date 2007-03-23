<?php

// Make sure that this script is loaded from the admin interface.
if(!defined("PHORUM_ADMIN")) return;

// Save settings in case this script is run after posting
// the settings form.
if(count($_POST)) 
{
    // Create the settings array for this module.
    $PHORUM["mod_example_settings"] = array(
        "displaytext"  => $_POST["displaytext"],
        "displaycount" => $_POST["displaycount"],
    );

    // Force the displaycount to be an integer value. 
    settype($PHORUM["mod_example_settings"]["displaycount"], "int");

    if(! phorum_db_update_settings(array("mod_example_settings"=>$PHORUM["mod_example_settings"]))) {
        $error="Database error while updating settings.";
    } else {
        echo "Settings Updated<br />";
    }
}

// Apply default values for the settings.
if (!isset($PHORUM["mod_example_settings"]["displaytext"]))
    $PHORUM["mod_example_settings"]["displaytext"] = "";
if (!isset($PHORUM["mod_example_settings"]["displaycount"]))
    $PHORUM["mod_example_settings"]["displaycount"] = 1;

// We build the settings form by using the PhorumInputForm object. When
// creating your own settings screen, you'll only have to change the
// "mod" hidden parameter to the name of your own module.
include_once "./include/admin/PhorumInputForm.php";
$frm = new PhorumInputForm ("", "post", "Save");
$frm->hidden("module", "modsettings");
$frm->hidden("mod", "example_settings"); 

// Here we display an error in case one was set by saving 
// the settings before.
if (!empty($error)) {
    echo "$error<br />";
}

// This adds a break line to your form, with a description on it.
// You can use this to separate your form into multiple sections.
$frm->addbreak("Edit settings for the example_settings module");

// This adds a text message to your form. You can use this to 
// explain things to the user.
$frm->addmessage("This is the settings screen for the example_settings module. This module is only written for demonstrating the use of a settings screen for you own modules. The module itself will display a configurable text for a configurable number of times on screen.");

// This adds a row with a form field for entering the display text.
$frm->addrow("Text to display (default: Hello, world!)", $frm->text_box('displaytext', $PHORUM["mod_example_settings"]["displaytext"], 50));

// This adds another row with a form field for entering the display count.
$frm->addrow("Number of times to display", $frm->text_box('displaycount', $PHORUM["mod_example_settings"]["displaycount"], 5));

// We are done building the settings screen.
// By calling show(), the screen will be displayed.
$frm->show();

?>
