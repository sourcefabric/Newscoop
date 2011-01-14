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

// Check that this file is not loaded directly.
if ( basename( __FILE__ ) == basename( $_SERVER["PHP_SELF"] ) ) exit();

// all other constants in ./include/constants.php
define( "PHORUM", "5.1-dev" );

// our internal version in format of year-month-day-serial
define( "PHORUMINTERNAL", "2006032300" );

define( "DEBUG", 0 );

include_once( "./include/constants.php" );

// setup the PHORUM var
$PHORUM = array();

// temp member to hold arrays and such in templates
$PHORUM["TMP"] = array();

// The data member is the data the templates can access
$PHORUM["DATA"] = array();
$PHORUM["DATA"]["GET_VARS"] = array();
$PHORUM["DATA"]["POST_VARS"] = "";

// get the forum id if set with a post
if ( isset( $_REQUEST["forum_id"] ) && is_numeric( $_REQUEST["forum_id"] ) ) {
    $PHORUM["forum_id"] = $_REQUEST["forum_id"];
}

// strip the slashes off of POST data if magic_quotes is on
if ( get_magic_quotes_gpc() && count( $_REQUEST ) ) {
    foreach( $_POST as $key => $value ) {
        if ( !is_array( $value ) )
            $_POST[$key] = stripslashes( $value );
        else
            $_POST[$key] = phorum_recursive_stripslashes( $value );
    }
    foreach( $_GET as $key => $value ) {
        if ( !is_array( $value ) )
            $_GET[$key] = stripslashes( $value );
        else
            $_GET[$key] = phorum_recursive_stripslashes( $value );
    }
}

// look for and parse the QUERY_STRING
// this only applies to urls that we create.
// scrips using urls from forms (search) should use $_GET or $_POST
if ( !defined( "PHORUM_ADMIN" ) ) {
    if ( isset( $_SERVER["QUERY_STRING"] ) || isset( $PHORUM["CUSTOM_QUERY_STRING"] ) ) {
        $Q_STR = empty( $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"] ) ? $_SERVER["QUERY_STRING"]: $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"];

        // ignore stuff past a #
        if ( strstr( $Q_STR, "#" ) ) list( $Q_STR, $other ) = explode( "#", $Q_STR );

        // explode it on comma
        $PHORUM["args"] = explode( ",", $Q_STR );

        // check for any assigned values
        if ( strstr( $Q_STR, "=" ) ) {
            foreach( $PHORUM["args"] as $key => $arg ) {

                // if an arg has an = create an element in args
                // with left part as key and right part as value
                if ( strstr( $arg, "=" ) ) {
                    list( $var, $value ) = explode( "=", $arg );
                    $PHORUM["args"][$var] = urldecode( $value );
                    // get rid of the numbered arg, it is useless.
                    unset( $PHORUM["args"][$key] );
                }
            }
        }

        // set forum_id if not set already by
        if ( empty( $PHORUM["forum_id"] ) && isset( $PHORUM["args"][0] ) ) {
            $PHORUM["forum_id"] = ( int )$PHORUM["args"][0];
        }
    }
}

// set the forum_id to 0 if not set by now.
if ( empty( $PHORUM["forum_id"] ) ) $PHORUM["forum_id"] = 0;

// Get the database settings.
if ( empty( $GLOBALS["PHORUM_ALT_DBCONFIG"] ) || $GLOBALS["PHORUM_ALT_DBCONFIG"]==$_REQUEST["PHORUM_ALT_DBCONFIG"] || !defined("PHORUM_WRAPPER") ) {
    // Backup display_errors setting.
    $orig = ini_get("display_errors");
    ini_set("display_errors", 0);

    // Load configuration.
    if (! include_once( "./include/db/config.php" )) {
        print '<html><head><title>Phorum error</title></head><body>';
        print '<h2>Phorum database configuration error</h2>';

        // No database configuration found.
        if (!file_exists("./include/db/config.php")) { ?>
            Phorum has been installed on this server, but the configuration<br/>
            for the database connection has not yet been made. Please read<br/>
            <a href="docs/install.txt">docs/install.txt</a> for installation instructions. <?php
        } else {
            $fp = fopen("./include/db/config.php", "r");
            // Unable to read the configuration file.
            if (!$fp) { ?>
                A database configuration file was found in ./include/db/config.php,<br/>
                but Phorum was unable to read it. Please check the file permissions<br/>
                for this file. <?php
            // Unknown error.
            } else {
                fclose($fp); ?>
                A database configuration file was found in ./include/dbconfig.php,<br/>
                but it could not be loaded. It possibly contains one or more errors.<br/>
                Please check your configuration file. <?php
            }
        }

        print '</body></html>';
        exit(1);
    }

    // Restore original display_errors setting.
    ini_set("display_errors", $orig);
} else {
    $PHORUM["DBCONFIG"] = $GLOBALS["PHORUM_ALT_DBCONFIG"];
}

// Load the database layer.
include_once( "./include/db/{$PHORUM['DBCONFIG']['type']}.php" );

if(!phorum_db_check_connection()){
    if(isset($PHORUM["DBCONFIG"]["down_page"])){
        header("Location: ".$PHORUM["DBCONFIG"]["down_page"]);
        exit();
    } else {
        echo "The database connection failed. Please check your database configuration in include/db/config.php. If the configuration is okay, check if the database server is running.";
        exit();
    }
}

// get the Phorum settings
phorum_db_load_settings();

// a hook for rewriting vars at the beginning of common.php,
//right after loading the settings from the database
phorum_hook( "common_pre", "" );

include_once( "./include/cache.php" );

// stick some stuff from the settings into the DATA member
$PHORUM["DATA"]["TITLE"] = ( isset( $PHORUM["title"] ) ) ? $PHORUM["title"] : "";
$PHORUM["DATA"]["HTML_TITLE"] = ( !empty( $PHORUM["html_title"] ) ) ? $PHORUM["html_title"] : $PHORUM["DATA"]["TITLE"];
$PHORUM["DATA"]["HEAD_TAGS"] = ( isset( $PHORUM["head_tags"] ) ) ? $PHORUM["head_tags"] : "";
$PHORUM["DATA"]["FORUM_ID"] = $PHORUM["forum_id"];

////////////////////////////////////////////////////////////
// only do this stuff if we are not in the admin

if ( !defined( "PHORUM_ADMIN" ) ) {

    // if the Phorum is disabled, display a message.
    if(isset($PHORUM["status"]) && $PHORUM["status"]=="disabled"){
        if(!empty($PHORUM["disabled_url"])){
            header("Location: ".$PHORUM["disabled_url"]);
            exit();
        } else {
            echo "This Phorum is currently disabled.  Please contact the web site owner at ".$PHORUM['system_email_from_address']." for more information.\n";
            exit();
        }
    }

    // checking for upgrade or new install
    if ( !isset( $PHORUM['internal_version'] ) ) {
        echo "<html><head><title>Phorum error</title></head><body>No Phorum settings were found. Either this is a brand new installation of Phorum or there is an error with your database server. If this is a new install, please <a href=\"admin.php\">go to the admin page</a> to complete the installation. If not, check your database server.</body></html>";
        exit();
    } elseif ( $PHORUM['internal_version'] < PHORUMINTERNAL ) {
        echo "<html><head><title>Error</title></head><body>Looks like you have installed a new version. Go to the admin to complete the upgrade!</body></html>";
        exit();
    }

    // load the forum's settings
    if ( !empty( $PHORUM["forum_id"] ) ) {
        $forum_settings = phorum_db_get_forums( $PHORUM["forum_id"] );
        if ( empty( $forum_settings[$PHORUM["forum_id"]] ) ) {
            phorum_hook( "common_no_forum", "" );
            phorum_redirect_by_url( phorum_get_url( PHORUM_INDEX_URL ) );
            exit();
        }
        $PHORUM = array_merge( $PHORUM, $forum_settings[$PHORUM["forum_id"]] );
    } else {
        // some defaults we might need if no forum is set (i.e. on the index-page)
        $PHORUM['vroot']=0;
        $PHORUM['parent_id']=0;
        $PHORUM['active']=1;
        $PHORUM['folder_flag']=1;
    }

    // stick some stuff from the settings into the DATA member
    $PHORUM["DATA"]["NAME"] = ( isset( $PHORUM["name"] ) ) ? $PHORUM["name"] : "";
    $PHORUM["DATA"]["DESCRIPTION"] = ( isset( $PHORUM["description"] ) ) ? $PHORUM["description"] : "";
    $PHORUM["DATA"]["ENABLE_PM"] = ( isset( $PHORUM["enable_pm"] ) ) ? $PHORUM["enable_pm"] : "";
    if ( !empty( $PHORUM["DATA"]["HTML_TITLE"] ) && !empty( $PHORUM["DATA"]["NAME"] ) ) {
        $PHORUM["DATA"]["HTML_TITLE"] .= PHORUM_SEPARATOR;
    }
    $PHORUM["DATA"]["HTML_TITLE"] .= $PHORUM["DATA"]["NAME"];

    // check the user session
    include_once( "./include/users.php" );
    if ( phorum_user_check_session() ) {
        $PHORUM["DATA"]["LOGGEDIN"] = true;

        if(!$PHORUM["tight_security"] || phorum_user_check_session( PHORUM_SESSION_SHORT_TERM )){
            $PHORUM["DATA"]["FULLY_LOGGEDIN"] = true;
        } else {
            $PHORUM["DATA"]["FULLY_LOGGEDIN"] = false;
        }

        // Let the templates know whether we have new private messages.
        $PHORUM["DATA"]["NEW_PRIVATE_MESSAGES"] = 0;
        if ( $PHORUM["enable_pm"] && isset($PHORUM["user"]["new_private_messages"]) ) {
             $PHORUM["DATA"]["NEW_PRIVATE_MESSAGES"] = $PHORUM["user"]["new_private_messages"];
        }

        $PHORUM["DATA"]["notice_messages"] = false;
        $PHORUM["DATA"]["notice_users"] = false;
        $PHORUM["DATA"]["notice_groups"] = false;

        // if moderator notifications are on and the person is a mod, lets find out if anything is new
        if ( $PHORUM["enable_moderator_notifications"] ) {
            $forummodlist = phorum_user_access_list( PHORUM_USER_ALLOW_MODERATE_MESSAGES );
            if ( count( $forummodlist ) > 0 ) {
                $PHORUM["DATA"]["notice_messages"] = ( count( phorum_db_get_unapproved_list( $forummodlist, true ) ) > 0 );
                $PHORUM["DATA"]["notice_messages_url"] = phorum_get_url( PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_UNAPPROVED );
            }
            if ( phorum_user_access_allowed( PHORUM_USER_ALLOW_MODERATE_USERS ) ) {
                $PHORUM["DATA"]["notice_users"] = ( count( phorum_db_user_get_unapproved() ) > 0 );
                $PHORUM["DATA"]["notice_users_url"] = phorum_get_url( PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_USERS );
            }
            if ( phorum_user_allow_moderate_group() ) {
                $groups = phorum_user_get_moderator_groups();
                if ( count( $groups ) > 0 ) {
                    $PHORUM["DATA"]["notice_groups"] = count( phorum_db_get_group_members( array_keys( $groups ), PHORUM_USER_GROUP_UNAPPROVED ) );
                    $PHORUM["DATA"]["notice_groups_url"] = phorum_get_url( PHORUM_CONTROLCENTER_URL, "panel=" . PHORUM_CC_GROUP_MODERATION );
                }
            }
        }

        $PHORUM["DATA"]["notice_all"] = ( $PHORUM["enable_pm"] && phorum_page!="pm" && $PHORUM["DATA"]["NEW_PRIVATE_MESSAGES"] ) || $PHORUM["DATA"]["notice_messages"] || $PHORUM["DATA"]["notice_users"] || $PHORUM["DATA"]["notice_groups"];

        // if the user has overridden thread settings, change it here.
        if ( !isset( $PHORUM['display_fixed'] ) || !$PHORUM['display_fixed'] ) {
            if ( $PHORUM["user"]["threaded_list"] == PHORUM_THREADED_ON ) {
                $PHORUM["threaded_list"] = true;
            } elseif ( $PHORUM["user"]["threaded_list"] == PHORUM_THREADED_OFF ) {
                $PHORUM["threaded_list"] = false;
            }
            if ( $PHORUM["user"]["threaded_read"] == PHORUM_THREADED_ON ) {
                $PHORUM["threaded_read"] = true;
            } elseif ( $PHORUM["user"]["threaded_read"] == PHORUM_THREADED_OFF ) {
                $PHORUM["threaded_read"] = false;
            }
        }
    }

    // set up the blank user if not logged in
    if ( empty( $PHORUM["user"] ) ) {
        $PHORUM["user"] = array( "user_id" => 0, "username" => "", "admin" => false, "newinfo" => array() );
        $PHORUM["DATA"]["LOGGEDIN"] = false;
    }


    // a hook for rewriting vars in common.php after loading the user
    phorum_hook( "common_post_user", "" );


    // set up the template

    // check for a template being passed on the url
    // only use valid template names
    if ( !empty( $PHORUM["args"]["template"] ) ) {
        $template = basename( $PHORUM["args"]["template"] );
        if ($template != '..') {
            $PHORUM["template"] = $template;
        }
    }

    // user output buffering so we don't get header errors
    // not loaded if we are running an external or scheduled script
    if (! defined('PHORUM_SCRIPT')) {
        ob_start();
        include_once( phorum_get_template( "settings" ) );
        ob_end_clean();
    }

    // get the language file
    if ( ( !isset( $PHORUM['display_fixed'] ) || !$PHORUM['display_fixed'] ) && isset( $PHORUM['user']['user_language'] ) && !empty($PHORUM['user']['user_language']) )
        $PHORUM['language'] = $PHORUM['user']['user_language'];

    if ( !isset( $PHORUM["language"] ) || empty( $PHORUM["language"] ) || !file_exists( "./include/lang/$PHORUM[language].php" ) )
        $PHORUM["language"] = $PHORUM["default_language"];

    if ( file_exists( "./include/lang/$PHORUM[language].php" ) ) {
        include_once( "./include/lang/$PHORUM[language].php" );
    }
    // load languages for localized modules
    if ( isset( $PHORUM["hooks"]["lang"] ) && is_array($PHORUM["hooks"]["lang"]) ) {
        foreach( $PHORUM["hooks"]["lang"]["mods"] as $mod ) {
            // load mods for this hook
            if ( file_exists( "./mods/$mod/lang/$PHORUM[language].php" ) ) {
                include_once "./mods/$mod/lang/$PHORUM[language].php";
            }
            elseif ( file_exists( "./mods/$mod/lang/english.php" ) ) {
                include_once "./mods/$mod/lang/english.php";
            }
        }
    }

    // HTML titles can't contain HTML code, so we strip HTML tags
    // and HTML escape the title.
$PHORUM["DATA"]["HTML_TITLE"] = htmlentities(strip_tags($PHORUM["DATA"]["HTML_TITLE"]), ENT_COMPAT, $PHORUM["DATA"]["CHARSET"]);

    // if the Phorum is disabled, display a message.
    if(isset($PHORUM["status"]) && $PHORUM["status"]=="admin-only" && !$PHORUM["user"]["admin"]){
        // set all our URL's
        phorum_build_common_urls();

        $PHORUM["DATA"]["MESSAGE"]=$PHORUM["DATA"]["LANG"]["AdminOnlyMessage"];
        include phorum_get_template("header");
        phorum_hook("after_header");
        include phorum_get_template("message");
        phorum_hook("before_footer");
        include phorum_get_template("footer");
        exit();

    }


    // a hook for rewriting vars at the end of common.php
    phorum_hook( "common", "" );

    $PHORUM['DATA']['USERINFO'] = $PHORUM['user'];
    $PHORUM['DATA']['PHORUM_PAGE'] = phorum_page;
    $PHORUM['DATA']['USERTRACK'] = $PHORUM['track_user_activity'];
}


//////////////////////////////////////////////////////////
// functions

/**
 * A common function to check that a user is logged in
 */
function phorum_require_login()
{
    $PHORUM = $GLOBALS['PHORUM'];
    if ( !$PHORUM["user"]["user_id"] ) {
        $url = phorum_get_url( PHORUM_LOGIN_URL, "redir=" . urlencode( $PHORUM["http_path"] . "/" . basename( $_SERVER["PHP_SELF"] ) . "?" . $_SERVER["QUERY_STRING"] ) );
        phorum_redirect_by_url( $url );
        exit();
    }
}

/**
 * A common function for checking the read-permissions for a forum-page
 * returns false if access is not allowed and an error page-was output
 */
function phorum_check_read_common()
{
    $PHORUM = $GLOBALS['PHORUM'];

    $retval = true;

    if ( $PHORUM["forum_id"] > 0 && !$PHORUM["folder_flag"] && !phorum_user_access_allowed( PHORUM_USER_ALLOW_READ ) ) {
        if ( $PHORUM["DATA"]["LOGGEDIN"] ) {
            // if they are logged in and not allowed, they don't have rights
            $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["NoRead"];
        } else {
            // check if they could read if logged in.
            // if so, let them know to log in.
            if ( ( empty( $PHORUM["DATA"]["POST"]["parentid"] ) && $PHORUM["reg_perms"] &PHORUM_USER_ALLOW_READ ) ) {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["PleaseLoginRead"];
            } else {
                $PHORUM["DATA"]["MESSAGE"] = $PHORUM["DATA"]["LANG"]["NoRead"];
            }
        }

        phorum_build_common_urls();

        include phorum_get_template( "header" );
        phorum_hook( "after_header" );
        include phorum_get_template( "message" );
        phorum_hook( "before_footer" );
        include phorum_get_template( "footer" );

        $retval = false;
    }

    return $retval;
}

// used for all url creation.
function phorum_get_url()
{
    $PHORUM = $GLOBALS["PHORUM"];

    $args = "";
    $url = "";
    $suffix = "";
    $add_forum_id = false;
    $add_get_vars = true;

    $argv = func_get_args();
    $type = array_shift( $argv );

    switch ( $type ) {
        case PHORUM_LIST_URL:
            $page = "list";
            if ( empty( $argv ) ) $add_forum_id = true;
            break;
        case PHORUM_READ_URL:
            $page = "read";
            $add_forum_id = true;
            if ( !empty( $argv[1] ) && is_numeric( $argv[1] ) ) $suffix = "#msg-$argv[1]";
            break;
        case PHORUM_FOREIGN_READ_URL:
            $page = "read";
            if ( !empty( $argv[2] ) && is_numeric( $argv[2] ) ) $suffix = "#msg-$argv[2]";
            break;
        case PHORUM_REPLY_URL:
            if(isset($PHORUM["reply_on_read_page"]) && $PHORUM["reply_on_read_page"]){
                $page = "read";
                $suffix = "#REPLY";
            } else {
                $page = "posting";
                // For reply on a separate page, we call posting.php on its own.
                // In that case argv[0] is the editor mode we want to use
                // (reply in this case). Currently, the thread id is in argv[0],
                // but we don't need that one for posting.php. So we simply
                // replace argv[0] with the correct argument.
                $argv[0] = "reply";
            }
            $add_forum_id = true;
            break;
        case PHORUM_POSTING_URL:
            $page = "posting";
            $add_forum_id = true;
            break;
        case PHORUM_REDIRECT_URL:
            $page = "redirect";
            $add_forum_id = false;
            break;
        case PHORUM_SEARCH_URL:
            $page = "search";
            $add_forum_id = true;
            break;
        case PHORUM_SEARCH_ACTION_URL:
            $page = "search";
            $add_get_vars = true;
            break;
        case PHORUM_DOWN_URL:
            $page = "down";
            $add_forum_id = true;
            break;
        case PHORUM_VIOLATION_URL:
            $page = "violation";
            $add_forum_id = true;
            break;
        case PHORUM_INDEX_URL:
            $page = "index";
            break;
        case PHORUM_LOGIN_URL:
            $page = "login";
            $add_forum_id = true;
            break;
        case PHORUM_LOGIN_ACTION_URL:
            $page = "login";
            break;
        case PHORUM_REGISTER_URL:
            $page = "register";
            $add_forum_id = true;
            break;
        case PHORUM_REGISTER_ACTION_URL:
            $page = "register";
            break;
        case PHORUM_PROFILE_URL:
            $page = "profile";
            $add_forum_id = true;
            break;
        case PHORUM_SUBSCRIBE_URL:
            $page = "subscribe";
            $add_forum_id = true;
            break;
        case PHORUM_MODERATION_URL:
            $page = "moderation";
            $add_forum_id = true;
            break;
        case PHORUM_MODERATION_ACTION_URL:
            $page = "moderation";
            $add_get_vars = false;
            break;
        case PHORUM_PREPOST_URL:
            $page = "control";
            $argv[] = "panel=messages";
            $add_forum_id = true;
            break;
        case PHORUM_CONTROLCENTER_URL:
            $page = "control";
            $add_forum_id = true;
            break;
        case PHORUM_CONTROLCENTER_ACTION_URL:
            $page = "control";
            break;
        case PHORUM_PM_URL:
            $page = "pm";
            $add_forum_id = true;
            break;
        case PHORUM_PM_ACTION_URL:
            $page = "pm";
            break;
        case PHORUM_FILE_URL:
            $page = "file";
            $add_forum_id = true;
            break;
        case PHORUM_FOLLOW_URL:
            $page = "follow";
            $add_forum_id = true;
            break;
        case PHORUM_FOLLOW_ACTION_URL:
            $page = "follow";
            $add_forum_id = false;
            break;
        case PHORUM_REPORT_URL:
            $page = "report";
            $add_forum_id = true;
            break;
        case PHORUM_RSS_URL:
            switch(phorum_page){
                case "list":
                    $add_forum_id = true;
                    break;
                case "read":
                    $add_forum_id = true;
                    array_push($argv, $PHORUM["args"]["1"]);
                    break;
            }
            $page = "rss";
            break;
        // this is for adding own generic urls
        case PHORUM_CUSTOM_URL:
            $page = array_shift($argv); // first arg is our page
            $add_forum_id_tmp=array_shift($argv); // second determining if we should add the forum_id
            $add_forum_id = $add_forum_id_tmp?true:false;
            break;

        case PHORUM_BASE_URL:
            // only to flag phorum_custom_get_url() that base url is requested
            $page = '';
            break;

        default:
            trigger_error( "Unhandled page type.", E_USER_WARNING );
            break;
    }

    // build the query string
    $query_items = array();

    if ( $add_forum_id ) {
        $query_items[] = ( int )$PHORUM["forum_id"];
    }

    if ( count( $argv ) > 0 ) {
        $query_items = array_merge( $query_items, $argv );
    }

    if ( !empty( $PHORUM["DATA"]["GET_VARS"] ) && $add_get_vars ) {
        $query_items = array_merge( $query_items, $PHORUM["DATA"]["GET_VARS"] );
    }
    // build the url
    if ( !function_exists( "phorum_custom_get_url" ) ) {
        if ($type == PHORUM_BASE_URL) return $PHORUM["http_path"] . '/';

        $url = "$PHORUM[http_path]/$page." . PHORUM_FILE_EXTENSION;

        if ( count( $query_items ) ) $url .= "?" . implode( ",", $query_items );

        if ( !empty( $suffix ) ) $url .= $suffix;
    } else {
        $url = phorum_custom_get_url( $page, $query_items, $suffix );
    }

    return $url;
}

// retrieve the appropriate template file name
function phorum_get_template( $page, $is_include = false )
{
    $PHORUM = $GLOBALS["PHORUM"];

    if ( ( !isset( $PHORUM['display_fixed'] ) || !$PHORUM['display_fixed'] ) && isset( $PHORUM['user']['user_template'] ) && !empty($PHORUM['user']['user_template'])) {
        $PHORUM['template'] = $PHORUM['user']['user_template'];
    }

    // If no user template is set or if the template folder cannot be found,
    // fallback to the default template.
    if (empty($PHORUM["template"]) || !file_exists("./templates/{$PHORUM['template']}")) {
        $PHORUM["template"] = $PHORUM["default_template"];
    }

    $tpl = "./templates/$PHORUM[template]/$page";
    // check for straight PHP file
    if ( file_exists( "$tpl.php" ) ) {
        $phpfile = "$tpl.php";
    } else {
        // not there, look for a template
        $tplfile = "$tpl.tpl";
        $safetemplate = str_replace("-", "_", $PHORUM["template"]);
        $safepage = str_replace("-", "_", $page);
        $phpfile = "$PHORUM[cache]/tpl-$safetemplate-$safepage-" .
               ($is_include ? "include" : "toplevel") . "-" .
               md5( dirname( __FILE__ ) ) . ".php";

        if ( $is_include || !file_exists( $phpfile ) ) {
            include_once "./include/templates.php";
            phorum_import_template( $tplfile, $phpfile );
        }
    }

    return $phpfile;
}

// creates URLs used on most pages
function phorum_build_common_urls()
{
    $PHORUM=$GLOBALS['PHORUM'];

    // those links are only needed in forums, not in folders
    if(isset($PHORUM['folder_flag']) && !$PHORUM['folder_flag']) {
        $GLOBALS["PHORUM"]["DATA"]["URL"]["TOP"] = phorum_get_url( PHORUM_LIST_URL );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["MARKREAD"] = phorum_get_url( PHORUM_LIST_URL, "markread=1" );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["POST"] = phorum_get_url( PHORUM_POSTING_URL );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["SUBSCRIBE"] = phorum_get_url( PHORUM_SUBSCRIBE_URL );
    }

    // those are general urls, needed nearly everywhere
    $GLOBALS["PHORUM"]["DATA"]["URL"]["SEARCH"] = phorum_get_url( PHORUM_SEARCH_URL );

    // RSS-Url only makes sense on a couple of pages
    if(isset($PHORUM['use_rss']) && $PHORUM['use_rss']
        && (phorum_page=="index" || phorum_page=="list" || phorum_page=="read")){
        $GLOBALS["PHORUM"]["DATA"]["URL"]["RSS"] = phorum_get_url( PHORUM_RSS_URL );
    }

    $index_id=-1;
    // in a folder

    if( $PHORUM['folder_flag'] && phorum_page != 'index'
    && ($PHORUM['forum_id'] == 0 || $PHORUM['vroot'] == $PHORUM['forum_id'])) {
        // folder where we usually don't show the index-link but on
        // additional pages like search and login its shown
        $index_id=$PHORUM['forum_id'];

    } elseif( ( $PHORUM['folder_flag'] &&
    ($PHORUM['forum_id'] != 0 && $PHORUM['vroot'] != $PHORUM['forum_id'])) ||
    (!$PHORUM['folder_flag'] && $PHORUM['active'])) {
        // either a folder where the link should be shown (not vroot or root)
        // or an active forum where the link should be shown

        if(isset($PHORUM["use_new_folder_style"]) && $PHORUM["use_new_folder_style"] ) {
            // go to root or vroot
            $index_id=$PHORUM["vroot"]; // vroot is either 0 (root) or another id

        } else {
            // go to parent
            $index_id=$PHORUM["parent_id"]; // parent_id is always set now

        }

    }
    if($index_id > -1) {
        // check if its the full root, avoid adding an id in this case (SE-optimized ;))
        if (!empty($index_id))
            $GLOBALS["PHORUM"]["DATA"]["URL"]["INDEX"] = phorum_get_url( PHORUM_INDEX_URL, $index_id );
        else
            $GLOBALS["PHORUM"]["DATA"]["URL"]["INDEX"] = phorum_get_url( PHORUM_INDEX_URL );
    }

    // these urls depend on the login-status of a user
    if ( $GLOBALS["PHORUM"]["DATA"]["LOGGEDIN"] ) {
        $GLOBALS["PHORUM"]["DATA"]["URL"]["LOGINOUT"] = phorum_get_url( PHORUM_LOGIN_URL, "logout=1" );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["REGISTERPROFILE"] = phorum_get_url( PHORUM_CONTROLCENTER_URL );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["PM"] = phorum_get_url( PHORUM_PM_URL );
    } else {
        $GLOBALS["PHORUM"]["DATA"]["URL"]["LOGINOUT"] = phorum_get_url( PHORUM_LOGIN_URL );
        $GLOBALS["PHORUM"]["DATA"]["URL"]["REGISTERPROFILE"] = phorum_get_url( PHORUM_REGISTER_URL );
    }
}

// calls phorum mod functions
function phorum_hook( $hook, $arg = "" )
{
    $PHORUM = $GLOBALS["PHORUM"];

    if ( isset( $PHORUM["hooks"][$hook] ) && is_array($PHORUM["hooks"][$hook])) {

        foreach( $PHORUM["hooks"][$hook]["mods"] as $mod ) {
            // load mods for this hook
            if ( file_exists( "./mods/$mod/$mod.php" ) ) {
                include_once "./mods/$mod/$mod.php";
            } elseif ( file_exists( "./mods/$mod.php" ) ) {
                include_once "./mods/$mod.php";
            }
        }

        foreach( $PHORUM["hooks"][$hook]["funcs"] as $func ) {
            // call functions for this hook
            if ( function_exists( $func ) ) {
                $arg = call_user_func( $func, $arg );
            }
        }
    }

    return $arg;
}

// HTML encodes a string
function phorum_html_encode( $string )
{
    $ret_string = "";
    $len = strlen( $string );
    for( $x = 0;$x < $len;$x++ ) {
        $ord = ord( $string[$x] );
        $ret_string .= "&#$ord;";
    }
    return $ret_string;
}

// removes slashes from all array-entries
function phorum_recursive_stripslashes( $array )
{
    if ( !is_array( $array ) ) {
        return $array;
    } else {
        foreach( $array as $key => $value ) {
            if ( !is_array( $value ) )
                $array[$key] = stripslashes( $value );
            else
                $array[$key] = phorum_recursive_stripslashes( $value );
        }
    }
    return $array;
}

// returns the available templates as an array
function phorum_get_template_info()
{
    $tpls = array();

    $d = dir( "./templates" );
    while ( false !== ( $entry = $d->read() ) ) {
        if ( $entry != "." && $entry != ".." && file_exists( "./templates/$entry/info.php" ) ) {
            include "./templates/$entry/info.php";
            if ( !isset( $template_hide ) || empty( $template_hide ) || defined( "PHORUM_ADMIN" ) ) {
                $tpls[$entry] = "$name $version";
            } else {
                unset( $template_hide );
            }
        }
    }

    return $tpls;
}

// returns the available languages as an array
function phorum_get_language_info()
{
    $langs = array();

    $d = dir( "./include/lang" );
    while ( false !== ( $entry = $d->read() ) ) {
        if ( substr( $entry, -4 ) == ".php" && is_file( "./include/lang/$entry" ) ) {
            @include "./include/lang/$entry";
            if ( !isset( $language_hide ) || empty( $language_hide ) || defined( "PHORUM_ADMIN" ) ) {
                $langs[str_replace( ".php", "", $entry )] = $language;
            } else {
                unset( $language_hide );
            }
        }
    }

    return $langs;
}

function phorum_redirect_by_url( $redir_url )
{
    if ( stristr( $_SERVER['SERVER_SOFTWARE'], "Microsoft-IIS" ) ) {
        // the ugly IIS-hack to avoid crashing IIS
        print "<html><head>\n<title>Redirecting ...</title>\n";
        print "<meta http-equiv=\"refresh\" content=\"0; URL=$redir_url\">";
        print "</head>\n";
        print "<body><a href=\"$redir_url\">Redirecting ...</a></body>\n";
        print "</html>";
    } else {
        // our standard-way
        header( "Location: $redir_url" );
    }
    exit(0);
}

// might remove these, might not.  Need it for debugging.
function print_var( $var )
{
    echo "<xmp>";
    print_r( $var );
    echo "</xmp>";
}

?>
