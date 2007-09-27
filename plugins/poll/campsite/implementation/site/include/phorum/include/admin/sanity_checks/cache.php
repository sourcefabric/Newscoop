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

    // Check if the cache directory is available and if
    // files and directories can be created in it. Also
    // do a basic check on Phorums caching API.

    $phorum_check = "Phorum cache";

    function phorum_check_cache(){
        $PHORUM = $GLOBALS["PHORUM"];
        $dir = $PHORUM["cache"];

        // Some general solution descriptions.
        $solution_1 = "Change the Cache Directory setting under
                       General Settings.";
        $solution_2 = "Change the Cache Directory setting under General 
                       Settings or give your webserver more permissions
                       for the current cache directory.";

        // Check if the cache directory exists.
        if (! file_exists($dir) || ! is_dir($dir)) return array(
            PHORUM_SANITY_CRIT,
            "The system is unable to find the cache
             directory \"".htmlspecialchars($dir)."\" on
             your system.", 
            $solution_1
        );

        // Check if we can create files in the cache directory.
        $fp = @fopen ("$dir/sanity_check_dummy_file", "w");
        if (! $fp) return array (
            PHORUM_SANITY_CRIT,
            "The system is unable to write files
             to your cache directory \"".htmlspecialchars($dir)."\".
             The system error was:<br/><br/>".
             htmlspecialchars($php_errormsg).".",
            $solution_2
        );
        fclose($fp);

        // Some very unusual thing might happen. On Windows2000 we have seen
        // that the webserver can write a message to the cache directory,
        // but that it cannot read it afterwards. Probably due to 
        // specific NTFS file permission settings. So here we have to make 
        // sure that we can open the file that we just wrote.
        $checkfp = fopen("$dir/sanity_check_dummy_file", "r");
        if (! $checkfp) return array(
            PHORUM_SANITY_CRIT,
            "The system was able to write a file to your cache directory 
             \"".htmlspecialchars($dir)."\", but afterwards the created
             file could not be read by the webserver. This is probably 
             caused by the file permissions on your cache directory.",
            $solution_2
        );

        unlink("$dir/sanity_check_dummy_file");

        // Check if we can create directories in the cache directory.
        if (! @mkdir("$dir/sanity_check_dummy_dir")) return array(
            PHORUM_SANITY_CRIT,
            "The system is unable to create directories
             in your cache directory \"".htmlspecialchars($dir)."\".
             The system error was:<br/><br/>".htmlspecialchars($php_errormsg).".",
            $solution_2
        );
        rmdir("$dir/sanity_check_dummy_dir");

        // All seems OK. Do a final system check where we check
        // the caching system like the Phorum system will do.
        phorum_cache_put('sanity_checks', 'dummy', 'dummy');
        $entry = phorum_cache_get('sanity_checks', 'dummy');
        phorum_cache_remove('sanity_checks', 'dummy');
        if ($entry != 'dummy') return array(
            PHORUM_SANITY_WARN,
            "There might be a problem in Phorum's caching system.
             Storing and retrieving a dummy key failed. If you
             experience problems with your Phorum installation,
             it might me because of this.",
            "As a work around, you can disable the caching facilities
             in the admin interface. Please contact the Phorum
             developers to find out what the problem is.",
        );

        return array (PHORUM_SANITY_OK, NULL);
    }
?>
