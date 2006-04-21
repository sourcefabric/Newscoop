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

    // Phorum 5 Admin

    define("PHORUM_ADMIN", 1);

    // set a sane error level for our admin.
    // this will make the coding time faster and
    // the code run faster.
    error_reporting  (E_ERROR | E_WARNING | E_PARSE);

    include_once "./common.php";
    include_once "./include/users.php";


    // if we are installing or upgrading, we don't need to check for a session
    // 2005081000 was the internal version that introduced the installed flag
    if(!isset($PHORUM['internal_version']) || (!isset($PHORUM['installed']) && $PHORUM['internal_version']>='2005081000')) {

        // this is an install
        $module="install";

    } elseif (isset($PHORUM['internal_version']) && $PHORUM['internal_version'] < PHORUMINTERNAL) {

        // this is an upgrade
        $module="upgrade";

    } else {

        // check for a session
        phorum_user_check_session("phorum_admin_session");

        if(!isset($GLOBALS["PHORUM"]["user"]) || !$GLOBALS["PHORUM"]["user"]["admin"]){
            // if not an admin
            unset($GLOBALS["PHORUM"]["user"]);
            $module="login";
        } else {
            // load the default module if none is specified
            if(!empty($_REQUEST["module"])){
                $module = basename($_REQUEST["module"]);
            } else {
                $module = "default";
            }

        }

    }

    ob_start();
    if($module!="help") include_once "./include/admin/header.php";
    @include_once "./include/admin/$module.php";
    if($module!="help") include_once "./include/admin/footer.php";
    ob_end_flush();


/////////////////////////////////////////////////

    function phorum_admin_error($error)
    {
        echo "<div class=\"PhorumAdminError\">$error</div>\n";
    }

    function phorum_admin_okmsg($error)
    {
        echo "<div class=\"PhorumAdminOkMsg\">$error</div>\n";
    }
    // phorum_get_language_info and phorum_get_template_info moved to common.php (used in the cc too)

    function phorum_get_folder_info()
    {
        $folders=array();
        $folder_data=array();

        $forums = phorum_db_get_forums();

        foreach($forums as $forum){
            if($forum["folder_flag"]){
                $path = $forum["name"];
                $parent_id=$forum["parent_id"];
                while($parent_id!=0  && $parent_id!=$forum["forum_id"]){
                    $path=$forums[$parent_id]["name"]."::$path";
                    $parent_id=$forums[$parent_id]["parent_id"];
                }
                $folders[$forum["forum_id"]]=$path;
            }
        }

        asort($folders);

        $tmp=array("--None--");

        foreach($folders as $id => $folder){
            $tmp[$id]=$folder;
        }

        $folders=$tmp;

        return $folders;

    }

    function phorum_get_forum_info($forums_only=0)
    {
        $folders=array();
        $folder_data=array();

        $forums = phorum_db_get_forums();

        foreach($forums as $forum){
            if($forums_only == 0 || $forum['folder_flag']==0 || ($forums_only=2 && $forum['vroot'] && $forum['vroot'] == $forum['forum_id']))  {
                $path = $forum["name"];
                $parent_id=$forum["parent_id"];
                while($parent_id!=0){
                    $path=$forums[$forum["parent_id"]]["name"]."::$path";

                    $parent_id=$forums[$parent_id]["parent_id"];
                }
                if($forum['vroot'] && $forum['vroot']==$forum['forum_id']) {
                        $path.=" (Virtual Root)";
                }
                $folders[$forum["forum_id"]]=$path;
            }
        }

        asort($folders);

        return $folders;

    }

    /*
     * Sets the given vroot for the descending forums / folders
     * which are not yet in another descending vroot
     *
     * $folder = folder from which we should go down
     * $vroot  = virtual root we set the folders/forums to
     * $old_vroot = virtual root which should be overrideen with the new value
     *
     */
    function phorum_admin_set_vroot($folder,$vroot=-1,$old_vroot=0) {
        // which vroot
        if($vroot == -1) {
            $vroot=$folder;
        }

        // get the desc forums/folders
        $descending=phorum_admin_get_descending($folder);
        $valid=array();

        // collecting vroots
        $vroots=array();
        foreach($descending as $id => $data) {
            if($data['folder_flag'] == 1 && $data['vroot'] != 0 && $data['forum_id'] == $data['vroot']) {
                $vroots[$data['vroot']]=true;
            }
        }

        // getting forums which are not in a vroot or not in *this* vroot
        foreach($descending as $id => $data) {
            if($data['vroot'] == $old_vroot || !isset($vroots[$data['vroot']])) {
                $valid[$id]=$data;
            }
        }

        // $valid = forums/folders which are not in another vroot
        $set_ids=array_keys($valid);
        $set_ids[]=$folder;

        $new_forum_data=array('forum_id'=>$set_ids,'vroot'=>$vroot);
        $returnval=phorum_db_update_forum($new_forum_data);

        return $returnval;
    }

    function phorum_admin_get_descending($parent) {

        $ret_data=array();
        $arr_data=phorum_db_get_forums(0,$parent);
        foreach($arr_data as $key => $val) {
            $ret_data[$key]=$val;
            if($val['folder_flag'] == 1) {
                $more_data=phorum_db_get_forums(0,$val['forum_id']);
                $ret_data=$ret_data + $more_data; // array_merge reindexes the array
            }
        }
        return $ret_data;
    }

    function phorum_upgrade_tables($fromversion,$toversion) {

          $PHORUM=$GLOBALS['PHORUM'];

          if(empty($fromversion) || empty($toversion)){
              die("Something is wrong with the upgrade script.  Please contact the Phorum Dev Team. ($fromversion,$toversion)");
          }

          $msg="";
          $upgradepath="./include/db/upgrade/{$PHORUM['DBCONFIG']['type']}/";

          // read in all existing files
          $dh=opendir($upgradepath);
          $upgradefiles=array();
          while ($file = readdir ($dh)) {
              if (substr($file,-4,4) == ".php") {
                  $upgradefiles[]=$file;
              }
          }
          unset($file);
          closedir($dh);

          // sorting by number
          sort($upgradefiles,SORT_NUMERIC);
          reset($upgradefiles);

          // advance to current version
          while(list($key,$val)=each($upgradefiles)) {
              if($val == $fromversion.".php")
              break;
          }



          // get the file for the next version (which we will upgrade to)
          list($dump,$file) = each($upgradefiles);

          // extract the pure version, needed as internal version
          $pure_version = basename($file,".php");

          if(empty($pure_version)){
              die("Something is wrong with the upgrade script.  Please contact the Phorum Dev Team. ($fromversion,$toversion)");
          }


          $upgradefile=$upgradepath.$file;

          if(file_exists($upgradefile)) {
              if (! is_readable($upgradefile))
                die("$upgradefile is not readable. Make sure the file has got the neccessary permissions and try again.");

              $msg.="Upgrading from db-version $fromversion to $pure_version ... ";
              $upgrade_queries=array();
              include($upgradefile);
              $err=phorum_db_run_queries($upgrade_queries);
              if($err){
                  $msg.= "an error occured: $err ... try to continue.<br />\n";
              } else {
                  $msg.= "done.<br />\n";
              }
              $GLOBALS["PHORUM"]["internal_version"]=$pure_version;
              phorum_db_update_settings(array("internal_version"=>$pure_version));
          } else {
              $msg="Ooops, the upgradefile is missing. How could this happen?";
          }

          return $msg;
    }

    function phorum_admin_gen_compare($txt) {
        $func = 0;
        if($txt == "gt") {
            $func = create_function('$a, $b', 'return $a > $b;');
        } elseif($txt == "gte") {
            $func = create_function('$a, $b', 'return $a >= $b;');
        } elseif($txt == "lt") {
            $func = create_function('$a, $b', 'return $a < $b;');
        } elseif($txt == "lte") {
            $func = create_function('$a, $b', 'return $a <= $b;');
        } elseif($txt == "eq") {
            $func = create_function('$a, $b', 'return $a == $b;');
        }
        if(!$func) {
            phorum_admin_error("Invalid posts comparison operator.");
            return NULL;
        }
        return $func;
    }

    function phorum_admin_filter_arr($arr,$field,$value,$cmpfn) {
        $new = array();
        foreach($arr as $item){
            if(isset($item[$field]) && $cmpfn($item[$field],$value)) {
                array_push($new,$item);
            }
        }
        return $new;
    }

?>
