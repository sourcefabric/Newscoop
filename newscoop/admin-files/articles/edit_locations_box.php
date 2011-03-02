<?php
/**
 * @package Newscoop
 */
$locations = array();
$map_name = "";
$map_name_title = "";
$map = $articleObj->getMap();
if (is_object($map) && $map->exists()) {
    $locations = $map->getLocations();
    $map_name = $map->getName();
    if (0 < strlen($map_name)) {
        $map_name_title = $map_name;
        $map_name_title = str_replace("&", "&amp;", $map_name_title);
        $map_name_title = str_replace("<", "&lt;", $map_name_title);
        $map_name_title = str_replace(">", "&gt;", $map_name_title);
        $map_name_title = str_replace("\\", "&#92;", $map_name_title);
        $map_name_title = str_replace("'", "&#39;", $map_name_title);
        $map_name_title = " title='$map_name_title'";
    }
    $map_name_max_len = 20;
    if ($map_name_max_len < strlen($map_name)) {
        $map_name = substr($map_name, 0, $map_name_max_len) . "...";
    }
    $map_name = str_replace("&", "&amp;", $map_name);
    $map_name = str_replace("<", "&lt;", $map_name);
    $map_name = str_replace(">", "&gt;", $map_name);
}
$detachMapUrl = "/$ADMIN/articles/locations/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();

$map_language_used = $f_language_selected;
if (0 == $map_language_used) {
    $map_language_used = $f_language_id;
}
$map_article_spec = '' . $f_article_number . '_' . $map_language_used;
?>
<script type="text/javascript">
geomap_popup_editing = null;
geomap_art_spec_popup = "";
geomap_art_spec_main = "" + '<?php echo $map_article_spec; ?>';
geomap_popup_show = function (edit) {
    var geomap_force_new = true;
    try {
        if ((!geomap_popup_editing) || geomap_popup_editing.closed) {geomap_art_spec_popup = "";}
    } catch(e) { geomap_art_spec_popup = ""; }
    try {
        if (geomap_art_spec_main == geomap_art_spec_popup) {
            geomap_popup_editing.focus();
            geomap_force_new = false;
        }
    } catch (e) { geomap_force_new = true; }
    if (geomap_force_new) {
        if (edit) {
            geomap_popup_editing = window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'geomap_edit_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1100, height=660, top=200, left=200');
        } else {
            geomap_popup_editing = window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'geomap_edit_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1100, height=660, top=200, left=200');
        }
        try {
            geomap_popup_editing.focus();
        } catch (e) {}
    };
}
</script>
<div id="locations_box" class="ui-widget-content small-block block-shadow">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php putGS('Locations'); ?></a></h3>
  </div>
  <div class="padded">
  <?php
  $canEdit = ($inEditMode && $g_user->hasPermission('ChangeArticle'));
  if ($map->exists()) {
  ?>
    <?php if ($canEdit) { ?>
    <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"
      class="iframe map-thumb"><img
      src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Edit'); ?>" title="<?php putGS('Edit'); ?>" /></a>
    <a class="ui-state-default icon-button right-floated"
      href="<?php p($detachMapUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;"><span
      class="ui-icon ui-icon-closethick"></span><?php putGS('Remove'); ?></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>"><span
      class="ui-icon ui-icon-zoomin"></span><?php putGS('Preview'); ?></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"><span
      class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <?php } else { ?>
    <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>"
        class="iframe map-thumb"><img
      src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Preview'); ?>" title="<?php putGS('Preview'); ?>" /></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>"><span
      class="ui-icon ui-icon-zoomin"></span><?php putGS('Preview'); ?></a>
  <?php }
  } elseif ($canEdit) { ?>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"><span
      class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
  <?php } ?>
  <?php if ($map->exists()) { ?>
    <h4 class="geo_map_name"<?php echo $map_name_title; ?>>
    <?php echo $map_name; ?>
    </h4>
  <?php } ?>
    <div class="clear"></div>
  <?php if ($map->exists() && !empty($locations)) { ?>
    <ul class="block-list">
    <?php
    $language_usage = $f_language_selected;
    if (!$language_usage) { $language_usage = $f_language_id; }
    foreach ($locations as $location) {
        $content = $location->getContent($language_usage);

        $poi_name = $content->getName();
        $poi_name_max_len = 40;
        if ($poi_name_max_len < strlen($poi_name)) {
            $poi_name = substr($poi_name, 0, $poi_name_max_len) . "...";
        }
        $poi_name = str_replace("&", "&amp;", $poi_name);
        $poi_name = str_replace("<", "&lt;", $poi_name);
        $poi_name = str_replace(">", "&gt;", $poi_name);
        if ($location->isEnabled($language_usage)) {
            echo '<li class="geomap_list_location_enabled">' . $poi_name . '</li>';
        } else {
            echo '<li class="geomap_list_location_disabled">' . $poi_name . '</li>';
        }
    }
    ?>
    </ul>
  <?php } ?>
  </div>
</div>
