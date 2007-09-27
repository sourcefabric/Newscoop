<?php
    // this Settings file was hacked together from the example module
    // kindly created by Chris Eaton (tridus@hiredgoons.ca)
    // Update history:
    // Checkboxes added for "links in window" and "anti-spam tags" by Adam Sheik (www.cantonese.sheik.co.uk)

    if(!defined("PHORUM_ADMIN")) return;

    // save settings
    if(count($_POST)){
        $PHORUM["mod_bb_code"]["links_in_new_window"]=$_POST["links_in_new_window"] ? 1 : 0;
        $PHORUM["mod_bb_code"]["rel_no_follow"]=$_POST["rel_no_follow"] ? 1 : 0;
        $PHORUM["mod_bb_code"]["quote_hook"]=$_POST["quote_hook"] ? 1 : 0;

        if(!phorum_db_update_settings(array("mod_bb_code"=>$PHORUM["mod_bb_code"]))){
            $error="Database error while updating settings.";
        }
        else {
            echo "Settings Updated<br />";
        }
    }

    include_once "./include/admin/PhorumInputForm.php";
    $frm =& new PhorumInputForm ("", "post", "Save");
    $frm->hidden("module", "modsettings");
    $frm->hidden("mod", "bbcode"); // this is the directory name that the Settings file lives in

    if (!empty($error)){
        echo "$error<br />";
    }
    $frm->addbreak("Edit settings for the BBCode module");
    $frm->addmessage("When users post links on your forum, you can choose whether they open in a new window.");
    $frm->addrow("Open links in new window: ", $frm->checkbox("links_in_new_window", "1", "", $PHORUM["mod_bb_code"]["links_in_new_window"]));
    $frm->addmessage("Enable <a href=\"http://en.wikipedia.org/wiki/Blog_spam\" target=\"_blank\">
        Google's new anti-spam protocol</a> for links posted on your forums.
        <br/>
        Note, this doesn't stop spam links being posted, but it does mean that
        spammers don't get credit from Google from that link.");
    $frm->addrow("Use 'rel=nofollow' anti-spam tag: ", $frm->checkbox("rel_no_follow", "1", "", $PHORUM["mod_bb_code"]["rel_no_follow"]));
    $frm->addmessage("As of Phorum 5.1, there is the option to have quoted text altered by modules.  Since it only makes sense to have one module modifying the quoted text, you can disable this one part of this module.");

    $frm->addrow("Enable quote hook", $frm->checkbox("quote_hook", "1", "", $PHORUM["mod_bb_code"]["quote_hook"]));
    $frm->show();
?>
