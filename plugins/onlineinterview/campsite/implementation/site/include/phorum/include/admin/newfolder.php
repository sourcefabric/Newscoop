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

    $error="";
    $setvroot=false; // is this folder set as vroot?

    if(count($_POST)){

        // set the defaults

        foreach($_POST as $field=>$value){

            switch($field){

                case "name":
                    if(empty($value)){
                        $error="Please fill in Title";
                    }
                    break;
                case "vroot":
                    // did we set this folder as vroot?
                    if($value > 0 && 
                        (isset($_POST['forum_id']) && $value != $_POST['forum_id'])) { // existing folder new vroot for everything below
                        $setvroot=true;   
                    } elseif($value > 0 && !defined("PHORUM_EDIT_FOLDER")) { // new folder which is vroot for everything below
                        $setvroot=true;   
                    }
                    break;

            }

            if($error) break;

        }

        if(empty($error)){
            unset($_POST["module"]);
            unset($_POST["vroot"]); // we set it separately below

            if(defined("PHORUM_EDIT_FOLDER")){
                $cur_folder_id=$_POST['forum_id'];
                
                // we need the old folder for vroots ... see below
                $oldfolder_tmp=phorum_db_get_forums($cur_folder_id);
                $oldfolder=array_shift($oldfolder_tmp);
                
                
                // update the folder
                $res=phorum_db_update_forum($_POST);
                
            } else {
                $oldfolder=array('vroot'=>0,'parent_id'=>0);
                // add the folder
                $res=phorum_db_add_forum($_POST);
                $cur_folder_id=$res;
            }

            if($res){ // other db-operations done, now doing the work for vroots
            
                $cur_folder_tmp=phorum_db_get_forums($cur_folder_id);
                $cur_folder=array_shift($cur_folder_tmp);
                               
                
                if(!$setvroot && (
                        ($oldfolder['vroot'] &&  $oldfolder['vroot'] == $cur_folder_id) || // we had a vroot before but now we removed it
                        ($oldfolder['parent_id'] != $cur_folder['parent_id'])  // or we moved this folder somewhere else
                      )
                   ) 
                   { 
                    // get the parent_id and set its vroot (if its a folder) to the desc folders/forums
                    if($cur_folder['parent_id'] > 0) { // is it a real folder?
                        $parent_folder=phorum_db_get_forums($cur_folder['parent_id']);
                        
                        // then set the vroot to the vroot of the parent-folder (be it 0 or a real vroot)
                        phorum_admin_set_vroot($cur_folder_id,$parent_folder[$cur_folder['parent_id']]['vroot'],$cur_folder_id);
                        
                    } else { // just default root ... 
                        phorum_admin_set_vroot($cur_folder_id,0,$cur_folder_id);
                    }
                    
                    // need to clear announcements in this vroot
                    $PHORUM['forum_id']=$oldfolder['vroot'];
                    $GLOBALS['PHORUM']['forum_id']=$oldfolder['vroot'];
                    $msg_array=phorum_db_get_message(PHORUM_SORT_ANNOUNCEMENT,'sort');
                    while(count($msg_array)) {
                    	// set announcements to forum-id=0 and hidden ...
                    	$new_msg=array('forum_id'=>0,'status'=>PHORUM_STATUS_HIDDEN);
                    	
                    	phorum_db_update_message($msg_array['message_id'],$new_msg);
                    	$msg_array=phorum_db_get_message(PHORUM_SORT_ANNOUNCEMENT,'sort');
                    }
                    

                // we have now set this folder as vroot
                } elseif($setvroot && ($oldfolder['vroot']==0 || $oldfolder['vroot'] != $cur_folder_id)) {    
                    if(!phorum_admin_set_vroot($cur_folder_id)) {
                        $error="Database error while setting virtual-root info.";
                    }            
                    
                } // is there an else?
                
            } else {
                $error="Database error while adding/updating folder.";
            }
        }

        if(empty($error)) {
            phorum_redirect_by_url($_SERVER['PHP_SELF']);
            exit();
        }

        foreach($_POST as $key=>$value){
            $$key=$value;
        }

    } elseif(defined("PHORUM_EDIT_FOLDER")) {

        $forum_settings = phorum_db_get_forums($_REQUEST["forum_id"]);
        extract($forum_settings[$_REQUEST["forum_id"]]);

    }

    if($error){
        phorum_admin_error($error);
    }

    include_once "./include/admin/PhorumInputForm.php";

    $frm =& new PhorumInputForm ("", "post");

    $folder_data=phorum_get_folder_info();

    if(defined("PHORUM_EDIT_FOLDER")){
        $frm->hidden("module", "editfolder");
        $frm->hidden("forum_id", $forum_id);
        $title="Edit Folder";

        $this_folder=$folder_data[$_REQUEST["forum_id"]];

        foreach($folder_data as $folder_id=> $folder){

            // remove children from the list
            if($folder_id!=$_REQUEST["forum_id"] && substr($folder, 0, strlen($this_folder)+2)!="$this_folder::"){
                $folders[$folder_id]=$folder;
            }
        }
        
        if($vroot == $forum_id) {
            $vroot=1;   
        } else {
            $foreign_vroot=$vroot;
            $vroot=0;
            
        }
            
            

    } else {
        $frm->hidden("module", "newfolder");
        $title="Add A Folder";

        $folders=$folder_data;
        $vroot=0;
        $active=1;
        $template="default";
    }



    $frm->hidden("folder_flag", "1");

    $frm->addbreak($title);

    $frm->addrow("Folder Title", $frm->text_box("name", $name, 30));

    $frm->addrow("Folder Description", $frm->textarea("description", $description, $cols=60, $rows=10, "style=\"width: 100%;\""), "top");

    $frm->addrow("Folder", $frm->select_tag("parent_id", $folders, $parent_id));

    $frm->addrow("Visible", $frm->select_tag("active", array("No", "Yes"), $active));

    $frm->addbreak("Display Settings");

    $frm->addrow("Template", $frm->select_tag("template", phorum_get_template_info(), $template));

    $frm->addrow("Language", $frm->select_tag("language", phorum_get_language_info(), $language));
    
    $frm->addrow("Virtual Root for descending forums/folders", $frm->checkbox("vroot","1","enabled",($vroot)?1:0));
    if($foreign_vroot > 0) {
        $frm->addrow("This folder is in the Virtual Root of:",$folders[$foreign_vroot]);
    } 

    $frm->show();

?>
