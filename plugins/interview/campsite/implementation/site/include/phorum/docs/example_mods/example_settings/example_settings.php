<?php

// This module demonstrates the use of settings for a Phorum module.
// This module will display a configurable string for a configurable
// number of times, right after the page header (how useful is that? :-).

if(!defined("PHORUM")) return;

function phorum_mod_example_settings_after_header () {
    $PHORUM = $GLOBALS["PHORUM"];

    // Apply default values for the settings.
    if (empty($PHORUM["mod_example_settings"]["displaytext"])) 
        $PHORUM["mod_example_settings"]["displaytext"] = "Hello, world!";
    if (!isset($PHORUM["mod_example_settings"]["displaycount"])) 
        $PHORUM["mod_example_settings"]["displaytext"] = 1;

    for ($i = 0; $i < $PHORUM["mod_example_settings"]["displaycount"]; $i++) {
        print $PHORUM["mod_example_settings"]["displaytext"] . " ";
    }
}

?>
