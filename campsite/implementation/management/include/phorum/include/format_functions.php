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

if ( !defined( "PHORUM" ) ) return;

/**
 * Formats forum messages.
 *
 * @param data - An array containing a messages to be formatted.
 * @return data - The formatted messages.
 */
function phorum_format_messages ($data)
{
    $PHORUM = $GLOBALS["PHORUM"];

    // Prepare the bad-words replacement code.
    $bad_word_check= false;
    $banlists = phorum_db_get_banlists();
    if (isset($banlists[PHORUM_BAD_WORDS]) && is_array($banlists[PHORUM_BAD_WORDS])) {
        $replace_vals  = array();
        $replace_words = array();
        foreach ($banlists[PHORUM_BAD_WORDS] as $item) {
            $replace_words[] = "/\b".preg_quote($item['string'])."(ing|ed|s|er|es)*\b/i";
            $replace_vals[]  = PHORUM_BADWORD_REPLACE;
            $bad_word_check  = true;
        }
    }

    // A special <br> tag to keep track of breaks that are added by phorum.
    $phorum_br = '<phorum break>';

    // Apply Phorum's formatting rules to all messages.
    foreach( $data as $key => $message )
    {
        // Work on the message body ========================

        if (isset($message["body"]))
        {
            $body = $message["body"];

            // Convert legacy <> urls into bare urls.
            $body = preg_replace("/<((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%]+?)>/i", "$1", $body);

            // Escape special HTML characters. The function htmlspecialchars()
            // does too much, prior to PHP version 4.0.3.
            $body = str_replace(array("&","<",">"), array("&amp;","&lt;","&gt;"), $body);

            // Replace newlines with $phorum_br temporarily.
            // This way the mods know what Phorum did vs the user.
            $body = str_replace("\n", "$phorum_br\n", $body);

            // Run bad word replacement code.
            if($bad_word_check) {
               $body = preg_replace($replace_words, $replace_vals, $body);
            }

            $data[$key]["body"] = $body;
        }

        // Work on the other fields ========================

        // Run bad word replacement code on subject and author.
        if($bad_word_check) {
            if (isset($message["subject"]))
                $message["subject"] = preg_replace($replace_words, $replace_vals, $message["subject"]);
            if (isset($message["author"]))
                $message["author"] = preg_replace($replace_words, $replace_vals, $message["author"]);
        }

        // Escape special HTML characters in fields.
        if (isset($message["email"]))
            $data[$key]["email"] = str_replace(array("<",">"), array("&lt;","&gt;"), $message["email"]);
        if (isset($message["subject"]))
            $data[$key]["subject"] = str_replace(array("&","<",">"), array("&amp;","&lt;","&gt;"), $message["subject"]);

        // Some special things we have to do for the escaped author name.
        // We never should have put HTML in the core. Now we have to
        // do this hack to get the escaped author name in the linked_author.
        if (isset($message["author"])) {
            $data[$key]["author"]  = str_replace(array("<",">"), array("&lt;","&gt;"), $message["author"]);
            $safe_author = str_replace(array("&","<",">"), array("&amp;","&lt;","&gt;"), $message["author"]);
            if ($safe_author != $data[$key]["author"] && isset($data[$key]["linked_author"])) {
                $data[$key]["linked_author"] = str_replace($data[$key]["author"], $safe_author, $data[$key]["linked_author"]);
                $data[$key]["author"] = $safe_author;
            }
        }
    }

    // A hook for module writers to apply custom message formatting.
    $data = phorum_hook("format", $data);

    // Clean up after the mods are done.
    foreach( $data as $key => $message ) {

        // Clean up line breaks inside pre and xmp tags. These tags
        // take care of showing newlines as breaks themselves.
        if (isset($message["body"])) {
            foreach (array("pre","goep","xmp") as $tagname) {
                if (preg_match_all( "/(<$tagname.*?>).+?(<\/$tagname>)/si", $message["body"], $matches)) {
                    foreach ($matches[0] as $match) {
                        $stripped = str_replace ($phorum_br, "", $match);
                        $message["body"] = str_replace ($match, $stripped, $message["body"]);
                    }
                }
            }
            // Remove line break after quote and code tags. These tags have
            // their own line break. Without this, there would be to much
            // white lines.
            $message["body"] = preg_replace("/\s*(<\/*(xmp|blockquote|pre).*?>)\s*\Q$phorum_br\E/", "$1", $message["body"]);

            // Normalize the Phorum line breaks that are left.
            $data[$key]["body"] = str_replace($phorum_br, "<br />", $message["body"]);
        }
    }

    return $data;
}

/**
 * Formats an epoch timestamp to a date/time for displaying on screen.
 *
 * @param picture - The time formatting to use, in strftime() format
 * @param ts - The epoch timestamp to format
 * @return datetime - The formatted date/time string
 */
function phorum_date( $picture, $ts )
{
    $PHORUM = $GLOBALS["PHORUM"];

    // Setting locale.
    if (!isset($PHORUM['locale']))
        $PHORUM['locale']="EN";
    setlocale(LC_TIME, $PHORUM['locale']);

    // Format the date.
    if ($PHORUM["user_time_zone"] && isset($PHORUM["user"]["tz_offset"]) && $PHORUM["user"]["tz_offset"]!=-99) {
        $ts += $PHORUM["user"]["tz_offset"] * 3600;
        return gmstrftime( $picture, $ts );
    } else {
        $ts += $PHORUM["tz_offset"] * 3600;
        return strftime( $picture, $ts );
    }
}

/**
 * Strips HTML <tags> and BBcode [tags] from the body.
 *
 * @param body - The block of body text to strip
 * @return stripped - The stripped body
 */
function phorum_strip_body( $body )
{
    // Strip HTML <tags>
    $stripped = preg_replace("|</*[a-z][^>]*>|i", "", $body);
    // Strip BB Code [tags]
    $stripped = preg_replace("|\[/*[a-z][^\]]*\]|i", "", $stripped);

    return $stripped;
}

/**
 * Formats a file size in bytes to a human readable format. Human
 * readable formats are MB (MegaByte), kB (KiloByte) and byte.
 *
 * @param bytes - The number of bytes
 * @param formatted - The formatted size
 */
function phorum_filesize( $bytes )
{
    if ($bytes >= 1024*1024) {
        return round($bytes/1024/1024, 2) . "MB";
    } elseif ($bytes >= 1024) {
        return round($bytes/1024, 1) . "kB";
    } else {
        return $bytes . ($bytes == 1 ? " byte" : " bytes");
    }
}

?>
