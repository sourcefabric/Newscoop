<?php

if(!defined("PHORUM")) return;

require_once("./mods/smileys/defaults.php");

function phorum_mod_smileys_editor_tools()
{ 
    $PHORUM = $GLOBALS["PHORUM"];

    if ($PHORUM["mods"]["smileys"]) {
        include("./mods/editor_tools/smileys_panel.php");
    }
}

?>
