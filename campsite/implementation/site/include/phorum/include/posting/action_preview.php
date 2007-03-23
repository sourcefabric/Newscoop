<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    ////                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

if(!defined("PHORUM")) return;

$previewmessage = $message;

// Add the user's signature to the message body.
if (isset($PHORUM["user"]["signature"]) && isset($message["show_signature"]) && $message["show_signature"]) {
    $previewmessage["body"] .= "\n\n" . $PHORUM["user"]["signature"];
}

// Add the list of attachments.
if ($attach_count) 
{
    define('PREVIEW_NO_ATTACHMENT_CLICK', 
           "javascript:alert('" . $PHORUM["DATA"]["LANG"]["PreviewNoClickAttach"] . "')");

    // Create the URL and formatted size for attachment files.
    foreach ($previewmessage["attachments"] as $nr => $data) {
        $previewmessage["attachments"][$nr]["url"] =
            phorum_get_url(PHORUM_FILE_URL, "file={$data['file_id']}");
        $previewmessage["attachments"][$nr]["size"] =
            phorum_filesize($data["size"]);
    }
}

// Format the message using the default formatting.
include_once("./include/format_functions.php");
$previewmessages = phorum_format_messages(array($previewmessage));
$previewmessage = array_shift($previewmessages);

// Recount the number of attachments. Formatting mods might have changed
// the number of attachments we have to display using default formatting.
$attach_count = 0;
if (isset($previewmessage["attachments"])) {
    foreach ($previewmessage["attachments"] as $attachment) {
        if ($attachment["keep"]) {
            $attach_count ++;
        }
    }    
}

if ($attach_count)
{
    // Disable clicking on attachments in the preview (to prevent the
    // browser from jumping to a viewing page, which might break the
    // editing flow). This is not done in the previous loop where the
    // URL is set, so the formatting code for things like inline
    // attachments can be used.
    foreach ($previewmessage["attachments"] as $nr => $data) {
        $previewmessage["attachments"][$nr]["url"] = PREVIEW_NO_ATTACHMENT_CLICK;
    }
} else {
    unset($previewmessage["attachments"]);
}

// Fill the author name and datestamp for new postings.
if ($mode != "edit" && $PHORUM["DATA"]["LOGGEDIN"]) {
    $previewmessage["author"] = $PHORUM["user"]["username"];
    $previewmessage["datestamp"] = time();
}

// Format datestamp. 
$previewmessage["datestamp"] = phorum_date($PHORUM["short_date"], $previewmessage["datestamp"]);
   
$PHORUM["DATA"]["PREVIEW"] = $previewmessage;
    
?>
