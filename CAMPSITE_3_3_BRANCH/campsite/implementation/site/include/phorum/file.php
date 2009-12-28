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
define('phorum_page','file');

ob_start();

ini_set ( "zlib.output_compression", "0");
ini_set ( "output_handler", "");

include_once("./common.php");

// set all our URL's
phorum_build_common_urls();

// checking read-permissions
if(!phorum_check_read_common()) {
  return;
}

if(empty($PHORUM["args"]["file"])){
    phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
    exit();
}

$filearg=(int)$PHORUM["args"]["file"];
$file=phorum_db_file_get($filearg);


if(empty($file)){
    phorum_redirect_by_url(phorum_get_url(PHORUM_LIST_URL));
    exit();
}

$send_file=true;

// check if this phorum allows off site links and if not, check the referrer
if(isset($_SERVER["HTTP_REFERER"]) && !$PHORUM["file_offsite"] && preg_match('!^https?://!', $_SERVER["HTTP_REFERER"])){

    $base = strtolower(phorum_get_url(PHORUM_BASE_URL));
    $len = strlen($base);
    if (strtolower(substr($_SERVER["HTTP_REFERER"], 0, $len)) != $base) {

        ob_end_flush();

        $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["FileForbidden"];
        include phorum_get_template("header");
        include phorum_get_template("message");
        include phorum_get_template("footer");

        $send_file=false;
    }
}

if($send_file){

    // Mime Types for Attachments
    $mime_types["default"]="text/plain";
    $mime_types["pdf"]="application/pdf";
    $mime_types["doc"]="application/msword";
    $mime_types["xls"]="application/vnd.ms-excel";
    $mime_types["gif"]="image/gif";
    $mime_types["png"]="image/png";
    $mime_types["jpg"]="image/jpeg";
    $mime_types["jpeg"]="image/jpeg";
    $mime_types["jpe"]="image/jpeg";
    $mime_types["tiff"]="image/tiff";
    $mime_types["tif"]="image/tiff";
    $mime_types["xml"]="text/xml";
    $mime_types["mpeg"]="video/mpeg";
    $mime_types["mpg"]="video/mpeg";
    $mime_types["mpe"]="video/mpeg";
    $mime_types["qt"]="video/quicktime";
    $mime_types["mov"]="video/quicktime";
    $mime_types["avi"]="video/x-msvideo";
    $mime_types["gz"]="application/x-gzip";
    $mime_types["tgz"]="application/x-gzip";
    $mime_types["zip"]="application/zip";
    $mime_types["tar"]="application/x-tar";
    $mime_types["exe"]="application/octet-stream";
    $mime_types["rar"]="application/octet-stream";
    $mime_types["wma"]="application/octet-stream";
    $mime_types["wmv"]="application/octet-stream";
    $mime_types["mp3"]="audio/mpeg";

    $type=strtolower(substr($file["filename"], strrpos($file["filename"], ".")+1));

    if(isset($mime_types[$type])){
        $mime=$mime_types[$type];
    }
    else{
        $mime=$mime_types["default"];
    }

    list($mime, $file) = phorum_hook("file", array($mime, $file));

    ob_end_clean();

    header("Content-Type: $mime");
    header("Content-Disposition: filename=\"{$file['filename']}\"");

    echo base64_decode($file["file_data"]);

    exit();
}

?>
