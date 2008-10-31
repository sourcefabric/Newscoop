<?php

    if(!defined("PHORUM_ADMIN")) return;

    $error="";
    $curr="NEW";

    $match_types = array("string", "PCRE");

    if(count($_POST) && $_POST["search"]!="" && $_POST["replace"]!=""){

        $item = array("search"=>$_POST["search"], "replace"=>$_POST["replace"], "pcre"=>$_POST["pcre"]);

        if($_POST["curr"]!="NEW"){
            $PHORUM["mod_replace"][$_POST["curr"]]=$item;
        } else {
            $PHORUM["mod_replace"][]=$item;
        }

        if(empty($error)){
            if(!phorum_db_update_settings(array("mod_replace"=>$PHORUM["mod_replace"]))){
                $error="Database error while updating settings.";
            } else {
                echo "Replacement Updated<br />";
            }
        }
    }

    if(isset($_GET["curr"])){
        if(isset($_GET["delete"])){
            unset($PHORUM["mod_replace"][$_GET["curr"]]);
            phorum_db_update_settings(array("mod_replace"=>$PHORUM["mod_replace"]));
            echo "Replacement Deleted<br />";
        } else {
            $curr = $_GET["curr"];
        }
    }


    if($curr!="NEW"){
        extract($PHORUM["mod_replace"][$curr]);
        $title="Edit Replacement";
        $submit="Update";
    } else {
        settype($string, "string");
        settype($type, "int");
        settype($pcre, "int");
        $title="Add A Replacement";
        $submit="Add";
    }

    include_once "./include/admin/PhorumInputForm.php";

    $frm =& new PhorumInputForm ("", "post", $submit);

    $frm->hidden("module", "modsettings");

    $frm->hidden("mod", "replace");

    $frm->hidden("curr", "$curr");

    $frm->addbreak($title);

    $frm->addrow("String To Match", $frm->text_box("search", $search, 50));

    $frm->addrow("Replacement", $frm->text_box("replace", $replace, 50));

    $frm->addrow("Compare As", $frm->select_tag("pcre", $match_types, $pcre));

    $frm->show();

    echo "If using PCRE for comparison, \"Sting To Match\" should be a valid PCRE expression. See <a href=\"http://php.net/pcre\">the PHP manual</a> for more information.";

    if($curr=="NEW"){

        echo "<hr class=\"PhorumAdminHR\" />";

        if(count($PHORUM["mod_replace"])){

            echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" class=\"PhorumAdminTable\" width=\"100%\">\n";
            echo "<tr>\n";
            echo "    <td class=\"PhorumAdminTableHead\">Search</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">Replace</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">Compare Method</td>\n";
            echo "    <td class=\"PhorumAdminTableHead\">&nbsp;</td>\n";
            echo "</tr>\n";

            foreach($PHORUM["mod_replace"] as $key => $item){
                echo "<tr>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".htmlspecialchars($item["search"])."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".htmlspecialchars($item["replace"])."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\">".$match_types[$item["pcre"]]."</td>\n";
                echo "    <td class=\"PhorumAdminTableRow\"><a href=\"$_SERVER[PHP_SELF]?module=modsettings&mod=replace&curr=$key&?edit=1\">Edit</a>&nbsp;&#149;&nbsp;<a href=\"$_SERVER[PHP_SELF]?module=modsettings&mod=replace&curr=$key&delete=1\">Delete</a></td>\n";
                echo "</tr>\n";
            }

            echo "</table>\n";

        } else {

            echo "No replacements in list currently.";

        }

    }

?>