<?php

/*

***** IT IS HIGHLY RECCOMENDED THAT YOU RUN THIS SCRIPT ON A CONSOLE
***** PHP VERSION 4.2.0 OR HIGHER IS REQUIRED FOR RUNNING THIS SCRIPT
***** THE SCRIPT IS WRITTEN FOR UPGRADING PHORUM 3.4.x

This script will convert the data from a Phorum 3 database to a Phorum 5
database. It does not change any of the old phorum3-tables. The data is
only copied over to the new Phorum 5 tables.

Instructions:

1. Be sure your Phorum 3 is running a 3.4.x version. If you are running
   an older version of Phorum 3, first upgrade to 3.4.x.

2. Copy or move this script one directory up, to the main Phorum 5 directory.

3. Edit the $CONVERT variables below to match the settings of your
   phorum3 installation.

4. Install Phorum 5 as usual. For speed and reliability, preferably use the
   same database as the database where Phorum 3 lives. Because Phorum 5 uses
   a table prefix (typically "phorum_"), the tables for Phorum 3 and Phorum 5
   can safely live next to each other in the same database.

5. Empty the phorum_messages and phorum_forums tables of the Phorum 5
   installation. You can do this either by dropping all forums from the
   Phorum 5 admin interface or by issuing the appropriate SQL queries from
   the MySQL prompt or from a database tool like "phpmyadmin". The queries
   to execute are (replace "phorum" with your own table_prefix if you changed
   this during install):

   DELETE FROM phorum_messages;
   DELETE FROM phorum_forums;

   I could do this from this script as well, but I would find that
   a little bit rude ;-))

6. Turn off unneeded modules for the conversion. All modules hooking into common.php
   or some other general hook will be run while doing the conversion which will lead to
   at least a slowdown, unexpected side effects and some strange output.

7. If you have shell access to your webserver, run this script using the
   shell from the command line. This is the preferred way of running the
   upgrade:

      php phorum3to5convert.php

   If you do not have shell access, call the upgrade script from your browser:

      <phorum5-url>/phorum3to5convert.php

   *** THIS STEP MAY TAKE A WHILE ***

8. Take a look at the Phorum 5 forums to see if everything was converted
   correctly.

9. Delete the upgrade script phorum3to5convert.php.

*/

ini_set ( "zlib.output_compression", "0");
ini_set ( "output_handler", "");
@ob_end_flush();

define("PHORUM5_CONVERSION", 1);

/***** CONFIGURATION FOR THE CONVERSION *****/

// The data for connecting to the old Phorum 3 database.
$CONVERT['old_dbhost'] = "localhost";
$CONVERT['old_dbuser'] = "phorum5";
$CONVERT['old_dbpass'] = "phorum5";

// The name of the old Phorum 3 database.
$CONVERT['olddb'] = "phorum";

// The main-table-name for phorum3 (default is "forums")
$CONVERT['forumstable'] = "forums";

// Separator character. If you are going to run this script from
// the web, make it "<br>\n". If you are going to run it from the
// shell prompt, make it "\n".
$CONVERT['lbr'] = "<br>\n";

// The full path to the directory where the attachments for Phorum 3.4.x
// are stored (like in the old admin).
$CONVERT['attachmentdir'] = "/full/path/to/files";

/***** THERE'S NO NEED TO CHANGE ANYTHING BELOW THIS LINE *****/


// we try to disable the execution timeout
// that command doesn't work in safe_mode :(
set_time_limit(0);

require './common.php';
require './include/thread_info.php';
require './scripts/phorum3_in.php';

// no need to change anything below this line
// establishing the first link to the old database
$oldlink = mysql_connect($CONVERT['old_dbhost'], $CONVERT['old_dbuser'], $CONVERT['old_dbpass'], true);
mysql_select_db($CONVERT['olddb'], $oldlink);
mysql_query("SET NAMES 'utf8'");

if (!$oldlink) {
    print "Couldn't connect to the old database.".$CONVERT['lbr'];
    exit();
}

// checking attachment-dir
if (!file_exists($CONVERT['attachmentdir']) || empty($CONVERT['attachmentdir'])) {
    echo "Directory {$CONVERT['attachmentdir']} doesn't exist. Attachments won't be converted. (doesn't matter if you don't have message-attachments) {$CONVERT['lbr']}";
}

$CONVERT['groups']=array();
$CONVERT['do_groups']=false;

// checking if the groups-table exists
if(phorum_convert_check_groups($oldlink)) {
    // reading groups (should be not too much, therefore we keep the array for later use)
    $CONVERT['groups'] = phorum_convert_getGroups($oldlink);
    if(count($CONVERT['groups'])) {
        echo "Writing groups ... {$CONVERT['lbr']}";
        foreach($CONVERT['groups'] as $groupid => $groupdata) {
            phorum_db_add_group($groupdata['name'],$groupid);
            $CONVERT['groups'][$groupid]['group_id']=$groupid;
        }
    }
    $CONVERT['do_groups']=true;
}

$CONVERT['do_users']=false;
// checking if the users-table exists
if(phorum_convert_check_users($oldlink)) {
    $CONVERT['do_users']=true;
}

// reading the forums
$forums = phorum_convert_getForums($oldlink);

// going through all the forums (and folders)
echo "Writing forumdata ... {$CONVERT['lbr']}";
flush();
$offsets=array();

foreach($forums as $forumid => $forumdata) {
    $newforum = phorum_convert_prepareForum($forumdata);

    phorum_db_add_forum($newforum);

    if (!$forumdata['folder']) {
        $PHORUM['forum_id'] = $forumid;
        $CONVERT['forum_id'] = $forumid;

        echo "Reading maximum message-id from messages-table... {$CONVERT['lbr']}";
        flush();
        $CONVERT['max_id'] = phorum_db_get_max_messageid();
        $offsets[$forumid]=$CONVERT['max_id'];

        if ($forumdata['allow_uploads']=='Y' && file_exists($CONVERT['attachmentdir']."/".$forumdata['table_name'])) {
            $CONVERT['attachments']=phorum_convert_getAttachments($forumdata['table_name']);
            echo "Reading attachments for forum " . $forumdata['name'] . "...{$CONVERT['lbr']}";
            flush();
        }

        echo "Writing postings for forum " . $forumdata['name'] . "...{$CONVERT['lbr']}";
        flush();

        $count = 1;
        $total = 0;

        $res = phorum_convert_selectMessages($forumdata, $oldlink);
        while ($newmessage = phorum_convert_getNextMessage($res,$forumdata['table_name'])) {

            if(phorum_db_post_message($newmessage, true)) {
              phorum_update_thread_info($newmessage['thread']);
              echo "+";
              flush();
              if ($count == 50) {
                  $total += $count;
                  echo " $total from \"{$forumdata['name']}\"";
                  if($CONVERT['lbr']=="\n"){
                      // lets just go back on this line if we are on the console
                      echo "\r";
                  } else {
                      echo $CONVERT['lbr'];
                  }
                  flush();
                  $count = 0;
              }
              $count++;
            } else {
              print "Error in message: ".$CONVERT['lbr'];
              print_var($newmessage);
              print $CONVERT['lbr'];
            }
        }

        echo "{$CONVERT['lbr']}Updating forum-statistics: {$CONVERT['lbr']}";
        flush();
        phorum_db_update_forum_stats(true);
        echo $CONVERT['lbr'];
        flush();
    }
}
unset($forums);

// storing the offsets of the forums
phorum_db_update_settings(array("conversion_offsets"=>$offsets));

if($CONVERT['do_groups'] && count($CONVERT['groups'])) { // here we set the group-permissions
    echo "Writing group-permissions ... {$CONVERT['lbr']}";
    foreach($CONVERT['groups'] as $groupid => $groupdata) {
        phorum_db_save_group($groupdata);
    }
}

if($CONVERT['do_users']) {
    echo "migrating users ...{$CONVERT['lbr']}";
    flush();
    $group_perms=phorum_convert_getUserGroups($oldlink);
    $res = phorum_convert_selectUsers($oldlink);

    if (!$res) {
        echo "No users found, All done now.{$CONVERT['lbr']}";
        flush();
        exit;
    }

    // there are users...
    $count = 0;
    $userdata["date_added"] = time();
    $cur_time = time();
    while ($cur_user = phorum_convert_getNextUser($res)) {
        if (isset($cur_user['user_id'])) {
            phorum_user_add($cur_user, -1);
            $user_groups=$group_perms[$cur_user['user_id']];
            if(count($user_groups)) { // setting the user's group-memberships
            phorum_db_user_save_groups($cur_user['user_id'],$user_groups);
            }
            $count++;
        }
    }
    unset($users);
    print "$count users converted{$CONVERT['lbr']}";
}
echo "{$CONVERT['lbr']}Done.{$CONVERT['lbr']}";
flush();

?>
