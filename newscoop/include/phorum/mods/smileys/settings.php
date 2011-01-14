<?php

if(!defined("PHORUM_ADMIN")) return;

require_once("./include/admin/PhorumInputForm.php");
require_once("./mods/smileys/smileyslib.php");
require_once("./mods/smileys/defaults.php");

// The definition of the possible uses for a smiley.
$PHORUM_MOD_SMILEY_USES = array(
    0   => "Body",
    1   => "Subject",
    2   => "Body + Subject",
);

// ---------------------------------------------------------------------------
// Handle actions for sent form data.
// ---------------------------------------------------------------------------

// The action to perform.
$action = isset($_POST["action"]) ? $_POST["action"] : "";

// Keep track if the settings must be saved in the database.
$do_db_update = false;

// Keep track of error and success messages.
$error="";
$okmsg = "";

// Initialize smiley_id parameter.
$smiley_id = isset($_POST["smiley_id"]) ?  $_POST["smiley_id"] : "NEW";

// ACTION: Changing the mod_smileys settings.
if (empty($error) && $action == "edit_settings") {
    $_POST["prefix"] = trim($_POST["prefix"]);
    // Is the field filled in?
    if (empty($_POST["prefix"])) {
        $error = "Please, fill in the smiley prefix path";
    // Deny absolute paths.
    } elseif (preg_match(MOD_SMILEYS_ABSPATH_MATCH, $_POST["prefix"])) {
        $error = "The smiley path must be a path, relative to Phorum's " .
                 "installation directory";
    // Is the specified prefix a directory?
    } elseif (!is_dir($_POST["prefix"])) {
        $error = "The smiley prefix path " . 
                 '"' . htmlspecialchars($_POST["prefix"]) . '" ' .
                 " does not exist";
    }

    // All is okay. Set the prefix path in the config.
    if (empty($error))
    { 
        // Make sure the prefix path ends with a "/".
        if (substr($_POST["prefix"], -1, 1) != '/') {
            $_POST["prefix"] .= "/";
        }

        $PHORUM["mod_smileys"]["prefix"] = $_POST["prefix"];

        $okmsg = "The smiley settings have been saved successfully";
        $do_db_update = true;
    }
}

// ACTION: Adding or updating smileys.
if (empty($error) && $action == "edit_smiley")
{
    // Trim whitespace from form input fields.
    foreach (array("search","smiley","alt") as $field) {
        if (isset($_POST[$field])) $_POST[$field] = trim($_POST[$field]);
    }

    // Check if the search string is entered.
    if (empty($_POST["search"]))
        $error = "Please enter the string to match";
    // Check if a replace smiley is selected.
    elseif (empty($_POST["smiley"]))
        $error = "Please, select a smiley to replace the string " .
                 htmlspecialchars($_POST["search"]) . " with";
    // Check if the smiley doesn't already exist.
    if (empty($error)) {
        foreach ($PHORUM["mod_smileys"]["smileys"] as $id => $smiley) {
            if ($smiley["search"] == $_POST["search"] &&
                $_POST["smiley_id"] != $id) {
                $error = "The smiley " .
                         '"' . htmlspecialchars($_POST["search"]) . '" ' .
                         "already exists";
                break;
            }
        }
    }

    // All fields are okay. Update the smiley list.
    if (empty($error))
    {
        $item = array(
            "search" => $_POST["search"],
            "smiley" => $_POST["smiley"],
            "alt"    => $_POST["alt"],
            "uses"   => $_POST['uses']
        );

        if ($smiley_id == "NEW") {
            $PHORUM["mod_smileys"]["smileys"][]=$item;
            $okmsg = "The smiley has been added successfully";
        } else {
            $PHORUM["mod_smileys"]["smileys"][$smiley_id]=$item;
            $okmsg = "The smiley has been updated successfully";
        }

        $do_db_update = true;
    }
}

// GET based actions.
if (empty($error) && isset($_GET["smiley_id"])) 
{
    // ACTION: Deleting a smiley from the list.
    if (isset($_GET["delete"])) {
        unset($PHORUM["mod_smileys"]["smileys"][$_GET["smiley_id"]]);
        $do_db_update = true;
        $okmsg = "The smiley has been deleted successfully";
    }

    // ACTION: Startup editing a smiley from the list.
    if (isset($_GET["edit"])) {
        $smiley_id = $_GET["smiley_id"];
    }
}


// ---------------------------------------------------------------------------
// Do database updates.
// ---------------------------------------------------------------------------

// Changes have been made to the smileys configuration.
// Store these changes in the database.
if (empty($error) && $do_db_update)
{
    list($modinfo, $message) = phorum_mod_smileys_store($PHORUM["mod_smileys"]);
    if ($modinfo == NULL) {
        $error = $message;
    } else {
        if (empty($okmsg)) $okmsg = $message;
        $PHORUM["mod_smileys"] = $modinfo;

        // Back to the startscreen
        unset($_POST);
        $smiley_id = 'NEW';
    }
}


// ---------------------------------------------------------------------------
// Display the settings page
// ---------------------------------------------------------------------------

// Get the current list of available smiley images.
$available_smileys = phorum_mod_smileys_available();

// Javascript for displaying a smiley preview when a smiley image
// is selected from the drop down box.
?>
<script type="text/javascript">
function change_image(new_image) {
  var div = document.getElementById("preview_div");
  var img = document.getElementById("preview_image");
  if (new_image.length == 0) {
    new_image = "./images/trans.gif";
    div.style.display = 'none';
  } else {
    new_image = "<?php print $PHORUM["mod_smileys"]["prefix"]?>" + new_image;
    div.style.display = 'block';
  }
  img.src =new_image;
}
</script>
<?php

// Display the result message.
if (! empty($error)) {
    phorum_admin_error($error);
} elseif (! empty($okmsg)) {
    phorum_admin_okmsg($okmsg);
}

// Count things.
$total_smileys = 0;
$inactive_smileys = 0;
foreach ($PHORUM["mod_smileys"]["smileys"] as $id => $smiley) {
    $total_smileys ++;
    if (! $smiley["active"]) $inactive_smileys ++;
}

// Display a warning in case there are no smiley images available.
if (! count($available_smileys)) {
    phorum_admin_error(
        "<strong>Warning:</strong><br/>" .
        "No smiley images were found in your current smiley prefix " .
        "path. Please place some smileys in the directory " .
        htmlspecialchars($PHORUM["mod_smileys"]["prefix"]) . 
        " or change your prefix path to point to a directory " .
        "containing smiley images.");
} elseif ($inactive_smileys) {
    phorum_admin_error(
        "<strong>Warning:</strong><br/>" .
        "You have $inactive_smileys smiley(s) configured for which the " .
        "image file was not found (marked as \"UNAVAILBLE\" in the list " .
        "below). Delete the smiley(s) from the list or place the missing " .
        "images in the directory \"" .
        htmlspecialchars($PHORUM["mod_smileys"]["prefix"]) . "\". After " .
        "placing new smiley images, click \"Save settings\" to update " .
        "the smiley settings."); 
}

// Create the smiley settings form.
if ($smiley_id == "NEW")
{
    $frm = new PhorumInputForm ("", "post", 'Save settings');
    $frm->hidden("module", "modsettings");
    $frm->hidden("mod", "smileys");
    $frm->hidden("action", "edit_settings");
    $frm->addbreak("Smiley Settings");
    $row = $frm->addrow("Smiley Prefix Path", $frm->text_box("prefix", $PHORUM["mod_smileys"]["prefix"], 50));
    $frm->addhelp($row,
        "Set the smiley image prefix path",
        "This option can be used to set the path to the directory where
         you have stored your smileys. This path must be relative to the
         directory in which you installed the Phorum software. Absolute
         paths cannot be used here.");
    $frm->show();
}

// No smiley images in the current prefix path? Then do not show the
// rest of the forms. Let the admin fix this issue first.
if (!count($available_smileys)) return;

// Create the smiley adding and editing form.
if (isset($_POST["smiley_id"])) {
    $search = $_POST["search"];
    $smiley = $_POST["smiley"];
    $alt    = $_POST["alt"];
    $uses   = $_POST["uses"];
}
if ($smiley_id == "NEW") {
    $title = "Add a new smiley";
    $submit = "Add smiley";

    // Fill initial form data for creating smileys.
    if (! isset($_POST["smiley_id"])) {
        $search = "";
        $smiley = "";
        $alt    = "";
        $uses   = 2;
    }
} else {
    $title = "Update a smiley";
    $submit = "Update smiley";

    // Fill initial form data for editing smileys.
    if (! isset($_POST["smiley_id"])) {
        $smileydata = $PHORUM["mod_smileys"]["smileys"][$smiley_id];
        $search = $smileydata["search"];
        $smiley = $smileydata["smiley"];
        $alt    = $smileydata["alt"];
        $uses   = $smileydata["uses"];
    }
}
$frm = new PhorumInputForm ("", "post", $submit);
$frm->hidden("module", "modsettings");
$frm->hidden("mod", "smileys");
$frm->hidden("smiley_id", $smiley_id);
$frm->hidden("action", "edit_smiley");
$frm->addbreak($title);
$frm->addrow("Smiley string to match", $frm->text_box("search", $search, 20));
$row = $frm->addrow("Image to replace the string with", $frm->select_tag("smiley", array_merge(array(''=>'Select smiley ...'),$available_smileys), $smiley, "onChange=\"change_image(this.options[this.selectedIndex].value);\"") . "&nbsp;&nbsp;<div style=\"display:none;margin-top:5px\" id=\"preview_div\"><strong>Preview: </strong><img src=\"images/trans.gif\" id=\"preview_image\" /></div>");
$frm->addhelp($row,
    "Smiley replacement image",
    "The drop down list shows all images that were found in your
     smiley prefix path. If you want to add your own smileys, simply place
     them in \"" . htmlspecialchars($PHORUM["mod_smileys"]["prefix"]) . "\"
     and reload this page.");
$frm->addrow("ALT tag for the image", $frm->text_box("alt", $alt, 40));
$frm->addrow("Used for", $frm->select_tag("uses", $PHORUM_MOD_SMILEY_USES, $uses));
$frm->show();

// Make the preview image visible in case a $smiley is set.
if (!empty($smiley)) {?>
    <script type="text/javascript">
    change_image('<?php print addslashes($smiley) ?>');
    </script><?php
}

// Show the configured list of smileys.
if ($smiley_id == "NEW")
{
    print "<hr class=\"PhorumAdminHR\" />";

    if (count($PHORUM["mod_smileys"]["smileys"]))
    { ?>
        <table cellspacing="1" class="PhorumAdminTable" width="100%">
        <tr>
          <td class="PhorumAdminTableHead">String</td>
          <td class="PhorumAdminTableHead">Image file</td>
          <td class="PhorumAdminTableHead">Image</td>
          <td class="PhorumAdminTableHead">ALT tag</td>
          <td class="PhorumAdminTableHead">Used for</td>
          <td class="PhorumAdminTableHead">&nbsp;</td>
        </tr>
        <?php

        foreach ($PHORUM["mod_smileys"]["smileys"] as $id => $item)
        {
            $used_for_txt = $PHORUM_MOD_SMILEY_USES[$item['uses']];
            foreach ($item as $key => $val) {
                $item[$key] = htmlspecialchars($val);
            }
            $action_url = "$_SERVER[PHP_SELF]?module=modsettings&mod=smileys&smiley_id=$id";

            print "<tr>\n";
            print "  <td class=\"PhorumAdminTableRow\">{$item["search"]}</td>\n";
            print "  <td class=\"PhorumAdminTableRow\">{$item["smiley"]}</td>\n";
            print "  <td class=\"PhorumAdminTableRow\" align=\"center\">";
            if ($item["active"]) {
              print "<img src=\"{$PHORUM["mod_smileys"]["prefix"]}{$item["smiley"]}\"/></td>\n";
            } else {
              print "<div style=\"color:red\">UNAVAILBLE</div>";
            }
            print "  <td class=\"PhorumAdminTableRow\">{$item["alt"]}</td>\n";
            print "  <td class=\"PhorumAdminTableRow\" style=\"white-space:nowrap\">$used_for_txt</td>\n";
            print "  <td class=\"PhorumAdminTableRow\">" .
                  "<a href=\"$action_url&edit=1\">Edit</a>&nbsp;&#149;&nbsp;" .
                  "<a href=\"$action_url&delete=1\">Delete</a></td>\n";
            print "</tr>\n";
        }

        print "</table>\n";

    } else {

        print "Currently, you have no smiley replacements configured.";

    }

    // For a more clear end of page.
    print "<br/><br/><br/>";
}

?>
