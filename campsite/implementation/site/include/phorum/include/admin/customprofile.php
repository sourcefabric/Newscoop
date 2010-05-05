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
    $exists_already=false;

    // reserved names for custom profile fields, extend as needed
    $reserved_customfield_names=array('panel','name','value','error');

    if(count($_POST) && $_POST["string"]!=""){
        $_POST['string']=trim($_POST['string']);


        if(!isset($_POST['html_disabled']))
            $_POST['html_disabled']=0;

        if($_POST['curr'] == 'NEW') {
            // checking names of existing fields
            foreach($PHORUM['PROFILE_FIELDS'] as $profile_field) {
                if($profile_field['name'] == $_POST['string']) {
                    $exists_already = true;
                    break;
                }
            }
        }

        if(preg_match("/^[^a-z]/i", $_POST["string"]) || preg_match("/[^a-z0-9_]/i", $_POST["string"])){
            $error="Field names can only contain letters, numbers and _.  They must start with a letter.";
        } elseif(in_array($_POST['string'],$reserved_customfield_names)) {
            $error="This name is reserved for use in phorum itself. Please use a different name for your new custom profile-field.";
        } elseif($exists_already) {
            $error="A custom profile-field with that name exists. Please use a different name for your new custom profile-field.";
        } else {
            if(!isset($PHORUM['PROFILE_FIELDS']["num_fields"])) {
                if(count($PHORUM['PROFILE_FIELDS'])) {
                    $PHORUM['PROFILE_FIELDS']["num_fields"]=count($PHORUM['PROFILE_FIELDS']);
                } else {
                    $PHORUM['PROFILE_FIELDS']["num_fields"]=0;
                }
            }

            if($_POST["curr"]!="NEW"){ // editing an existing field
                $PHORUM["PROFILE_FIELDS"][$_POST["curr"]]['name']=$_POST["string"];
                $PHORUM["PROFILE_FIELDS"][$_POST["curr"]]['length']=$_POST['length'];
                $PHORUM["PROFILE_FIELDS"][$_POST["curr"]]['html_disabled']=$_POST['html_disabled'];
            } else { // adding a new field
                $PHORUM['PROFILE_FIELDS']["num_fields"]++;
                $PHORUM["PROFILE_FIELDS"][$PHORUM['PROFILE_FIELDS']["num_fields"]]=array();
                $PHORUM["PROFILE_FIELDS"][$PHORUM['PROFILE_FIELDS']["num_fields"]]['name']=$_POST["string"];
                $PHORUM["PROFILE_FIELDS"][$PHORUM['PROFILE_FIELDS']["num_fields"]]['length']=$_POST['length'];
                $PHORUM["PROFILE_FIELDS"][$PHORUM['PROFILE_FIELDS']["num_fields"]]['html_disabled']=$_POST['html_disabled'];
            }

            if(!phorum_db_update_settings(array("PROFILE_FIELDS"=>$PHORUM["PROFILE_FIELDS"]))){
                $error="Database error while updating settings.";
            } else {
                echo "Profile Field Updated<br />";
            }

        }

    }

    if(isset($_GET["curr"])){
        if(isset($_GET["delete"])){
            unset($PHORUM["PROFILE_FIELDS"][$_GET["curr"]]);
            phorum_db_update_settings(array("PROFILE_FIELDS"=>$PHORUM["PROFILE_FIELDS"]));
            echo "Profile Field Deleted<br />";
        } else {
            $curr = $_GET["curr"];
        }
    }


    if($curr!="NEW"){
        $string=$PHORUM["PROFILE_FIELDS"][$curr]['name'];
        $length=$PHORUM["PROFILE_FIELDS"][$curr]['length'];
        $html_disabled=$PHORUM["PROFILE_FIELDS"][$curr]['html_disabled'];
        $title="Edit Profile Field";
        $submit="Update";
    } else {
        settype($string, "string");
        $title="Add A Profile Field";
        $submit="Add";
        $length=255;
        $html_disabled=1;
    }

    if($error){
        phorum_admin_error($error);
    }

    include_once "./include/admin/PhorumInputForm.php";

    $frm = new PhorumInputForm ("", "post", $submit);

    $frm->hidden("module", "customprofile");

    $frm->hidden("curr", "$curr");

    $frm->addbreak($title);

    $frm->addrow("Field Name", $frm->text_box("string", $string, 50));
    $frm->addrow("Field Length (Max. 65000)", $frm->text_box("length", $length, 50));
    $frm->addrow("Disable HTML", $frm->checkbox("html_disabled",1,"Yes",$html_disabled));

    $frm->show();

    echo "This will only add the field to the list of allowed fields.  You will need to edit the register and profile templates to actually allow users to use the fields.  Use the name you enter here as the name property of the HTML form element.";

    if($curr=="NEW"){

        echo "<hr class=\"PhorumAdminHR\" />";
        if(isset($PHORUM['PROFILE_FIELDS']["num_fields"]))
            unset($PHORUM['PROFILE_FIELDS']["num_fields"]);

        if(count($PHORUM["PROFILE_FIELDS"])){

            echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"PhorumAdminTable\" width=\"100%\">\n";
            echo "<tr>\n";
            echo "    <td class=\"PhorumAdminTableHead\">Field</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">Length</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">HTML disabled</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">&nbsp;</td>\n";
            echo "</tr>\n";

            foreach($PHORUM["PROFILE_FIELDS"] as $key => $item){
                echo "<tr>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".$item['name']."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".$item['length']."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".($item['html_disabled']?"Yes":"No")."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\"><a href=\"$_SERVER[PHP_SELF]?module=customprofile&curr=$key&?edit=1\">Edit</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=customprofile&curr=$key&delete=1\">Delete</a></td>\n";
                echo "</tr>\n";
            }

            echo "</table>\n";

        } else {

            echo "No custom fields currently allowed.";

        }

    }

?>
