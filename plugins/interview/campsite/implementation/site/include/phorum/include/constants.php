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

    if(!defined("PHORUM")) return;

    // put constants here that are configurable
    // these should be things that will not be changed
    // very often.  Things that are likely to be changed
    // by most admins should go in the admin.

    define("PHORUM_FILE_EXTENSION", "php");

    // number of messages remembered as new
    define("PHORUM_MAX_NEW_INFO", 1000);

    // can moderators view email addresses
    define("PHORUM_MOD_EMAIL_VIEW", true);

    // can moderators view user's ip
    define("PHORUM_MOD_IP_VIEW", true);

    // change the author's name on deleting the user
    define("PHORUM_DELETE_CHANGE_AUTHOR", true);

    // enforce the use of only unregistered names for unregistered users
    define("PHORUM_ENFORCE_UNREGISTERED_NAMES",true);

    // maximum time in seconds for having the message editor open
    // this is used in determining stale attachment files
    define("PHORUM_MAX_EDIT_TIME", 86400);

    // string used to separate things like items in the title tag.
    define("PHORUM_SEPARATOR", " :: ");

    // default TTL for cache-data if not specified different
    define('PHORUM_CACHE_DEFAULT_TTL',3600);

    // split-variable for file-based cache
    define('PHORUM_CACHE_SPLIT',4);

    // the replace string for masking bad words.
    define('PHORUM_BADWORD_REPLACE', '@#$%&');

    /////////////////////////////////////////
    //                                     //
    //     DO NOT EDIT BELOW THIS AREA     //
    //                                     //
    /////////////////////////////////////////

    // put constants here that need to stay the same value here.

    define("PHORUM_UPLOADS_SELECT", 0);
    define("PHORUM_UPLOADS_REG", 1);

    define("PHORUM_MODERATE_OFF", 0);
    define("PHORUM_MODERATE_ON", 1);

    define("PHORUM_EMAIL_MODERATOR_OFF", 0);
    define("PHORUM_EMAIL_MODERATOR_ON", 1);

    define("PHORUM_STATUS_APPROVED", 2);
    define("PHORUM_STATUS_HOLD", -1);
    define("PHORUM_STATUS_HIDDEN", -2);

    define("PHORUM_SORT_ANNOUNCEMENT", 0);
    define("PHORUM_SORT_STICKY", 1);
    define("PHORUM_SORT_DEFAULT", 2);

    define("PHORUM_THREADED_DEFAULT", 0);
    define("PHORUM_THREADED_ON", 1);
    define("PHORUM_THREADED_OFF", 2);

    define("PHORUM_SUBSCRIPTION_MESSAGE", 0);
    define("PHORUM_SUBSCRIPTION_DIGEST", 1);
    define("PHORUM_SUBSCRIPTION_BOOKMARK", 2);

    define("PHORUM_REGISTER_INSTANT_ACCESS", 0);
    define("PHORUM_REGISTER_VERIFY_EMAIL", 1);
    define("PHORUM_REGISTER_VERIFY_MODERATOR", 2);
    define("PHORUM_REGISTER_VERIFY_BOTH", 3);

    define("PHORUM_USER_PENDING_BOTH", -3);
    define("PHORUM_USER_PENDING_EMAIL", -2);
    define("PHORUM_USER_PENDING_MOD", -1);
    define("PHORUM_USER_INACTIVE", 0);
    define("PHORUM_USER_ACTIVE", 1);

    define("PHORUM_USER_ALLOW_READ", 1);
    define("PHORUM_USER_ALLOW_REPLY", 2);
    define("PHORUM_USER_ALLOW_EDIT", 4);
    define("PHORUM_USER_ALLOW_NEW_TOPIC", 8);
    define("PHORUM_USER_ALLOW_ATTACH", 32);
    define("PHORUM_USER_ALLOW_MODERATE_MESSAGES", 64);
    define("PHORUM_USER_ALLOW_MODERATE_USERS", 128);
    define("PHORUM_USER_ALLOW_FORUM_PROPERTIES", 256);

    define("PHORUM_MODERATE_ALLOWED_ANYWHERE", -1);

    define("PHORUM_USER_GROUP_REMOVE", -128);
    define("PHORUM_USER_GROUP_SUSPENDED", -1);
    define("PHORUM_USER_GROUP_UNAPPROVED", 0);
    define("PHORUM_USER_GROUP_APPROVED", 1);
    define("PHORUM_USER_GROUP_MODERATOR", 2);

    define("PHORUM_GROUP_CLOSED", 0);
    define("PHORUM_GROUP_OPEN", 1);
    define("PHORUM_GROUP_REQUIRE_APPROVAL", 2);

    define("PHORUM_NEWFLAG_MSG", 0);
    define("PHORUM_NEWFLAG_MIN_ID", 1);

    define("PHORUM_LINK_USER", "user");
    define("PHORUM_LINK_MESSAGE", "message");
    define("PHORUM_LINK_EDITOR", "editor");

    // Definitions for the PM system.
    // Special folders
    define("PHORUM_PM_INBOX", "inbox");
    define("PHORUM_PM_OUTBOX", "outbox");
    define("PHORUM_PM_ALLFOLDERS", "allfolder");
    // Flag types
    define("PHORUM_PM_READ_FLAG", "read_flag");
    define("PHORUM_PM_REPLY_FLAG", "reply_flag");

    // constants below here do not have to have a constant value,
    // as long as each is unique.  They are used for enumeration.
    // Add to them as you wish knowing that.

    $i=1;

    define("PHORUM_BAD_IPS", $i++);
    define("PHORUM_BAD_NAMES", $i++);
    define("PHORUM_BAD_EMAILS", $i++);
    define("PHORUM_BAD_WORDS", $i++);
    define("PHORUM_BAD_USERID", $i++);
    define("PHORUM_BAD_SPAM_WORDS", $i++);

    define("PHORUM_LIST_URL", $i++);
    define("PHORUM_READ_URL", $i++);
    define("PHORUM_FOREIGN_READ_URL", $i++);
    define("PHORUM_REPLY_URL", $i++);
    define("PHORUM_POSTING_URL", $i++);
    define("PHORUM_REDIRECT_URL", $i++);
    define("PHORUM_SEARCH_URL", $i++);
    define("PHORUM_SEARCH_ACTION_URL", $i++);
    define("PHORUM_DOWN_URL", $i++);
    define("PHORUM_VIOLATION_URL", $i++);
    define("PHORUM_USER_URL", $i++);
    define("PHORUM_INDEX_URL", $i++);
    define("PHORUM_LOGIN_URL", $i++);
    define("PHORUM_LOGIN_ACTION_URL", $i++);
    define("PHORUM_REGISTER_URL", $i++);
    define("PHORUM_REGISTER_ACTION_URL", $i++);
    define("PHORUM_PROFILE_URL", $i++);
    define("PHORUM_SUBSCRIBE_URL", $i++);
    define("PHORUM_MODERATION_URL", $i++);
    define("PHORUM_MODERATION_ACTION_URL", $i++);
    define("PHORUM_CONTROLCENTER_URL", $i++);
    define("PHORUM_CONTROLCENTER_ACTION_URL", $i++);
    define("PHORUM_PM_URL", $i++);
    define("PHORUM_PM_ACTION_URL", $i++);
    define("PHORUM_FILE_URL", $i++);
    define("PHORUM_GROUP_MODERATION_URL", $i++);
    define("PHORUM_FOLLOW_URL", $i++);
    define("PHORUM_FOLLOW_ACTION_URL", $i++);
    define("PHORUM_PREPOST_URL", $i++);
    define("PHORUM_REPORT_URL", $i++);
    define("PHORUM_RSS_URL", $i++);
    define("PHORUM_CUSTOM_URL", $i++);
    define("PHORUM_BASE_URL", $i++);

    define("PHORUM_DELETE_MESSAGE", $i++);
    define("PHORUM_DELETE_TREE", $i++);
    define("PHORUM_MOVE_THREAD", $i++);
    define("PHORUM_DO_THREAD_MOVE", $i++);
    define("PHORUM_CLOSE_THREAD", $i++);
    define("PHORUM_REOPEN_THREAD", $i++);
    define("PHORUM_APPROVE_MESSAGE", $i++);
    define("PHORUM_HIDE_POST", $i++);
    define("PHORUM_APPROVE_MESSAGE_TREE", $i++);
    define("PHORUM_MERGE_THREAD", $i++);
    define("PHORUM_DO_THREAD_MERGE", $i++);
    define("PHORUM_SPLIT_THREAD", $i++);
    define("PHORUM_DO_THREAD_SPLIT", $i++);

    define("PHORUM_SANITY_OK", $i++);
    define("PHORUM_SANITY_WARN", $i++);
    define("PHORUM_SANITY_CRIT", $i++);

    define("PHORUM_CC_SUMMARY", "summary");
    define("PHORUM_CC_SUBSCRIPTION_THREADS", "subthreads");
    define("PHORUM_CC_SUBSCRIPTION_FORUMS", "subforums");
    define("PHORUM_CC_USERINFO", "user");
    define("PHORUM_CC_SIGNATURE", "sig");
    define("PHORUM_CC_MAIL", "email");
    define("PHORUM_CC_BOARD", "forum");
    define("PHORUM_CC_PASSWORD", "password");
    define("PHORUM_CC_UNAPPROVED", "messages");
    define("PHORUM_CC_FILES", "files");
    define("PHORUM_CC_USERS", "users");
    define("PHORUM_CC_PM", "pm");
    define("PHORUM_CC_PRIVACY", "privacy");
    define("PHORUM_CC_GROUP_MODERATION", "groupmod");
    define("PHORUM_CC_GROUP_MEMBERSHIP", "groups");

?>
