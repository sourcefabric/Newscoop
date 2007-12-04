<?php

///////////////////////////////////////////////////////////////////////////////
//                                                                           //
// Copyright (C) 2006  Phorum Development Team                               //
// http://www.phorum.org                                                     //
//                                                                           //
// This program is free software. You can redistribute it and/or modify      //
// it under the terms of either the current Phorum License (viewable at      //
// phorum.org) or the Phorum License that was distributed with this file     //
//                                                                           //
// This program is distributed in the hope that it will be useful,           //
// but WITHOUT ANY WARRANTY, without even the implied warranty of            //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      //
//                                                                           //
// You should have received a copy of the Phorum License                     //
// along with this program.                                                  //
///////////////////////////////////////////////////////////////////////////////
define('phorum_page','index');

include_once( "./common.php" );

include_once( "./include/format_functions.php" );

if(!phorum_check_read_common()) {
  return;
}

// check for markread
if (!empty($PHORUM["args"][1]) && $PHORUM["args"][1] == 'markread'){
    // setting all posts read
    if(isset($PHORUM["forum_id"])){
        unset($PHORUM['user']['newinfo']);
        phorum_db_newflag_allread($PHORUM["forum_id"]);
    }

    // redirect to a fresh list without markread in url
    $dest_url = phorum_get_url(PHORUM_INDEX_URL);
    phorum_redirect_by_url($dest_url);
    exit();

}

// somehow we got to a forum in index.php
if(!empty($PHORUM["forum_id"]) && $PHORUM["folder_flag"]==0){
    $dest_url = phorum_get_url(PHORUM_LIST_URL);
    phorum_redirect_by_url($dest_url);
    exit();
}

if ( isset( $PHORUM["forum_id"] ) ) {
    $parent_id = (int)$PHORUM["forum_id"];
} else {
    $parent_id = 0;
}


if($PHORUM["use_new_folder_style"]){
    include_once "./include/index_new.php";
} else {
    include_once "./include/index_classic.php";
}

?>
