<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2005  Phorum Development Team                              //
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

// cvs-info: $Id: postgresql.php 938 2006-03-14 19:51:50Z mmakaay $

if (!defined("PHORUM")) return;

/**
 * The other Phorum code does not care how the messages are stored.
 *    The only requirement is that they are returned from these functions
 *    in the right way.  This means each database can use as many or as
 *    few tables as it likes.  It can store the fields anyway it wants.
 *    The only thing to worry about is the table_prefix for the tables.
 *    all tables for a Phorum install should be prefixed with the
 *    table_prefix that will be entered in include/db/config.php.  This
 *    will allow multiple Phorum installations to use the same database.
 */

/**
 * These are the table names used for this database system.
 */

// tables needed to be "partitioned"
$PHORUM["message_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_messages";
$PHORUM["user_newflags_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_user_newflags";
$PHORUM["subscribers_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_subscribers";
$PHORUM["files_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_files";
$PHORUM["search_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_search";

// tables common to all "partitions"
$PHORUM["settings_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_settings";
$PHORUM["forums_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_forums";
$PHORUM["user_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_users";
$PHORUM["user_permissions_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_user_permissions";
$PHORUM["groups_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_groups";
$PHORUM["forum_group_xref_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_forum_group_xref";
$PHORUM["user_group_xref_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_user_group_xref";
$PHORUM['user_custom_fields_table'] = "{$PHORUM['DBCONFIG']['table_prefix']}_user_custom_fields";
$PHORUM["banlist_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_banlists";
$PHORUM["pm_messages_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_pm_messages";
$PHORUM["pm_folders_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_pm_folders";
$PHORUM["pm_xref_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_pm_xref";
$PHORUM["pm_buddies_table"] = "{$PHORUM['DBCONFIG']['table_prefix']}_pm_buddies";
/*
* fields which are always strings, even if they contain only numbers
* used in post-message and update-message, otherwise strange things happen
*/
$PHORUM['string_fields']= array('author', 'subject', 'body', 'email');

/* A piece of SQL code that can be used for identifying moved messages. */
define('PHORUM_SQL_MOVEDMESSAGES', '(parent_id = 0 and thread != message_id)');

/**
 * This function executes a query to select the visible messages from
 * the database for a given page offset. The main Phorum code handles
 * actually sorting the threads into a threaded list if needed.
 *
 * By default, the message body is not included in the fetch queries.
 * If the body is needed in the thread list, $PHORUM['TMP']['bodies_in_list']
 * must be set to "1" (for example using setting.tpl).
 *
 * NOTE: ALL dates should be returned as Unix timestamps
 *
 * @param offset - the index of the page to return, starting with 0
 * @param messages - an array containing forum messages
 */

function phorum_db_get_thread_list($offset)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($offset, "int");

    $conn = phorum_db_postgresql_connect();

    $table = $PHORUM["message_table"];

    // The messagefields that we want to fetch from the database.
    $messagefields =
       "$table.author,
        $table.datestamp,
        $table.email,
        $table.message_id,
        $table.meta,
        $table.moderator_post,
        $table.modifystamp,
        $table.parent_id,
        $table.sort,
        $table.status,
        $table.subject,
        $table.thread,
        $table.thread_count,
        $table.user_id,
        $table.viewcount,
        CASE WHEN $table.closed THEN 'TRUE' ELSE 'FALSE' END as closed";

    if (isset($PHORUM['TMP']['bodies_in_list']) && $PHORUM['TMP']['bodies_in_list'] == 1) {
        $messagefields .= "\n,$table.body\n,$table.ip";
    }

    // The sort mechanism to use.
    if($PHORUM["float_to_top"]){
            $sortfield = "modifystamp";
            $index = "list_page_float";
    } else{
            $sortfield = "thread";
            $index = "list_page_flat";
    }

    // Initialize the return array.
    $messages = array();

    // The groups of messages we want to fetch from the database.
    $groups = array();
    if ($offset == 0) $groups[] = "specials";
    $groups[] = "threads";
    if ($PHORUM["threaded_list"]) $groups[] = "replies";

    // for remembering message ids for which we want to fetch the replies.
    $replymsgids = array();

    // Process all groups.
    foreach ($groups as $group) {


        $sql = NULL;

        switch ($group) {

            // Announcements and stickies.
            case "specials":

                $sql = "select $messagefields
                       from $table
                       where
                         status=".PHORUM_STATUS_APPROVED." and
                         ((parent_id=0 and sort=".PHORUM_SORT_ANNOUNCEMENT."
                           and forum_id={$PHORUM['vroot']})
                         or
                         (parent_id=0 and sort=".PHORUM_SORT_STICKY."
                          and forum_id={$PHORUM['forum_id']}))
                       order by
                         sort, $sortfield desc";
                break;

            // Threads.
            case "threads":

                if ($PHORUM["threaded_list"]) {
                    $limit = $PHORUM['list_length_threaded'];
                    $extrasql = '';
                } else {
                    $limit = $PHORUM['list_length_flat'];
                }
                $start = $offset * $limit;

                $sql = "select $messagefields
                        from $table
                        where
                          $sortfield > 0 and
                          forum_id = {$PHORUM["forum_id"]} and
                          status = ".PHORUM_STATUS_APPROVED." and
                          parent_id = 0 and
                          sort > 1
                        order by
                          $sortfield desc
                        LIMIT $limit OFFSET $start";
                break;

            // Reply messages.
            case "replies":

                // We're done if we did not collect any messages with replies.
                if (! count($replymsgids)) break;

                $sortorder = "sort, $sortfield desc, message_id";
                if(isset($PHORUM["reverse_threading"]) && $PHORUM["reverse_threading"])
                    $sortorder.=" desc";

                $sql = "select $messagefields
                        from $table
                        where
                          status = ".PHORUM_STATUS_APPROVED." and
                          thread in (" . implode(",",$replymsgids) .")
                        order by $sortorder";
                break;

        } // End of switch ($group)

        // Continue with the next group if no SQL query was formulated.
        if (is_null($sql)) continue;

        // Fetch the messages for the current group.
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        $rows = pg_num_rows($res);
        if($rows > 0){
            while ($rec = pg_fetch_assoc($res)){
                $messages[$rec["message_id"]] = $rec;
                $messages[$rec["message_id"]]["meta"] = array();
                if(!empty($rec["meta"])){
                    $messages[$rec["message_id"]]["meta"] = unserialize($rec["meta"]);
                }

                // We need the message ids for fetching reply messages.
                if ($group == 'threads' && $rec["thread_count"] > 1) {
                    $replymsgids[] = $rec["message_id"];
                }
            }
        }
    }

    return $messages;
}

/**
 * This function executes a query to get the recent messages for
 * all forums the user can read, a particular forum, or a particular
 * thread, and and returns an array of the messages order by message_id.
 * You can optionally retrieve only new threads.
 *
 * The original version of this function came from Jim Winstead of mysql.com
 */
function phorum_db_get_recent_messages($count, $forum_id = 0, $thread = 0, $threads_only = 0)
{
    $PHORUM = $GLOBALS["PHORUM"];
    settype($count, "int");
    settype($forum_id, "int");
    settype($thread, "int");
    $arr = array();

    $conn = phorum_db_postgresql_connect();

    // we need to differentiate on which key to use
    // last_post_time is for sort by modifystamp
    // forum_max_message is for sort by message-id
    if($threads_only) {
        $use_key='last_post_time';
    } else {
        $use_key='post_count';
    }

    $sql = "SELECT {$PHORUM['message_table']}.* FROM {$PHORUM['message_table']} WHERE status=".PHORUM_STATUS_APPROVED;

    // have to check what forums they can read first.
    // even if $thread is passed, we have to make sure
    // the user can read the forum
    if($forum_id <= 0) {
        $allowed_forums=phorum_user_access_list(PHORUM_USER_ALLOW_READ);

        // if they are not allowed to see any forums, return the emtpy $arr;
        if(empty($allowed_forums))
            return $arr;
    } else {
        // only single forum, *much* fast this way
        if(!phorum_user_access_allowed(PHORUM_USER_ALLOW_READ,$forum_id)) {
            return $arr;
        }
    }

    if($forum_id > 0){
        $sql.=" and forum_id=$forum_id";
    } else {
        $sql.=" and forum_id in (".implode(",", $allowed_forums).")";
    }

    if($thread){
        $sql.=" and thread=$thread";
    }

    if($threads_only) {
        $sql.= " and parent_id = 0";
        $sql.= " ORDER BY thread DESC";
    } else {
        $sql.= " ORDER BY message_id DESC";
    }

    if($count){
        $sql.= " LIMIT $count";
    }

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    while ($rec = pg_fetch_assoc($res)){
        $arr[$rec["message_id"]] = $rec;
		$arr[$rec["message_id"]]["closed"] = $arr[$rec["message_id"]]["closed"] === 't' ? TRUE : FALSE;

        // convert meta field
        if(empty($rec["meta"])){
            $arr[$rec["message_id"]]["meta"]=array();
        } else {
            $arr[$rec["message_id"]]["meta"]=unserialize($rec["meta"]);
        }
        if(empty($arr['users'])) $arr['users']=array();
        if($rec["user_id"]){
            $arr['users'][]=$rec["user_id"];
        }

    }

    return $arr;
}


/**
 * This function executes a query to select messages from the database
 * and returns an array.  The main Phorum code handles actually sorting
 * the threads into a threaded list if needed.
 *
 * NOTE: ALL dates should be returned as Unix timestamps
 * @param forum - the forum id to work with. Omit or NULL for all forums.
 *                You can also pass an array of forum_id's.
 * @param waiting_only - only take into account messages which have to
 *                be approved directly after posting. Do not include
 *                messages which are hidden by a moderator.
 */

function phorum_db_get_unapproved_list($forum = NULL, $waiting_only=false,$moddays=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $table = $PHORUM["message_table"];

    $arr = array();

    $sql = "select
            $table.*
          from
            $table ";

    if (is_array($forum)){
        $sql .= "where forum_id in (" . implode(",", $forum) . ") and ";
    } elseif (! is_null($forum)){
        settype($forum, "int");
        $sql .= "where forum_id = $forum and ";
    } else {
        $sql .= "where ";
    }

    if($moddays > 0) {
        $checktime=time()-(86400*$moddays);
        $sql .=" datestamp > $checktime AND";
    }

    if($waiting_only){
        $sql.=" status=".PHORUM_STATUS_HOLD;
    } else {
        $sql="($sql status=".PHORUM_STATUS_HOLD.") " .
             "union ($sql status=".PHORUM_STATUS_HIDDEN.")";
    }


    $sql .=" order by thread, message_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    while ($rec = pg_fetch_assoc($res)){
        $arr[$rec["message_id"]] = $rec;
		$arr[$rec["message_id"]]["closed"] = $arr[$rec["message_id"]]["closed"] === 't' ? TRUE : FALSE;
        $arr[$rec["message_id"]]["meta"] = array();
        if(!empty($rec["meta"])){
            $arr[$rec["message_id"]]["meta"] = unserialize($rec["meta"]);
        }
    }

    return $arr;
}


/**
 * This function posts a message to the tables.
 * The message is passed by reference and message_id and thread are filled
 */

function phorum_db_post_message(&$message,$convert=false){
    $PHORUM = $GLOBALS["PHORUM"];
    $table = $PHORUM["message_table"];

    $conn = phorum_db_postgresql_connect();

    $success = false;

    foreach($message as $key => $value){
        if (is_numeric($value) && !in_array($key,$PHORUM['string_fields'])){
            $message[$key] = (int)$value;
        } elseif(is_array($value)) {
            $message[$key] = pg_escape_string(serialize($value));
        } else{
            $message[$key] = pg_escape_string($value);
        }
    }

    if(!$convert) {
        $NOW = time();
    } else {
        $NOW = $message['datestamp'];
    }

    // duplicate-check
    if(isset($PHORUM['check_duplicate']) && $PHORUM['check_duplicate'] && !$convert) {
        // we check for dupes in that number of minutes
        $check_minutes=60;
        $check_timestamp =$NOW - ($check_minutes*60);
        // check_query
        $chk_query="SELECT message_id FROM $table WHERE forum_id = {$message['forum_id']} AND author='{$message['author']}' AND subject='{$message['subject']}' AND body='{$message['body']}' AND datestamp > $check_timestamp";
       $res = pg_query($conn, $chk_query);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $chk_query");
        if(pg_num_rows($res))
            return 0;
    }

	$columns = 'forum_id, datestamp, thread, parent_id, author, subject, email, ip, user_id, moderator_post, status, sort, msgid, body, closed';
	$values  = "{$message['forum_id']},
            $NOW,
            {$message['thread']},
            {$message['parent_id']},
            '{$message['author']}',
            '{$message['subject']}',
            '{$message['email']}',
            '{$message['ip']}',
            {$message['user_id']},
            {$message['moderator_post']},
            {$message['status']},
            {$message['sort']},
            '{$message['msgid']}',
            '{$message['body']}',
            " . ($message['closed'] ? 'TRUE' : 'FALSE');

    if (isset($message['meta'])){
		$columns .= ', meta';
		$values  .= ", '" . $message['meta'] . "'";
    }

    // if in conversion we need the message-id too
    if ($convert && isset($message['message_id'])) {
		$columns .= ', message_id';
		$values  .= ', ' . $message['message_id'];
    }

    if (isset($message['modifystamp'])) {
		$columns .= ', modifystamp';
        $values  .= ', ' . $message['modifystamp'];
    }

    if (isset($message['viewcount'])) {
		$columns .= ', viewcount';
        $values  .= ', ' . $message['viewcount'];
    }

    $sql = "INSERT INTO $table ($columns) values ($values)";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if ($res){
        $message["message_id"] = pgsql_insert_id($conn, "{$table}_message_id_seq");

        if(!empty($message["message_id"])){

            $message["datestamp"]=$NOW;

            if ($message["thread"] == 0){
                $message["thread"] = $message["message_id"];
                $sql = "update $table set thread={$message['message_id']} where message_id={$message['message_id']}";
                $res = pg_query($conn, $sql);
                if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
            }

            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            // start ft-search stuff
            $search_text="$message[author] | $message[subject] | $message[body]";
            $sql="insert into {$PHORUM['search_table']} (message_id, forum_id, search_text) values ({$message['message_id']}, {$message['forum_id']}, '$search_text')";
            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            // end ft-search stuff

            $success = true;
            // some data for later use, i.e. email-notification
            $GLOBALS['PHORUM']['post_returns']['message_id']=$message["message_id"];
            $GLOBALS['PHORUM']['post_returns']['thread_id']=$message["thread"];
        }
    }

    return $success;
}

/**
 * This function deletes messages from the messages table.
 *
 * @param message $ _id the id of the message which should be deleted
 * mode the mode of deletion, PHORUM_DELETE_MESSAGE for reconnecting the children, PHORUM_DELETE_TREE for deleting the children
 */

function phorum_db_delete_message($message_id, $mode = PHORUM_DELETE_MESSAGE)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($message_id, "int");

#    // lock the table so we don't leave orphans.
#    pg_query($conn, "LOCK TABLES {$PHORUM['message_table']} WRITE");

    $threadset = 0;
    // get the parents of the message to delete.
    $sql = "select forum_id, message_id, thread, parent_id from {$PHORUM['message_table']} where message_id = $message_id ";
    $res = pg_query($conn, $sql);
    $rec = pg_fetch_assoc($res);

    if($mode == PHORUM_DELETE_TREE){
        $mids = phorum_db_get_messagetree($message_id, $rec['forum_id']);
    }else{
        $mids = $message_id;
    }

    $thread = $rec['thread'];
    if($thread == $message_id && $mode == PHORUM_DELETE_TREE){
        $threadset = 1;
    }else{
        $threadset = 0;
    }

    if($mode == PHORUM_DELETE_MESSAGE){
        $count = 1;
        // change the children to point to their parent's parent
        // forum_id is in here for speed by using a key only
        $sql = "update {$PHORUM['message_table']} set parent_id=$rec[parent_id] where forum_id=$rec[forum_id] and parent_id=$rec[message_id]";
        pg_query($conn, $sql);
    }else{
        $count = count(explode(",", $mids));
    }

    // delete the messages
    $sql = "delete from {$PHORUM['message_table']} where message_id in ($mids)";
    pg_query($conn, $sql);

#    // clear the lock
#    pg_query($conn, "UNLOCK TABLES");

    // start ft-search stuff
    $sql="delete from {$PHORUM['search_table']} where message_id in ($mids)";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    // end ft-search stuff

    // it kind of sucks to have this here, but it is the best way
    // to ensure that it gets done if stuff is deleted.
    // leave this include here, it needs to be conditional
    include_once("./include/thread_info.php");
    phorum_update_thread_info($thread);

    // we need to delete the subscriptions for that thread too
    $sql = "DELETE FROM {$PHORUM['subscribers_table']} WHERE forum_id > 0 AND thread=$thread";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // this function will be slow with a lot of messages.
    phorum_db_update_forum_stats(true);

    return explode(",", $mids);
}

/**
 * gets all attached messages to a message
 *
 * @param id $ id of the message
 */
function phorum_db_get_messagetree($parent_id, $forum_id){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($parent_id, "int");
    settype($forum_id, "int");

    $conn = phorum_db_postgresql_connect();

    $sql = "Select message_id from {$PHORUM['message_table']} where forum_id=$forum_id and parent_id=$parent_id";

    $res = pg_query($conn, $sql);

    $tree = "$parent_id";

    while($rec = pg_fetch_row($res)){
        $tree .= "," . phorum_db_get_messagetree($rec[0],$forum_id);
    }

    return $tree;
}

/**
 * This function updates the message given in the $message array for
 * the row with the given message id.  It returns non 0 on success.
 */

function phorum_db_update_message($message_id, $message)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($message_id, "int");

    if (count($message) > 0){
        $conn = phorum_db_postgresql_connect();
/*
echo '<pre>';
var_dump($message);
echo '</pre>';

echo '<pre>';
var_dump($PHORUM['string_fields']);
echo '</pre>';
*/

        foreach($message as $field => $value){
            if (is_numeric($value) && !in_array($field,$PHORUM['string_fields'])){
                $fields[] = "$field=$value";
            } elseif (is_array($value)){
                $value = pg_escape_string(serialize($value));
                $fields[] = "$field='$value'";
                $message[$field] = $value;
			} elseif (is_bool($value)) {
                $value = ($value ? 'TRUE' : 'FALSE');
				$fields[] = "$field=$value";
                $message[$field] = $value;
            } else {
                $value = pg_escape_string($value);
                $fields[] = "$field='$value'";
                $message[$field] = $value;
            }
        }

        $sql = "update {$PHORUM['message_table']} set " . implode(", ", $fields) . " where message_id=$message_id";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        if($res){
            // start ft-search stuff
            if(isset($message["author"]) && isset($message["subject"]) && isset($message["body"])){
                $search_text="$message[author] | $message[subject] | $message[body]";
                $sql="UPDATE {$PHORUM['search_table']} set forum_id={$message['forum_id']}, search_text='$search_text' WHERE message_id={$message_id}";
                $res = pg_query($conn, $sql);
				if (pg_affected_rows($res) == 0) {
		            $sql = "INSERT INTO {$PHORUM['search_table']} (message_id, forum_id, search_text) values ({$message_id}, {$message['forum_id']}, '$search_text')";
    		        $res = pg_query($conn, $sql);
				}
                if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
            }
            // end ft-search stuff
        }

        return ($res > 0) ? true : false;

    }else{
        trigger_error("\$message cannot be empty in phorum_update_message()", E_USER_ERROR);
    }
}


/**
 * This function executes a query to get the row with the given value
 * in the given field and returns the message in an array.
 */

function phorum_db_get_message($value, $field="message_id", $ignore_forum_id=false)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $field=pg_escape_string($field);
    $multiple=false;

    $conn = phorum_db_postgresql_connect();


    $forum_id_check = "";
    if (!$ignore_forum_id && !empty($PHORUM["forum_id"])){
        $forum_id_check = "(forum_id = {$PHORUM['forum_id']} OR forum_id={$PHORUM['vroot']}) and";
    }

    if(is_array($value)) {
        $checkvar="$field IN('".implode("','",$value)."')";
        $multiple=true;
    } else {
        $value=pg_escape_string($value);
        $checkvar="$field='$value'";
    }


    $sql = "select {$PHORUM['message_table']}.* from {$PHORUM['message_table']} where $forum_id_check $checkvar";
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $ret = $multiple ? array() : NULL;

    if(pg_num_rows($res)){
        if($multiple) {
            while($rec=pg_fetch_assoc($res)) {
                // convert meta field
                if(empty($rec["meta"])){
                    $rec["meta"]=array();
                } else {
                    $rec["meta"]=unserialize($rec["meta"]);
                }
                $ret[$rec['message_id']]=$rec;
				$ret[$rec['message_id']]['closed'] = $ret[$rec['message_id']]['closed'] === 't' ? TRUE : FALSE;
            }
        } else {
            $rec = pg_fetch_assoc($res);

            // convert meta field
            if(empty($rec["meta"])){
                $rec["meta"]=array();
            } else {
                $rec["meta"]=unserialize($rec["meta"]);
            }
            $ret=$rec;
			$ret['closed'] = $ret['closed'] === 't' ? TRUE : FALSE;
        }
    }

    return $ret;
}

/**
 * This function executes a query to get the rows with the given thread
 * id and returns an array of the message.
 */
function phorum_db_get_messages($thread,$page=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($thread, "int");

    $conn = phorum_db_postgresql_connect();

    $forum_id_check = "";
    if (!empty($PHORUM["forum_id"])){
        $forum_id_check = "(forum_id = {$PHORUM['forum_id']} OR forum_id={$PHORUM['vroot']}) and";
    }

    // are we really allowed to show this thread/message?
    $approvedval = "";
    if(!phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) {
        $approvedval="AND {$PHORUM['message_table']}.status =".PHORUM_STATUS_APPROVED;
    }

    if($page > 0) {
           $start=$PHORUM["read_length"]*($page-1);
           $sql = "select {$PHORUM['message_table']}.* from {$PHORUM['message_table']} where $forum_id_check thread=$thread $approvedval order by message_id LIMIT " . $PHORUM["read_length"] . " OFFSET $start";
    } else {
           $sql = "select {$PHORUM['message_table']}.* from {$PHORUM['message_table']} where $forum_id_check thread=$thread $approvedval order by message_id";
           if(isset($PHORUM["reverse_threading"]) && $PHORUM["reverse_threading"]) $sql.=" desc";
    }

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $arr = array();

    while ($rec = pg_fetch_assoc($res)){
        $arr[$rec["message_id"]] = $rec;
        $arr[$rec["message_id"]]['closed'] = $arr[$rec["message_id"]]['closed'] == 't' ? TRUE : FALSE;

        // convert meta field
        if(empty($rec["meta"])){
            $arr[$rec["message_id"]]["meta"]=array();
        } else {
            $arr[$rec["message_id"]]["meta"]=unserialize($rec["meta"]);
        }
        if(empty($arr['users'])) $arr['users']=array();
        if($rec["user_id"]){
            $arr['users'][]=$rec["user_id"];
        }

    }

    if(count($arr) && $page != 0) {
        // selecting the thread-starter
        $sql = "select {$PHORUM['message_table']}.* from {$PHORUM['message_table']} where $forum_id_check message_id=$thread $approvedval";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        if(pg_num_rows($res) > 0) {
            $rec = pg_fetch_assoc($res);
            $arr[$rec["message_id"]] = $rec;
	        $arr[$rec["message_id"]]['closed'] = $arr[$rec["message_id"]]['closed'] == 't' ? TRUE : FALSE;
            $arr[$rec["message_id"]]["meta"]=unserialize($rec["meta"]);
        }
    }
    return $arr;
}

/**
 * this function returns the index of a message in a thread
 */
function phorum_db_get_message_index($thread=0,$message_id=0) {
    $PHORUM = $GLOBALS["PHORUM"];

    // check for valid values
    if(empty($message_id) || empty($message_id)) {
        return 0;
    }

    settype($thread, "int");
    settype($message_id, "int");

    $approvedval="";
    $forum_id_check="";

    $conn = phorum_db_postgresql_connect();

    if (!empty($PHORUM["forum_id"])){
        $forum_id_check = "(forum_id = {$PHORUM['forum_id']} OR forum_id={$PHORUM['vroot']}) AND";
    }

    if(!phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES)) {
        $approvedval="AND {$PHORUM['message_table']}.status =".PHORUM_STATUS_APPROVED;
    }

    $sql = "select count(*) as msg_index from {$PHORUM['message_table']} where $forum_id_check thread=$thread $approvedval AND message_id <= $message_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $rec = pg_fetch_assoc($res);

    return $rec['msg_index'];
}

/**
 * This function searches the database for the supplied search
 * criteria and returns an array with two elements.  One is the count
 * of total messages that matched, the second is an array of the
 * messages from the results based on the $start (0 base) given and
 * the $length given.
 */

function phorum_db_search($search, $offset, $length, $match_type, $match_date, $match_forum)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $start = $offset * $PHORUM["list_length"];

    $arr = array("count" => 0, "rows" => array());

    $conn = phorum_db_postgresql_connect();

    // have to check what forums they can read first.
    $allowed_forums=phorum_user_access_list(PHORUM_USER_ALLOW_READ);
    // if they are not allowed to search any forums, return the emtpy $arr;
    if(empty($allowed_forums) || ($PHORUM['forum_id']>0 && !in_array($PHORUM['forum_id'], $allowed_forums)) ) return $arr;

    // Add forum 0 (for announcements) to the allowed forums.
    $allowed_forums[] = 0;

    if($PHORUM['forum_id']!=0 && $match_forum!="ALL"){
        $forum_where=" and forum_id={$PHORUM['forum_id']}";
    } else {
        $forum_where=" and forum_id in (".implode(",", $allowed_forums).")";
    }

    if($match_type=="AUTHOR"){

        $id_table=$PHORUM['search_table']."_auth_".md5(microtime());

        $search = pg_escape_string($search);

        $sql = "SELECT message_id INTO $id_table FROM {$PHORUM['message_table']} WHERE author = '$search' $forum_where";
        if ($match_date > 0 ){
            $ts  = time() - 86400 * $match_date;
            $sql.=" and datestamp >= $ts";
        }

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $sql = "ALTER TABLE $id_table ADD PRIMARY KEY (message_id)";
        $res = pg_query($conn, $sql);
   	    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    } else {

        if($match_type=="PHRASE"){
            $terms = array('"'.$search.'"');
        } else {
            $quote_terms=array();
            if ( strstr( $search, '"' ) ){
                //first pull out all the double quoted strings (e.g. '"iMac DV" or -"iMac DV"')
                preg_match_all( '/-*"(.*?)"/', $search, $match );
                $search = preg_replace( '/-*".*?"/', '', $search );
                $quote_terms = $match[0];
//                $quote_terms = preg_replace( '/"/', '', $match[0] );
            }

            //finally pull out the rest words in the string
            $terms = preg_split( "/\s+/", $search, 0, PREG_SPLIT_NO_EMPTY );

            //merge them all together and return
            $terms = array_merge( $terms, $quote_terms);
        }

        if(count($terms)){

            $use_key="";
            $extra_where="";

            /* using this code on larger forums has shown to make the search faster.
               However, on smaller forums, it does not appear to help and in fact
               appears to slow down searches.

            if($match_date){
                $min_time=time()-86400*$match_date;
                $sql="select min(message_id) as min_id from {$PHORUM['message_table']} where datestamp>=$min_time";
                $res=pg_query($conn, $sql);
                if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
                $min_id=pg_fetch_result($res, 0, "min_id");
#                $use_key=" use key (primary)";
                $extra_where="and message_id>=$min_id";
            }
            */

            $id_table=$PHORUM['search_table']."_ft_".md5(microtime());

            if($PHORUM["DBCONFIG"]["mysql_use_ft"]){

                if($match_type=="ALL" && count($terms)>1){
                    $against="+".pg_escape_string(implode(" +", $terms));
                } else {
                    $against=pg_escape_string(implode(" ", $terms));
                }

                $clause="MATCH (search_text) AGAINST ('$against' IN BOOLEAN MODE)";

            } else {

                if($match_type=="ALL"){
                    $conj="and";
                } else {
                    $conj="or";
                }

                foreach($terms as $id => $term) {
                    $terms[$id] = pg_escape_string($term);
                }

                $clause = "( search_text like '%".implode("%' $conj search_text like '%", $terms)."%' )";

            }

            $sql = "SELECT message_id INTO $id_table from {$PHORUM['search_table']} WHERE $clause $extra_where";
            $res = pg_query($conn, $sql);	# was mysql_unbuffered_query
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            $sql = "ALTER TABLE $id_table ADD PRIMARY KEY (message_id)";
            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        }
    }


    if (isset($id_table)) {

        // create a temporary table of the messages we want
        $table = $PHORUM['search_table']."_".md5(microtime());
        $sql   = "SELECT {$PHORUM['message_table']}.message_id, {$PHORUM['message_table']}.datestamp, status, forum_id INTO $table FROM {$PHORUM['message_table']} inner join $id_table using (message_id) where status = " . PHORUM_STATUS_APPROVED . " $forum_where";

        if ($match_date > 0) {
            $ts  = time() - 86400 * $match_date;
            $sql.=" and datestamp >= $ts";
        }

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $sql = "ALTER TABLE $table ADD PRIMARY KEY (forum_id, status, datestamp)";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $sql="select count(*) as count from $table";
        $res = pg_query($conn, $sql);

        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        $total_count=pg_fetch_result($res, 0, 0);

        $sql = "select message_id from $table order by datestamp desc limit $length offset $start";
        $res = pg_query($conn, $sql);   # was unbuffered

        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $idstring="";
        while ($rec = pg_fetch_row($res)){
            $idstring.="$rec[0],";
        }
        $idstring=substr($idstring, 0, -1);

        if($idstring){
            $sql = "select * from {$PHORUM['message_table']} where message_id in ($idstring) order by datestamp desc";
            $res = pg_query($conn, $sql);  # was unbuffered

            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            $rows = array();

            while ($rec = pg_fetch_assoc($res)){
                $rows[$rec["message_id"]] = $rec;
		        $rows[$rec["message_id"]]['closed'] = $rows[$rec["message_id"]]['closed'] == 't' ? TRUE : FALSE;
            }

            $arr = array("count" => $total_count, "rows" => $rows);
        }
    }

    return $arr;
}

/**
 * This function returns the closest thread that is greater than $thread
 */

function phorum_db_get_newer_thread($key){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($key, "int");

    $conn = phorum_db_postgresql_connect();

    $keyfield = ($PHORUM["float_to_top"]) ? "modifystamp" : "thread";

    // are we really allowed to show this thread/message?
    $approvedval = "";
    if(!phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES) && $PHORUM["moderation"] == PHORUM_MODERATE_ON) {
        $approvedval="AND {$PHORUM['message_table']}.status =".PHORUM_STATUS_APPROVED;
    } else {
        $approvedval="AND {$PHORUM['message_table']}.parent_id = 0";
    }

    $sql = "select thread from {$PHORUM['message_table']} where forum_id={$PHORUM['forum_id']} $approvedval and $keyfield>$key order by $keyfield limit 1";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return (pg_num_rows($res)) ? pg_fetch_result($res, 0, "thread") : 0;
}

/**
 * This function returns the closest thread that is less than $thread
 */

function phorum_db_get_older_thread($key){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($key, "int");

    $conn = phorum_db_postgresql_connect();

    $keyfield = ($PHORUM["float_to_top"]) ? "modifystamp" : "thread";
    // are we really allowed to show this thread/message?
    $approvedval = "";
    if(!phorum_user_access_allowed(PHORUM_USER_ALLOW_MODERATE_MESSAGES) && $PHORUM["moderation"] == PHORUM_MODERATE_ON) {
        $approvedval="AND {$PHORUM['message_table']}.status=".PHORUM_STATUS_APPROVED;
    } else {
        $approvedval="AND {$PHORUM['message_table']}.parent_id = 0";
    }

    $sql = "select thread from {$PHORUM['message_table']} where forum_id={$PHORUM['forum_id']}  $approvedval and $keyfield<$key order by $keyfield desc limit 1";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return (pg_num_rows($res)) ? pg_fetch_result($res, 0, "thread") : 0;
}

/**
 * This function executes a query to get bad items of type $type and
 * returns an array of the results.
 */

function phorum_db_load_settings(){
    global $PHORUM;


    $conn = phorum_db_postgresql_connect();

    $sql = "select * from {$PHORUM['settings_table']}";

    $res = pg_query($conn, $sql);
    if (!$res && !defined("PHORUM_ADMIN")){
		$err = pg_last_error($conn);
		$looking_for = 'relation "' . $PHORUM['settings_table'] . '" does not exist';

		$pos = strpos($err, $looking_for);
        if ($pos === FALSE) {
			if ($err) {
            	phorum_db_pg_last_error("$err: $sql");
			}
        } else {
            // settings table does not exist
            return;
        }
    }

    if (empty($err) && $res){
        while ($rec = pg_fetch_assoc($res)){

            // only load the default forum options in the admin
            if($rec["name"]=="default_forum_options" && !defined("PHORUM_ADMIN")) continue;

            if ($rec["type"] == "V"){
                if ($rec["data"] == 'true'){
                    $val = true;
                }elseif ($rec["data"] == 'false'){
                    $val = false;
                }elseif (is_numeric($rec["data"])){
                    $val = $rec["data"];
                }else{
                    $val = "$rec[data]";
                }
            }else{
                $val = unserialize($rec["data"]);
            }

            $PHORUM[$rec['name']]=$val;
            $PHORUM['SETTINGS'][$rec['name']]=$val;
        }
    }
}

/**
 * This function executes a query to get bad items of type $type and
 * returns an array of the results.
 */

function phorum_db_update_settings($settings){
    global $PHORUM;

    if (count($settings) > 0){
        $conn = phorum_db_postgresql_connect();

        foreach($settings as $field => $value){
            if (is_numeric($value)){
                $type = 'V';
            }elseif (is_string($value)){
                $value = pg_escape_string($value);
                $type = 'V';
            }else{
                $value = pg_escape_string(serialize($value));
                $type = 'S';
            }

            $sql = "UPDATE {$PHORUM['settings_table']} set data='$value', type='$type' WHERE name='$field'";
            $res = pg_query($conn, $sql);
			if ($res) {
				if (pg_affected_rows($res) == 0) {
		            $sql = "INSERT INTO {$PHORUM['settings_table']} (name, data, type) values ('$field', '$value', '$type')";
    		        $res = pg_query($conn, $sql);
				}
			}
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        }

        return ($res > 0) ? true : false;
    }else{
        trigger_error("\$settings cannot be empty in phorum_db_update_settings()", E_USER_ERROR);
    }
}

/**
 * This function executes a query to select all forum data from
 * the database for a flat/collapsed display and returns the data in
 * an array.
 */


function phorum_db_get_forums($forum_ids = 0, $parent_id = -1, $vroot = null, $inherit_id = null){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($parent_id, "int");

    $conn = phorum_db_postgresql_connect();

    if (is_array($forum_ids)) {
        $int_ids = array();
        foreach ($forum_ids as $id) {
            settype($id, "int");
            $int_ids[] = $id;
        }
        $forum_ids = implode(",", $int_ids);
    } else {
        settype($forum_ids, "int");
    }

    $sql = "select * from {$PHORUM['forums_table']} ";
    if ($forum_ids){
        $sql .= " where forum_id in ($forum_ids)";
    } elseif ($inherit_id != null) {
        $sql .= " where inherit_id = $inherit_id";
        if(!defined("PHORUM_ADMIN")) $sql.=" and active=1";
    } elseif ($parent_id >= 0) {
        $sql .= " where parent_id = $parent_id";
        if(!defined("PHORUM_ADMIN")) $sql.=" and active=1";
    }  elseif($vroot !== null) {
        $sql .= " where vroot = $vroot";
    } else {
        $sql .= " where forum_id <> 0";
    }

    $sql .= " order by display_order ASC, name";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $forums = array();

    while ($row = pg_fetch_assoc($res)){
        $forums[$row["forum_id"]] = $row;
    }

    return $forums;
}

/**
 * This function updates the forums stats.  If refresh is true, it pulls the
 * numbers from the table.
 */

function phorum_db_update_forum_stats($refresh=false, $msg_count_change=0, $timestamp=0, $thread_count_change=0, $sticky_count_change=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    // always refresh on small forums
    if (isset($PHORUM["message_count"]) && $PHORUM["message_count"]<1000) {
        $refresh=true;
    }

    if($refresh || empty($msg_count_change)){
        $sql = "select count(*) as message_count from {$PHORUM['message_table']} where forum_id={$PHORUM['forum_id']} and status=".PHORUM_STATUS_APPROVED;

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $message_count = (int)pg_fetch_result($res, 0, "message_count");
    } else {
        $message_count="message_count+$msg_count_change";
    }

    if($refresh || empty($timestamp)){

        $sql = "select max(modifystamp) as last_post_time from {$PHORUM['message_table']} where status=".PHORUM_STATUS_APPROVED." and forum_id={$PHORUM['forum_id']}";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $last_post_time = (int)pg_fetch_result($res, 0, "last_post_time");
    } else {

        $last_post_time = $timestamp;
    }

    if($refresh || empty($thread_count_change)){

        $sql = "select count(*) as thread_count from {$PHORUM['message_table']} where forum_id={$PHORUM['forum_id']} and parent_id=0 and status=".PHORUM_STATUS_APPROVED;
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        $thread_count = (int)pg_fetch_result($res, 0, "thread_count");

    } else {

        $thread_count="thread_count+$thread_count_change";
    }

    if($refresh || empty($sticky_count_change)){

        $sql = "select count(*) as sticky_count from {$PHORUM['message_table']} where forum_id={$PHORUM['forum_id']} and sort=".PHORUM_SORT_STICKY." and parent_id=0 and status=".PHORUM_STATUS_APPROVED;
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        $sticky_count = (int)pg_fetch_result($res, 0, "sticky_count");

    } else {

        $sticky_count="sticky_count+$sticky_count_change";
    }

    $sql = "update {$PHORUM['forums_table']} set thread_count=$thread_count, message_count=$message_count, sticky_count=$sticky_count, last_post_time=$last_post_time where forum_id={$PHORUM['forum_id']}";
    pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

}

/**
 * actually moves a thread to the given forum
 */
function phorum_db_move_thread($thread_id, $toforum)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($thread_id, "int");
    settype($toforum, "int");

    if($toforum > 0 && $thread_id > 0){
        $conn = phorum_db_postgresql_connect();
        // retrieving the messages for the newflags and search updates below
        $thread_messages=phorum_db_get_messages($thread_id);

        // just changing the forum-id, simple isn't it?
        $sql = "UPDATE {$PHORUM['message_table']} SET forum_id=$toforum where thread=$thread_id";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        // we need to update the number of posts in the current forum
        phorum_db_update_forum_stats(true);

        // and of the new forum
        $old_id=$GLOBALS["PHORUM"]["forum_id"];
        $GLOBALS["PHORUM"]["forum_id"]=$toforum;
        phorum_db_update_forum_stats(true);
        $GLOBALS["PHORUM"]["forum_id"]=$old_id;

        // move the new-flags and the search records for this thread
        // to the new forum too
        unset($thread_messages['users']);

        $new_newflags=phorum_db_newflag_get_flags($toforum);
        $message_ids = array();
        $delete_ids = array();
        $search_ids = array();
        foreach($thread_messages as $mid => $data) {
            // gather information for updating the newflags
            if($mid > $new_newflags['min_id']) { // only using it if its higher than min_id
                $message_ids[]=$mid;
            } else { // newflags to delete
                $delete_ids[]=$mid;
            }

            // gather the information for updating the search table
            $search_ids[] = $mid;
        }

        if(count($message_ids)) { // we only go in if there are messages ... otherwise an error occured

            $ids_str=implode(",",$message_ids);

            // then doing the update to newflags
            $sql="UPDATE {$PHORUM['user_newflags_table']} SET forum_id = $toforum where message_id IN($ids_str)";
            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            // then doing the update to subscriptions
            $sql="UPDATE {$PHORUM['subscribers_table']} SET forum_id = $toforum where thread IN($ids_str)";
            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        }

        if(count($delete_ids)) {
            $ids_str=implode(",",$delete_ids);
            // then doing the delete
            $sql="DELETE FROM {$PHORUM['user_newflags_table']} where message_id IN($ids_str)";
            pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        }

        if (count($search_ids)) {
            $ids_str = implode(",",$search_ids);
            // then doing the search table update
            $sql = "UPDATE {$PHORUM['search_table']} set forum_id = $toforum where message_id in ($ids_str)";
            pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        }

    }
}

/**
 * closes the given thread
 */
function phorum_db_close_thread($thread_id){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($thread_id, "int");

    if($thread_id > 0){
        $conn = phorum_db_postgresql_connect();

        $sql = "UPDATE {$PHORUM['message_table']} SET closed = TRUE where thread = $thread_id";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }
}

/**
 * (re)opens the given thread
 */
function phorum_db_reopen_thread($thread_id){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($thread_id, "int");

    if($thread_id > 0){
        $conn = phorum_db_postgresql_connect();

        $sql = "UPDATE {$PHORUM['message_table']} SET closed = FALSE where thread = $thread_id";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }
}

/**
 * This function executes a query to insert a forum into the forums
 * table and returns the forums id on success or 0 on failure.
 */

function phorum_db_add_forum($forum)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

	$values  = array();
	$columns = array();

    foreach($forum as $column => $value){
        if (is_numeric($value)){
            $value = (int)$value;
            $values[] = "$value";
        } elseif($value=="NULL") {
            $values[] = "$value";
        }else{
            $value = pg_escape_string($value);
            $values[] = "'$value'";
        }
		$columns[] = $column;
    }

    $sql = "insert into {$PHORUM['forums_table']} (" . implode(', ', $columns) . ") values (" . implode(", ", $values) . ')';

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $forum_id = 0;

    if ($res){
        $forum_id = pgsql_insert_id($conn, "{$PHORUM['forums_table']}_forum_id_seq");
    }

    return $forum_id;
}

/**
 * This function executes a query to remove a forum from the forums
 * table and its messages.
 */

function phorum_db_drop_forum($forum_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");

    $conn = phorum_db_postgresql_connect();

    $tables = array (
        $PHORUM['message_table'],
        $PHORUM['user_permissions_table'],
        $PHORUM['user_newflags_table'],
        $PHORUM['subscribers_table'],
        $PHORUM['forum_group_xref_table'],
        $PHORUM['forums_table'],
        $PHORUM['banlist_table'],
        $PHORUM['search_table']
    );

    foreach($tables as $table){
        $sql = "delete from $table where forum_id=$forum_id";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }

$sql = "select file_id from {$PHORUM['files_table']} left join {$PHORUM['message_table']} using (message_id) where {$PHORUM['files_table']}.message_id > 0 AND link='" . PHORUM_LINK_MESSAGE . "' AND {$PHORUM['message_table']}.message_id is NULL";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    while($rec=pg_fetch_assoc($res)){
        $files[]=$rec["file_id"];
    }
    if(isset($files)){
        $sql = "delete from {$PHORUM['files_table']} where file_id in (".implode(",", $files).")";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }


}

/**
 * This function executes a query to remove a folder from the forums
 * table and change the parent of its children.
 */

function phorum_db_drop_folder($forum_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");

    $conn = phorum_db_postgresql_connect();

    $sql = "select parent_id from {$PHORUM['forums_table']} where forum_id=$forum_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $new_parent_id = pg_fetch_result($res, 0, "parent_id");

    $sql = "update {$PHORUM['forums_table']} set parent_id=$new_parent_id where parent_id=$forum_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $sql = "delete from {$PHORUM['forums_table']} where forum_id=$forum_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
}

/**
 * This function executes a query to update a forum in the forums
 * table and returns non zero on success or 0 on failure.
 */

function phorum_db_update_forum($forum){
    $PHORUM = $GLOBALS["PHORUM"];

    $res = 0;

    if (!empty($forum["forum_id"])){

        // this way we can also update multiple forums at once
        if(is_array($forum["forum_id"])) {
            $forumwhere="forum_id IN (".implode(",",$forum["forum_id"]).")";
        } else {
            $forumwhere="forum_id=".$forum["forum_id"];
        }

        unset($forum["forum_id"]);

        $conn = phorum_db_postgresql_connect();

        foreach($forum as $key => $value){
            if (is_numeric($value)){
                $value = (int)$value;
                $values[] = "$key=$value";
            } elseif($value=="NULL") {
                $values[] = "$key=$value";
            } else {
                $value = pg_escape_string($value);
                $values[] = "$key='$value'";
            }
        }

        $sql = "update {$PHORUM['forums_table']} set " . implode(", ", $values) . " where $forumwhere";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }else{
        trigger_error("\$forum[forum_id] cannot be empty in phorum_update_forum()", E_USER_ERROR);
    }

    return $res;
}

/**
*
*/

function phorum_db_get_groups($group_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $conn = phorum_db_postgresql_connect();

    settype($group_id, "integer");

    $sql="select * from {$PHORUM['groups_table']}";
    if($group_id!=0) $sql.=" where group_id=$group_id";

    $res = pg_query($conn, $sql);

    $groups=array();
    while($rec=pg_fetch_assoc($res)){

        $groups[$rec["group_id"]]=$rec;
        $groups[$rec["group_id"]]["permissions"]=array();
    }

    $sql="select * from {$PHORUM['forum_group_xref_table']}";
    if($group_id!=0) $sql.=" where group_id=$group_id";

    $res = pg_query($conn, $sql);

    while($rec=pg_fetch_assoc($res)){

        $groups[$rec["group_id"]]["permissions"][$rec["forum_id"]]=$rec["permission"];

    }

    return $groups;

}

/**
* Get the members of a group.
* @param group_id - can be an integer (single group), or an array of groups
* @param status - a specific status to look for, defaults to all
* @return array - users (key is userid, value is group membership status)
*/

function phorum_db_get_group_members($group_id, $status = PHORUM_USER_GROUP_REMOVE)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $conn = phorum_db_postgresql_connect();

    if(is_array($group_id)){
        $group_id=implode(",", $group_id);
    } else {
        settype($group_id, "int");
    }

    // this join is only here so that the list of users comes out sorted
    // if phorum_db_user_get() sorts results itself, this join can go away
    $sql="select {$PHORUM['user_group_xref_table']}.user_id, {$PHORUM['user_group_xref_table']}.status from {$PHORUM['user_table']}, {$PHORUM['user_group_xref_table']} where {$PHORUM['user_table']}.user_id = {$PHORUM['user_group_xref_table']}.user_id and group_id in ($group_id)";
    if ($status != PHORUM_USER_GROUP_REMOVE) $sql.=" and {$PHORUM['user_group_xref_table']}.status = $status";
    $sql .=" order by username asc";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    $users=array();
    while($rec=pg_fetch_assoc($res)){
        $users[$rec["user_id"]]=$rec["status"];
    }

    return $users;

}

/**
*
*/

function phorum_db_save_group($group)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $conn = phorum_db_postgresql_connect();

    $ret=false;

    if(isset($group["name"])){
        $sql="update {$PHORUM['groups_table']} set name='{$group['name']}', open={$group['open']} where group_id={$group['group_id']}";

        $res=pg_query($conn, $sql);

        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    }

    if(!$err){

        if(isset($group["permissions"])){
            $sql="delete from {$PHORUM['forum_group_xref_table']} where group_id={$group['group_id']}";

            $res=pg_query($conn, $sql);

            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            foreach($group["permissions"] as $forum_id=>$permission){
                $sql="insert into {$PHORUM['forum_group_xref_table']} (group_id, permission, forum_id) values ({$group['group_id']}, $permission, $forum_id)";
                $res=pg_query($conn, $sql);
                if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
                if(!$res) break;
            }
        }
    }

    if($res>0) $ret=true;

    return $ret;

}

function phorum_db_delete_group($group_id)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $conn = phorum_db_postgresql_connect();

    settype($group_id, "int");

    $sql = "delete from {$PHORUM['groups_table']} where group_id = $group_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // delete things associated with groups
    $sql = "delete from {$PHORUM['user_group_xref_table']} where group_id = $group_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $sql = "delete from {$PHORUM['forum_group_xref_table']} where group_id = $group_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
}

/**
 * phorum_db_add_group()
 *
 * @param $group_name $group_id
 * @return
 **/
function phorum_db_add_group($group_name,$group_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $conn = phorum_db_postgresql_connect();

    settype($group_id, "int");

    if($group_id > 0) { // only used in conversion
        $sql="insert into {$PHORUM['groups_table']} (group_id,name) values ($group_id,'$group_name')";
    } else {
        $sql="insert into {$PHORUM['groups_table']} (name) values ('$group_name')";
    }

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $group_id = 0;

    if ($res) {
        $group_id = pgsql_insert_id($conn, "{$PHORUM['groups_table']}_group_id_seq");
    }

    return $group_id;
}

/**
* This function returns all moderators for a particular forum
*/
function phorum_db_user_get_moderators($forum_id,$ignore_user_perms=false,$for_email=false) {

   $PHORUM = $GLOBALS["PHORUM"];
   $userinfo=array();

   $conn = phorum_db_postgresql_connect();

   settype($forum_id, "int");

   if(!$ignore_user_perms) { // sometimes we just don't need them
       if(!$PHORUM['email_ignore_admin']) {
            $admincheck=" OR U.admin=1";
       } else {
            $admincheck="";
       }


       $sql="SELECT DISTINCT U.user_id, U.email, U.moderation_email FROM {$PHORUM['user_table']} as U LEFT JOIN {$PHORUM['user_permissions_table']} as perm ON perm.user_id=U.user_id WHERE (perm.permission >= ".PHORUM_USER_ALLOW_MODERATE_MESSAGES." AND (perm.permission & ".PHORUM_USER_ALLOW_MODERATE_MESSAGES." > 0) AND perm.forum_id=$forum_id)$admincheck";


       $res = pg_query($conn, $sql);

       if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

       while ($row = pg_fetch_row($res)){
           if(!$for_email || $row[2] == 1)
                $userinfo[$row[0]]=$row[1];
       }

   }

   // get users who belong to groups that have moderator access
   $sql = "SELECT DISTINCT U.user_id, U.email, U.moderation_email FROM {$PHORUM['user_table']} AS U, {$PHORUM['groups_table']} AS groups, {$PHORUM['user_group_xref_table']} AS usergroup, {$PHORUM['forum_group_xref_table']} AS forumgroup WHERE U.user_id = usergroup.user_id AND usergroup.group_id = groups.group_id AND groups.group_id = forumgroup.group_id AND forum_id = $forum_id AND permission & ".PHORUM_USER_ALLOW_MODERATE_MESSAGES." > 0 AND usergroup.status >= ".PHORUM_USER_GROUP_APPROVED;

   $res = pg_query($conn, $sql);

   if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

   while ($row = pg_fetch_row($res)){
       if(!$for_email || $row[2] == 1)
           $userinfo[$row[0]]=$row[1];
   }
   return $userinfo;
}

/**
 * This function executes a query to select data about a user including
 * his permission data and returns that in an array.
 */

function phorum_db_user_get($user_id, $detailed)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if(is_array($user_id)){
        $user_ids=implode(",", $user_id);
    } else {
        $user_ids=(int)$user_id;
    }

    $users = array();

    $sql = "select * from {$PHORUM['user_table']} where user_id in ($user_ids)";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res)){
        while($rec=pg_fetch_assoc($res)){
            $users[$rec["user_id"]] = $rec;
        }

        if ($detailed){
            // get the users' permissions
            $sql = "select * from {$PHORUM['user_permissions_table']} where user_id in ($user_ids)";

            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            while ($row = pg_fetch_assoc($res)){
                $users[$row["user_id"]]["forum_permissions"][$row["forum_id"]] = $row["permission"];
            }

            // get the users' groups and forum permissions through those groups
            $sql = "select user_id, {$PHORUM['user_group_xref_table']}.group_id, forum_id, permission from {$PHORUM['user_group_xref_table']} left join {$PHORUM['forum_group_xref_table']} using (group_id) where user_id in ($user_ids) AND {$PHORUM['user_group_xref_table']}.status >= ".PHORUM_USER_GROUP_APPROVED;

            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

            while ($row = pg_fetch_assoc($res)){
                $users[$row["user_id"]]["groups"][$row["group_id"]] = $row["group_id"];
                if(!empty($row["forum_id"])){
                    if(!isset($users[$row["user_id"]]["group_permissions"][$row["forum_id"]])) {
                         $users[$row["user_id"]]["group_permissions"][$row["forum_id"]] = 0;
                    }
                    $users[$row["user_id"]]["group_permissions"][$row["forum_id"]] = $users[$row["user_id"]]["group_permissions"][$row["forum_id"]] | $row["permission"];
                }
            }

        }
        $sql = "select * from {$PHORUM['user_custom_fields_table']} where user_id in ($user_ids)";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        while ($row = pg_fetch_assoc($res)){
            if(isset($PHORUM["PROFILE_FIELDS"][$row['type']])) {
                if($PHORUM["PROFILE_FIELDS"][$row['type']]['html_disabled']) {
                    $users[$row["user_id"]][$PHORUM["PROFILE_FIELDS"][$row['type']]['name']] = htmlspecialchars($row["data"]);
                } else { // not html-disabled
                    if(substr($row["data"],0,6) == 'P_SER:') {
                        // P_SER (PHORUM_SERIALIZED) is our marker telling this field is serialized
                        $users[$row["user_id"]][$PHORUM["PROFILE_FIELDS"][$row['type']]['name']] = unserialize(substr($row["data"],6));
                    } else {
                        $users[$row["user_id"]][$PHORUM["PROFILE_FIELDS"][$row['type']]['name']] = $row["data"];
                    }
                }
            }
        }

    }

    if(is_array($user_id)){
        return $users;
    } else {
        return isset($users[$user_id]) ? $users[$user_id] : NULL;
    }

}

/*
 * Generic function to retrieve a couple of fields from the user-table
 * for a couple of users or only one of them
 *
 * result is always an array with one or more users in it
 */

function phorum_db_user_get_fields($user_id, $fields)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    // input could be either array or string
    if(is_array($user_id)){
        $user_ids=implode(",", $user_id);
    } else {
        $user_ids=(int)$user_id;
    }


    if(is_array($fields)) {
        $fields_str=implode(",",$fields);
    } else {
        $fields_str=$fields;
    }

    $users = array();



    $sql = "select user_id,$fields_str from {$PHORUM['user_table']} where user_id in ($user_ids)";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res)){
        while($rec=pg_fetch_assoc($res)){
            $users[$rec["user_id"]] = $rec;
        }
    }

    return $users;

}

/**
 * This function gets a list of all the active users.
 * @return array - (key: userid, value: array (username, displayname)
 */
function phorum_db_user_get_list(){
   $PHORUM = $GLOBALS["PHORUM"];

   $conn = phorum_db_postgresql_connect();

   $users = array();
   $sql = "select user_id, username from {$PHORUM['user_table']} order by username asc";
   $res = pg_query($conn, $sql);
   if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

   while ($row = pg_fetch_assoc($res)){
       $users[$row["user_id"]] = array("username" => $row["username"], "displayname" => $row["username"]);
   }

   return $users;
}

/**
 * This function executes a query to select data about a user including
 * his permission data and returns that in an array.
 */

function phorum_db_user_check_pass($username, $password, $temp_password=false){
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $username = pg_escape_string($username);

    $password = pg_escape_string($password);

    $pass_field = ($temp_password) ? "password_temp" : "password";

    $sql = "select user_id from {$PHORUM['user_table']} where username='$username' and $pass_field='$password'";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return ($res && pg_num_rows($res)) ? pg_fetch_result($res, 0, "user_id") : 0;
}

/**
 * This function executes a query to check for the given field in the
 * user tableusername and return the user_id of the user it matches or 0
 * if no match is found.
 *
 * The parameters can be arrays.  If they are, all must be passed and all
 * must have the same number of values.
 *
 * If $return_array is true, an array of all matching rows will be returned.
 * Otherwise, only the first user_id from the results will be returned.
 */

function phorum_db_user_check_field($field, $value, $operator="=", $return_array=false){
    $PHORUM = $GLOBALS["PHORUM"];

    $ret = 0;

    $conn = phorum_db_postgresql_connect();

    if(!is_array($field)){
        $field=array($field);
    }

    if(!is_array($value)){
        $value=array($value);
    }

    if(!is_array($operator)){
        $operator=array($operator);
    }

    foreach($field as $key=>$name){
        $value[$key] = pg_escape_string($value[$key]);
        $clauses[]="$name $operator[$key] '$value[$key]'";
    }

    $sql = "select user_id from {$PHORUM['user_table']} where ".implode(" and ", $clauses);

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if ($res && pg_num_rows($res)){
        if($return_array){
            $ret=array();
            while($row=pg_fetch_assoc($res)){
                $ret[$row["user_id"]]=$row["user_id"];
            }
        } else {
            $ret = pg_fetch_result($res, 0, "user_id");
        }
    }

    return $ret;
}


/**
 * This function executes a query to add the given user data to the
 * database and returns the userid or 0
 */

function phorum_db_user_add($userdata){
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if (isset($userdata["forum_permissions"]) && !empty($userdata["forum_permissions"])){
        $forum_perms = $userdata["forum_permissions"];
        unset($userdata["forum_permissions"]);
    }

    if (isset($userdata["user_data"]) && !empty($userdata["user_data"])){
        $user_data = $userdata["user_data"];
        unset($userdata["user_data"]);
    }


    $sql = "insert into {$PHORUM['user_table']} ";

	$columns = array();
    $values  = array();

    foreach($userdata as $column => $value){
        if (!is_numeric($value)){
            $value = pg_escape_string($value);
            $values[] = "'$value'";
        } else {
            $values[] = "$value";
        }

		$columns[] = $column;
    }

    $sql .= '(' . implode(', ', $columns) . ') values (' . implode(", ", $values) . ')';

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $user_id = 0;
    if ($res){
        $user_id = pgsql_insert_id($conn, "{$PHORUM['user_table']}_user_id_seq");
    }

    if ($res){
        if(isset($forum_perms)) {
            // storing forum-permissions
            foreach($forum_perms as $fid => $p){
                $sql = "insert into {$PHORUM['user_permissions_table']} (user_id, forum_id, permission) values ($user_id, $fid, $p)";
                $res = pg_query($conn, $sql);
                if ($err = pg_last_error()){
                    phorum_db_pg_last_error("$err: $sql");
                    break;
                }
            }
        }
        if(isset($user_data)) {
            /* storing custom-fields */
            foreach($user_data as $key => $val){
                if(is_array($val)) { /* arrays need to be serialized */
                    $val = 'P_SER:'.serialize($val);
                    /* P_SER: (PHORUM_SERIALIZED is our marker telling this Field is serialized */
                } else { /* other vars need to be escaped */
                    $val = pg_escape_string($val);
                }
                $sql = "insert into {$PHORUM['user_custom_fields_table']} (user_id,type,data) VALUES($user_id,$key,'$val')";
                $res = pg_query($conn, $sql);
                if ($err = pg_last_error()){
                    phorum_db_pg_last_error("$err: $sql");
                    break;
                }
            }
        }
    }

    return $user_id;
}


/**
 * This function executes a query to update the given user data in the
 * database and returns the true or false
 */
function phorum_db_user_save($userdata){
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if(isset($userdata["permissions"])){
        unset($userdata["permissions"]);
    }

    if (isset($userdata["forum_permissions"])){
        $forum_perms = $userdata["forum_permissions"];
        unset($userdata["forum_permissions"]);
    }

    if (isset($userdata["groups"])){
        $groups = $userdata["groups"];
        unset($userdata["groups"]);
        unset($userdata["group_permissions"]);
    }
    if (isset($userdata["user_data"])){
        $user_data = $userdata["user_data"];
        unset($userdata["user_data"]);
    }

    $user_id = $userdata["user_id"];
    unset($userdata["user_id"]);

    if(count($userdata)){

        $sql = "update {$PHORUM['user_table']} set ";

        $values = array();

        foreach($userdata as $key => $value){
            $values[] = "$key='".pg_escape_string($value)."'";
        }

        $sql .= implode(", ", $values);

        $sql .= " where user_id=$user_id";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }

    if (isset($forum_perms)){

        $sql = "delete from {$PHORUM['user_permissions_table']} where user_id = $user_id";
        $res=pg_query($conn, $sql);

        foreach($forum_perms as $fid=>$perms){
            $sql = "insert into {$PHORUM['user_permissions_table']} (user_id, forum_id, permission) values ($user_id, $fid, $perms)";
            $res = pg_query($conn, $sql);
            if ($err = pg_last_error()){
                phorum_db_pg_last_error("$err: $sql");
            }
        }
    }
    if(isset($user_data)) {
        // storing custom-fields
        $sql = "delete from {$PHORUM['user_custom_fields_table']} where user_id = $user_id";
        $res=pg_query($conn, $sql);

        if(is_array($user_data)) {
            foreach($user_data as $key => $val){
                if(is_array($val)) { /* arrays need to be serialized */
                    $val = 'P_SER:'.serialize($val);
                    /* P_SER: (PHORUM_SERIALIZED is our marker telling this Field is serialized */
                } else { /* other vars need to be escaped */
                    $val = pg_escape_string($val);
                }

                $sql = "insert into {$PHORUM['user_custom_fields_table']} (user_id,type,data) VALUES($user_id,$key,'$val')";
                $res = pg_query($conn, $sql);
                if ($err = pg_last_error()){
                    phorum_db_pg_last_error("$err: $sql");
                    break;
                }
            }
        }
    }

    return (bool)$res;
}

/**
 * This function saves a users group permissions.
 */
function phorum_db_user_save_groups($user_id, $groups)
{
    $PHORUM = $GLOBALS["PHORUM"];
    if (!$user_id > 0){
        return false;
    }

    settype($user_id, "int");

    // erase the group memberships they have now
    $conn = phorum_db_postgresql_connect();
    $sql = "delete from {$PHORUM['user_group_xref_table']} where user_id = $user_id";
    $res=pg_query($conn, $sql);

    foreach($groups as $group_id => $group_perm){
        $sql = "insert into {$PHORUM['user_group_xref_table']} (user_id, group_id, status) values ($user_id, $group_id, $group_perm)";
        pg_query($conn, $sql);
        if ($err = pg_last_error()){
            phorum_db_pg_last_error("$err: $sql");
            break;
        }
    }
    return (bool)$res;
}

/**
 * This function executes a query to subscribe a user to a forum/thread.
 */

function phorum_db_user_subscribe($user_id, $forum_id, $thread, $type)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($user_id, "int");
    settype($forum_id, "int");
    settype($thread, "int");
    settype($type, "int");

    $conn = phorum_db_postgresql_connect();

    $sql = "UPDATE {$PHORUM['subscribers_table']} SET sub_type=$type WHERE user_id=$user_id AND forum_id=$forum_id AND thread=$thread";
    $res = pg_query($conn, $sql);
	if ($res) {
		if (pg_affected_rows($res) == 0) {
            $sql = "INSERT INTO {$PHORUM['subscribers_table']} (user_id, forum_id, sub_type, thread) values ($user_id, $forum_id, $type, $thread)";
	        $res = pg_query($conn, $sql);
		}
	}

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return (bool)$res;
}

/**
  * This function increases the post-counter for a user by one
  */
function phorum_db_user_addpost() {

        $conn = phorum_db_postgresql_connect();

        $sql="UPDATE ".$GLOBALS['PHORUM']['user_table']." SET posts=posts+1 WHERE user_id = ".$GLOBALS['PHORUM']['user']['user_id'];
        $res=pg_query($conn, $sql);

        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        return (bool)$res;
}

/**
 * This function executes a query to unsubscribe a user to a forum/thread.
 */

function phorum_db_user_unsubscribe($user_id, $thread, $forum_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($user_id, "int");
    settype($forum_id, "int");
    settype($thread, "int");

    $conn = phorum_db_postgresql_connect();

    $sql = "DELETE FROM {$PHORUM['subscribers_table']} WHERE user_id=$user_id AND thread=$thread";
    if($forum_id) $sql.=" and forum_id=$forum_id";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return (bool)$res;
}

/**
 * This function will return a list of groups the user
 * is a member of, as well as the users permissions.
 */
function phorum_db_user_get_groups($user_id)
{
    $PHORUM = $GLOBALS["PHORUM"];
    $groups = array();

    if (!$user_id > 0){
           return $groups;
    }

    settype($user_id, "int");

    $conn = phorum_db_postgresql_connect();
    $sql = "SELECT group_id, status FROM {$PHORUM['user_group_xref_table']} WHERE user_id = $user_id ORDER BY status DESC";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    while($row = pg_fetch_assoc($res)){
        $groups[$row["group_id"]] = $row["status"];
    }

    return $groups;
}

/**
 * This function executes a query to select data about a user including
 * his permission data and returns that in an array.
 * If $search is empty, all users should be returned.
 */

function phorum_db_search_users($search)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $users = array();

    $search = trim($search);

    $sql = "select user_id, username, email, active, posts, date_last_active from {$PHORUM['user_table']} where username like '%$search%' or email like '%$search%'order by username";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res)){
        while ($user = pg_fetch_assoc($res)){
            $users[$user["user_id"]] = $user;
        }
    }

    return $users;
}


/**
 * This function gets the users that await approval
 */

function phorum_db_user_get_unapproved()
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $sql="select user_id, username, email from {$PHORUM['user_table']} where active in(".PHORUM_USER_PENDING_BOTH.", ".PHORUM_USER_PENDING_MOD.") order by username";
    $res=pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    $users=array();
    if($res){
        while($rec=pg_fetch_assoc($res)){
            $users[$rec["user_id"]]=$rec;
        }
    }

    return $users;

}
/**
 * This function deletes a user completely
 * - entry in the users-table
 * - entries in the permissions-table
 * - entries in the newflags-table
 * - entries in the subscribers-table
 * - entries in the group_xref-table
 * - entries in the private-messages-table
 * - entries in the files-table
 * - sets entries in the messages-table to anonymous
 *
 */
function phorum_db_user_delete($user_id) {
    $PHORUM = $GLOBALS["PHORUM"];

    // how would we check success???
    $ret = true;

    settype($user_id, "int");

    $conn = phorum_db_postgresql_connect();
    // user-table
    $sql = "delete from {$PHORUM['user_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // permissions-table
    $sql = "delete from {$PHORUM['user_permissions_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // newflags-table
    $sql = "delete from {$PHORUM['user_newflags_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // subscribers-table
    $sql = "delete from {$PHORUM['subscribers_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // group-xref-table
    $sql = "delete from {$PHORUM['user_group_xref_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // private messages
    $sql = "select * from {$PHORUM["pm_xref_table"]} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    while ($row = pg_fetch_assoc($res)) {
        $folder = $row["pm_folder_id"] == 0 ? $row["special_folder"] : $row["pm_folder_id"];
        phorum_db_pm_delete($row["pm_message_id"], $folder, $user_id);
    }

    // pm_buddies
    $sql = "delete from {$PHORUM["pm_buddies_table"]} where user_id=$user_id or buddy_user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // private message folders
    $sql = "delete from {$PHORUM["pm_folders_table"]} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // files-table
    $sql = "delete from {$PHORUM['files_table']} where user_id=$user_id and message_id=0 and link='" . PHORUM_LINK_USER . "'";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // custom-fields-table
    $sql = "delete from {$PHORUM['user_custom_fields_table']} where user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // messages-table
    if(PHORUM_DELETE_CHANGE_AUTHOR) {
      $sql = "update {$PHORUM['message_table']} set user_id=0,email='',author='".pg_escape_string($PHORUM['DATA']['LANG']['AnonymousUser'])."' where user_id=$user_id";
    } else {
      $sql = "update {$PHORUM['message_table']} set user_id=0,email='' where user_id=$user_id";
    }
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return $ret;
}


/**
 * This function gets the users file list
 */

function phorum_db_get_user_file_list($user_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($user_id, "int");

    $files=array();

    $sql="select file_id, filename, filesize, add_datetime from {$PHORUM['files_table']} where user_id=$user_id and message_id=0 and link='" . PHORUM_LINK_USER . "'";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    if($res){
        while($rec=pg_fetch_assoc($res)){
            $files[$rec["file_id"]]=$rec;
        }
    }

    return $files;
}


/**
 * This function gets the message's file list
 */

function phorum_db_get_message_file_list($message_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $files=array();

    $sql="select file_id, filename, filesize, add_datetime from {$PHORUM['files_table']} where message_id=$message_id and link='" . PHORUM_LINK_MESSAGE . "'";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    if($res){
        while($rec=pg_fetch_assoc($res)){
            $files[$rec["file_id"]]=$rec;
        }
    }

    return $files;
}


/**
 * This function retrieves a file from the db
 */

function phorum_db_file_get($file_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($file_id, "int");

    $file=array();

    $sql="select * from {$PHORUM['files_table']} where file_id=$file_id";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    if($res){
        $file=pg_fetch_assoc($res);
    }

    return $file;
}


/**
 * This function saves a file to the db
 */

function phorum_db_file_save($user_id, $filename, $filesize, $buffer, $message_id=0, $link=null)
{
    $PHORUM = $GLOBALS["PHORUM"];

    if (is_null($link)) {
        $link = $message_id ? PHORUM_LINK_MESSAGE : PHORUM_LINK_USER;
    } else {
        $link = addslashes($link);
    }

    $conn = phorum_db_postgresql_connect();

    $file_id=0;

    settype($user_id, "int");
    settype($message_id, "int");
    settype($filesize, "int");

    $filename=addslashes($filename);

    $sql="insert into {$PHORUM['files_table']} (user_id, message_id, link, filename, filesize, file_data, add_datetime) values ($user_id, $message_id, '$link', '$filename', $filesize, '$buffer', " . time() . ')';

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    if($res){
        $file_id=pgsql_insert_id($conn, "{$PHORUM['files_table']}_file_id_seq");
    }

    return $file_id;
}


/**
 * This function saves a file to the db
 */

function phorum_db_file_delete($file_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($file_id, "int");

    $sql="delete from {$PHORUM['files_table']} where file_id=$file_id";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    return $res;
}

/**
 * This function links a file to a specific message
 */

function phorum_db_file_link($file_id, $message_id, $link = null)
{
    $PHORUM = $GLOBALS["PHORUM"];

    if (is_null($link)) {
        $link = $message_id ? PHORUM_LINK_MESSAGE : PHORUM_LINK_USER;
    } else {
        $link = addslashes($link);
    }

    $conn = phorum_db_postgresql_connect();

    settype($file_id, "int");
    settype($message_id, "int");

    $sql="update {$PHORUM['files_table']} " .
         "set message_id=$message_id, link='$link' " .
         "where file_id=$file_id";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    return $res;
}

/**
 * This function reads the current total size of all files for a user
 */

function phorum_db_get_user_filesize_total($user_id)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($user_id, "int");

    $total=0;

    $sql="select sum(filesize) as total from {$PHORUM['files_table']} where user_id=$user_id and message_id=0 and link='" . PHORUM_LINK_USER . "'";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }

    if($res){
        $total=pg_fetch_result($res, 0,"total");
    }

    return $total;

}

/**
 * This function is used for cleaning up stale files from the
 * database. Stale files are files that are not linked to
 * anything. These can for example be caused by users that
 * are writing a message with attachments, but never post
 * it.
 * @param live_run - If set to false (default), the function
 *                  will return a list of files that will
 *                  be purged. If set to true, files will
 *                  be purged.
 */
function phorum_db_file_purge_stale_files($live_run = false)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $where = "link='" . PHORUM_LINK_EDITOR. "' " .
             "and add_datetime<". (time()-PHORUM_MAX_EDIT_TIME);

    // Purge files.
    if ($live_run) {

        // Delete files that are linked to the editor and are
        // added a while ago. These are from abandoned posts.
        $sql = "delete from {$PHORUM['files_table']} " .
               "where $where";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        return true;

    // Only select a list of files that can be purged.
    } else {

        // Select files that are linked to the editor and are
        // added a while ago. These are from abandoned posts.
        $sql = "select file_id, filename, filesize, add_datetime " .
               "from {$PHORUM['files_table']} " .
               "where $where";

        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $purge_files = array();
        if (pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $row["reason"] = "Stale editor file";
                $purge_files[$row["file_id"]] = $row;
            }
        }

        return $purge_files;
    }
}

/**
 * This function returns the newinfo-array for markallread
 */

function phorum_db_newflag_allread($forum_id=0)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $conn = phorum_db_postgresql_connect();

    settype($forum_id, "int");

    if(empty($forum_id)) $forum_id=$PHORUM["forum_id"];

    // delete all newflags for this user and forum
    phorum_db_newflag_delete(0,$forum_id);

    // get the maximum message-id in this forum
    $sql = "select max(message_id) from {$PHORUM['message_table']} where forum_id=$forum_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()){
        phorum_db_pg_last_error("$err: $sql");
    }elseif (pg_num_rows($res) > 0){
        $row = pg_fetch_row($res);
        if($row[0] > 0) {
            // set this message as min-id
            phorum_db_newflag_add_read(array(0=>array('id'=>$row[0],'forum'=>$forum_id)));
        }
    }

}


/**
* This function returns the read messages for the current user and forum
* optionally for a given forum (for the index)
*/
function phorum_db_newflag_get_flags($forum_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");

    $read_msgs=array('min_id'=>0);

    if(empty($forum_id)) $forum_id=$PHORUM["forum_id"];

    $sql="SELECT message_id,forum_id FROM ".$PHORUM['user_newflags_table']." WHERE user_id={$PHORUM['user']['user_id']} AND forum_id IN({$forum_id},{$PHORUM['vroot']})";

    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    while($row=pg_fetch_row($res)) {
        // set the min-id if given flag is set
        if($row[1] != $PHORUM['vroot'] && ($read_msgs['min_id']==0 || $row[0] < $read_msgs['min_id'])) {
            $read_msgs['min_id']=$row[0];
        } else {
            $read_msgs[$row[0]]=$row[0];
        }
    }

    return $read_msgs;
}


/**
* This function returns the count of unread messages the current user and forum
* optionally for a given forum (for the index)
*/
function phorum_db_newflag_get_unread_count($forum_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");

    if(empty($forum_id)) $forum_id=$PHORUM["forum_id"];

    // get the read message array
    $read_msgs = phorum_db_newflag_get_flags($forum_id);

    if($read_msgs["min_id"]==0) return array(0,0);

    $sql="SELECT count(*) as count FROM ".$PHORUM['message_table']." WHERE message_id NOT in (".implode(",", $read_msgs).") and message_id > {$read_msgs['min_id']} and forum_id in ({$forum_id},0) and status=".PHORUM_STATUS_APPROVED." and not ".PHORUM_SQL_MOVEDMESSAGES;

    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $counts[] = pg_fetch_result($res, 0, "count");

    $sql="SELECT count(*) as count FROM ".$PHORUM['message_table']." WHERE message_id NOT in (".implode(",", $read_msgs).") and message_id > {$read_msgs['min_id']} and forum_id in ({$forum_id},0) and parent_id=0 and status=".PHORUM_STATUS_APPROVED." and not ".PHORUM_SQL_MOVEDMESSAGES;

    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $counts[] = pg_fetch_result($res, 0, "count");

    return $counts;
}


/**
 * This function marks a message as read
 */
function phorum_db_newflag_add_read($message_ids) {
    $PHORUM = $GLOBALS["PHORUM"];

    $num_newflags=phorum_db_newflag_get_count();

    // maybe got just one message
    if(!is_array($message_ids)) {
        $message_ids=array(0=>(int)$message_ids);
    }
    // deleting messages which are too much
    $num_end=$num_newflags+count($message_ids);
    if($num_end > PHORUM_MAX_NEW_INFO) {
        phorum_db_newflag_delete($num_end - PHORUM_MAX_NEW_INFO);
    }
    // building the query
    $values=array();
    $cnt=0;

    foreach($message_ids as $id=>$data) {
        if(is_array($data)) {
            $values[]="({$PHORUM['user']['user_id']},{$data['forum']},{$data['id']})";
        } else {
            $values[]="({$PHORUM['user']['user_id']},{$PHORUM['forum_id']},$data)";
        }
        $cnt++;
    }
    if($cnt) {
        $insert_sql="INSERT INTO ".$PHORUM['user_newflags_table']." (user_id,forum_id,message_id) VALUES".join(",",$values);

        // fire away
        $conn = phorum_db_postgresql_connect();
        $res = pg_query($conn, $insert_sql);

        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $insert_sql");
    }
}

/**
* This function returns the number of newflags for this user and forum
*/
function phorum_db_newflag_get_count($forum_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");

    if(empty($forum_id)) $forum_id=$PHORUM["forum_id"];

    $sql="SELECT count(*) FROM ".$PHORUM['user_newflags_table']." WHERE user_id={$PHORUM['user']['user_id']} AND forum_id={$forum_id}";

    // fire away
    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $row=pg_fetch_row($res);

    return $row[0];
}

/**
* This function removes a number of newflags for this user and forum
*/
function phorum_db_newflag_delete($numdelete=0,$forum_id=0)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");
    settype($numdelete, "int");

    if(empty($forum_id)) $forum_id=$PHORUM["forum_id"];

    if($numdelete>0) {
        $lvar=" ORDER BY message_id ASC LIMIT $numdelete";
    } else {
        $lvar="";
    }
    // delete the number of newflags given
    $del_sql="DELETE FROM ".$PHORUM['user_newflags_table']." WHERE user_id={$PHORUM['user']['user_id']} AND forum_id={$forum_id}".$lvar;
    // fire away
    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, $del_sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $del_sql");
}

/**
 * This function executes a query to get the user ids of the users
 * subscribed to a forum/thread.
 */

function phorum_db_get_subscribed_users($forum_id, $thread, $type){
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");
    settype($thread, "int");
    settype($type, "int");

    $conn = phorum_db_postgresql_connect();

    $userignore="";
    if ($PHORUM["DATA"]["LOGGEDIN"])
       $userignore="and b.user_id != {$PHORUM['user']['user_id']}";

    $sql = "select DISTINCT(b.email),user_language from {$PHORUM['subscribers_table']} as a,{$PHORUM['user_table']} as b where a.forum_id=$forum_id and (a.thread=$thread or a.thread=0) and a.sub_type=$type and b.user_id=a.user_id $userignore";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        $arr=array();

    while ($rec = pg_fetch_row($res)){
        if(!empty($rec[1])) // user-language is set
            $arr[$rec[1]][] = $rec[0];
        else // no user-language is set
            $arr[$PHORUM['language']][]= $rec[0];
    }

    return $arr;
}

/**
 * This function executes a query to get the subscriptions of a user-id,
 * together with the forum-id and subjects of the threads
 */

function phorum_db_get_message_subscriptions($user_id,$days=2){
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $userignore="";
    if ($PHORUM["DATA"]["LOGGEDIN"])
       $userignore="and b.user_id != {$PHORUM['user']['user_id']}";

    if($days > 0) {
         $timestr=" AND (".time()." - b.modifystamp) <= ($days * 86400)";
    } else {
        $timestr="";
    }

    $sql = "select a.thread, a.forum_id, a.sub_type, b.subject,b.modifystamp,b.author,b.user_id,b.email from {$PHORUM['subscribers_table']} as a,{$PHORUM['message_table']} as b where a.user_id=$user_id and b.message_id=a.thread and (a.sub_type=".PHORUM_SUBSCRIPTION_MESSAGE." or a.sub_type=".PHORUM_SUBSCRIPTION_BOOKMARK.")"."$timestr ORDER BY b.modifystamp desc";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $arr=array();
    $forum_ids=array();

    while ($rec = pg_fetch_assoc($res)){
        $unsub_url=phorum_get_url(PHORUM_CONTROLCENTER_URL, "panel=".PHORUM_CC_SUBSCRIPTION_THREADS, "unsub_id=".$rec['thread'], "unsub_forum=".$rec['forum_id'], "unsub_type=".$rec['sub_type']);
        $rec['unsubscribe_url']=$unsub_url;
        $arr[] = $rec;
        $forum_ids[]=$rec['forum_id'];
    }
    $arr['forum_ids']=$forum_ids;

    return $arr;
}

/**
 * This function executes a query to find out if a user is subscribed to a thread
 */

function phorum_db_get_if_subscribed($forum_id, $thread, $user_id, $type=PHORUM_SUBSCRIPTION_MESSAGE)
{
    $PHORUM = $GLOBALS["PHORUM"];

    settype($forum_id, "int");
    settype($thread, "int");
    settype($user_id, "int");
    settype($type, "int");

    $conn = phorum_db_postgresql_connect();

    $sql = "select user_id from {$PHORUM['subscribers_table']} where forum_id=$forum_id and thread=$thread and user_id=$user_id and sub_type=$type";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        $retval = true;
    }else{
        $retval = false;
    }

    return $retval;
}


/**
 * This function retrieves the banlists for the current forum
 */

function phorum_db_get_banlists($ordered=false) {
    $PHORUM = $GLOBALS["PHORUM"];

    $retarr = array();
    $forumstr = "";

    $conn = phorum_db_postgresql_connect();

    if(isset($PHORUM['forum_id']) && !empty($PHORUM['forum_id']))
        $forumstr = "WHERE forum_id = {$PHORUM['forum_id']} OR forum_id = 0";

    if(isset($PHORUM['vroot']) && !empty($PHORUM['vroot']))
        $forumstr .= " OR forum_id = {$PHORUM['vroot']}";



    $sql = "SELECT * FROM {$PHORUM['banlist_table']} $forumstr";

    if($ordered) {
        $sql.= " ORDER BY type, string";
    }

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        while($row = pg_fetch_assoc($res)) {
            $retarr[$row['type']][$row['id']]=array('pcre'=>$row['pcre'],'string'=>$row['string'],'forum_id'=>$row['forum_id']);
        }
    }
    return $retarr;
}


/**
 * This function retrieves one item from the banlists
 */

function phorum_db_get_banitem($banid) {
    $PHORUM = $GLOBALS["PHORUM"];

    $retarr = array();

    $conn = phorum_db_postgresql_connect();

    settype($banid, "int");

    $sql = "SELECT * FROM {$PHORUM['banlist_table']} WHERE id = $banid";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        while($row = pg_fetch_assoc($res)) {
            $retarr=array('pcre'=>$row['pcre'],'string'=>$row['string'],'forumid'=>$row['forum_id'],'type'=>$row['type']);
        }
    }
    return $retarr;
}


/**
 * This function deletes one item from the banlists
 */

function phorum_db_del_banitem($banid) {
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $sql = "DELETE FROM {$PHORUM['banlist_table']} WHERE id = $banid";

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if(pg_affected_rows($res) > 0) { return true;
    } else {
        return false;
    }
}


/**
 * This function adds or modifies a banlist-entry
 */

function phorum_db_mod_banlists($type,$pcre,$string,$forum_id,$id=0) {
    $PHORUM = $GLOBALS["PHORUM"];

    $retarr = array();

    $conn = phorum_db_postgresql_connect();

    settype($type, "int");
    settype($pcre, "int");
    settype($forum_id, "int");
    settype($id, "int");

    if($id > 0) { // modifying an entry
        $sql = "UPDATE {$PHORUM['banlist_table']} SET forum_id = $forum_id, type = $type, pcre = $pcre, string = '".pg_escape_string($string)."' where id = $id";
    } else { // adding an entry
        $sql = "INSERT INTO {$PHORUM['banlist_table']} (forum_id,type,pcre,string) VALUES($forum_id,$type,$pcre,'".pg_escape_string($string)."')";
    }

    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if(pg_affected_rows($res) > 0) {
        return true;
    } else {
        return false;
    }
}



/**
 * This function lists all private messages in a folder.
 * @param folder - The folder to use. Either a special folder
 *                 (PHORUM_PM_INBOX or PHORUM_PM_OUTBOX) or the
 *                 id of a user's custom folder.
 * @param user_id - The user to retrieve messages for or NULL
 *                 to use the current user (default).
 * @param reverse - If set to a true value (default), sorting
 *                 of messages is done in reverse (newest first).
 */

function phorum_db_pm_list($folder, $user_id = NULL, $reverse = true)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    $folder_sql = "user_id = $user_id AND ";
    if (is_numeric($folder)) {
        $folder_sql .= "pm_folder_id=$folder";
    } elseif ($folder == PHORUM_PM_INBOX || $folder == PHORUM_PM_OUTBOX) {
        $folder_sql .= "pm_folder_id=0 AND special_folder='$folder'";
    } else {
        die ("Illegal folder '$folder' requested for user id '$user_id'");
    }

    $sql = "SELECT m.pm_message_id, from_user_id, from_username, subject, " .
           "datestamp, meta, pm_xref_id, user_id, pm_folder_id, " .
           "special_folder, read_flag, reply_flag " .
           "FROM {$PHORUM['pm_messages_table']} as m, {$PHORUM['pm_xref_table']} as x " .
           "WHERE $folder_sql " .
           "AND x.pm_message_id = m.pm_message_id " .
           "ORDER BY x.pm_message_id " . ($reverse ? "DESC" : "ASC");
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $list = array();
    if (pg_num_rows($res) > 0){
        while($row = pg_fetch_assoc($res)) {

            // Add the recipient information unserialized to the message..
            $meta = unserialize($row['meta']);
            $row['recipients'] = $meta['recipients'];

            $list[$row["pm_message_id"]]=$row;
        }
    }

    return $list;
}

/**
 * This function retrieves a private message from the database.
 * @param pm_id - The id for the private message to retrieve.
 * @param user_id - The user to retrieve messages for or NULL
 *                 to use the current user (default).
 * @param folder_id - The folder to retrieve the message from or
 *                    NULL if the folder does not matter.
 */

function phorum_db_pm_get($pm_id, $folder = NULL, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");
    settype($pm_id, "int");

    if (is_null($folder)) {
        $folder_sql = '';
    } elseif (is_numeric($folder)) {
        $folder_sql = "pm_folder_id=$folder AND ";
    } elseif ($folder == PHORUM_PM_INBOX || $folder == PHORUM_PM_OUTBOX) {
        $folder_sql = "pm_folder_id=0 AND special_folder='$folder' AND ";
    } else {
        die ("Illegal folder '$folder' requested for message id '$pm_id'");
    }

    $sql = "SELECT * " .
           "FROM {$PHORUM['pm_messages_table']} as m, {$PHORUM['pm_xref_table']} as x " .
           "WHERE $folder_sql x.pm_message_id = $pm_id AND x.user_id = $user_id " .
           "AND x.pm_message_id = m.pm_message_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        $row = pg_fetch_assoc($res);

        // Add the recipient information unserialized to the message..
        $meta = unserialize($row['meta']);
        $row['recipients'] = $meta['recipients'];

        return $row;
    } else {
        return NULL;
    }
}

/**
 * This function creates a new folder for a user.
 * @param foldername - The name of the folder to create.
 * @param user_id - The user to create the folder for or
 *                  NULL to use the current user (default).
 */
function phorum_db_pm_create_folder($foldername, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    $sql = "INSERT INTO {$PHORUM['pm_folders_table']} SET " .
           "user_id=$user_id, " .
           "foldername='".pg_escape_string($foldername)."'";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}

/**
 * This function renames a folder for a user.
 * @param folder_id - The id of the folder to rename.
 * @param newname - The new name for the folder.
 * @param user_id - The user to rename the folder for or
 *                  NULL to use the current user (default).
 */
function phorum_db_pm_rename_folder($folder_id, $newname, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");
    settype($folder_id, "int");

    $sql = "UPDATE {$PHORUM['pm_folders_table']} " .
           "SET foldername = '".pg_escape_string($newname)."' " .
           "WHERE pm_folder_id = $folder_id AND user_id = $user_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}



/**
 * This function deletes a folder for a user. Along with the
 * folder, all contained messages are deleted as well.
 * @param folder_id - The id of the folder to delete.
 * @param user_id - The user to delete the folder for or
 *                  NULL to use the current user (default).
 */
function phorum_db_pm_delete_folder($folder_id, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");
    settype($folder_id, "int");

    // Get messages in this folder and delete them.
    $list = phorum_db_pm_list($folder_id, $user_id);
    foreach ($list as $id => $data) {
        phorum_db_pm_delete($id, $folder_id, $user_id);
    }

    // Delete the folder itself.
    $sql = "DELETE FROM {$PHORUM['pm_folders_table']} " .
           "WHERE pm_folder_id = $folder_id AND user_id = $user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}

/**
 * This function retrieves the list of folders for a user.
 * @param user_id - The user to retrieve folders for or NULL
 *                 to use the current user (default).
 * @param count_messages - Count the number of messages for the
 *                 folders. Default, this is not done.
 */
function phorum_db_pm_getfolders($user_id = NULL, $count_messages = false)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    // Setup the list of folders. Our special folders are
    // not in the database, so these are added here.
    $folders = array(
        PHORUM_PM_INBOX => array(
            'id'   => PHORUM_PM_INBOX,
            'name' => $PHORUM["DATA"]["LANG"]["INBOX"],
        ),
    );

    // Select all custom folders for the user.
    $sql = "SELECT * FROM {$PHORUM['pm_folders_table']} " .
           "WHERE user_id = $user_id ORDER BY foldername";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // Add them to the folderlist.
    if (pg_num_rows($res) > 0){
        while (($row = pg_fetch_assoc($res))) {
            $folders[$row["pm_folder_id"]] = array(
                'id' => $row["pm_folder_id"],
                'name' => $row["foldername"],
            );
        }
    }

    // Add the outgoing box.
    $folders[PHORUM_PM_OUTBOX] = array(
        'id'   => PHORUM_PM_OUTBOX,
        'name' => $PHORUM["DATA"]["LANG"]["SentItems"],
    );

    // Count messages if requested.
    if ($count_messages)
    {
        // Initialize counters.
        foreach ($folders as $id => $data) {
            $folders[$id]["total"] = $folders[$id]["new"] = 0;
        }

        // Collect count information.
        $sql = "SELECT pm_folder_id, special_folder, " .
               "count(*) as total, (count(*) - sum(read_flag)) as new " .
               "FROM {$PHORUM['pm_xref_table']}  " .
               "WHERE user_id = $user_id " .
               "GROUP BY pm_folder_id, special_folder";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

        // Add counters to the folderlist.
        if (pg_num_rows($res) > 0){
            while (($row = pg_fetch_assoc($res))) {
                $folder_id = $row["pm_folder_id"] ? $row["pm_folder_id"] : $row["special_folder"];
                // If there are stale messages, we do not want them
                // to create non-existant mailboxes in the list.
                if (isset($folders[$folder_id])) {
                    $folders[$folder_id]["total"] = $row["total"];
                    $folders[$folder_id]["new"] = $row["new"];
                }
            }
        }
    }

    return $folders;
}

/**
 * This function computes the number of private messages a user has
 * and returns both the total and the number unread.
 * @param folder - The folder to use. Either a special folder
 *                 (PHORUM_PM_INBOX or PHORUM_PM_OUTBOX), the
 *                 id of a user's custom folder or
 *                 PHORUM_PM_ALLFOLDERS for all folders.
 * @param user_id - The user to retrieve messages for or NULL
 *                 to use the current user (default).
 */

function phorum_db_pm_messagecount($folder, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    if (is_numeric($folder)) {
        $folder_sql = "pm_folder_id=$folder AND";
    } elseif ($folder == PHORUM_PM_INBOX || $folder == PHORUM_PM_OUTBOX) {
        $folder_sql = "pm_folder_id=0 AND special_folder='$folder' AND";
    } elseif ($folder == PHORUM_PM_ALLFOLDERS) {
        $folder_sql = '';
    } else {
        die ("Illegal folder '$folder' requested for user id '$user_id'");
    }

    $sql = "SELECT count(*) as total, (count(*) - sum(read_flag)) as new " .
           "FROM {$PHORUM['pm_xref_table']}  " .
           "WHERE $folder_sql user_id = $user_id";

    $messagecount=array("total" => 0, "new" => 0);

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        $row = pg_fetch_assoc($res);
        $messagecount["total"] = $row["total"];
        $messagecount["new"] = ($row["new"] >= 1) ? $row["new"] : 0;
    }

    return $messagecount;
}

/**
 * This function does a quick check if the user has new private messages.
 * This is useful in case you only want to know whether the user has
 * new messages or not and when you are not interested in the exact amount
 * of new messages.
 *
 * @param user_id - The user to retrieve messages for or NULL
 *                 to use the current user (default).
 * @return A true value, in case there are new messages available.
 */
function phorum_db_pm_checknew($user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    $sql = "SELECT user_id " .
           "FROM {$PHORUM['pm_xref_table']} " .
           "WHERE user_id = $user_id AND read_flag = 0 LIMIT 1";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    return pg_num_rows($res);
}

/**
 * This function inserts a private message in the database. The return value
 * is the pm_message_id of the created message.
 * @param subject - The subject for the private message.
 * @param message - The message text for the private message.
 * @param to - A single user_id or an array of user_ids for the recipients.
 * @param from - The user_id of the sender. The current user is used in case
 *               the parameter is set to NULL (default).
 * @param keepcopy - If set to a true value, a copy of the mail will be put in
 *                   the outbox of the user. Default value is false.
 */
function phorum_db_pm_send($subject, $message, $to, $from=NULL, $keepcopy=false)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    // Prepare the sender.
    if ($from == NULL) $from = $PHORUM['user']['user_id'];
    settype($from, "int");
    $fromuser = phorum_db_user_get($from, false);
    if (! $fromuser) die("Unknown sender user_id '$from'");

    // This array will be filled with xref database entries.
    $xref_entries = array();

    // Prepare the list of recipients.
    $rcpts = array();
    if (! is_array($to)) $to = array($to);
    foreach ($to as $user_id) {
        settype($user_id, "int");
        $user = phorum_db_user_get($user_id, false);
        if (! $user) die("Unknown recipient user_id '$user_id'");
        $rcpts[$user_id] = array(
            'user_id' => $user_id,
            'username' => $user["username"],
            'read_flag' => 0,
        );
        $xref_entries[] = array(
            'user_id' => $user_id,
            'pm_folder_id' => 0,
            'special_folder' => PHORUM_PM_INBOX,
            'read_flag' => 0,
        );
    }

    // Keep copy of this message in outbox?
    if ($keepcopy) {
        $xref_entries[] = array(
            'user_id' => $from,
            'pm_folder_id' => 0,
            'special_folder' => PHORUM_PM_OUTBOX,
            'read_flag' => 1,
        );
    }

    // Prepare message meta data.
    $meta = pg_escape_string(serialize(array(
        'recipients' => $rcpts
    )));

    // Create the message.
    $sql = "INSERT INTO {$PHORUM["pm_messages_table"]} (" .
           "from_user_id, from_username, subject, message, datestamp, meta) " .
		   "VALUES ( " .
           "$from, " .
           "'".pg_escape_string($fromuser["username"])."', " .
           "'".pg_escape_string($subject)."', " .
           "'".pg_escape_string($message)."', " .
           "'".time()."', " .
           "'$meta')";
    pg_query($conn, $sql);
    if ($err = pg_last_error()) {
        phorum_db_pg_last_error("$err: $sql");
        return;
    }

    // Get the message id.
    $pm_message_id = pgsql_insert_id($conn, "{$PHORUM["pm_messages_table"]}_pm_message_id_seq");

    // Put the message in the recipient inboxes.
    foreach ($xref_entries as $xref) {
        $sql = "INSERT INTO {$PHORUM["pm_xref_table"]} (" .
               "user_id, pm_folder_id, special_folder, pm_message_id, read_flag, reply_flag) " .
			   " values ( " .
               "{$xref["user_id"]}, " .
               "{$xref["pm_folder_id"]}, " .
               "'{$xref["special_folder"]}', " .
               "$pm_message_id, " .
               "{$xref["read_flag"]}, " .
               "0)";
        pg_query($conn, $sql);
        if ($err = pg_last_error()) {
            phorum_db_pg_last_error("$err: $sql");
            return;
        }

    }

    return $pm_message_id;
}

/**
 * This function updates a flag for a private message.
 * @param pm_id - The id of the message to update.
 * @param flag - The flag to update. Options are PHORUM_PM_READ_FLAG
 *               and PHORUM_PM_REPLY_FLAG.
 * @param value - The value for the flag (true or false).
 * @param user_id - The user to set a flag for or NULL
 *                 to use the current user (default).
 */
function phorum_db_pm_setflag($pm_id, $flag, $value, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($pm_id, "int");

    if ($flag != PHORUM_PM_READ_FLAG && $flag != PHORUM_PM_REPLY_FLAG) {
        trigger_error("Invalid value for \$flag in function phorum_db_pm_setflag(): $flag", E_USER_WARNING);
        return 0;
    }

    $value = $value ? 1 : 0;

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    // Update the flag in the database.
    $sql = "UPDATE {$PHORUM["pm_xref_table"]} " .
           "SET $flag = $value " .
           "WHERE pm_message_id = $pm_id AND user_id = $user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // Update message counters.
    if ($flag == PHORUM_PM_READ_FLAG) {
        phorum_db_pm_update_message_info($pm_id);
    }

    return $res;
}

/**
 * This function deletes a private message from a folder.
 * @param folder - The folder from which to delete the message
 * @param pm_id - The id of the private message to delete
 * @param user_id - The user to delete the message for or NULL
 *                 to use the current user (default).
 */
function phorum_db_pm_delete($pm_id, $folder, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($pm_id, "int");

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    if (is_numeric($folder)) {
        $folder_sql = "pm_folder_id=$folder AND";
    } elseif ($folder == PHORUM_PM_INBOX || $folder == PHORUM_PM_OUTBOX) {
        $folder_sql = "pm_folder_id=0 AND special_folder='$folder' AND";
    } else {
        die ("Illegal folder '$folder' requested for user id '$user_id'");
    }

    $sql = "DELETE FROM {$PHORUM["pm_xref_table"]} " .
           "WHERE $folder_sql " .
           "user_id = $user_id AND pm_message_id = $pm_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // Update message counters.
    phorum_db_pm_update_message_info($pm_id);

    return $res;
}

/**
 * This function moves a private message to a different folder.
 * @param pm_id - The id of the private message to move.
 * @param from - The folder to move the message from.
 * @param to - The folder to move the message to.
 * @param user_id - The user to move the message for or NULL
 *                 to use the current user (default).
 */
function phorum_db_pm_move($pm_id, $from, $to, $user_id = NULL)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    settype($pm_id, "int");

    if ($user_id == NULL) $user_id = $PHORUM['user']['user_id'];
    settype($user_id, "int");

    if (is_numeric($from)) {
        $folder_sql = "pm_folder_id=$from AND";
    } elseif ($from == PHORUM_PM_INBOX || $from == PHORUM_PM_OUTBOX) {
        $folder_sql = "pm_folder_id=0 AND special_folder='$from' AND";
    } else {
        die ("Illegal source folder '$from' specified");
    }

    if (is_numeric($to)) {
        $pm_folder_id = $to;
        $special_folder = 'NULL';
    } elseif ($to == PHORUM_PM_INBOX || $to == PHORUM_PM_OUTBOX) {
        $pm_folder_id = 0;
        $special_folder = "'$to'";
    } else {
        die ("Illegal target folder '$to' specified");
    }

    $sql = "UPDATE {$PHORUM["pm_xref_table"]} SET " .
           "pm_folder_id = $pm_folder_id, " .
           "special_folder = $special_folder " .
           "WHERE $folder_sql user_id = $user_id AND pm_message_id = $pm_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}

/**
 * This function updates the meta information for a message. If it
 * detects that no xrefs are available for the message anymore,
 * the message will be deleted from the database. So this function
 * has to be called after setting the read_flag and after deleting
 * a message.
 * PMTODO maybe we need some locking here to prevent concurrent
 * updates of the message info.
 */
function phorum_db_pm_update_message_info($pm_id)
{
    $PHORUM = $GLOBALS['PHORUM'];

    $conn = phorum_db_postgresql_connect();

    settype($pm_id, "int");

    // Find the message record. Return immediately if no message is found.
    $sql = "SELECT * " .
           "FROM {$PHORUM['pm_messages_table']} " .
           "WHERE pm_message_id = $pm_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    if (pg_num_rows($res) == 0) return $res;
    $pm = pg_fetch_assoc($res);

    // Find the xrefs for this message.
    $sql = "SELECT * " .
           "FROM {$PHORUM["pm_xref_table"]} " .
           "WHERE pm_message_id = $pm_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    // No xrefs left? Then the message can be fully deleted.
    if (pg_num_rows($res) == 0) {
        $sql = "DELETE FROM {$PHORUM['pm_messages_table']} " .
               "WHERE pm_message_id = $pm_id";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        return $res;
    }

    // Update the read flags for the recipients in the meta data.
    $meta = unserialize($pm["meta"]);
    $rcpts = $meta["recipients"];
    while ($row = pg_fetch_assoc($res)) {
        // Only update if available. A kept copy in the outbox will
        // not be in the meta list, so if the copy is read, the
        // meta data does not have to be updated here.
        if (isset($rcpts[$row["user_id"]])) {
            $rcpts[$row["user_id"]]["read_flag"] = $row["read_flag"];
        }
    }
    $meta["recipients"] = $rcpts;

    // Store the new meta data.
    $meta = pg_escape_string(serialize($meta));
    $sql = "UPDATE {$PHORUM['pm_messages_table']} " .
           "SET meta = '$meta' " .
           "WHERE pm_message_id = $pm_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}

/* Take care of warning about deprecation of the old PM API functions. */
function phorum_db_get_private_messages($arg1, $arg2) {
    phorum_db_pm_deprecated('phorum_db_get_private_messages'); }
function phorum_db_get_private_message($arg1) {
    phorum_db_pm_deprecated('phorum_db_get_private_message'); }
function phorum_db_get_private_message_count($arg1) {
    phorum_db_pm_deprecated('phorum_db_get_private_message_count'); }
function phorum_db_put_private_messages($arg1, $arg2, $arg3, $arg4, $arg5) {
    phorum_db_pm_deprecated('phorum_db_put_private_messages'); }
function phorum_db_update_private_message($arg1, $arg2, $arg3){
    phorum_db_pm_deprecated('phorum_db_update_private_message'); }
function phorum_db_pm_deprecated($func) {
    die("${func}() has been deprecated. Please use the new private message API.");
}

/**
 * This function checks if a certain user is buddy of another user.
 * The function return the pm_buddy_id in case the user is a buddy
 * or NULL in case the user isn't.
 * @param buddy_user_id - The user_id to check for if it's a buddy.
 * @param user_id - The user_id for which the buddy list must be
 *                  checked or NULL to use the current user (default).
 */
function phorum_db_pm_is_buddy($buddy_user_id, $user_id = NULL)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $conn = phorum_db_postgresql_connect();
    settype($buddyuser_id, "int");
    if (is_null($user_id)) $user_id = $PHORUM["user"]["user_id"];
    settype($user_id, "int");

    $sql = "SELECT pm_buddy_id FROM {$PHORUM["pm_buddies_table"]} " .
           "WHERE user_id = $user_id AND buddy_user_id = $buddy_user_id";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    if (pg_num_rows($res)) {
        $row = pg_fetch_array($res);
        return $row[0];
    } else {
        return NULL;
    }
}

/**
 * This function adds a buddy for a user. It will return the
 * pm_buddy_id for the new buddy. If the buddy already exists,
 * it will return the existing pm_buddy_id. If a non existant
 * user_id is used for the buddy_user_id, the function will
 * return NULL.
 * @param buddy_user_id - The user_id that has to be added as a buddy.
 * @param user_id - The user_id the buddy has to be added for or
 *                  NULL to use the current user (default).
 */
function phorum_db_pm_buddy_add($buddy_user_id, $user_id = NULL)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $conn = phorum_db_postgresql_connect();
    settype($buddyuser_id, "int");
    if (is_null($user_id)) $user_id = $PHORUM["user"]["user_id"];
    settype($user_id, "int");

    // Check if the buddy_user_id is a valid user_id.
    $valid = phorum_db_user_get($buddy_user_id, false);
    if (!$valid) return NULL;

    $pm_buddy_id = phorum_db_pm_is_buddy($buddy_user_id);
    if (is_null($pm_buddy_id)) {
        $sql = "INSERT INTO {$PHORUM["pm_buddies_table"]} (user_id, buddy_user_id) " .
               "values($user_id, $buddy_user_id)";
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
        $pm_buddy_id = pgsql_insert_id($conn, "{$PHORUM["pm_buddies_table"]}_pm_buddy_id_seq");
    }

    return $pm_buddy_id;
}

/**
 * This function deletes a buddy for a user.
 * @param buddy_user_id - The user_id that has to be deleted as a buddy.
 * @param user_id - The user_id the buddy has to be delete for or
 *                  NULL to use the current user (default).
 */
function phorum_db_pm_buddy_delete($buddy_user_id, $user_id = NULL)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $conn = phorum_db_postgresql_connect();
    settype($buddyuser_id, "int");
    if (is_null($user_id)) $user_id = $PHORUM["user"]["user_id"];
    settype($user_id, "int");

    $sql = "DELETE FROM {$PHORUM["pm_buddies_table"]} WHERE " .
           "buddy_user_id = $buddy_user_id AND user_id = $user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    return $res;
}

/**
 * This function retrieves a list of buddies for a user.
 * @param user_id - The user_id for which to retrieve the buddies
 *                  or NULL to user the current user (default).
 * @param find_mutual - Wheter to find mutual buddies or not (default not).
 */
function phorum_db_pm_buddy_list($user_id = NULL, $find_mutual = false)
{
    $PHORUM = $GLOBALS['PHORUM'];
    $conn = phorum_db_postgresql_connect();
    if (is_null($user_id)) $user_id = $PHORUM["user"]["user_id"];
    settype($user_id, "int");

    // Get all buddies for this user.
    $sql = "SELECT buddy_user_id FROM {$PHORUM["pm_buddies_table"]} " .
           "WHERE user_id = $user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $buddies = array();
    if (pg_num_rows($res)) {
        while ($row = pg_fetch_array($res)) {
            $buddies[$row[0]] = array (
                'user_id' => $row[0]
            );
        }
    }

    // If we do not have to lookup mutual buddies, we're done.
    if (! $find_mutual) return $buddies;

    // Initialize mutual buddy value.
    foreach ($buddies as $id => $data) {
        $buddies[$id]["mutual"] = false;
    }

    // Get all mutual buddies.
    $sql = "SELECT DISTINCT a.buddy_user_id " .
           "FROM {$PHORUM["pm_buddies_table"]} as a, {$PHORUM["pm_buddies_table"]} as b " .
           "WHERE a.user_id=$user_id " .
           "AND b.user_id=a.buddy_user_id " .
           "AND b.buddy_user_id=$user_id";
    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res)) {
        while ($row = pg_fetch_array($res)) {
            $buddies[$row[0]]["mutual"] = true;
        }
    }

    return $buddies;
}

/**
* This function returns messages or threads which are newer or older
* than the given timestamp
*
* $time  - holds the timestamp the comparison is done against
* $forum - get Threads from this forum
* $mode  - should we compare against datestamp (1) or modifystamp (2)
*
*/
function phorum_db_prune_oldThreads($time,$forum=0,$mode=1) {

    $PHORUM = $GLOBALS['PHORUM'];

    $conn = phorum_db_postgresql_connect();
    $numdeleted=0;

    $compare_field = "datestamp";
    if($mode == 2) {
      $compare_field = "modifystamp";
    }

    $forummode="";
    if($forum > 0) {
      $forummode=" AND forum_id = $forum";
    }

    // retrieving which threads to delete
    $sql = "select thread from {$PHORUM['message_table']} where $compare_field < $time AND parent_id=0 $forummode";

    $res = pg_query($conn, $sql);
    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    $ret=array();
    while($row=pg_fetch_row($res)) {
        $ret[]=$row[0];
    }

    $thread_ids=implode(",",$ret);

    if(count($ret)) {
      // deleting the messages/threads
      $sql="delete from {$PHORUM['message_table']} where thread IN ($thread_ids)";
      $res = pg_query($conn, $sql);
      if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

      $numdeleted = pg_affected_rows($res);
      if($numdeleted < 0) {
        $numdeleted=0;
      }

      // deleting the associated notification-entries
      $sql="delete from {$PHORUM['subscribers_table']} where thread IN ($thread_ids)";
      $res = pg_query($conn, $sql);
      if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");


      // optimizing the message-table
      $sql="optimize table {$PHORUM['message_table']}";
      $res = pg_query($conn, $sql);
      if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
    }

    return $numdeleted;
}

/**
 * split thread
 */
function phorum_db_split_thread($message, $forum_id)
{
    settype($message, "int");
    settype($forum_id, "int");

    if($message > 0 && $forum_id > 0){
        // get message tree for update thread id
        $tree =phorum_db_get_messagetree($message, $forum_id);
        $queries =array();
        $queries[0]="UPDATE {$GLOBALS['PHORUM']['message_table']} SET thread='$message', parent_id='0' WHERE message_id ='$message'";
        $queries[1]="UPDATE {$GLOBALS['PHORUM']['message_table']} SET thread='$message' WHERE message_id IN ($tree)";
        phorum_db_run_queries($queries);
    }
}

/**
 * This function returns the maximum message-id in the database
 */
function phorum_db_get_max_messageid() {
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();
    $maxid = 0;

    $sql="SELECT max(message_id) from ".$PHORUM["message_table"];
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if (pg_num_rows($res) > 0){
        $row = pg_fetch_row($res);
        $maxid = $row[0];
    }

    return $maxid;
}

/**
 * This function increments the viewcount for a post
 */

function phorum_db_viewcount_inc($message_id) {
    if($message_id < 1 || !is_numeric($message_id)) {
        return false;
    }

    $conn = phorum_db_postgresql_connect();
    $sql="UPDATE ".$GLOBALS['PHORUM']['message_table']." SET viewcount=viewcount+1 WHERE message_id=$message_id";
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");


    return true;

}


function phorum_db_get_custom_field_users($field_id,$field_content,$match) {


    $field_id=(int)$field_id;
    $field_content=pg_escape_string($field_content);

    $conn = phorum_db_postgresql_connect();

    if($match) {
        $compval="LIKE";
    } else {
        $compval="=";
    }

    $sql = "select user_id from {$GLOBALS['PHORUM']['user_custom_fields_table']} where type=$field_id and data $compval '$field_content'";
    $res = pg_query($conn, $sql);

    if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");

    if(pg_num_rows($res)) {
        $retval=array();
        while ($row = pg_fetch_row($res)){
            $retval[$row[0]]=$row[0];
        }
    } else {
        $retval=NULL;
    }

    return $retval;

}


/**
 * This function creates the tables needed in the database.
 */

function phorum_db_create_tables()
{
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $retmsg = "";

    $queries = array(

        // create tables
        "CREATE TABLE {$PHORUM['forums_table']} ( forum_id serial8, name varchar(50) NOT NULL default '', active smallint NOT NULL default 0, description text NOT NULL default '', template varchar(50) NOT NULL default '', folder_flag smallint NOT NULL default 0, parent_id integer NOT NULL default 0, list_length_flat integer NOT NULL default 0, list_length_threaded integer NOT NULL default 0, moderation integer NOT NULL default 0, threaded_list smallint NOT NULL default 0, threaded_read smallint NOT NULL default 0, float_to_top smallint NOT NULL default 0, check_duplicate smallint NOT NULL default 0, allow_attachment_types varchar(100) NOT NULL default '', max_attachment_size integer NOT NULL default 0, max_totalattachment_size integer NOT NULL default 0, max_attachments integer NOT NULL default 0, pub_perms integer NOT NULL default 0, reg_perms integer NOT NULL default 0, display_ip_address smallint NOT NULL default '1', allow_email_notify smallint NOT NULL default '1', language varchar(100) NOT NULL default 'english', email_moderators smallint NOT NULL default 0, message_count integer NOT NULL default 0, sticky_count integer NOT NULL default 0, thread_count integer NOT NULL default 0, last_post_time integer NOT NULL default 0, display_order integer NOT NULL default 0, read_length integer NOT NULL default 0, vroot integer NOT NULL default 0, edit_post smallint NOT NULL default '1',template_settings text NOT NULL default '', count_views smallint NOT NULL default 0, display_fixed smallint NOT NULL default 0, reverse_threading smallint NOT NULL default 0,inherit_id integer NULL default NULL, PRIMARY KEY (forum_id))",
        "CREATE INDEX name          on {$PHORUM['forums_table']} (name)",
		"CREATE INDEX forums_active on {$PHORUM['forums_table']} (active,parent_id)",
		"CREATE INDEX group_id      on {$PHORUM['forums_table']} (parent_id)",
        "CREATE TABLE {$PHORUM['message_table']} ( message_id serial8, forum_id integer NOT NULL default 0, thread integer NOT NULL default 0, parent_id integer NOT NULL default 0, author varchar(37) NOT NULL default '', subject varchar(255) NOT NULL default '', body text NOT NULL, email varchar(100) NOT NULL default '', ip varchar(255) NOT NULL default '', status smallint NOT NULL default '2', msgid varchar(100) NOT NULL default '', modifystamp integer NOT NULL default 0, user_id integer NOT NULL default 0, thread_count integer NOT NULL default 0, moderator_post smallint NOT NULL default 0, sort smallint NOT NULL default '2', datestamp integer NOT NULL default 0, meta text NOT NULL, viewcount integer NOT NULL default 0, closed boolean NOT NULL default false, PRIMARY KEY (message_id))",
		"CREATE INDEX thread_message    on {$PHORUM['message_table']} (thread,message_id)",
		"CREATE INDEX thread_forum      on {$PHORUM['message_table']} (thread,forum_id)",
		"CREATE INDEX special_threads   on {$PHORUM['message_table']} (sort,forum_id)",
		"CREATE INDEX status_forum      on {$PHORUM['message_table']} (status,forum_id)",
		"CREATE INDEX list_page_float   on {$PHORUM['message_table']} (forum_id,parent_id,modifystamp)",
		"CREATE INDEX list_page_flat    on {$PHORUM['message_table']} (forum_id,parent_id,thread)",
		"CREATE INDEX post_count        on {$PHORUM['message_table']} (forum_id,status,parent_id)",
		"CREATE INDEX dup_check         on {$PHORUM['message_table']} (forum_id,author,subject,datestamp)",
		"CREATE INDEX forum_max_message on {$PHORUM['message_table']} (forum_id,message_id,status,parent_id)",
		"CREATE INDEX last_post_time    on {$PHORUM['message_table']} (forum_id,status,modifystamp)",
		"CREATE INDEX next_prev_thread  on {$PHORUM['message_table']} (forum_id,status,thread)",
        "CREATE TABLE {$PHORUM['settings_table']} ( name varchar(255) NOT NULL default '', type char(1) NOT NULL default 'V' check (type in ('V','S')) , data text NOT NULL, PRIMARY KEY (name))",
        "CREATE TABLE {$PHORUM['subscribers_table']} ( user_id integer NOT NULL default 0, forum_id integer NOT NULL default 0, sub_type integer NOT NULL default 0, thread integer NOT NULL default 0, PRIMARY KEY (user_id,forum_id,thread))",
		"CREATE INDEX sub_forum_id on {$PHORUM['subscribers_table']} (forum_id,thread,sub_type)",
        "CREATE TABLE {$PHORUM['user_permissions_table']} ( user_id integer NOT NULL default 0, forum_id integer NOT NULL default 0, permission integer NOT NULL default 0, PRIMARY KEY  (user_id,forum_id))",
		"CREATE INDEX perm_forum_id on {$PHORUM['user_permissions_table']} (forum_id,permission)",
        "CREATE TABLE {$PHORUM['user_table']} ( user_id serial8, username varchar(50) NOT NULL default '', password varchar(50) NOT NULL default '',cookie_sessid_lt varchar(50) NOT NULL default '', sessid_st varchar(50) NOT NULL default '', sessid_st_timeout integer NOT NULL default 0, password_temp varchar(50) NOT NULL default '', email varchar(100) NOT NULL default '',  email_temp varchar(110) NOT NULL default '', hide_email smallint NOT NULL default 0, active smallint NOT NULL default 0, user_data text NOT NULL default '', signature text NOT NULL default '', threaded_list smallint NOT NULL default 0, posts integer NOT NULL default 0, admin smallint NOT NULL default 0, threaded_read smallint NOT NULL default 0, date_added integer NOT NULL default 0, date_last_active integer NOT NULL default 0, last_active_forum integer NOT NULL default 0, hide_activity smallint NOT NULL default 0,show_signature smallint DEFAULT 0 NOT NULL, email_notify smallint DEFAULT 0 NOT NULL, pm_email_notify smallint DEFAULT 1 NOT NULL, tz_offset smallint DEFAULT -99 NOT NULL,is_dst smallint DEFAULT 0 NOT NULL ,user_language VARCHAR( 100 ) NOT NULL default '',user_template VARCHAR( 100 ) NOT NULL default '', moderator_data text NOT NULL default '', moderation_email smallint not null default 1, PRIMARY KEY (user_id))",
		"CREATE UNIQUE INDEX username         on {$PHORUM['user_table']} (username)",
		"CREATE        INDEX user_active      on {$PHORUM['user_table']} (active)",
		"CREATE        INDEX userpass         on {$PHORUM['user_table']} (username,password)",
		"CREATE        INDEX sessid_st        on {$PHORUM['user_table']} (sessid_st)",
		"CREATE        INDEX cookie_sessid_lt on {$PHORUM['user_table']} (cookie_sessid_lt)",
		"CREATE        INDEX activity         on {$PHORUM['user_table']} (date_last_active,hide_activity,last_active_forum)",
		"CREATE        INDEX date_added       on {$PHORUM['user_table']} (date_added)",
		"CREATE        INDEX email_temp       on {$PHORUM['user_table']} (email_temp)",
        "CREATE TABLE {$PHORUM['user_newflags_table']} ( user_id integer NOT NULL default 0, forum_id bigint NOT NULL default 0, message_id bigint NOT NULL default 0, PRIMARY KEY  (user_id,forum_id,message_id) )",
        "CREATE TABLE {$PHORUM['groups_table']} ( group_id serial8, name varchar(255) NOT NULL default 0, open smallint NOT NULL default 0, PRIMARY KEY  (group_id) )",
        "CREATE TABLE {$PHORUM['forum_group_xref_table']} ( forum_id integer NOT NULL default 0, group_id bigint NOT NULL default 0, permission integer NOT NULL default 0, PRIMARY KEY  (forum_id,group_id) )",
        "CREATE TABLE {$PHORUM['user_group_xref_table']} ( user_id integer NOT NULL default 0, group_id bigint NOT NULL default 0, status smallint NOT NULL default 1, PRIMARY KEY  (user_id,group_id) )",
        "CREATE TABLE {$PHORUM['files_table']} ( file_id serial8, user_id bigint NOT NULL default 0, filename varchar(255) NOT NULL default '', filesize bigint NOT NULL default 0, file_data text NOT NULL default '', add_datetime integer NOT NULL default 0, message_id integer NOT NULL default 0, link varchar(10) NOT NULL default '', PRIMARY KEY (file_id))",
		"CREATE INDEX add_datetime    on {$PHORUM['files_table']} (add_datetime)",
		"CREATE INDEX message_id_link on {$PHORUM['files_table']} (message_id,link)",

        "CREATE TABLE {$PHORUM['search_table']} ( message_id bigint NOT NULL default 0, forum_id bigint NOT NULL default 0, search_text text NOT NULL default '', PRIMARY KEY  (message_id))",
		"CREATE INDEX search_forum_id on {$PHORUM['search_table']} (forum_id)",
		"CREATE INDEX search_text     on {$PHORUM['search_table']} (search_text)",

        "CREATE TABLE {$PHORUM['banlist_table']} ( id serial8, forum_id bigint NOT NULL default 0, type smallint NOT NULL default 0, pcre smallint NOT NULL default 0, string varchar(255) NOT NULL default '', PRIMARY KEY  (id))",
		"CREATE INDEX forum_id on {$PHORUM['banlist_table']} (forum_id)",

        "CREATE TABLE {$PHORUM['user_custom_fields_table']} ( user_id integer DEFAULT 0 NOT NULL , type INT DEFAULT 0 NOT NULL , data TEXT NOT NULL default '', PRIMARY KEY ( user_id , type ))",
        "CREATE TABLE {$PHORUM['pm_messages_table']} ( pm_message_id serial8, from_user_id integer NOT NULL default 0, from_username varchar(50) NOT NULL default '', subject varchar(100) NOT NULL default '', message text NOT NULL default '', datestamp integer NOT NULL default 0, meta text NOT NULL default '', PRIMARY KEY(pm_message_id))",
        "CREATE TABLE {$PHORUM['pm_folders_table']} ( pm_folder_id serial8, user_id integer NOT NULL default 0, foldername varchar(20) NOT NULL default '', PRIMARY KEY (pm_folder_id))",
        "CREATE TABLE {$PHORUM['pm_xref_table']} ( pm_xref_id serial8, user_id integer NOT NULL default 0, pm_folder_id integer NOT NULL default 0, special_folder varchar(10), pm_message_id integer NOT NULL default 0, read_flag smallint NOT NULL default 0, reply_flag smallint NOT NULL default 0, PRIMARY KEY (pm_xref_id))",
		"CREATE INDEX xref      on {$PHORUM['pm_xref_table']} (user_id,pm_folder_id,pm_message_id)",
		"CREATE INDEX read_flag on {$PHORUM['pm_xref_table']} (read_flag)",
        "CREATE TABLE {$PHORUM['pm_buddies_table']} ( pm_buddy_id serial8, user_id integer NOT NULL default 0, buddy_user_id integer NOT NULL default 0, PRIMARY KEY (pm_buddy_id))",
		"CREATE UNIQUE INDEX userids       on {$PHORUM['pm_buddies_table']} (user_id, buddy_user_id)",
		"CREATE        INDEX buddy_user_id on {$PHORUM['pm_buddies_table']} (buddy_user_id)",

    );
    foreach($queries as $sql){
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()){
            $retmsg = "$err<br />$sql";
            phorum_db_pg_last_error("$err: $sql");
            break;
        }
    }

    return $retmsg;
}

// uses the database-dependant functions to escape a string
function phorum_db_escape_string($str) {
    $str_tmp=pg_escape_string($str);

    return $str_tmp;
}

/**
 * This function goes through an array of queries and executes them
 */

function phorum_db_run_queries($queries){
    $PHORUM = $GLOBALS["PHORUM"];

    $conn = phorum_db_postgresql_connect();

    $retmsg = "";

    foreach($queries as $sql){
        $res = pg_query($conn, $sql);
        if ($err = pg_last_error()){
            // skip duplicate column name errors
            if(!stristr($err, "duplicate column")){
                $retmsg.= "$err<br />";
                phorum_db_pg_last_error("$err: $sql");
            }
        }
    }

    return $retmsg;
}

/**
 * This function checks that a database connection can be made.
 */

function phorum_db_check_connection(){
    $conn = phorum_db_postgresql_connect();

    return ($conn > 0) ? true : false;
}

/**
 * handy little connection function.  This allows us to not connect to the
 * server until a query is actually run.
 * NOTE: This is not a required part of abstraction
 */

function phorum_db_postgresql_connect(){
    $PHORUM = $GLOBALS["PHORUM"];

    static $conn;
    if (empty($conn)){
		$connection_string  = 'host='     . $PHORUM["DBCONFIG"]["server"]   . ' ';
		$connection_string .= 'user='     . $PHORUM["DBCONFIG"]["user"]     . ' ';
#		$connection_string .= 'password=' . $PHORUM["DBCONFIG"]["password"] . ' ';
		$connection_string .= 'dbname='   . $PHORUM["DBCONFIG"]["name"];

        $conn = pg_connect($connection_string, PGSQL_CONNECT_FORCE_NEW);
    }
    return $conn;
}

/**
 * error handling function
 * NOTE: This is not a required part of abstraction
 */

function phorum_db_pg_last_error($err){

    if(isset($GLOBALS['PHORUM']['error_logging'])) {
        $logsetting = $GLOBALS['PHORUM']['error_logging'];
    } else {
        $logsetting = "";
    }
    $adminemail = $GLOBALS['PHORUM']['system_email_from_address'];
    $cache_dir  = $GLOBALS['PHORUM']['cache'];

    if (!defined("PHORUM_ADMIN")){
        if($logsetting == 'mail') {
            include_once("./include/email_functions.php");
            $data=array('mailmessage'=>"An SQL-error occured in your phorum-installation.\n\nThe error-message was:\n$err\n\n",
                        'mailsubject'=>'Phorum: an SQL-error occured');
            phorum_email_user(array($adminemail),$data);

        } elseif($logsetting == 'file') {
            $fp = fopen($cache_dir."/phorum-sql-errors.log",'a');
            fputs($fp,time().": $err\n");
            fclose($fp);

        } else {
            echo htmlspecialchars($err);
        }
        exit();
    }else{
        echo "<!-- $err -->";
    }
}

/**
 * This function is used by the sanity checking system in the
 * admin interface to determine how much data can be transferred
 * in one query. This is used to detect problems with uploads that
 * are larger than the database server can handle.
 * The function returns the size in bytes. For database implementations
 * which do not have this kind of limit, NULL can be returned.
 */
function phorum_db_maxpacketsize ()
{
    return NULL;
}

/**
 * This function is used by the sanity checking system to let the
 * database layer do sanity checks of its own. This function can
 * be used by every database layer to implement specific checks.
 *
 * The return value for this function should be exactly the same
 * as the return value expected for regular sanity checking
 * function (see include/admin/sanity_checks.php for information).
 *
 * There's no need to load the sanity_check.php file for the needed
 * constants, because this function should only be called from the
 * sanity checking system.
 */
function phorum_db_sanitychecks()
{
    $PHORUM = $GLOBALS["PHORUM"];

    // Retrieve the PostgreSQL server version.
    $conn = phorum_db_postgresql_connect();
    $res = pg_query($conn, "SELECT version()");
    if (!$res) {
		echo ' oh, we do have an error';
		return array(
        PHORUM_SANITY_WARN,
        "The database layer could not retrieve the version of the
         running PostgreSQL server",
        "This probably means that you are running a really old PostgreSQL
         server, which does not support \"SELECT version()\"
         as an SQL command. If you are not running a PostgresSQL server
         with version 7.4 or higher, then please upgrade your
         PostgreSQL server. Else, contact the Phorum developers to see
         where this warning is coming from"
    	);
	}

    if (pg_num_rows($res))
    {
        $row = pg_fetch_array($res);
        $ver = explode(" ", $row[0]);

        // Version numbering format which is not recognized.
        if (count($ver) < 1) return array(
            PHORUM_SANITY_WARN,
            "The database layer was unable to recognize the PostgresSQL server's
             version number \"" . htmlspecialchars($row[0]) . "\". Therefore,
             checking if the right version of MySQL is used is not possible.",
            "Contact the Phorum developers and report this specific
             version number, so the checking scripts can be updated."
        );

		$version = $ver[1];

echo 'version is ' . $version;

		$vers = explode('.', $version);

        settype($vers[0], 'int');
        settype($vers[1], 'int');
        settype($vers[2], 'int');

        // MySQL before version 4.
        if ($vers[0] < 7) return array(
            PHORUM_SANITY_CRIT,
            "The PostgreSQL database server that is used is too old. The
             running version is \"" . htmlspecialchars($row[0]) . "\",
             while PostgreSQL 7.4 or higher is recommended.",
            "Upgrade your MySQL server to a newer version. If your
             website is hosted with a service provider, please contact
             the service provider to upgrade your MySQL database."
        );

// XXX - this condition needs to be fixed once we have full text search going
        // MySQL before version 4.0.18, with full text search enabled.
        if ($PHORUM["DBCONFIG"]["mysql_use_ft"] &&
            $ver[0] == 4 && $ver[1] == 0 && $ver[2] < 18) return array(
            PHORUM_SANITY_WARN,
            "The PostgreSQL database server that is used does not
             support all Phorum features. The running version is
             \"" . htmlspecialchars($row[0]) . "\", while PostgreSQL version
             7.4 or higher is recommended.",
            "Upgrade your PostgreSQL server to a newer version. If your
             website is hosted with a service provider, please contact
             the service provider to upgrade your PostgreSQL database.
             If upgrading is not possible, you can also disable the
             option \"mysql_use_ft\" in your database configuration
             file \"include/db/config.php\""
        );

        // All checks are okay.
        return array (PHORUM_SANITY_OK, NULL);
    }

    return array(
        PHORUM_SANITY_CRIT,
        "An unexpected problem was found in running the sanity
         check function phorum_db_sanitychecks().",
        "Contact the Phorum developers to find out what the problem is."
    );
}

function pgsql_insert_id($conn, $sequence) {

	$sql = "select currval('{$sequence}') as currval";
	$res = pg_query($conn, $sql);
	if ($res) {
		$rec = pg_fetch_assoc($res);
		$insert_id = $rec['currval'];
	} else {
		if ($err = pg_last_error()) phorum_db_pg_last_error("$err: $sql");
	}

	return $insert_id;
}

?>
