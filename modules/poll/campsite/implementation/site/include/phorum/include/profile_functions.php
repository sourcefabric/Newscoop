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

if(!defined("PHORUM")) return;


function phorum_gen_password($charpart=4, $numpart=3)
{
    $vowels = array("a", "e", "i", "o", "u");
    $cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");

    $num_vowels = count($vowels);
    $num_cons = count($cons);

    $password="";

    for($i = 0; $i < $charpart; $i++){
        $password .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
    }

    $password = substr($password, 0, $charpart);

    if($numpart){
        $max=(int)str_pad("", $numpart, "9");
        $min=(int)str_pad("1", $numpart, "0");

        $num=(string)mt_rand($min, $max);
    }

    return strtolower($password.$num);
}

// ----------------------------------------------------------------------------
// Banlist checking
// ----------------------------------------------------------------------------

/**
 * This function can perform multiple banlist checks at once and will
 * automatically generate an appropriate error message when a banlist
 * match is found.
 * @param bans - an array of bans to check. Each element in this array is an
 *               array itself with two elements: the value to check and the
 *               type of banlist to check against. One special case:
 *               if the type if PHORUM_BAD_IPS, the value may be NULL.
 *               In that case the IP/hostname of the client will be checked.
 * @return - An error message in case a banlist match was found or NULL
 *           if no match was found.
 */
function phorum_check_bans($bans)
{
    $PHORUM = $GLOBALS["PHORUM"];

    // A mapping from bantype -> error message to return on match.
    $phorum_bantype2error = array (
        PHORUM_BAD_NAMES      => "ErrBannedName",
        PHORUM_BAD_EMAILS     => "ErrBannedEmail",
        PHORUM_BAD_USERID     => "ErrBannedUser",
        PHORUM_BAD_IPS        => "ErrBannedIP",
        PHORUM_BAD_SPAM_WORDS => "ErrBannedContent",
    );

    // These language strings are set dynamically, so the language
    // tool won't recognize them automatically. Therefore they are
    // mentioned here.
    // $PHORUM["DATA"]["LANG"]["ErrBannedName"]
    // $PHORUM["DATA"]["LANG"]["ErrBannedEmail"]
    // $PHORUM["DATA"]["LANG"]["ErrBannedUser"]
    // $PHORUM["DATA"]["LANG"]["ErrBannedIP"]

    // Load the ban lists.
    if (! isset($GLOBALS["PHORUM"]["banlists"]))
        $GLOBALS["PHORUM"]["banlists"] = phorum_db_get_banlists();
    if(! isset($GLOBALS['PHORUM']['banlists'])) return NULL;

    // Run the checks.
    for (;;) {
        // An array for adding ban checks on the fly.
        $add_bans = array();

        foreach ($bans as $ban) {
            // Checking IP/hostname, but no value set? Then add the IP-address
            // and hostname (if DNS lookups are enabled) to the end of the checking
            // queue and continue with the next check.
            if ($ban[1] == PHORUM_BAD_IPS && $ban[0] == NULL) {
                $add_bans[] = array($_SERVER["REMOTE_ADDR"], PHORUM_BAD_IPS);
                if ($PHORUM["dns_lookup"]) {
                    $resolved = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
                    if (!empty($resolved) && $resolved != $_SERVER["REMOTE_ADDR"]) {
                        $add_bans[] = array($resolved, PHORUM_BAD_IPS);
                    }
                }
                continue;
            }

            // Do a single banlist check. Return an error if we find a match.
            if (! phorum_check_ban_lists($ban[0], $ban[1])) {
                $msg = $PHORUM["DATA"]["LANG"][$phorum_bantype2error[$ban[1]]];
                // Replace %name% with the blocked string.
                $msg = str_replace('%name%', htmlspecialchars($ban[0]), $msg);
                return $msg;
            }
        }

        // Bans added on the fly? Then restart the loop.
        if (count($add_bans) == 0) {
            break;
        } else {
            $bans = $add_bans;
        }
    }

    return NULL;
}

/**
 * Check a single banlist for a match.
 * @param value - The value to check.
 * @param type - The type of banlist to check the value against.
 * @return True if all is okay. False if a match has been found.
 */
function phorum_check_ban_lists($value, $type)
{
    // Load the ban lists.
    if (! isset($GLOBALS["PHORUM"]["banlists"]))
        $GLOBALS["PHORUM"]["banlists"] = phorum_db_get_banlists();
    if(! isset($GLOBALS['PHORUM']['banlists'])) return true;

    $banlists = $GLOBALS['PHORUM']['banlists'];

    $value = trim($value);

    if (!empty($value)) {
        if (isset($banlists[$type]) && is_array($banlists[$type])) {
            foreach($banlists[$type] as $item) {
                if ( !empty($item['string']) && (
                     ($item["pcre"] && @preg_match("/\b".$item['string']."\b/i", $value)) ||
                     (!$item["pcre"] && stristr($value , $item["string"]) && $type != PHORUM_BAD_USERID) ||
                     ($type == PHORUM_BAD_USERID && $value == $item["string"])) ) {
                    return false;
                }
            }
        }
    }

    return true;
}


/*

    function phorum_dyn_profile_html($field, $value="")
    {

        // $PHORUM["PROFILE_FIELDS"][]=array("name"=>"real_name", "type"=>"text", "length"=>100, "required"=>0);
        // $PHORUM["PROFILE_FIELDS"][]=array("name"=>"email", "type"=>"text", "length"=>100, "required"=>1);
        // $PHORUM["PROFILE_FIELDS"][]=array("name"=>"hide_email", "type"=>"bool", "default"=>1);
        // $PHORUM["PROFILE_FIELDS"][]=array("name"=>"sig", "type"=>"text", "length"=>0, "required"=>0);


        $PHORUM=$GLOBALS["PHORUM"];

        $html="";

        switch ($field["type"]){

            case "text":
                if($field["length"]==0){
                    $html="<textarea name=\"$field[name]\" rows=\"15\" cols=\"50\" style=\"width: 100%\">$value</textarea>";
                } else {
                    $html="<input type=\"text\" name=\"$field[name]\" size=\"30\" maxlength=\"$field[length]\" value=\"$value\" />";
                }
                break;
            case "check":
                $html ="<input type=\"checkbox\" name=\"$field[name]\" value=\"1\" ";
                if($value) $html.="checked ";
                $html.="/> $field[caption]";
                break;
            case "radio":
                foreach($field["options"] as $option){
                    $html.="<input type=\"radio\" name=\"$field[name]\" value=\"$option\" ";
                    if($value==$option) $html.="checked ";
                    $html.="/> $option&nbsp;&nbsp;";
                }
                break;
            case "select":
                $html ="<select name=\"$field[name]\" size=\"1\">";
                foreach($field["options"] as $option){
                    $html.="<option value=\"$option\"";
                    if($value==$option) $html.=" selected";
                    $html.=">$option</option>";
                }
                $html.="</select>";
                break;

        }

        return $html;

    }

*/

?>
