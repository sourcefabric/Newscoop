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

$parent_id = (int)(isset($_GET["parent_id"])) ? $_GET["parent_id"] : 0;
$parent_parent_id = (int)(isset($_GET["pparent"])) ? $_GET["pparent"] : 0;

$forums=phorum_db_get_forums(0, $parent_id);

// change the display-order
if(isset($_GET['display_up']) || isset($_GET['display_down'])) {

    // load all the forums up for ordering
    foreach($forums as $forum_id=>$forum_data){
        $forum_order[]=$forum_id;
    }

    // find the one we are moving
    $key=array_search(isset($_GET['display_up'])?$_GET['display_up']:$_GET['display_down'], $forum_order);
    
    $newkey=NULL;
    
    // set the new key for it
    if($key>0 && isset($_GET['display_up'])){
        $newkey=$key-1;
    }
    
    if($key<count($forum_order)-1 && isset($_GET['display_down'])){
        $newkey=$key+1;
    }

    // if we have a newkey, make the move
    if(isset($newkey)){
        $tmp=$forum_order[$key];
        $forum_order[$key]=$forum_order[$newkey];
        $forum_order[$newkey]=$tmp;
        

        // loop through all the forums and updated the ones that changed.
        // We have to look at them all because the default value for
        // display order is 0 for all forums.  So, in an unsorted forlder
        // all the values are set to 0 until you move one.
        foreach($forum_order as $new_display_order=>$forum_id){
            if($forums[$forum_id]["display_order"]!=$new_display_order){
                $forums[$forum_id]["display_order"]=$new_display_order;
                phorum_db_update_forum($forums[$forum_id]);
            }
        }

        // get a fresh forum list with updated order.
        $forums=phorum_db_get_forums(0, $parent_id);
    }

}

foreach($forums as $forum_id => $forum){



    if($forum["folder_flag"]){
        $type="Folder";
        $actions="<a href=\"$_SERVER[PHP_SELF]?module=default&parent_id=$forum_id&pparent=$parent_id\">Browse</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=editfolder&forum_id=$forum_id\">Edit</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=deletefolder&forum_id=$forum_id\">Delete</a>";
        $editurl="$_SERVER[PHP_SELF]?module=editfolder&forum_id=$forum_id";
    } else {
        $type="Forum";
        $actions="<a href=\"$_SERVER[PHP_SELF]?module=editforum&forum_id=$forum_id\">Edit</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=deleteforum&forum_id=$forum_id\">Delete</a>";
        $editurl="$_SERVER[PHP_SELF]?module=editforum&forum_id=$forum_id";
    }

    $rows.="<tr><td class=\"PhorumAdminTableRow\"><a href=\"$editurl\">$forum[name]</a><br />$forum[description]</td><td class=\"PhorumAdminTableRow\">$type</td><td class=\"PhorumAdminTableRow\"><a href=\"$_SERVER[PHP_SELF]?module=default&display_up=$forum_id&parent_id=$parent_id\">Up</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=default&display_down=$forum_id&parent_id=$parent_id\">Down</a></td><td class=\"PhorumAdminTableRow\">$actions</td></tr>\n";
}

if(empty($rows)){
    $rows="<tr><td colspan=\"4\" class=\"PhorumAdminTableRow\">There are no forums or folders in this folder.</td></tr>\n";
}

if($parent_id>0){
    $folder_data=phorum_get_folder_info();

    $path=$folder_data[$parent_id];
} else {
    $path="Choose a forum or folder.";
}



?>

<div class="PhorumAdminTitle"><?php echo "$path &nbsp;&nbsp; <a href=\"$_SERVER[PHP_SELF]?module=default&parent_id={$parent_parent_id}\"><span class=\"PhorumAdminTitle\">Go Up</span></a>";?></div>
<table border="0" cellspacing="2" cellpadding="3" width="100%">
<tr>
    <td class="PhorumAdminTableHead">Name</td>
    <td class="PhorumAdminTableHead">Type</td>
    <td class="PhorumAdminTableHead">Move</td>
    <td class="PhorumAdminTableHead">Actions</td>
</tr>
<?php echo $rows; ?>
</table>
