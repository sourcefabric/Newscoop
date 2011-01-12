<?php
/**
 * @package Newscoop
 */
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
$map_article_spec = '' . $f_article_number . '_' . $map_language_used;
?>
<script type="text/javascript">
geomap_popup_editing = null;
geomap_art_spec_popup = "";
geomap_art_spec_main = "" + '<?php echo $map_article_spec; ?>';
geomap_popup_show = function () {
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
        geomap_popup_editing = window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/popup.php"); ?>', 'geomap_edit_window', 'scrollbars=yes, resizable=no, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');
        try {
            geomap_popup_editing.focus();
        } catch (e) {}
    };
}
</script>
<div class="ui-widget-content small-block block-shadow">
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
    <a class="ui-state-default icon-button right-floated"
      href="<?php p($detachMapUrl); ?>" onclick="return confirm('<?php putGS("Are you sure you want to remove the map from the article?"); ?>'); return false;"><span
      class="ui-icon ui-icon-closethick"></span><?php putGS('Remove'); ?></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>">
      <?php putGS('Preview'); ?></a>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"><span
      class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
    <a href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/preview.php'); ?>"
        class="iframe map-thumb"><img
      src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Preview'); ?>" title="<?php putGS('Preview'); ?>" /></a>
    <?php } else { ?>
    <a class="ui-state-default icon-button right-floated"
      href="#" onclick="window.open('<?php echo camp_html_article_url($articleObj, $f_language_id, "locations/preview.php"); ?>', 'geomap_preview_window', 'scrollbars=yes, resizable=yes, menubar=no, toolbar=no, width=1050, height=600, top=200, left=200');">
      <?php putGS('Preview'); ?></a>
    <img src="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/images/map_thumb.png" alt="<?php putGS('Preview'); ?>" title="<?php putGS('Preview'); ?>" />
  <?php }
  } elseif ($canEdit) { ?>
    <a class="iframe ui-state-default icon-button right-floated"
      href="<?php echo camp_html_article_url($articleObj, $f_language_id, 'locations/popup.php'); ?>"><span
      class="ui-icon ui-icon-pencil"></span><?php putGS('Edit'); ?></a>
  <?php } ?>
    <div class="clear"></div>
  <?php if ($map->exists() && !empty($locations)) { ?>
    <ul class="block-list">
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
    </ul>
  <?php } ?>
  </div>
</div>
