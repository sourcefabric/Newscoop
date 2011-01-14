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

/**
 * Parses the Phorum version number.
 * @param version - version number to parse
 * @return An array containing two elements. The first one holds the release
 *         type, which can be "unknown" (parse failed), "snapshot", "stable"
 *         or "development". The version can either be NULL or an array
 *         containing a splitted up version number (only for "stable"
 *         and "development").
 */
function phorum_parse_version($version)
{
    if (preg_match('/^\w+-(svn|cvs)-\d+$/', $version)) {
        $release = 'snapshot';
        $parsed_version = array(0,0,0,0);
    } elseif (preg_match('/^(\d+)\.(\d+).(\d+)([a-z])?$/', $version, $m)) {
        $release = 'stable';
        $parsed_version = array_slice($m, 1);
    } elseif (preg_match('/^(\d+)\.(\d+)-(dev)/', $version, $m)) {
        $release = 'development';
        $parsed_version = array($m[1], $m[2], 0, $m[3]);
    } elseif (preg_match('/^(\d+)\.(\d+).(\d+)(-alpha|-beta|-RC\d+)?$/', $version, $m)) {
        $release = 'development';
        $parsed_version = array_slice($m, 1);
    } else {
        $release = 'unknown';
        $parsed_version = NULL;
    }

    return array($release, $parsed_version);
}

/**
 * Compares two version numbers as returned by phorum_parse_version()
 * and tells which of those two is larger.
 * @param version1 - The first version number
 * @param version2 - The second version number
 * @return 1 if version1 is higher than version2, 0 if equal, -1 if lower
 */
 function phorum_compare_version($version1, $version2)
{
    // Compare segment by segment which version is higher.
    // Segments 1, 2 and 3 are always numbers. Segment 4 can be
    // a post-release version letter (a, b, c, etc.) or a
    // development release marker (-alpha and -beta).
    for ($s=0; $s<=3; $s++) {
        if ($s != 3) {
            if ($version1[$s] > $version2[$s]) return 1;
            if ($version1[$s] < $version2[$s]) return -1;
        } else {
            // Build a numerical representation for segment 4.
            // * 0 if no segment 4 is set
            // * 1 for alpha
            // * 2 for beta
            // * ord for single char version additions (a = 97)
            $v1 = 0; $v2 = 0;
            if (isset($version1[$s])) {
                if ($version1[$s] == '-alpha') $v1 = 1;
                elseif ($version1[$s] == '-beta') $v1 = 2;
                elseif (strlen($version1[$s]) == 1) $v1 = ord($version1[$s]);
            }
            if (isset($version2[$s])) {
                if ($version2[$s] == '-alpha') $v2 = 1;
                elseif ($version2[$s] == '-beta') $v2 = 2;
                elseif (strlen($version2[$s]) == 1) $v2 = ord($version2[$s]);
            }
            // Same version number with a development suffix is
            // considered lower than without any suffix.
            if ($v1 == 0 && ($v2 == 1 || $v2 == 2)) return 1;
            if (($v1 == 1 || $v1 == 2) && $v2 == 0) return -1;

            if ($v1 > $v2) return 1;
            if ($v1 < $v2) return -1;
        }
    }

    // No difference was found.
    return 0;
}

/**
 * Retrieves the available software versions from the Phorum website.
 * The format of the data returned from the server is two lines. The first
 * line is for the stable version and the second for the development version.
 * Each line contains pipe separated values, with the following fields in it:
 * <version>|<release date>|<downloadloc 1>|<downloadloc 2>|...|<downloadloc n>
 * @return releases - An array of releases for release types "stable" and "development".
 */
function phorum_available_releases()
{
    $releases = array();
    $fp = @fopen("http://phorum.org/version.php", "r");
    if ($fp) {
        foreach (array("stable", "development") as $release) {
            $line = fgets($fp, 1024);
            if (strstr($line, '|')) {
                $fields = explode('|', $line);
                if (count($fields) >= 3) {
                    // See if we can parse the version and if the parsed
                    // release type matches the release type we're expecting.
                    $parsed_version = phorum_parse_version($fields[0]);
                    if ($parsed_version[0] == $release) {
                        $releases[$release] = array(
                            "version"   => array_shift($fields),
                            "pversion"  => $parsed_version[1],
                            "date"      => array_shift($fields),
                            "locations" => $fields
                        );
                    }
                }
            }
        }
        fclose($fp);
    }

    return $releases;
}

/**
 * Finds out if there are any upgrades available for a version of Phorum.
 * @param version - the version to check for (default is the running version)
 * @return releases - An array of available releases with the
 *         "upgrade" field set in case the release would be an
 *         upgrade for the currently running Phorum software.
 */
function phorum_find_upgrades($version = PHORUM)
{
    // Parse the running version of phorum.
    list ($running_release, $running_version) = phorum_parse_version($version);

    // Retrieve the available releases.
    $releases = phorum_available_releases();

    // Check if an upgrade is available for the running release.
    // If we're running a stable version, we only compare to the current
    // stable release. If we're running a development version, we compare both
    // stable and development.
    if (isset($releases["stable"])) {
        $avail_version = $releases["stable"]["pversion"];
        if (phorum_compare_version($running_version, $avail_version) == -1) {
            $releases["stable"]["upgrade"] = true;
        } else {
            $releases["stable"]["upgrade"] = false;
        }
    }
    if (($running_release == 'development' || $running_release == 'snapshot') && isset($releases["development"])) {
        $avail_version = $releases["development"]["pversion"];
        if (phorum_compare_version($running_version, $avail_version) == -1) {
            $releases["development"]["upgrade"] = true;
        } else {
            $releases["development"]["upgrade"] = false;
        }
    }

    return $releases;
}

?>
