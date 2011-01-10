<?php
$locations = array();
$map = $articleObj->getMap();
if (is_object($map) && $map->exists()) {
    $locations = $map->getLocations();
}
$detachMapUrl = "/$ADMIN/articles/locations/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();

$map_language_used = $f_language_selected;
if (0 == $map_language_used) {
    $map_language_used = $f_language_id;
}
$map_article_spec = "" . $f_article_number . "_" . $map_language_used;

?>
<table style="width: 100%; border: 1px solid #EEEEEE;">
<tr>
  <td>
    <table cellpadding="3" cellspacing="0" style="width: 100%; background-color: #eee;">
    <tr>
      <td style="text-align: left">
        <strong><?php putGS('Locations'); ?></strong>
      </td>
      <td style="text-align: right">
        <table cellpadding="2" cellspacing="0">
        <tr>
          <script type="text/javascript">
                geomap_popup_editing = null;
                geomap_art_spec_popup = "";
                geomap_art_spec_main = "" + '<?php echo $map_article_spec; ?>';
                geomap_popup_show = function ()
                {
                    var geomap_force_new = true;
                    try { 
                        if ((!geomap_popup_editing) || geomap_popup_editing.closed) {geomap_art_spec_popup = "";}
                    }
                    catch(e) {geomap_art_spec_popup = "";}
                    try {
                        if (geomap_art_spec_main == geomap_art_spec_popup)
                        {
                            geomap_popup_editing.focus();
                            geomap_force_new = false;
                        }
                    } catch (e) { geomap_force_new = true; }
                    if (geomap_force_new) 
                    {
                        geomap_popup_editing = window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'geomap_edit_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1100, height=660, top=200, left=200');
                        try {
                            geomap_popup_editing.focus();
                        } catch (e) {} 
                    };
                }
          </script>
          <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) {  ?>
          <td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" /></td>
          <td><a href="javascript: void(0);" onclick="geomap_popup_show(); return false;"><?php putGS('Edit'); ?></a></td>
          <?php } ?>
        </tr>
        </table>
      </td>
    </tr>
    </table>
  </td>
</tr>
<?php if ($map->exists()) { ?>
<tr>
  <td align="center" colspan="2">
    <table>
    <tr>
      <td>
        <a href="#" onclick="geomap_popup_preview = window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'geomap_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200'); geomap_popup_preview.focus(); return false;"><img src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/world_map.png" /></a>
      </td>
      <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) { ?>
      <td align="left">
        <a href="<?php p($detachMapUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
      </td>
      <?php } ?>
    </tr>
    </table>
  </td>
</tr>
<?php
}
if (!empty($locations)) { ?>
<tr>
  <td>
    <ol class="points">
      <?php
      $language_usage = $f_language_selected;
      if (!$language_usage) { $language_usage = $f_language_id; }
      foreach ($locations as $location) {
          $content = $location->getContent($language_usage);
          if ($location->isEnabled($language_usage)) {
              echo '<li class="geomap_list_location_enabled">' . $content->getName() . '</li>';
          } else {
              echo '<li class="geomap_list_location_disabled">' . $content->getName() . '</li>';
          }
      }
      ?>
    </ol>
  </td>
</tr>
<?php } ?>
</table>
