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

if ($do_detach)
{
    // Find the message to detach.
    foreach ($message["attachments"] as $id => $info)
    {
        if ($info["file_id"] == $do_detach && $info["keep"])
        {
            // Attachments which are not yet linked to a message
            // can be deleted immediately. Linked attachments should
            // be kept in the db, in case the users clicks "Cancel".
            if (! $info["linked"]) {
                phorum_db_file_delete($info["file_id"]);
                unset($message["attachments"][$id]);
            } else {
                $message["attachments"][$id]["keep"] = false;
            }

            // Run the after_detach hook.
            list($message,$info) =
                phorum_hook("after_detach", array($message,$info));

            $attach_count--;

            break;
        }
    }
}

// Attachment(s) uploaded.
elseif ($do_attach && ! empty($_FILES))
{
    // The editor template that I use only supports one upload
    // at a time. This code supports multiple uploads.
    $attached = 0;
    foreach ($_FILES as $file)
    {
        // Not too many attachments?
        if ($attach_count >= $PHORUM["max_attachments"]) break;

        // Check if the tempfile is an uploaded file?
        if(! is_uploaded_file($file["tmp_name"])) continue;

        // Some problems in uploading result in files which are
        // zero in size. We asume that people who upload zero byte
        // files will almost always have problems uploading.
        if ($file["size"] == 0) continue;

        // check with PHP and MySQL on attachment size
        $php_limit = ini_get('upload_max_filesize')*1024;
        $max_packetsize = phorum_db_maxpacketsize();
        if ($max_packetsize == NULL) {
            $db_limit = $php_limit;
        } else {
            $db_limit = $max_packetsize/1024*.6;
        }
        if($PHORUM["max_attachment_size"]==0){
            $PHORUM["max_attachment_size"] = min($php_limit, $db_limit);
        } else {
            $PHORUM["max_attachment_size"] = min($PHORUM["max_attachment_size"], $php_limit, $db_limit);
        }

        // Isn't the attachment too large?
        if ($PHORUM["max_attachment_size"] > 0 &&
            $file["size"] > $PHORUM["max_attachment_size"]*1024) {
            $PHORUM["DATA"]["ERROR"] = str_replace(
                '%size%',
                phorum_filesize($PHORUM["max_attachment_size"] * 1024),
                $PHORUM["DATA"]["LANG"]["AttachFileSize"]
            );
            phorum_filesize($PHORUM["max_attachment_size"] * 1024);
            $error_flag = true;
            break;
        }

        // Isn't the total attachment size too large?
        if ($PHORUM["max_totalattachment_size"] > 0 &&
            ($file["size"] + $attach_totalsize) > $PHORUM["max_totalattachment_size"]*1024) {
            $PHORUM["DATA"]["ERROR"] = str_replace(
                '%size%',
                phorum_filesize($PHORUM["max_totalattachment_size"] * 1024),
                $PHORUM["DATA"]["LANG"]["AttachTotalFileSize"]
            );
            $error_flag = true;
            break;
        }

        // Is the type of file acceptable?
        if(! empty($PHORUM["allow_attachment_types"]))
        {
            $ext=substr($file["name"], strrpos($file["name"], ".")+1);
            $allowed_exts=explode(";", $PHORUM["allow_attachment_types"]);
            if (! in_array(strtolower($ext), $allowed_exts)) {
                $PHORUM["DATA"]["ERROR"] =
                    $PHORUM["DATA"]["LANG"]["AttachInvalidType"] . " ".
                    str_replace('%types%', str_replace(";", ", ", $PHORUM["allow_attachment_types"]), $PHORUM["DATA"]["LANG"]["AttachFileTypes"]);
                $error_flag = true;
                break;
            }
        }

        // Read in the file.
        $file["data"] = base64_encode(file_get_contents($file["tmp_name"]));

        // copy the current user_id to the $file array for the hook
        $file["user_id"]=$PHORUM["user"]["user_id"];

        // Run the before_attach hook.
        list($message, $file) =
            phorum_hook("before_attach", array($message, $file));

        // Add the file to the database. We add it using message_id
        // 0 (zero). Only when the message gets saved definitely,
        // the message_id will be updated to link the file to the
        // forum message. This is mainly done so we can support
        // attachments for new messages, which do not yet have
        // a message_id assigned.
        $file_id = phorum_db_file_save(
            $PHORUM["user"]["user_id"],
            $file["name"], $file["size"],
            $file["data"], 0, PHORUM_LINK_EDITOR
        );

        // Create new attachment information.
        $new_attachment = array(
            "file_id" => $file_id,
            "name"    => $file["name"],
            "size"    => $file["size"],
            "keep"    => true,
            "linked"  => false,
        );

        // Run the after_attach hook.
        list($message, $new_attachment) =
            phorum_hook("after_attach", array($message, $new_attachment));

        // Add the attachment to the message.
        $message['attachments'][] = $new_attachment;
        $attach_totalsize += $new_attachment["size"];
        $attach_count++;
        $attached++;
    }

    // Show a generic error message if nothing was attached and
    // no specific message was set.
    if (! $error_flag && ! $attached) {
        $PHORUM["DATA"]["ERROR"] =
            $PHORUM["DATA"]["LANG"]["AttachmentsMissing"];
        $error_flag = true;
    }

    // Show a success message in case an attachment is added.
    if (! $error_flag && $attached) {
        $PHORUM["DATA"]["OKMSG"] = $PHORUM["DATA"]["LANG"]["AttachmentAdded"];

    }
}
?>
