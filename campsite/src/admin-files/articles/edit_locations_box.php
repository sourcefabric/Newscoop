<?php
//camp_load_translation_strings("geolocation");

$locations = array();
$map = $articleObj->getMap();
if (is_object($map) && $map->exists()) {
    $locations = $map->getLocations();
}
$detachUrl = "/$ADMIN/articles/locations/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();
?>
<table style="width: 100%; border: 1px solid #EEEEEE;">
<tr>
  <td>
    <table cellpadding="3" cellspacing="0" style="width: 100%; background-color: #eee;">
    <tr>
      <td style="text-align: left">
        <strong><?php putGS("Locations"); ?></strong>
      </td>
      <td style="text-align: right">
        <table cellpadding="2" cellspacing="0">
        <tr>
          <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) {  ?>
          <td><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/add.png" /></td>
          <td><a href="javascript: void(0);" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'autopublish_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');"><?php putGS("Edit"); ?></a></td>
          <?php } ?>
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
            <a href="#" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'map_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');"><img src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/world_map.png" /></a>
          </td>
          <?php if (($f_edit_mode == "edit") && $g_user->hasPermission('ChangeArticle')) {  ?>
          <td align="left">
            <a href="<?php p($detachUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;"><img src="<?php p($Campsite["ADMIN_IMAGE_BASE_URL"]);?>/unlink.png" border="0"></a>
          </td>
          <?php } ?>
        </tr>
        </table>
      </td>
    </tr>
    <?php } ?>
    </table>
  </td>
</tr>
<?php
if (!empty($locations)) { ?>
<tr>
  <td>
    <ol class="points">
      <?php
      $language_usage = $f_language_selected;
      if (!$language_usage) {$language_usage = $f_language_id;}
      foreach ($locations as $location) {
          $content = $location->getContent($language_usage);
          if ($location->isEnabled($f_language_id)) {
              echo '<li class="map_list_location_enabled">' . $content->getName() . '</li>';
          } else {
              echo '<li class="map_list_location_disabled">' . $content->getName() . '</li>';
          }
      }
      ?>
    </ol>
  </td>
</tr>
<?php } ?>
</table>
