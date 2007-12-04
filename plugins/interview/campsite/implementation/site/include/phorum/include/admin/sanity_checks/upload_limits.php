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

    // Check if the Phorum file uploading settings match the
    // limits that are imposed by the system.

    // TODO (document in faq / documentation)
    // The upload size can be limited by Apache's LimitRequestBody directive,
    // but we cannot check that one from PHP.

    require_once('./include/format_functions.php'); // For phorum_filesize()

    $phorum_check = "File uploading (personal files and attachments)";

    function phorum_check_upload_limits() {
        $PHORUM = $GLOBALS["PHORUM"];

        // Keep track if uploads are used.
        $upload_used = false;

        // Get the maximum file upload size for PHP.
        $php_max_upload = phorum_php_max_upload();

        // Get the maximum packet size for the database.
        // For determining the maximum allowed upload size,
        // we have to take packet overhead into account.
        $max_packetsize = phorum_db_maxpacketsize();
        if ($max_packetsize == NULL) {
            $db_max_upload = $php_max_upload;
        } else {
            $db_max_upload = phorum_db_maxpacketsize() * 0.6;
        }

        // Check limits for file uploading in personal profile.
        if ($PHORUM["file_uploads"] && $PHORUM["max_file_size"]) {
            $upload_used = true;
            $res = phorum_single_check_upload_limits(
                $PHORUM["max_file_size"]*1024,
                "the Max File Size option for user file uploads " .
                "(in their profile)",
                $php_max_upload, $db_max_upload
            );
            if ($res != NULL) return $res;
        }

        // Check limits for attachment uploading in forums.
        $forums = phorum_db_get_forums();
        foreach ($forums as $id => $forum) {
            if ($forum["max_attachments"] > 0 && $forum["max_attachment_size"]) {
                $upload_used = true;
                $res = phorum_single_check_upload_limits(
                    $forum["max_attachment_size"]*1024,
                    "the Max File Size option for uploading attachments
                     in the forum \"{$forum['name']}\"",
                    $php_max_upload, $db_max_upload
                );
            }
        }

        // No upload functionality found so far? Then we're done.
        if (! $upload_used) return array(PHORUM_SANITY_OK, NULL);

        // Check if the upload temp directory can be written.
        $tmpdir = get_cfg_var('upload_tmp_dir');
        if (!empty($tmpdir)) {
            $fp = @fopen("$tmpdir/sanity_checks_dummy_uploadtmpfile", "w");
            if (! $fp) return array(
                PHORUM_SANITY_CRIT,
                "The system is unable to write files
                 to PHP's upload tmpdir \"".htmlspecialchars($tmpdir)."\".
                 The system error was:<br/><br/>".
                 htmlspecialchars($php_errormsg).".",
                "Change the upload_tmp_dir setting in your php.ini file
                 or give your webserver more permissions for the current
                 upload directory."
            );
        }
        fclose($fp);
        unlink("$tmpdir/sanity_checks_dummy_uploadtmpfile");

        return array(PHORUM_SANITY_OK, NULL);
    }

    // ========================================================================
    // Helper functions
    // ========================================================================

    // We have to check multiple upload limits. Using this function,
    // we do not have to rebuild all error messages over and over
    // again.
    function phorum_single_check_upload_limits ($howmuch, $what, $maxphp, $maxdb)
    {
        // Check PHP limits.
        if (!empty($maxphp) && $howmuch > $maxphp) return array(
            PHORUM_SANITY_WARN,
            "You have configured ".htmlspecialchars($what)." to ".
             phorum_filesize($howmuch).". Your PHP installation only 
             supports ".phorum_filesize($maxphp).". Your users might
             have problems with uploading their files because of this.",
            "Raise the options post_max_size and upload_max_filesize in your
             php.ini file to match the Max File Size option or lower this
             configuration option for your forums."
        );

        // Check database limits.
        if (!empty($maxdb) && $howmuch > $maxdb) return array(
            PHORUM_SANITY_WARN,
            "You have configured ".htmlspecialchars($what)." to ".
             phorum_filesize($howmuch).". Your database only supports ".
             phorum_filesize($maxdb).". Your users might have problems with
             uploading their files because of this.",
            "Configure your database to allow larger packets or lower the
             Max File Size configuration option for your forums."
        );

        return NULL;
    }

    function phorum_php_max_upload()
    {
        // Determine the PHP system upload limit. The limit for
        // maximum upload filesize is not the only thing we
        // have to look at. We should also take the maximum
        // POST size in account.
        $pms = phorum_phpcfgsize2bytes(get_cfg_var('post_max_size'));
        $umf = phorum_phpcfgsize2bytes(get_cfg_var('upload_max_filesize'));
        $limit = ($umf > $pms ? $pms : $umf);

        return $limit;
    }

    // Convert the size parameters that can be used in the
    // PHP ini-file (e.g. 1024, 10k, 8M) to a number of bytes.
    function phorum_phpcfgsize2bytes($val) {
        $val = trim($val);
        $last = strtolower($val{strlen($val)-1});
        switch($last) {
           // The 'G' modifier is available since PHP 5.1.0
           case 'g':
               $val *= 1024;
           case 'm':
               $val *= 1024;
           case 'k':
               $val *= 1024;
        }
        return $val;
    }
?>
