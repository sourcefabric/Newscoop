<?php

if(!defined("PHORUM")) return;

require_once("./mods/smileys/defaults.php");

function phorum_mod_smileys_after_header()
{
    $PHORUM = $GLOBALS["PHORUM"];

    // Return immediately if we have no active smiley replacements.
    if (!isset($PHORUM["mod_smileys"])||!$PHORUM["mod_smileys"]["do_smileys"]){
        return $data;
    } ?>

    <style type="text/css">
    .mod_smileys_img {
        vertical-align: bottom;
        margin: 0px 3px 0px 3px;
    }
    </style> <?php
}

function phorum_mod_smileys_format($data)
{
    $PHORUM = $GLOBALS["PHORUM"];

    // Return immediately if we have no active smiley replacements.
    if (!isset($PHORUM["mod_smileys"])||!$PHORUM["mod_smileys"]["do_smileys"]){
        return $data;
    }

    // Run smiley replacements.
    $replace = $PHORUM["mod_smileys"]["replacements"];
	foreach ($data as $key => $message)
    {
        // Do subject replacements.
        if (isset($replace["subject"]) && isset($message["subject"])) {
            $data[$key]['subject'] = str_replace ($replace["subject"][0] , $replace["subject"][1], $message['subject'] );
        }
        // Do body replacements.
        if (isset($replace["body"]) && isset($message["body"])) {
            $data[$key]['body'] = str_replace ($replace["body"][0] , $replace["body"][1], $message['body'] );
        }
	}

	return $data;
}

?>
