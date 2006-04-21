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

    if(!defined("PHORUM_ADMIN")) return;

    include_once("./include/format_functions.php");

    // Execute file purging.
    if(count($_POST))
    {
        // Run a hook, so file purging can work for alternative
        // file storage systems as well.
        if (isset($PHORUM["hooks"]["admin_file_purge"])) {
            $stale_files = phorum_db_file_purge_stale_files();
            phorum_hook("admin_file_purge", $stale_files);
        }

        // Purge stale files from the database.
        phorum_db_file_purge_stale_files(true);
    }

    // Get a list of stale files.
    $purge_files = phorum_db_file_purge_stale_files();

    include_once "./include/admin/PhorumInputForm.php";
    $frm =& new PhorumInputForm ("", "post", count($purge_files) ? "Purge stale files now" : "Refresh screen");

    $frm->hidden("module", "file_purge");

    $frm->addbreak("Purging stale files...");
    $frm->addmessage("If users write messages with attachments, but do not post them in the end, the attachment files will be left behind in the database. Using this maintenance tool, you can purge those stale files from your database.");

    if (count($purge_files)) {
        $frm->addbreak("There are currently " . count($purge_files) . 
                       " stale files in the database");
        foreach($purge_files as $id => $file) {
            $frm->addrow(htmlspecialchars($file["filename"]), phorum_filesize($file["filesize"]));
        }
    } else {
        $frm->addmessage("There are currently no stale files in the database");
    }

    $frm->show();


?>
