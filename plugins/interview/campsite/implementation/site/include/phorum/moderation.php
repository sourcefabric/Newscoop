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
define('phorum_page','moderation');

include_once("./common.php");
include_once("./include/moderation_functions.php");
include_once("./include/thread_info.php");
include_once("./include/email_functions.php");

if(!phorum_check_read_common()) {
  return;
}

$PHORUM["DATA"]["MODERATOR"] = phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES);

$msgthd_id = (isset($_POST["thread"])) ? (int)$_POST["thread"] : (int)$PHORUM['args'][2];

$mod_step = (isset($_POST["mod_step"])) ? (int)$_POST["mod_step"] : (int)$PHORUM['args'][1];

if(empty($msgthd_id) || !phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) {
   phorum_return_to_list();
}

// If the user is not fully logged in, send him to the login page.
// because moderation action can vary so much, the only safe bet is to send them
// to the referrer if they are not fully logged in
if(!$PHORUM["DATA"]["FULLY_LOGGEDIN"]){
    phorum_redirect_by_url(phorum_get_url(PHORUM_LOGIN_URL, "redir=".$_SERVER["HTTP_REFERER"]));
    exit();
}


$template="message";
// set all our URL's
phorum_build_common_urls();

// make it possible to override this var in a hook
$is_admin_user=$PHORUM["user"]["admin"];

// a hook for doing stuff in moderation, i.e. logging moderator-actions
phorum_hook("moderation",$mod_step);


switch ($mod_step) {

   case PHORUM_DELETE_MESSAGE: // this is a message delete

        // check that they're an admin if they want to delete an announcement
        $message = phorum_db_get_message($msgthd_id);
        if ($message["sort"] == PHORUM_SORT_ANNOUNCEMENT && !$is_admin_user){
            $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]["LANG"]["DeleteAnnouncementForbidden"];
            break;
        }
        $msg_ids=phorum_db_delete_message($msgthd_id, PHORUM_DELETE_MESSAGE);
        foreach($msg_ids as $id){
            $files=phorum_db_get_message_file_list($id);
            foreach($files as $file_id=>$data){
                phorum_db_file_delete($file_id);
            }
        }
        phorum_hook("delete", $msg_ids);
        $nummsgs=count($msg_ids);
        $PHORUM['DATA']['MESSAGE']=$nummsgs." ".$PHORUM["DATA"]['LANG']['MsgDeletedOk'];
        if(isset($PHORUM['args']["prepost"])) {
            $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_CONTROLCENTER_URL,"panel=".PHORUM_CC_UNAPPROVED);
        } else {
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }
        break;

   case PHORUM_DELETE_TREE: // this is a message delete
        // check that they're an admin if they want to delete an announcement
        $message = phorum_db_get_message($msgthd_id);
        if ($message["sort"] == PHORUM_SORT_ANNOUNCEMENT && !$is_admin_user){
            $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]["LANG"]["DeleteAnnouncementForbidden"];
            break;
        }

        // Delete the message and all its replies.
        $msg_ids=phorum_db_delete_message($msgthd_id, PHORUM_DELETE_TREE);

        // Cleanup the attachments for all deleted messages.
        foreach($msg_ids as $id){
            $files=phorum_db_get_message_file_list($id);
            foreach($files as $file_id=>$data){
                phorum_db_file_delete($file_id);
            }
        }

        // Check if we have moved threads to delete.
        // We unset the forum id, so phorum_db_get_messages()
        // will return messages with the same thread id in
        // other forums as well (those are the move notifications).
        $forum_id = $PHORUM["forum_id"];
        $PHORUM["forum_id"] = 0;
        $moved = phorum_db_get_messages($msgthd_id);
        $PHORUM["forum_id"] = $forum_id;
        foreach ($moved as $id => $data) {
            if (isset($data["meta"]["moved"])) {
                phorum_db_delete_message($id, PHORUM_DELETE_MESSAGE);
            }
        }

        // Run a hook for performing custom cleanup actions.
        phorum_hook("delete", $msg_ids);

        $nummsgs=count($msg_ids);
        $PHORUM['DATA']['MESSAGE']=$nummsgs." ".$PHORUM["DATA"]["LANG"]['MsgDeletedOk'];
        if(isset($PHORUM['args']["prepost"])) {
            $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_CONTROLCENTER_URL,"panel=".PHORUM_CC_UNAPPROVED);
        } else {
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }
        break;

   case PHORUM_MOVE_THREAD: // this is the first step of a message move
        // check if the thread to move is an announcement thread
        $message = phorum_db_get_message($msgthd_id);
        if ($message["sort"] == PHORUM_SORT_ANNOUNCEMENT) {
            $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]["LANG"]["MoveAnnouncementForbidden"];
            break;
        }
        $PHORUM['DATA']['URL']["ACTION"]=phorum_get_url(PHORUM_MODERATION_ACTION_URL);
        $PHORUM['DATA']["FORM"]["forum_id"]=$PHORUM["forum_id"];
        $PHORUM['DATA']["FORM"]["thread_id"]=$msgthd_id;
        $PHORUM['DATA']["FORM"]["mod_step"]=PHORUM_DO_THREAD_MOVE;

        // get all the forums the moderator may move to
        $PHORUM['DATA']["MoveForumsOption"]="";


        $forums=phorum_db_get_forums(0,-1,$PHORUM['vroot']);
        asort($forums);

        foreach($forums as $id=>$forum){
            if ($id == $PHORUM["forum_id"]) continue;
            // add  && phorum_user_moderate_allowed($id) if the mod should only be able
            // to move to forums he also moderates
            if($forum["folder_flag"]==0){
                 // it makes no sense to move to the forum we are in already
                 if($forum['forum_id'] != $PHORUM['forum_id']) {
                    $forum_data[strtolower($forum["name"])]=array("forum_id"=>$id, "name"=>$forum["name"]);
                 }
            }
        }

        $PHORUM['DATA']['FRM']=1;
        $PHORUM['DATA']['FORUMS']=$forum_data;
        $output=true;

        $template="move_form";

        break;

   case PHORUM_DO_THREAD_MOVE: // this is the last step of a message move

        $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]['LANG']['MsgMoveOk'];
        $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        $message = phorum_db_get_message($msgthd_id);

        // find out if we have a notification-message already in this
        // target-forum for this thread ... it doesn't make sense to keep this
        // message any longer as the thread has reappeared on its original location
        $temp_forum_id=$PHORUM['forum_id'];
        $PHORUM['forum_id']=$_POST['moveto'];
        $check_messages=phorum_db_get_messages($msgthd_id);

        unset($check_messages['users']);

        // ok, we found exactly one message of this thread in the target forum
        if(is_array($check_messages) && count($check_messages) == 1) {
            // ... going to delete it
            $tmp_message=array_shift($check_messages);
            $retval=phorum_db_delete_message($tmp_message['message_id']);
        }

        $PHORUM['forum_id']=$temp_forum_id;

        // Move the thread to another forum.
        phorum_db_move_thread($msgthd_id, $_POST['moveto']);

        // Create a new message in place of the old one to notify
        // visitors that the thread was moved.
        if(isset($_POST['create_notification']) && $_POST['create_notification']) {
            $newmessage = $message;
            $newmessage['body']=" -- moved topic -- ";
            $newmessage['meta']=array('moved' => 1);
            $newmessage['sort']=PHORUM_SORT_DEFAULT;
            unset($newmessage['message_id']);

            phorum_db_post_message($newmessage);
        }
        phorum_hook("move_thread", $msgthd_id);
        break;

   case PHORUM_CLOSE_THREAD: // we have to close a thread

        $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]['LANG']['ThreadClosedOk'];
        $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        phorum_db_close_thread($msgthd_id);
        phorum_hook("close_thread", $msgthd_id);
        break;

    case PHORUM_REOPEN_THREAD: // we have to reopen a thread

        $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]['LANG']['ThreadReopenedOk'];
        $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        phorum_db_reopen_thread($msgthd_id);
        phorum_hook("reopen_thread", $msgthd_id);
        break;

    case PHORUM_APPROVE_MESSAGE: // approving a message

        $PHORUM['DATA']['MESSAGE']="1 ".$PHORUM["DATA"]['LANG']['MsgApprovedOk'];

        $old_message = phorum_db_get_message($msgthd_id);
        $newpost=array("status"=>PHORUM_STATUS_APPROVED);

        // setting the new status
        phorum_db_update_message($msgthd_id, $newpost);

        // updating the thread-info
        phorum_update_thread_info($old_message['thread']);

        // updating the forum-stats
        phorum_db_update_forum_stats(false, 1, $old_message["datestamp"]);

        if($old_message['status'] != PHORUM_STATUS_HIDDEN ) {
          phorum_email_notice($old_message);
        }

        if(isset($PHORUM['args']['old_forum']) && is_numeric($PHORUM['args']['old_forum']) && $PHORUM['folder_flag'] && $old_message['sort'] == PHORUM_SORT_ANNOUNCEMENT) {
            $PHORUM['forum_id']=(int)$PHORUM['args']['old_forum'];
        }


        if(isset($PHORUM['args']["prepost"])) {
            $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_CONTROLCENTER_URL,"panel=".PHORUM_CC_UNAPPROVED);
        } else {
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }
        break;

    case PHORUM_APPROVE_MESSAGE_TREE: // approve a message and all answers to it

        $old_message = phorum_db_get_message($msgthd_id);
        $newpost=array("status"=>PHORUM_STATUS_APPROVED);

        $mids = phorum_db_get_messagetree($msgthd_id, $old_message["forum_id"]);
        // make an array from the string
        $mids_arr=explode(",",$mids);

        // count the entries for later use
        $num_approved=count($mids_arr);
        foreach($mids_arr as $key => $mid) {
            // setting the new status
            phorum_db_update_message($mid, $newpost);

        }

        // updating the thread-info
        phorum_update_thread_info($old_message['thread']);

        // updating the forum-stats
        phorum_db_update_forum_stats(false, "+$num_approved", $old_message["datestamp"]);

        if(isset($PHORUM['args']['old_forum']) && is_numeric($PHORUM['args']['old_forum']) && $PHORUM['folder_flag'] && $old_message['sort'] == PHORUM_SORT_ANNOUNCEMENT) {
            $PHORUM['forum_id']=(int)$PHORUM['args']['old_forum'];
        }


        $PHORUM['DATA']['MESSAGE']="$num_approved ".$PHORUM['DATA']['LANG']['MsgApprovedOk'];
        if(isset($PHORUM['args']["prepost"])) {
            $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_CONTROLCENTER_URL,"panel=".PHORUM_CC_UNAPPROVED);
        } else {
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }
        break;

    case PHORUM_HIDE_POST: // hiding a message (and its replies)

        $old_message = phorum_db_get_message($msgthd_id);
        $newpost=array("status"=>PHORUM_STATUS_HIDDEN);

        $mids = phorum_db_get_messagetree($msgthd_id, $old_message["forum_id"]);
        // make an array from the string
        $mids_arr=explode(",",$mids);

        // count the entries for later use
        $num_hidden=count($mids_arr);
        foreach($mids_arr as $key => $mid) {
            // setting the new status
            phorum_db_update_message($mid, $newpost);

        }

        phorum_hook("hide", $msgthd_id);

        // updating the thread-info
        phorum_update_thread_info($old_message['thread']);

        // updating the forum-stats
        phorum_db_update_forum_stats(false, "-$num_hidden", $old_message["datestamp"]);

        $PHORUM['DATA']['MESSAGE']="$num_hidden ".$PHORUM['DATA']['LANG']['MsgHiddenOk'];
        if(isset($PHORUM['args']["prepost"])) {
            $PHORUM['DATA']["URL"]["REDIRECT"]=phorum_get_url(PHORUM_CONTROLCENTER_URL,"panel=".PHORUM_CC_UNAPPROVED);
        } else {
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }
        break;

   case PHORUM_MERGE_THREAD: // this is the first step of a thread merge

        $template="merge_form";
        $PHORUM['DATA']['URL']["ACTION"]     = phorum_get_url(PHORUM_MODERATION_ACTION_URL);
        $PHORUM['DATA']["FORM"]["forum_id"]  = $PHORUM["forum_id"];
        $PHORUM['DATA']["FORM"]["thread_id"] = $msgthd_id;
        $PHORUM['DATA']["FORM"]["mod_step"]  = PHORUM_DO_THREAD_MERGE;

        // the moderator selects the target thread to merge to
        $merge_t1 = phorum_moderator_data_get('merge_t1');
        if( !$merge_t1 || $merge_t1==$msgthd_id ) {
            phorum_moderator_data_put('merge_t1', $msgthd_id);
            $PHORUM['DATA']["FORM"]["merge_none"] =true;
        }
        // the moderator selects the source thread to merge from
        else {
            $PHORUM['DATA']["FORM"]["merge_t1"] =$merge_t1;
            $message = phorum_db_get_message($merge_t1, "message_id", true);
            $PHORUM['DATA']["FORM"]["merge_subject1"] =htmlentities($message["subject"], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]);
            $message = phorum_db_get_message($msgthd_id);
            $PHORUM['DATA']["FORM"]["thread_subject"] =htmlentities($message["subject"], ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]);
        }
        break;

   case PHORUM_DO_THREAD_MERGE: // this is the last step of a thread merge

        if( isset($_POST['thread1']) && $_POST['thread1']) {
            // Commit Thread Merge
            settype($_POST['thread1'], "int");
            settype($_POST['thread'], "int"); // Thread 2
            $PHORUM['DATA']['MESSAGE'] = $PHORUM["DATA"]['LANG']['MsgMergeOk'];
            $PHORUM['DATA']["URL"]["REDIRECT"] = $PHORUM["DATA"]["URL"]["TOP"];
            $PHORUM["reverse_threading"] = 0;

            // Get the target thread.
            $target =phorum_db_get_message($_POST['thread1'], "message_id", true);
            if (!$target) die("Can't retrieve target thread " . $_POST['thread1']);

            // Get all messages from the thread that we have to merge.
            $merge_messages=phorum_db_get_messages($_POST['thread']);
            unset($merge_messages['users']);

            // Create new messages in the target thread for
            // all messages that have to be merged.
            $msgid_translation=array();
            foreach($merge_messages as $msg)
            {
                $oldid=$msg['message_id'];

                $msg['thread']   = $target['thread'];   // the thread we merge with
                $msg['forum_id'] = $target['forum_id']; // the forum_id of the new thread
                $msg['sort']     = $target['sort'];     // the sort type of the new thread

                if($msg['message_id'] == $msg['thread']) {
                    $msg['parent_id']=$target['thread'];
                } elseif(isset($msgid_translation[$msg['parent_id']])) {
                    $msg['parent_id']=$msgid_translation[$msg['parent_id']];
                } else {
                    $msg['parent_id']=$msg['thread'];
                }

                unset($msg['message_id']);
                unset($msg['modifystamp']);

                phorum_db_post_message($msg,true);

                // save the new message-id for later use
                $msgid_translation[$oldid]=$msg['message_id'];
            }

            // deleting messages which are now doubled
            phorum_db_delete_message($_POST['thread'], PHORUM_DELETE_TREE);

            // update message count / stats
            phorum_db_update_forum_stats(true);
            // change forum_id for the following calls to update the right forum
            $PHORUM["forum_id"] =$target['forum_id'];
            // update message count / stats
            phorum_update_thread_info($target['thread']);
            phorum_db_update_forum_stats(true);
        } else {
            // Cancel Thread Merge
            $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]['LANG']['MsgMergeCancel'];
            $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
        }

        // unset temporary moderator_data
        phorum_moderator_data_remove('merge_t1');

        break;

   case PHORUM_SPLIT_THREAD: // this is the first step of a thread split

           $PHORUM['DATA']['URL']["ACTION"]=phorum_get_url(PHORUM_MODERATION_ACTION_URL);
           $PHORUM['DATA']["FORM"]["forum_id"]=$PHORUM["forum_id"];
           $message =phorum_db_get_message($msgthd_id);
           $PHORUM['DATA']["FORM"]["thread_id"]=$message["thread"];
           $PHORUM['DATA']["FORM"]["message_id"]=$msgthd_id;
           $PHORUM['DATA']["FORM"]["message_subject"]=htmlentities($message["subject"],  ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]);
           $PHORUM['DATA']["FORM"]["mod_step"]=PHORUM_DO_THREAD_SPLIT;
           $template="split_form";
           break;

   case PHORUM_DO_THREAD_SPLIT: // this is the last step of a thread split

           $PHORUM['DATA']['MESSAGE']=$PHORUM["DATA"]['LANG']['MsgSplitOk'];
           $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
           settype($_POST['forum_id'], "int");
           settype($_POST['message'], "int");
           settype($_POST['thread'], "int");
           phorum_db_split_thread($_POST['message'],$_POST['forum_id']);
           // update message count / stats
           phorum_update_thread_info($_POST['thread']);
           phorum_update_thread_info($_POST['message']);
           phorum_db_update_forum_stats(true);
           break;

    default:

        if(!isset($PHORUM['DATA']['MESSAGE'])) $PHORUM['DATA']['MESSAGE']="";
        $PHORUM['DATA']["URL"]["REDIRECT"]=$PHORUM["DATA"]["URL"]["TOP"];
}

if(!isset($PHORUM['DATA']['BACKMSG'])) {
    $PHORUM['DATA']["BACKMSG"]=$PHORUM['DATA']["LANG"]["BackToList"];
}

include phorum_get_template("header");
phorum_hook("after_header");
include phorum_get_template($template);
phorum_hook("before_footer");
include phorum_get_template("footer");

?>
