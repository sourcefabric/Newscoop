<?php
if(!defined("PHORUM")) return;
$PHORUM = $GLOBALS["PHORUM"];
$prefix = $PHORUM["mod_smileys"]["prefix"];

include("./mods/editor_tools/smileys_js.php");

?>

<style type="text/css">
#phorum_mod_editor_tools_panel { display: none; }
#phorum_mod_editor_tools_smileys_dots { display: inline; }
#phorum_mod_editor_tools_smileys_loading { display: none; }
#phorum_mod_editor_tools_smileys { display: none; padding: 0px 5px 5px 5px; }
#phorum_mod_editor_tools_smileys img {
    margin: 0px 7px 0px 0px;
    vertical-align: bottom;
    cursor: pointer;
    cursor: hand;
}
</style>

<div id="phorum_mod_editor_tools_panel"
     class="PhorumStdBlockHeader PhorumNarrowBlock">

  <a href="javascript:toggle_smileys()">
    <b><?php print $PHORUM["DATA"]["LANG"]["AddSmiley"]?></b>
  </a>
  <div id="phorum_mod_editor_tools_smileys_dots"><b>...</b></div>
  <div id="phorum_mod_editor_tools_smileys_loading">
    (<?php print $PHORUM["DATA"]["LANG"]["LoadingSmileys"]; ?>)
  </div>

  <div id="phorum_mod_editor_tools_smileys"> <?php
    // Create a list of stub smiley images. The real images are only
    // loaded when the user opens the smiley panel.
    foreach($PHORUM["mod_smileys"]["smileys"] as $id => $smiley) {
      if (! $smiley["active"] || $smiley["is_alias"] || $smiley["uses"] == 1) continue;
      print "<img id=\"smiley-button-$id\" onclick=\"phorum_mod_smileys_insert_smiley('" . urlencode($smiley["search"]) . "')\" onload=\"phorum_mod_smileys_load_smiley(this)\" src=\"\"/>";
    } ?>
  </div>

</div>

<script type="text/javascript">
// Display the smileys panel. This way browsers that do not
// support javascript (but which do support CSS) will not
// show the smileys panel (since the default display style for the
// smileys panel is 'none').
document.getElementById("phorum_mod_editor_tools_panel").style.display = 'block';
</script>
