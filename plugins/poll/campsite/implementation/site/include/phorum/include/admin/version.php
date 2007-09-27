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

// Check for a new version of the Phorum software. If there's a new version,
// inform the admin about this.

if(!defined("PHORUM_ADMIN")) return;

require_once("./include/version_functions.php");

// Put in a variable, for easy testing of other version numbers.
$phorum_ver = PHORUM;

print '<div class="PhorumAdminTitle">Check for new Phorum version</div>';
print '<br/>';

// Show the current software version.
list ($running_type, $dummy) = phorum_parse_version($phorum_ver);
print "You are currently running the $running_type version $phorum_ver " .
      " of the Phorum software.<br/>";


// Find all available upgrades. If no releases can be found
// for some reason, we ignore this and simply pretend the installation
// is up-to-date.
$releases = phorum_find_upgrades($phorum_ver);

$new_s = isset($releases["stable"]) && $releases["stable"]["upgrade"];
$new_d = isset($releases["development"]) && $releases["development"]["upgrade"];

// Notice: when running a snapshot version.
if ($running_type == 'snapshot') {
    print "<br/>If this Phorum installation is run on a production server, " .
          "the Phorum team recommends upgrading to either a stable " .
          "release or the latest development release. Snapshots should " .
          "only be used for testing new bleeding edge features.<br/>";
}

// Notice: when running a stable release while a new stable is available.
if ($running_type == 'stable' && $new_s) {
    print "<br/>A new stable release is available. The Phorum team " .
          "recommends upgrading to this release as soon as possible.<br/>";
}

// Notice: when running a development release while a new stable
// and development release are available.
if ($running_type == 'development' && $new_s && $new_d) {
    print "<br/>There's both a new stable and a new development release " .
          "available. If this Phorum installation " .
          "is run on a production server, the Phorum team recommends " .
          "upgrading to the stable version.<br/>";
}

// Notice: when running a development release while a new dev is available.
if ($running_type == 'development' && $new_d && ! $new_s) {
    print "<br/>A new development release is available. If this Phorum " .
          "installation is run on a production server, the Phorum team " .
          "recommends only to upgrade in case new features are needed, " .
          "bugs you are suffering from are fixed or security holes have been " .
          "closed. Else wait until a stable release is available.<br/>";
}

// Display available upgrades.
$found_upgrade = false;
foreach (array("stable","development") as $type) {
    if (isset($releases[$type]) && $releases[$type]["upgrade"])
    {
        $found_upgrade = true;

        $ver = $releases[$type]["version"];
        print "<br/><h3 class=\"input-form-th\">";
        if ($running_type == 'snapshot') {
            print "The current $type release is version $ver";
        } else {
            print "A new $type release (version $ver) is available";
        }
        print "</h3>";

        print "This release can be downloaded from:<br/><ul>";
        foreach ($releases["$type"]["locations"] as $url) {
            print "<li><a href=\"". htmlspecialchars($url) . "\">" .
                  htmlspecialchars($url) . "</a></li>";
        }
        print "</ul>";
    }
}

if (! $found_upgrade) {
    print "<br/><h3 class=\"input-form-th\">" .
          "Your Phorum installation is up to date</h3>";
}
?>
