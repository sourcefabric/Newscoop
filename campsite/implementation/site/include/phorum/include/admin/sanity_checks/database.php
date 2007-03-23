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

    // Check the database connection and setup. We may want to have finer
    // granulated checks here to give users with problem real good
    // information about what should be fixed, but for that the
    // database layer must be extended. For now it's just a simple
    // connect check that will mostly just sit there and be pretty ;-)
    //
    // Extra checks to think about:
    // - test if all needed permissions are set;
    // - catch the error from the database on connection failure and
    //   try to give the user specific data for fixing the problem.

    $phorum_check = "Database connection";

    function phorum_check_database() {
        $PHORUM = $GLOBALS["PHORUM"];

        // Check if we have a database configuration available.
        if (! isset($PHORUM["DBCONFIG"])) return array(
            PHORUM_SANITY_CRIT,
            "No database configuration was found in your environment.",
            "You probably have not copied include/db/config.php.sample
             to include/db/config.php. Read Phorum's install.txt for
             installation instructions."
        );

        // Check if a connection can be made.
        $connected = @phorum_db_check_connection();
        if (! $connected) return array(
            PHORUM_SANITY_CRIT,
            "Connecting to the database failed.",
            "Check your database settings in the file include/db/conf.php"
        );

        // Do a database layer specific check, if available.
        if (function_exists("phorum_db_sanitychecks")) {
            $res = phorum_db_sanitychecks();
            if ($res[0] != PHORUM_SANITY_OK) return $res;
        }

        // All checks are OK.
        return array(PHORUM_SANITY_OK, NULL);
    }
?>
