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
 * These functions are Phorum's interface to the user data.  If you want
 * to use your own user data, just replace these functions.
 *
 * The functions do use Phorum's database layer.  Of course, it is not
 * required.
 */
// if you write your own user layer, set this to false
define( "PHORUM_ORIGINAL_USER_CODE", true );

define( "PHORUM_SESSION_LONG_TERM" , "phorum_session_v5" );
define( "PHORUM_SESSION_SHORT_TERM", "phorum_session_st" );
define( "PHORUM_SESSION_ADMIN", "phorum_admin_session" );

function phorum_user_check_session( $cookie = PHORUM_SESSION_LONG_TERM )
{
    $PHORUM = $GLOBALS["PHORUM"];

    // If we do URI based authentication, we will only look at the
    // PHORUM_SESSION_LONG_TERM session (which is the session key that is
    // stored in the URI). Here we rewrite requests for
    // PHORUM_SESSION_SHORT_TERM so we will handle tighter security correctly.
    if ( isset($PHORUM["use_cookies"]) && ! $PHORUM["use_cookies"] &&
         $cookie == PHORUM_SESSION_SHORT_TERM) {
        $cookie = PHORUM_SESSION_LONG_TERM;
    }

    if ( ( $cookie != PHORUM_SESSION_LONG_TERM || ( isset( $PHORUM["use_cookies"] ) && $PHORUM["use_cookies"] ) ) && isset( $_COOKIE[$cookie] ) ) { // REAL cookies ;)
        $sessid = $_COOKIE[$cookie];
        $GLOBALS["PHORUM"]["use_cookies"]=true;
    } elseif ( isset( $PHORUM["args"][$cookie] ) ) { // in the p5-urls
        $sessid = $PHORUM["args"][$cookie];
        $GLOBALS["PHORUM"]["use_cookies"]=false;
    } elseif ( isset( $_POST[$cookie] ) ) { // from post-forms
        $sessid = $_POST[$cookie];
        $GLOBALS["PHORUM"]["use_cookies"]=false;
    } elseif ( isset( $_GET[$cookie] ) ) { // should rarely happen but helps in some cases
        $sessid = $_GET[$cookie];
        $GLOBALS["PHORUM"]["use_cookies"]=false;
    }

    $success = false;

    if ( !empty( $sessid ) && $GLOBALS["PHORUM"]["use_cookies"]) {
        // this part is for cookie-authentication where we have username and password
        list( $userid, $md5session ) = explode( ":", $sessid, 2 );

        if(!is_numeric($userid)) {
            phorum_user_clear_session( $cookie );
            return false;
        }

        $user=phorum_user_get($userid, true, true);
        if (empty($user)) {
            phorum_user_clear_session( $cookie );
            return false;
        }

        if ( ($cookie==PHORUM_SESSION_LONG_TERM && !empty($user['cookie_sessid_lt']) && $user['cookie_sessid_lt'] == $md5session) ||
             ($cookie==PHORUM_SESSION_SHORT_TERM && !empty($user['sessid_st']) && $user['sessid_st'] == $md5session) ||
             ($cookie==PHORUM_SESSION_ADMIN && !empty($user['cookie_sessid_lt']) && md5($user['cookie_sessid_lt'].$PHORUM["admin_session_salt"]) == $md5session) ) {
            if ( $user["active"] ) {
                // write access is false by default, need to check the st-cookie too
                $user['write_access']=false;

                $GLOBALS["PHORUM"]["user"] = $user;
                $success = true;

                phorum_user_create_session( $cookie );
            } else {
                phorum_user_clear_session( $cookie );
            }
        }
    } elseif( !empty( $sessid ) && !$GLOBALS["PHORUM"]["use_cookies"]) {
        // this part is for uri-authentication where we only have a session-id
        $uri_session_id = urldecode( $sessid );
        if ( $user_id = phorum_db_user_check_field('sessid_st',$uri_session_id,'=')) {
            $user = phorum_user_get( $user_id, true, true );
            if ( $user["active"] ) {

                // write access is enabled for uri-authentication as thats requiring login at every visit
                $user['write_access']=true;

                $GLOBALS["PHORUM"]["user"] = $user;
                $success = true;
                phorum_user_create_session( $cookie, false, $user['sessid_st'] );
            } else {
                phorum_user_clear_session( $cookie );
            }
        }
    }

    // track user activity
    if($success && $PHORUM["track_user_activity"] && $GLOBALS["PHORUM"]["user"]["date_last_active"] < time() - $PHORUM["track_user_activity"] ) {
        $tmp_user["user_id"] = $GLOBALS["PHORUM"]["user"]["user_id"];
        $tmp_user["date_last_active"] = time();
        if(isset($PHORUM['forum_id'])) {
            $tmp_user["last_active_forum"]= $PHORUM['forum_id'];
        } else {
            $tmp_user["last_active_forum"]= 0;
        }
        phorum_user_save_simple( $tmp_user);
    }

    return $success;
}

function phorum_user_create_session( $cookie = PHORUM_SESSION_LONG_TERM, $refresh = false, $uri_session_id = '' )
{
    $PHORUM = $GLOBALS["PHORUM"];

    // require that the global user exists
    if ( !empty( $PHORUM["user"] ) ) {
        $user = $PHORUM["user"];

        if ( (isset( $PHORUM["use_cookies"] ) && $PHORUM["use_cookies"]) || $cookie == PHORUM_SESSION_ADMIN ) {

            switch($cookie){
                case PHORUM_SESSION_SHORT_TERM:

                    // creating a new shortterm-session-id if none exists yet or it has timed out
                    if($refresh || empty($user['sessid_st']) || $user["sessid_st_timeout"]<time()) {
                        $sessid=md5($user['username'].microtime().$user['password']);
                        $timeout = time() + $PHORUM["short_session_timeout"]*60;
                        $simple_user=array('user_id'=>$user['user_id'],'sessid_st'=>$sessid,'sessid_st_timeout'=>$timeout);
                        phorum_user_save_simple($simple_user);

                    // if the cookie is half expired, reset it.
                    } elseif(time() - $user["sessid_st_timeout"] < $PHORUM["short_session_timeout"]*60/2){
                        $sessid=$user['sessid_st'];
                        $timeout = time() + $PHORUM["short_session_timeout"]*60;
                        $simple_user=array('user_id'=>$user['user_id'],'sessid_st'=>$sessid,'sessid_st_timeout'=>$timeout);
                        phorum_user_save_simple($simple_user);
                    }

                    // if a timeout was set, we need to set a new cookie
                    if($timeout){
                        setcookie( $cookie, $user['user_id'].':'.$sessid, $timeout, $PHORUM["session_path"], $PHORUM["session_domain"] );
                    }
                    break;

                case PHORUM_SESSION_LONG_TERM:
                    // creating a new longterm-session-id if none exists yet
                    if($refresh || empty($user['cookie_sessid_lt'])) {
                        $sessid=md5($user['username'].microtime().$user['password']);
                        $simple_user=array('user_id'=>$user['user_id'],'cookie_sessid_lt'=>$sessid);
                        phorum_user_save_simple($simple_user);
                    } else {
                        $sessid=$user['cookie_sessid_lt'];
                    }

                    if($PHORUM["session_timeout"]==0){
                        $timeout = 0;
                    } else {
                        $timeout = time() + 86400 * $PHORUM["session_timeout"];
                    }

                    setcookie( $cookie, $user['user_id'].':'.$sessid, $timeout, $PHORUM["session_path"], $PHORUM["session_domain"] );

                    break;

                case PHORUM_SESSION_ADMIN:
                    // creating a new longterm-session-id if none exists yet
                    if(empty($user['cookie_sessid_lt'])) {
                        $sessid=md5($user['username'].microtime().$user['password']);
                        $simple_user=array('user_id'=>$user['user_id'],'cookie_sessid_lt'=>$sessid);
                        phorum_user_save_simple($simple_user);
                    } else {
                        $sessid=$user['cookie_sessid_lt'];
                    }

                    setcookie( $cookie, $user['user_id'].':'.md5($sessid.$PHORUM["admin_session_salt"]), 0, $PHORUM["session_path"], $PHORUM["session_domain"] );

                    break;

            }

        } else {
            $sessid = $uri_session_id;
            $GLOBALS["PHORUM"]["DATA"]["GET_VARS"][$cookie] = "$cookie=" . urlencode( $sessid );
            $GLOBALS["PHORUM"]["DATA"]["POST_VARS"] .= "<input type=\"hidden\" name=\"$cookie\" value=\"$sessid\" />";
        }
    }
}





function phorum_user_clear_session( $cookie = PHORUM_SESSION_LONG_TERM )
{
    setcookie( $cookie, "", time()-86400, $GLOBALS["PHORUM"]["session_path"], $GLOBALS["PHORUM"]["session_domain"] );
}

/**
 * This function retrieves a user from the database, given the user id.
 * If $user_id is an array of user ids, it will retrieve all of the users
 * in the array. If $detailed is set to true, the function gets the users
 * full information. Setting this to false omits permission data, pm counts,
 * and group membership. $detailed is true by default and may be omitted.
 * @param user_id - can be a single user id, or an array of user ids.
 * @param detailed - get detailed user information (defaults to true).
 * @param checknewpm - check for new private messages for the user (defaults to false).
 * @return array - either an array representing a single user's information,
 *                 or an array of users
 */
function phorum_user_get( $user_id, $detailed = true, $checkpm = false )
{
    $PHORUM = $GLOBALS["PHORUM"];

    if ( !is_array( $user_id ) ) {
        $user_ids = array( $user_id );
    } else {
        $user_ids = $user_id;
    }

    if ( count( $user_ids ) ) {
        $cache_users=array();
        $tmp_users=array();
        $cachecnt=0;

        // get users from cache if enabled
        if(isset($PHORUM['cache_users']) && $PHORUM['cache_users']) {
            foreach($user_ids as $id => $cur_user_id) {
                $data=phorum_cache_get('user',$cur_user_id);
                if($data != null) { // null if no key found
                    $cache_users[$cur_user_id]=$data;

                    unset($user_ids[$id]);
                    $cachecnt++;
                }
            }
            unset($data);
            // we need to get the dynamic data too!
            // only selecting date_last_active, forum_last_active,
            // posts ... any more?
            if($cachecnt > 0) {
                $dynamic_data=phorum_db_user_get_fields(array_keys($cache_users),array('date_last_active','last_active_forum','posts'));
                foreach($dynamic_data as $d_uid => $d_data) {
                        $cache_users[$d_uid]=array_merge($cache_users[$d_uid],$d_data);
                }

            }
        }

        if(count($user_ids)) {
            $tmp_users = phorum_db_user_get( $user_ids, $detailed );

            foreach( $tmp_users as $uid => $user ) {

                if ( !$user["admin"] ) {
                    if ( isset( $user["group_permissions"] ) ) {
                        foreach( $user["group_permissions"] as $forum_id => $perm ) {
                            if(!isset($user["permissions"][$forum_id]))
                                $user["permissions"][$forum_id]=0;

                            $user["permissions"][$forum_id] = $user["permissions"][$forum_id] | $perm;
                        }
                    }

                    if ( isset( $user["forum_permissions"] ) ) {
                        foreach( $user["forum_permissions"] as $forum_id => $perm ) {
                            $user["permissions"][$forum_id] = $perm;
                        }
                    }
                }

                // check if the user has new private messages
                if ( ($checkpm || (isset($PHORUM['cache_users']) && $PHORUM['cache_users'])) && $PHORUM["enable_pm"] && $PHORUM["enable_new_pm_count"] ) {
                    $user["new_private_messages"] = phorum_db_pm_checknew( $uid );
                }

                // store users in cache if enabled
                if( $detailed && isset($PHORUM['cache_users']) && $PHORUM['cache_users']) {
                    phorum_cache_put('user',$uid,$user);
                }
                $tmp_users[$uid] = $user;
            }
        }
    }

    // merging cached and retrieved users
    $ret = $tmp_users + $cache_users;

    if ( !is_array( $user_id ) ) {
        if (isset($ret[$user_id]))
            $ret = $ret[$user_id];
        else
            $ret = NULL;
    }

    return $ret;
}

/**
 * This function gets a list of all the active users.
 * @return array of users (same format as phorum_user_get)
 */
function phorum_user_get_list()
{
   return phorum_hook("user_list", phorum_db_user_get_list());
}

function phorum_user_save( $user )
{
    if ( empty( $user["user_id"] ) ) return false;

    $old_user = phorum_user_get( $user['user_id'] );
    $db_user = phorum_user_prepare_data( $user, $old_user );

    $ret = phorum_db_user_save( $db_user );

    // remove that user from the cache
    if(isset($GLOBALS["PHORUM"]['cache_users']) && $GLOBALS["PHORUM"]['cache_users']) {
        phorum_cache_remove('user',$user['user_id']);
    }

    // Is this the currently logged in user?
    // If so, re-get his stuff from the system.
    if ( isset($GLOBALS["PHORUM"]["user"]) && $GLOBALS["PHORUM"]["user"]["user_id"] == $user["user_id"] ) {
        $GLOBALS["PHORUM"]["user"] = phorum_user_get( $user["user_id"] );
    }

    // Sync the campsite user
    require_once('../../admin-files/localizer/Localizer.php');
    require_once('../../classes/User.php');
    $campsiteUser = new User($user["fk_campsite_user_id"]);
    if ($campsiteUser->exists()) {
        if (array_key_exists('password', $user)) {
            $campsiteUser->setPassword($user["password"]);
        }
    }

    return $ret;
}
/**
 * This function quickly updates real columns without any further checks
 * it just stores the data as fast as possible
 *
 */
function phorum_user_save_simple($user)
{
    if ( empty( $user["user_id"] ) ) return false;

    // clear the cache only if we are not just updating the activity
    if(isset($GLOBALS['PHORUM']['cache_users']) && $GLOBALS['PHORUM']['cache_users']) {
        if(!(count($user) == 3 && isset($user['date_last_active'])))
            phorum_cache_remove('user',$user['user_id']);
    }

    $ret = phorum_db_user_save( $user );

    return $ret;
}

function phorum_user_check_login( $username, $password )
{
    $ret = false;
    $temp_check = false;

    $user_id = phorum_db_user_check_pass( $username, sha1( $password ) );
    // regular password failed, try the temp password
    if ( $user_id == 0 ) {
        $user_id = phorum_db_user_check_pass( $username, sha1( $password ), true );
        $temp_check = true;
    }

    if ( $user_id > 0 ) {
        // if this was a temp password, set the normal pass to the temp password
        // do this before we get the user so the data is up to date.
        // leave the temp password alone as setting to empty is bad.
        if ( $temp_check ) {
            $tmp_user["user_id"] = $user_id;
            $tmp_user["password"] = $password;
            phorum_user_save( $tmp_user );
        }

        $ret = phorum_user_set_current_user( $user_id );
    }

    return $ret;
}

function phorum_user_verify( $user_id, $tmp_pass )
{
    $user_id = phorum_db_user_check_field( array( "user_id", "password_temp" ), array( $user_id, sha1( $tmp_pass ) ), array( "=", "=" ) );
    return $user_id;
}

function phorum_user_set_current_user( $user_id )
{
    $ret = false;

    $user = phorum_user_get( $user_id );
    if ( $user["active"] == PHORUM_USER_ACTIVE ) {
        $GLOBALS["PHORUM"]["user"] = $user;
        $ret = true;
    }

    return $ret;
}

function phorum_user_check_username( $username )
{
    return phorum_db_user_check_field( "username", $username );
}

function phorum_user_check_email( $email )
{
    return phorum_db_user_check_field( "email", $email );
}

/**
* (generic) function for checking a user-field in the database
*/
function phorum_user_check_field( $field_name, $field_value)
{
    return phorum_db_user_check_field( $field_name , $field_value );
}

/**
* function for adding a user to the database (using the db-layer)
*/
function phorum_user_add( $user, $pwd_unchanged = false )
{
    if ( empty( $user["password_temp"] ) ) $user["password_temp"] = $user["password"];
    $db_user = phorum_user_prepare_data( $user, array(), $pwd_unchanged );
    if(empty($db_user["date_added"])) $db_user["date_added"]=time();
    if(empty($db_user["date_last_active"])) $db_user["date_last_active"]=time();
    return phorum_db_user_add( $db_user );
}

function phorum_user_prepare_data( $new_user, $old_user, $pwd_unchanged = false )
{
    $PHORUM = $GLOBALS["PHORUM"];
    // how the user appears to the app and how it is stored in the db are different.
    // This function prepares the data for storage in the database.
    // While this may seem like a crossing of database vs. front end, it is better that
    // this is here as it is not directly related to database interaction.
    // we need to preserve some data, therefore we use the old user
    unset( $old_user['password'] );
    unset( $old_user['password_temp'] );
    if ( is_array( $old_user ) ) {
        $user = $old_user;
    } else {
        $user = array();
    }
    foreach( $new_user as $key => $val ) {
        $user[$key] = $val;
    }

    foreach( $user as $key => $val ) {
        switch ( $key ) {
            // these are all the actual fields in the user
            // table.  We don't need to do anything to them.
            case "user_id":
            case "username":
            case "email":
            case "email_temp":
            case "hide_email":
            case "active":
            case "user_data":
            case "signature":
            case "threaded_list":
            case "posts":
            case "admin":
            case "threaded_read":
            case "hide_activity":
            case "permissions":
            case "forum_permissions":
            case "date_added":
            case "date_last_active":
            case "group_permissions":
            case "groups":
            case "show_signature":
            case "email_notify":
            case "pm_email_notify":
            case "tz_offset":
            case "is_dst":
            case "user_language":
            case "user_template":
            case "moderation_email":
                break;
            // the phorum built in user module stores sha1 passwords.
            case "password":
            case "password_temp":
                if ( !$pwd_unchanged ) {
                    $user[$key] = sha1( $val );
                } elseif ( $pwd_unchanged == -1 ) {
                    $user[$key] = $val;
                }
                break;
            // everything that is not one of the above fields is stored in a
            // serialized text field for dynamic profile variables.
            // If the field is not in the PROFILE_FIELDS array, we don't add it.
            default:
                $type=-1;
                // find out which ID that custom-field has
                foreach($PHORUM['PROFILE_FIELDS'] as $ctype => $cdata) {
                    if($cdata['name'] == $key) {
                        $type=$ctype;
                        break;
                    }
                }
                if($type != -1) { // store it only if we found it
                    if( $val!=="") {
                         if(!is_array($val)) {
                            $user_data[$type] = substr($val,0,$PHORUM['PROFILE_FIELDS'][$type]['length']);
                         } else {
                            $user_data[$type] = $val;
                         }
                    } elseif(!isset($user_data)){
                         $user_data=array();
                    }
                }
                unset( $user[$key] );
        }
        // create the serialized var
        if ( isset( $user_data ) ) {
            $user["user_data"] = $user_data;
        }
    }

    return $user;
}

function phorum_user_subscribe( $user_id, $forum_id, $thread, $type )
{
    $list=phorum_user_access_list( PHORUM_USER_ALLOW_READ );
    if(!in_array($forum_id, $list)) return;
    return phorum_db_user_subscribe( $user_id, $forum_id, $thread, $type );
}

function phorum_user_unsubscribe( $user_id, $thread, $forum_id=0 )
{
    if($forum_id){
        return phorum_db_user_unsubscribe( $user_id, $thread, $forum_id );
    } else {
        return phorum_db_user_unsubscribe( $user_id, $thread );
    }
}

/**
 * This function returns true if the current user is allowed to moderate $forum_id or the user given through user_data
 */

function phorum_user_moderate_allowed( $forum_id = 0, $user_data = 0 )
{
    $PHORUM = $GLOBALS["PHORUM"];

    if ( $user_data == 0 ) {
        $user_data = $PHORUM["user"];
    }
    // if this is an admin, stop now
    if ( $user_data["admin"] ) return true;

    // they have no special permissions, return
    if(!isset($user_data["permissions"])){
        return false;
    }

    // this sets up a check for moderation at any level
    if ( $forum_id==PHORUM_MODERATE_ALLOWED_ANYWHERE ){
        $perms = $user_data["permissions"];
    } else {
        // else we check only one forum
        // if no forum_id passed, check current forum
        if ( $forum_id==0 ){
            $forum_id = $PHORUM["forum_id"];
        }
        if(isset($user_data["permissions"][$forum_id])){
            $perms[$forum_id] = $user_data["permissions"][$forum_id];
        } else {
            return false;
        }
    }

    // check the users permission array
    foreach($perms as $forum_id => $perm) {
        if ( $perm & PHORUM_USER_ALLOW_MODERATE_MESSAGES ) {
            return true;
        }
    }

    return false;
}

/**
 * calls the db-function for listing all the moderators for a forum
 * This returns an array of moderators, key as their userid, value as their email address.
 */
function phorum_user_get_moderators( $forum_id , $ignore_user_perms = false, $for_email = false)
{
    $gotmods=false;
    if(isset($GLOBALS["PHORUM"]['cache_users']) && $GLOBALS["PHORUM"]['cache_users']) {
        $mods=phorum_cache_get('user','moderators-'.$forum_id.'-'.$ignore_user_perms);
        if($mods != null) {
            $gotmods=true;
        }
    }
    if(!$gotmods) {
        $mods=phorum_db_user_get_moderators( $forum_id , $ignore_user_perms, $for_email);
    }
    return $mods;
}

/**
 * phorum_user_access_allowed()
 *
 * @param  $permission Use the PHORUM_ALLOW_* constants
 * @return bool
 */
function phorum_user_access_allowed( $permission, $forum_id = 0 )
{
    $PHORUM = $GLOBALS["PHORUM"];

    if ( empty( $forum_id ) ) $forum_id = $PHORUM["forum_id"];

    $ret = false;
    // user is an admin, he gets it all
    if ( !empty( $PHORUM["user"]["admin"] ) ) {
        $ret = true;
    } else {
        // user is logged in.
        if ( $PHORUM["user"]["user_id"] > 0 ) {
            // if the user has perms for this forum, use them.
            if ( isset( $PHORUM["user"]["permissions"][$forum_id] ) ) {
                $perms = $PHORUM["user"]["permissions"][$forum_id];
            // else we use the forum's default perms
            // for registered users
            } elseif ( $forum_id ) {
                if ( $forum_id != $PHORUM["forum_id"] ) {
                    $forums = phorum_db_get_forums( $forum_id );
                    $forum = array_shift( $forums );
                } else {
                    $forum = $PHORUM;
                }
                $perms = $forum["reg_perms"];
            }
        // user is not logged in
        // use the forum default perms for public users
        } elseif ( $forum_id ) {
            if ( $forum_id != $PHORUM["forum_id"] ) {
                $forums = phorum_db_get_forums( $forum_id );
                $forum = array_shift( $forums );
            } else {
                $forum = $PHORUM;
            }
            if(isset($forum['pub_perms']))
                $perms = $forum["pub_perms"];
        }

        if ( !empty( $perms ) && ( $ret || ( $perms &$permission ) ) ) {
            $ret = true;
        } else {
            $ret = false;
        }
    }

    return $ret;
}

/**
 * phorum_user_access_list()
 *
 * This function will return a list of forum ids in which
 * the current user has $permission
 *
 * @param  $permission Use the PHORUM_ALLOW_* constants
 * @return bool
 */

function phorum_user_access_list( $permission )
{
    $PHORUM = $GLOBALS["PHORUM"];

    $forums = phorum_db_get_forums(0,-1,$PHORUM['vroot']);
    $forum_list = array();

    $field = ( $PHORUM["user"]["user_id"] > 0 ) ? "reg_perms" : "pub_perms";

    foreach( $forums as $forum_id => $forum ) {
        if ( $PHORUM["user"]["admin"] || $forum[$field] &$permission ) {
            $forum_list[$forum_id] = $forum_id;
        }
        // if its a folder, they have read but nothing else
        elseif ($forum["folder_flag"] && $permission == PHORUM_USER_ALLOW_READ){
            $forum_list[$forum_id] = $forum_id;
        }
    }

    if ( !$PHORUM["user"]["admin"] && !empty( $PHORUM["user"]["permissions"] ) ) {
        foreach( $PHORUM["user"]["permissions"] as $forum_id => $perms ) {
            if ( isset( $forum_list[$forum_id] ) ) unset( $forum_list[$forum_id] );
            if ( $perms & $permission ) {
                $forum_list[$forum_id] = $forum_id;
            }
        }
    }

    // Admins also have rights for forum_id 0 (announcements)
    if ($PHORUM["user"]["admin"]) {
        $forum_list[0] = 0;
    }

    return $forum_list;
}

/**
 * phorum_user_allow_moderate_group()
 *
 * Return true if the current user is allowed to moderate
 * a given group, or any group if no group is given.
 *
 * @param int - a group id to check (default, all)
 * @return bool
 */
function phorum_user_allow_moderate_group($group_id = 0)
{
    $groups = phorum_user_get_moderator_groups();
    if ($group_id == 0 && count($groups) > 0){
        return true;
    }
    elseif (isset($groups[$group_id])){
        return true;
    }
    else{
        return false;
    }
}

/**
 * phorum_user_get_moderator_groups()
 *
 * This function will return a list of the groups the current user
 * is allowed to moderate. For admins, this will return all the groups.
 *
 * The array is of the form array[group_id] = groupname.
 * @return array
 */
function phorum_user_get_moderator_groups()
{
    $PHORUM=$GLOBALS["PHORUM"];
    $groups = array();
    $fullgrouplist = phorum_db_get_groups();

    // if its an admin, return all groups as a moderator
    if ($PHORUM["user"]["admin"]){
        // the permission here is for a forum, we don't care about that
        foreach ($fullgrouplist as $groupid => $groupperm){
            $groups[$groupid] = $fullgrouplist[$groupid]["name"];
        }
    }
    else {
        $grouplist = phorum_user_get_groups($PHORUM["user"]["user_id"]);
        foreach ($grouplist as $groupid => $perm){
            if ($perm == PHORUM_USER_GROUP_MODERATOR){
                $groups[$groupid] = $fullgrouplist[$groupid]["name"];
            }
        }
    }
    return $groups;
}

/**
 * phorum_user_get_groups()
 *
 * This function will return a list of groups the user
 * is a member of, as well as the users permissions.
 *
 * The returned list has the group id as the key, and
 * the permission as the value. Permissions are the
 * PHORUM_USER_GROUP constants.
 * @param int - the users user_id
 * @return array
 */
function phorum_user_get_groups($user_id)
{
    return phorum_db_user_get_groups($user_id);
}

/**
 * phorum_user_save_groups()
 *
 * This function saves a users group permissions. The data
 * to save should be an array of the form array[group_id] = permission
 * @param int - the users user_id
 * @param array - group permissions to save
 * @return bool - true if successful
 */
function phorum_user_save_groups($user_id, $groups)
{
    if(isset($GLOBALS["PHORUM"]['cache_users']) && $GLOBALS["PHORUM"]['cache_users']) {
        phorum_cache_remove('user',$user_id);
    }
    return phorum_db_user_save_groups($user_id, $groups);
}

function phorum_user_addpost()
{
    return phorum_db_user_addpost();
}

function phorum_user_delete($user_id)
{
    if(isset($GLOBALS["PHORUM"]['cache_users']) && $GLOBALS["PHORUM"]['cache_users']) {
        phorum_cache_remove('user',$user_id);
    }
    return phorum_db_user_delete($user_id);
}

/**
 * phorum_user_check_custom_field()
 *
 * This function takes a custom-fields name and content
 * as arguments and returns an array of the user_ids found
 * or NULL if no users are found
 *
 * optional match-parameter
 * 0 - exact match
 * 1 - like-clause
 */
function phorum_user_check_custom_field($field_name,$field_content,$match=0) {

    $type=-1;
    foreach($GLOBALS['PHORUM']['PROFILE_FIELDS'] as $ctype => $cdata) {
        if($cdata['name'] == $field_name) {
            $type=$ctype;
            break;
        }
    }
    if($type > -1) {
        $retval=phorum_db_get_custom_field_users($type,$field_content,$match);
    } else {
        $retval=NULL;
    }

    return $retval;
}


?>
