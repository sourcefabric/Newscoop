<?php
if(!defined("PHORUM5_CONVERSION")) return;

// Phorum3 - to - Phorum5 Conversion Library

function phorum_convert_check_groups($link) {
    GLOBAL $CONVERT;

    $sql="show tables LIKE '{$CONVERT['forumstable']}_groups'";
    $res=mysql_query($sql,$link);
    if(mysql_num_rows($res)>0) {
        $ret=true;
    } else {
        $ret=false;
    }
    return $ret;
}


function phorum_convert_check_users($link) {
    GLOBAL $CONVERT;

    $sql="show tables LIKE '{$CONVERT['forumstable']}_auth'";
    $res=mysql_query($sql,$link);
    if(mysql_num_rows($res)>0) {
        $ret=true;
    } else {
        $ret=false;
    }
    return $ret;
}

function phorum_convert_getForums($link) {
	global $CONVERT;

    $sql="SELECT * FROM {$CONVERT['forumstable']} ORDER BY id ASC";
    $res=mysql_unbuffered_query($sql,$link);
    $forums=array();

    if ($err = mysql_error($link)) phorum_db_mysql_error("$err: $sql");


    echo "Reading forums from phorum3-table {$CONVERT['forumstable']} ...{$CONVERT['lbr']}";
    while($row=mysql_fetch_array($res)) {
       $forums[$row['id']]=$row;
    }

	return $forums;
}


function phorum_convert_getGroups($link) {
	global $CONVERT;

    $sql="SELECT * FROM {$CONVERT['forumstable']}_groups ORDER BY id ASC";
    $res=mysql_unbuffered_query($sql,$link);
    $groups=array();

    if ($err = mysql_error($link)) phorum_db_mysql_error("$err: $sql");

    while($row=mysql_fetch_array($res)) {
       $groups[$row['id']]=$row;
       $groups[$row['id']]['open']=PHORUM_GROUP_CLOSED;

    }

	return $groups;
}

function phorum_convert_getForumGroups($forum_id) {
    global $CONVERT;

    $sql="SELECT * FROM {$CONVERT['forumstable']}_forum2group";
    $res=mysql_unbuffered_query($sql,$GLOBALS['oldlink']);
    $groups=array();
    while($row=mysql_fetch_array($res)) {
        $groups[]=$row['group_id'];
    }

    return $groups;
}

function phorum_convert_prepareForum($forumdata) {
     global $CONVERT;

     if($forumdata['folder']) { // folders
         if(!get_magic_quotes_runtime()){
               $forumdata['name'] = $forumdata['name'];
               $forumdata['description'] = $forumdata['description'];
         }

         $newforum = array(
            		 'forum_id' => $forumdata['id'],
            		 'name' => $forumdata['name'],
            		 'active' => $forumdata['active'],
            		 'description' => $forumdata['description'],
            		 'template' => 'default',
            		 'folder_flag' => $forumdata['folder'],
            		 'parent_id' => $forumdata['parent'],
                     'pub_perms' => PHORUM_USER_ALLOW_READ,
                     'reg_perms' => PHORUM_USER_ALLOW_READ
                     );


     } else { // forums
         echo "Preparing data for forum {$forumdata['name']} ...{$CONVERT['lbr']}";
         // rewriting some vars
         if($forumdata['moderation'] == "a")
            $moderation = PHORUM_MODERATE_ON;
         else
            $moderation = PHORUM_MODERATE_OFF;

         if($forumdata['moderation'] == "n")
             $email_mod = PHORUM_EMAIL_MODERATOR_OFF;
         else
             $email_mod = PHORUM_EMAIL_MODERATOR_ON;

         if($forumdata['collapse'] == 1)
            $threaded = 0;
         else
            $threaded = 1;

         if($forumdata['multi_level'] == 2)
            $float_to_top = 1;
         else
            $float_to_top = 0;

         if(!isset($forumdata['allow_edit'])) // hmm could this really happen?
             $forumdata['allow_edit'] = 0;


         if(!get_magic_quotes_runtime()){
               $forumdata['name'] = $forumdata['name'];
               $forumdata['description'] = $forumdata['description'];
         }

         // checking security setting
         if($forumdata['security'] > 1) {
             if($forumdata['security'] == 2) { // login to post
                 $pub_perms= 0 | PHORUM_USER_ALLOW_READ;
                 $reg_perms= 0 | PHORUM_USER_ALLOW_READ | PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY | PHORUM_USER_ALLOW_EDIT;

             } elseif($forumdata['security'] == 3) { // login to read (so to say, login to everything)
                 $pub_perms= 0;
                 $reg_perms= 0 | PHORUM_USER_ALLOW_READ | PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY | PHORUM_USER_ALLOW_EDIT;
             }
         } else {
             $pub_perms = 0 | PHORUM_USER_ALLOW_READ | PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY;
             $reg_perms = 0 | PHORUM_USER_ALLOW_READ | PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY | PHORUM_USER_ALLOW_EDIT;
         }

         // checking groups
         if($CONVERT['do_groups']) {
             $groups=phorum_convert_getForumGroups($forumdata['id']);
             if(count($groups) && ($forumdata['permissions'] == 1 || $forumdata['permissions'] > 2) ) {
                 foreach($groups as $bogus => $group_id) {
                     $CONVERT['groups'][$group_id]['permissions'][$forumdata['id']]=$reg_perms;
                 }
                 $reg_perms=0;
                 $pub_perms=0;
             }
         }


         $newforum = array(
            		 'forum_id' => $forumdata['id'],
            		 'name' => $forumdata['name'],
            		 'active' => $forumdata['active'],
            		 'description' => $forumdata['description'],
            		 'template' => 'default',
            		 'folder_flag' => $forumdata['folder'],
            		 'parent_id' => $forumdata['parent'],
                     'list_length_flat' => $forumdata['display'],
                     'list_length_threaded' => $forumdata['display'],
                     'read_length' => 20,
                     'moderation' => $moderation,
                     'threaded_list' => $threaded,
                     'threaded_read' => $threaded,
                     'float_to_top' => $float_to_top,
                     'allow_attachment_types' => $forumdata['upload_types'],
                     'max_attachment_size' => $forumdata['upload_size'],
                     'max_attachments' => $forumdata['max_uploads'],
                     'pub_perms' => $pub_perms,
                     'reg_perms' => $reg_perms,
                     'display_ip_address' => $forumdata['showip'],
                     'allow_email_notify' => $forumdata['emailnotification'],
                     'language' => basename($forumdata['lang'],".php"),
                     'email_moderators' => $email_mod,
                     'edit_post' => $forumdata['allow_edit']
                     );
    }
    return $newforum;
}

function phorum_convert_getAttachments($table_name) {
    GLOBAL $CONVERT;

    $sql="SELECT * FROM ".$table_name."_attachments";
    $res=mysql_unbuffered_query($sql,$GLOBALS['oldlink']);
    $att=array();
    while($row=mysql_fetch_assoc($res)) {
        $att[$row['message_id']][]=$row;
    }

    return $att;
}

function phorum_convert_selectMessages($forumdata,$link) {

    $sql="SELECT a.*,b.body,UNIX_TIMESTAMP(a.datestamp) as unixtime  FROM ".$forumdata['table_name']." as a, ".$forumdata['table_name']."_bodies as b WHERE b.id = a.id ORDER BY a.id ASC";
    $res=mysql_unbuffered_query($sql, $link);

    if ($err = mysql_error($link)) phorum_db_mysql_error("$err: $sql");

    return $res;
}

function phorum_convert_getNextMessage($res,$table_name) {
      global $CONVERT;

      // fetching the message from the database
      $mdata = mysql_fetch_assoc($res);
      if(!$mdata) {
            return false;
      }
      $max_id= $CONVERT['max_id'];

      $id=$mdata['id'];
      if($mdata['closed'])
            $closed=1;
      else
            $closed=0;

      if($mdata['approved'] != "Y")
            $post_status=PHORUM_STATUS_HOLD;
      else
            $post_status=PHORUM_STATUS_APPROVED;

      $post_sort=PHORUM_SORT_DEFAULT;

      $parentid=($mdata['parent']>0)?($mdata['parent']+$max_id):0;

      if(!get_magic_quotes_runtime()){
            $mdata['author'] = $mdata['author'];
            $mdata['subject'] = $mdata['subject'];
            $mdata['body'] = $mdata['body'];
            $mdata['email'] = $mdata['email'];
      }

      //find [%sig%] and cut it
      if (preg_match ("/\[%sig%\]/", $mdata['body'])) {
      	$mdata['body'] = preg_replace ( "/\[%sig%\]/", "", $mdata['body']);
      	$add_signature = true;
      } else {
        $add_signature = false;
      }

      // bah, there are really people trying to upgrade from 3.2.x ;)
      $userid = (isset($mdata['userid']) ? $mdata['userid'] : 0);


      // building the new message
      $newmessage = array(
          'message_id'=> $mdata['id']+$max_id,
          'forum_id'  => $CONVERT['forum_id'],
          'datestamp' => $mdata['unixtime'],
          'thread'    => ($mdata['thread']+$max_id),
          'parent_id' => $parentid,
          'author'    => $mdata['author'],
          'subject'   => $mdata['subject'],
          'email'     => $mdata['email'],
          'ip'        => $mdata['host'],
          'user_id'   => $userid,
          'moderator_post' => 0,
          'status'    => $post_status,
          'sort'      => $post_sort,
          'msgid'     => $mdata['msgid'],
          'closed'    => $closed,
          'body'      => $mdata['body']
      );

      if($add_signature) {
          $newmessage["meta"]["show_signature"]=1;
      }
      if(isset($mdata['viewcount'])) {
          $newmessage['viewcount']=$mdata['viewcount'];
      }
      $newmessage['viewcount'] = (isset($mdata['viewcount']) ? $mdata['viewcount'] : 0);
      // converting attachments if needed
      $inserted_files=array();
      if (isset($CONVERT['attachments'][$mdata['id']]) && count($CONVERT['attachments'][$mdata['id']])) {
          foreach($CONVERT['attachments'][$mdata['id']] as $attachment) {
              $filename = $CONVERT['attachmentdir']."/".$table_name."/".$attachment['id'].strtolower(strrchr($attachment['filename'], "."));
              if(file_exists($filename) && filesize($filename)>0) {
                  $fp=fopen($filename, "r");
                  $buffer=base64_encode(fread($fp, filesize($filename)));
                  fclose($fp);
                  $file_id = phorum_db_file_save($userid, $attachment['filename'], filesize($filename), $buffer, $newmessage['message_id']);
                  unset($buffer); // free that large buffer
                  $inserted_files[]=array("file_id"=>$file_id, "name"=>$attachment['filename'], "size"=>filesize($filename));
              }
          }
      }
      if(count($inserted_files)) {
          $newmessage["meta"]["attachments"]=$inserted_files;
      }


      return $newmessage;
}

function phorum_convert_selectUsers($link) {
    global $CONVERT;


    // collecting permissions
    $CONVERT['perms'] = phorum_convert_getPermissions($link);

    // selecting the users
    $res=mysql_unbuffered_query("SELECT * FROM ".$CONVERT['forumstable']."_auth ORDER BY id", $link);

    if(mysql_error($link)) {
        return false;
    }

    return $res;
}

function phorum_convert_getNextUser($res) {
    global $CONVERT;

    $userdata=array();
    $userdata=mysql_fetch_assoc($res);
    if(!$userdata) {
        return false;
    }

    unset($userdata['lang']);
    unset($userdata['password_tmp']);
    unset($userdata['combined_token']);
    unset($userdata['max_group_permission_level']);
    unset($userdata['permission_level']);

    $userdata['user_id']=$userdata['id'];
    unset($userdata['id']);

    $userdata['real_name']=$userdata['name'];
    unset($userdata['name']);

    $userdata['active']=1;
    if(isset($CONVERT['perms'][$userdata['user_id']][0])) {
        echo "Setting {$userdata['user_id']} as administrator.{$CONVERT['lbr']}";
        $userdata['admin']=1;
        unset($CONVERT['perms'][$userdata['user_id']][0]);
    }
    if(isset($CONVERT['perms'][$userdata['user_id']])) {
        foreach($CONVERT['perms'][$userdata['user_id']] as $key => $val) {
            echo "Setting {$userdata['user_id']} as moderator for forum $key.{$CONVERT['lbr']}";
            $userdata['forum_permissions'][$key] = 0 | PHORUM_USER_ALLOW_READ | PHORUM_USER_ALLOW_NEW_TOPIC | PHORUM_USER_ALLOW_REPLY | PHORUM_USER_ALLOW_EDIT | PHORUM_USER_ALLOW_MODERATE_MESSAGES;
        }
    }
    // set the date_added and active to current time
    $userdata["date_added"]=time();
    $userdata["date_last_active"]=time();

    return $userdata;

}

function phorum_convert_getUserGroups($link) {
    GLOBAL $CONVERT;

    $res=mysql_unbuffered_query("SELECT * FROM ".$CONVERT['forumstable']."_user2group",$link);

    if(mysql_error()) {
       echo "No user2group-table found? : ".mysql_error().$CONVERT['lbr'];
       return false;
    }
    $groups=array();

    while($row=mysql_fetch_array($res)) {
         $groups[$row['user_id']][$row['group_id']]=PHORUM_USER_GROUP_APPROVED;
    }

    return $groups;
}

function phorum_convert_getPermissions($link) {
    GLOBAL $CONVERT;

    $res=mysql_unbuffered_query("SELECT * FROM ".$CONVERT['forumstable']."_moderators",$link);

    if(mysql_error()) {
       echo "No moderators-table found? : ".mysql_error().$CONVERT['lbr'];
       return false;
    }

    while($row=mysql_fetch_array($res)) {
         $perms[$row['user_id']][$row['forum_id']]=true;
    }
    return $perms;
}

?>
