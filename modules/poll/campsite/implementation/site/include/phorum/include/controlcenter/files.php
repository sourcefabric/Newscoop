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

if(!defined("PHORUM_CONTROL_CENTER")) return;

if ($PHORUM["file_uploads"] || $PHORUM["user"]["admin"]) {

    if(!empty($_FILES) && is_uploaded_file($_FILES["newfile"]["tmp_name"])){

        if($PHORUM["max_file_size"]>0 && $_FILES["newfile"]["size"]>$PHORUM["max_file_size"]*1024){
            $error_msg = true;
            $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["FileTooLarge"];
        }

        if(!empty($PHORUM["file_types"])){
            $ext=strtolower(substr($_FILES["newfile"]["name"], strrpos($_FILES["newfile"]["name"], ".")+1));
            $allowed_exts=explode(";", $PHORUM["file_types"]);                
            if(!in_array($ext, $allowed_exts)){
                $error_msg = true;
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["FileWrongType"];
            }
        }

        if($PHORUM["file_space_quota"]>0 && phorum_db_get_user_filesize_total($PHORUM["user"]["user_id"])+$_FILES["newfile"]["size"]>=$PHORUM["file_space_quota"]*1024){
            $error_msg = true;
            $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["FileOverQuota"];
        }

        if(empty($error_msg)){

            // read in the file
            $fp=fopen($_FILES["newfile"]["tmp_name"], "r");
            $buffer=base64_encode(fread($fp, $_FILES["newfile"]["size"]));
            fclose($fp);

            $file_id=phorum_db_file_save($PHORUM["user"]["user_id"], $_FILES["newfile"]["name"], $_FILES["newfile"]["size"], $buffer);

        }

    } elseif(!empty($_POST["delete"])) {

        foreach($_POST["delete"] as $file_id){

            phorum_db_file_delete($file_id);

        }                

    }

    $files = phorum_db_get_user_file_list($PHORUM["user"]["user_id"]);

    $total_size=0;

    foreach($files as $key => $file) {
        $files[$key]["filesize"] = phorum_filesize($file["filesize"]);
        $files[$key]["dateadded"]=phorum_date($PHORUM["short_date"], $file["add_datetime"]);

        $files[$key]["url"]=phorum_get_url(PHORUM_FILE_URL, "file=$key");

        $total_size+=$file["filesize"];
    } 

    $template = "cc_files";

    if($PHORUM["max_file_size"]){
        $PHORUM["DATA"]["FILE_SIZE_LIMIT"]=$PHORUM["DATA"]["LANG"]["FileSizeLimits"] . ' ' . phorum_filesize($PHORUM["max_file_size"]*1024);
    }

    if($PHORUM["file_types"]){
        $PHORUM["DATA"]["FILE_TYPE_LIMIT"]=$PHORUM["DATA"]["LANG"]["FileTypeLimits"];
    }

    if($PHORUM["file_space_quota"]){
        $PHORUM["DATA"]["FILE_QUOTA_LIMIT"]=$PHORUM["DATA"]["LANG"]["FileQuotaLimits"] . ' ' . phorum_filesize($PHORUM["file_space_quota"]*1024);;
    }

    $PHORUM["DATA"]["FILES"] = $files;

    $PHORUM["DATA"]["TOTAL_FILES"] = count($files);
    $PHORUM["DATA"]["TOTAL_FILE_SIZE"] = phorum_filesize($total_size);

} else {
    $template = "message";

    $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["UploadNotAllowed"];
} 

?>
