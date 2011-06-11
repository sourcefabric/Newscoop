<?php
/**
 * @package Newscoop
 */
$locations = array();
$map_name = '';
$map_name_title = '';
$map = $articleObj->getMap();
if (is_object($map) && $map->exists()) {
    $locations = $map->getLocations();
    $map_name = $map->getName();
    if (0 < strlen($map_name)) {
        $map_name_title = $map_name;
        $map_name_title = str_replace("&", '&amp;', $map_name_title);
        $map_name_title = str_replace("<", '&lt;', $map_name_title);
        $map_name_title = str_replace(">", '&gt;', $map_name_title);
        $map_name_title = str_replace("\\", '&#92;', $map_name_title);
        $map_name_title = str_replace("'", '&#39;', $map_name_title);
        $map_name_title = " title='$map_name_title'";
    }
    $map_name_max_len = 20;
    if ($map_name_max_len < strlen($map_name)) {
        $map_name = substr($map_name, 0, $map_name_max_len) . "...";
    }
    $map_name = str_replace("&", '&amp;', $map_name);
    $map_name = str_replace("<", '&lt;', $map_name);
    $map_name = str_replace(">", '&gt;', $map_name);
}
$detachMapUrl = "/$ADMIN/articles/locations/do_unlink.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_article_number=$f_article_number&f_language_selected=$f_language_selected&f_language_id=$f_language_id&".SecurityToken::URLParameter();

$map_language_used = $f_language_selected;
if (0 == $map_language_used) {
    $map_language_used = $f_language_id;
}
$map_article_spec = '' . $f_article_number . '_' . $map_language_used;
?>
<div id="locations_box" class="ui-widget-content small-block block-shadow locations-box">
  <div class="collapsible">
    <h3 class="head ui-accordion-header ui-helper-reset ui-state-default ui-widget">
    <span class="ui-icon"></span>
    <a href="#" tabindex="-1"><?php putGS('Geolocation'); ?></a></h3>
  </div>
  <div class="padded">
  <?php
  $canEdit = ($inEditMode && $g_user->hasPermission('ChangeArticle'));
  if ($map->exists()) {
  ?>
    <?php if ($canEdit) { ?>
    <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"
      class="iframe map-thumb"><img
      src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Edit'); ?>" title="<?php putGS('Edit'); ?>" /><span><?php putGS('Edit'); ?></span></a>
    <a class="ui-state-default icon-button right-floated"
      href="<?php p($detachMapUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;" style="margin-bottom:8px;"><span
      class="ui-icon ui-icon-closethick"></span><?php putGS('Remove'); ?></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>&focus=default&loader=article"><span
      class="ui-icon ui-icon-zoomin"></span><?php putGS('Preview'); ?></a>
    <?php } else { ?>
    <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>&focus=default&loader=article"
        class="iframe map-thumb"><img
      src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Preview'); ?>" title="<?php putGS('Preview'); ?>" /></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>&focus=default&loader=article"><span
      class="ui-icon ui-icon-zoomin"></span><?php putGS('Preview'); ?></a>
  <?php }
  } elseif ($canEdit) { ?>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"><span
      class="ui-icon ui-icon-plusthick"></span><?php putGS('Add'); ?></a>
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
        $poi_name = str_replace("&", '&amp;', $poi_name);
        $poi_name = str_replace("<", '&lt;', $poi_name);
        $poi_name = str_replace(">", '&gt;', $poi_name);
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
