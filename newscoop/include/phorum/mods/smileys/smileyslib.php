<?php

// A library of common functions and definitions for
// the smileys mod. This library is only loaded when
// initializing or saving module settings.

if(!defined("PHORUM") && !defined("PHORUM_ADMIN")) return;

// A match for filtering files that are accepted as smiley images.
define('MOD_SMILEYS_IMAGE_MATCH', '/^.+\.(gif|png|jpg|jpeg)$/i');

// A match for matching absolute file paths. Paths that I could think of:
// UNIX path        /...
// URL              proto://...
// Windows path     X:\... or X:/...
// Windows net path \\...
define('MOD_SMILEYS_ABSPATH_MATCH', '!^/|^\w+://|^\w:[/\\\\]|^\\\\\\\\!i');

// The default smiley prefix path.
global $MOD_SMILEY_DEFAULT_PREFIX;
$MOD_SMILEY_DEFAULT_PREFIX = './mods/smileys/images/';

// The default list of smileys to install upon initial setup.
global $MOD_SMILEY_DEFAULT_SMILEYS;
$MOD_SMILEY_DEFAULT_SMILEYS = array(
    "(:P)"      => "smiley25.gif spinning smiley sticking its tongue out",
    "(td)"      => "smiley23.gif thumbs up",
    "(tu)"      => "smiley24.gif thumbs down",
    ":)-D"      => "smiley15.gif smileys with beer",
    ">:D<"      => "smiley14.gif the finger smiley",
    "(:D"       => "smiley12.gif smiling bouncing smiley",
    "8-)"       => "smilie8.gif  eye rolling smiley",
    ":)o"       => "smiley16.gif drinking smiley",
    "::o"       => "smilie10.gif eye popping smiley",
    "B)-"       => "smilie7.gif  smoking smiley",
    ":("        => "smilie2.gif  sad smiley",
    ":)"        => "smilie1.gif  smiling smiley",
    ":?"        => "smiley17.gif moody smiley",
    ":D"        => "smilie5.gif  grinning smiley",
    ":P"        => "smilie6.gif  tongue sticking out smiley",
    ":S"        => "smilie11.gif confused smiley",
    ":X"        => "smilie9.gif  angry smiley",
    ":o"        => "smilie4.gif  yawning smiley",
    ";)"        => "smilie3.gif  winking smiley",
    "B)"        => "cool.gif     cool smiley",
    "X("        => "hot.gif      hot smiley",
);

/**
 * Sets up initial settings for the smileys mod or upgrades
 * the settings from old versions.
 * @return modinfo - Updated module information.
 */
function phorum_mod_smileys_initsettings()
{
    $PHORUM = $GLOBALS["PHORUM"];
    global $MOD_SMILEY_DEFAULT_PREFIX;
    global $MOD_SMILEY_DEFAULT_SMILEYS;
    $modinfo = isset($PHORUM["mod_smileys"]) ? $PHORUM["mod_smileys"] : array();

    // Keep track if we need to store settings in the database.
    $do_db_update = false;

    // Set default for the image prefix path.
    if(! isset($modinfo['prefix'])) {
        $modinfo['prefix'] = $MOD_SMILEY_DEFAULT_PREFIX;
        // So phorum_mod_smileys_available() sees it right away.
        $GLOBALS["PHORUM"]["mod_smileys"]["prefix"] = $MOD_SMILEY_DEFAULT_PREFIX;
        $do_db_update = true;
    }

    // Set a default list of smileys or upgrade from existing smiley mod.
    if (! isset($modinfo['smileys']))
    {
        $modinfo['smileys'] = array();

        // Check if we have smileys from the previous version of the
        // smiley mod. These were stored at the same level as the
        // settings.
        $upgrade_list = array();
        if (isset($PHORUM["mod_smileys"])) {
            foreach ($PHORUM["mod_smileys"] as $id => $smiley) {
                if (is_numeric($id)) {
                    $upgrade_list[$id] = $smiley;
                }
            }
        }

        // We have an existing list of smileys to upgrade. Move the
        // smileys to their new location.
        if (count($upgrade_list)) {
            foreach ($upgrade_list as $id => $smiley) {
                unset($modinfo[$id]);
                $modinfo["smileys"][$id] = $smiley;
            }
            $do_db_update = true;
        }
        // Set an initial list of smileys.
        else {
            foreach ($MOD_SMILEY_DEFAULT_SMILEYS as $search => $data) {
                list($smiley, $alt) = preg_split('/\s+/', $data, 2);
                $modinfo["smileys"][] = array(
                    "search"    => $search,
                    "alt"       => $alt,
                    "smiley"    => $smiley,
                    "uses"      => 2,
                );
            }
            $do_db_update = true;
        }
    }

    // Store the changed settings in the database. Errors are
    // silently ignored here, to keep them away from end-users.
    if ($do_db_update) {
        list($modinfo, $message) = phorum_mod_smileys_store($modinfo);
        $GLOBALS["PHORUM"]["mod_smileys"] = $modinfo;
        return $modinfo;
    }
}

/**
 * Reads in the list of available smiley images.
 * @return smileys - An array of smiley image filenames.
 */
function phorum_mod_smileys_available()
{
    $PHORUM = $GLOBALS["PHORUM"];

    $available_smileys = array();
    if(file_exists($PHORUM['mod_smileys']['prefix'])){
        $d = dir($PHORUM['mod_smileys']['prefix']);
        while($entry=$d->read()) {
            if(preg_match(MOD_SMILEYS_IMAGE_MATCH, $entry)) {
                $available_smileys[$entry]=$entry;
            }
        }
    }
    asort($available_smileys);
    return $available_smileys;
}

/**
 * Compiles replacement arrays for the smileys mod and stores the
 * data for the module in the database.
 * @param modinfo - The configuration array for mod_smileys.
 * @return result - An array containing two elements:
 *                  updated module info or NULL on failure and
 *                  a message that can be displayed to the user.
 */
function phorum_mod_smileys_store($modinfo)
{
    // Get the current list of available smiley images.
    $available_smileys = phorum_mod_smileys_available();

    // Sort the smileys by length. We need to do this to replace the
    // longest smileys matching strings first. Else for example the
    // smiley ":)-D" could end up as (smileyimage)-D, because ":)"
    // was replaced first.
    uasort($modinfo["smileys"],'phorum_mod_smileys_sortbylength');

    // Create and fill replacement arrays for subject and body.
    $smiley_subject_key = array();
    $smiley_subject_val = array();
    $smiley_body_key = array();
    $smiley_body_val = array();
    $seen_images = array();
    foreach ($modinfo["smileys"] as $id => $smiley)
    {
        // Check if the smiley image is available. Skip and keep track
        // of missing smiley images.
        $active = isset($available_smileys[$smiley["smiley"]]) ? true : false;
        $modinfo["smileys"][$id]['active'] = $active;
        if (! $active) continue;

        // Check if the smiley image has been seen before. If is has, mark
        // the current smiley as being an alias. This is used in the editor
        // smiley help, to show only one version of a smiley image.
        $is_alias = isset($seen_images[$smiley["smiley"]]) ? true : false;
        $seen_images[$smiley["smiley"]] = 1;
        $modinfo["smileys"][$id]["is_alias"] = $is_alias;

        // Create HTML image code for the smiley.
        $prefix = $modinfo["prefix"];
        $src = htmlspecialchars("$prefix{$smiley['smiley']}");
        $alttxt = empty($smiley['alt']) ? $smiley["search"] : $smiley["alt"];
        $alt = htmlspecialchars($alttxt);
        $img = "<img class=\"mod_smileys_img\" src=\"$src\" alt=\"$alt\" title=\"$alt\"/>";

        // Below we use htmlspecialchars() on the search string.
        // This is done, because the smiley mod is run after formatting
        // by Phorum, so characters like < and > are HTML escaped.

        // Body only replace (0) or subject and body replace (2).
        if ($smiley['uses'] == 0 || $smiley['uses'] == 2) {
            $smiley_body_key[] = htmlspecialchars($smiley['search']);
            $smiley_body_val[] = $img;
        }

        // Subject only replace (1) or subject and body replace (2).
        if ($smiley['uses'] == 1 || $smiley['uses'] == 2) {
            $smiley_subject_key[] = htmlspecialchars($smiley['search']);
            $smiley_subject_val[] = $img;
        }
    }

    // Store replacement arrays in the module settings.
    $modinfo["replacements"] = array(
        "subject" => count($smiley_subject_key)
                   ? array($smiley_subject_key, $smiley_subject_val)
                   : NULL,
        "body"    => count($smiley_body_key)
                   ? array($smiley_body_key, $smiley_body_val)
                   : NULL
    );

    // For quickly determining if the smiley replacements must be run.
    $modinfo["do_smileys"] = $modinfo["replacements"]["subject"] != NULL ||
                             $modinfo["replacements"]["body"] != NULL;

    // Store the module settings in the database.
    if (! phorum_db_update_settings(array("mod_smileys" => $modinfo))) {
        return array(NULL, "Saving the smiley settings to the database failed.");
    } else {
        return array($modinfo, "The smiley settings were successfully saved.");
    }
}

/**
 * A callback function for sorting smileys by their search string length.
 * usage: uasort($array_of_smileys, 'phorum_mod_smileys_sortbylength');
 */
function phorum_mod_smileys_sortbylength($a, $b) {
    if (isset($a["search"]) && isset($b["search"])) {
        if (strlen($a["search"]) == strlen($b["search"])) {
            return strcmp($a["search"], $b["search"]);
        } else {
            return strlen($a["search"]) < strlen($b["search"]);
        }
    } else {
        return 0;
    }
}
?>
