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
    $curr="NEW";

    // retrieving the forum-info
    $forum_list=phorum_get_forum_info(2);

    $forum_list[0]="GLOBAL";

    // conversion of old data if existing
    if(isset($PHORUM["bad_words"]) && count($PHORUM['bad_words'])) {
    print "upgrading badwords<br>";
        foreach($PHORUM['bad_words'] as $key => $data) {
            phorum_db_mod_banlists(PHORUM_BAD_WORDS ,0 ,$data ,0 ,0);
            unset($PHORUM["bad_words"][$key]);
        }
        phorum_db_update_settings(array("bad_words"=>$PHORUM["bad_words"]));
    }

    if(count($_POST) && $_POST["string"]!=""){
        if($_POST["curr"]!="NEW"){
            $ret=phorum_db_mod_banlists(PHORUM_BAD_WORDS ,0 ,$_POST["string"] ,$_POST['forumid'] ,$_POST['curr']);
        } else {
            $ret=phorum_db_mod_banlists(PHORUM_BAD_WORDS ,0 ,$_POST["string"] ,$_POST['forumid'] ,0);
        }

        if(!$ret){
            $error="Database error while updating badwords.";
        } else {
            echo "Bad Word Added<br />";
        }
    }

    if(isset($_GET["curr"])){
        if(isset($_GET["delete"])){
            phorum_db_del_banitem($_GET['curr']);
            echo "Ban Item Deleted<br />";
        } else {
            $curr = $_GET["curr"];
        }
    }
    if($curr!="NEW"){
        extract(phorum_db_get_banitem($curr));
        $title="Edit Bad Word Item";
        $submit="Update";
    } else {
        settype($string, "string");
        settype($type, "int");
        settype($pcre, "int");
        settype($forumid,"int");
        $title="Add A Bad Word";
        $submit="Add";
    }


    settype($string, "string");
    settype($type, "int");
    settype($pcre, "int");

    if($error){
        phorum_admin_error($error);
    }

    // load bad-words-list
    $banlists=phorum_db_get_banlists();
    $bad_words=$banlists[PHORUM_BAD_WORDS];

    include_once "./include/admin/PhorumInputForm.php";

    $frm = new PhorumInputForm ("", "post", $submit);

    $frm->hidden("module", "badwords");

    $frm->hidden("curr", "$curr");

    $frm->addbreak($title);

    $frm->addrow("Bad Word", $frm->text_box("string", $string, 50));

    $frm->addrow("Valid for Forum", $frm->select_tag("forumid", $forum_list, $forumid));

    $frm->show();

    echo "<hr class=\"PhorumAdminHR\" />";

    if(count($bad_words)){

        echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"PhorumAdminTable\" width=\"100%\">\n";
        echo "<tr>\n";
        echo "    <td class=\"PhorumAdminTableHead\">Word</td>\n";
        echo "    <td class=\"PhorumAdminTableHead\">Valid for Forum</td>\n";
        echo "    <td class=\"PhorumAdminTableHead\">&nbsp;</td>\n";
        echo "</tr>\n";

        foreach($bad_words as $key => $item){
            $ta_class = "PhorumAdminTableRow".($ta_class == "PhorumAdminTableRow" ? "Alt" : "");
            echo "<tr>\n";
            echo "    <td class=\"".$ta_class."\">".htmlspecialchars($item[string])."</td>\n";
            echo "    <td class=\"".$ta_class."\">".$forum_list[$item["forum_id"]]."</td>\n";
            echo "    <td class=\"".$ta_class."\"><a href=\"$_SERVER[PHP_SELF]?module=badwords&curr=$key&edit=1\">Edit</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=badwords&curr=$key&delete=1\">Delete</a></td>\n";
            echo "</tr>\n";
        }

        echo "</table>\n";

    } else {

        echo "No bad words in list currently.";

    }
?>
