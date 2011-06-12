<?php
/**
 * @package
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoPreferences.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/GeoMap.php');

camp_load_translation_strings('api');
camp_load_translation_strings('geolocation');

$f_language_id = Input::Get('f_language_selected', 'int', 0);
if (0 == $f_language_id) {
    $f_language_id = Input::Get('f_language_id', 'int', 0);
}
$f_article_number = Input::Get('f_article_number', 'int', 0);

$loaded_from = Input::Get('loader', 'string', 'map', true);

$f_focus = Input::Get('focus', 'string', 'default', true);
$focus_default = true;
if ('revert' == $f_focus) {$focus_default = false;}

if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Expires" content="now" />
    <title><?php putGS('Map Preview'); ?></title>

    <?php include dirname(__FILE__) . '/../../html_head.php'; ?>

    <link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-preview.css" />

    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/base64.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/json2.js"></script>

    <script type="text/javascript">
var map_preview_focus_default = function()
{
    var cur_location = window.location.href;
    var new_location = cur_location.replace("focus=revert", "focus=default");
    try {
        window.location.replace(new_location);
    } catch (e) {}
};

var map_preview_focus_revert = function()
{
    var cur_location = window.location.href;
    var new_location = cur_location.replace("focus=default", "focus=revert");
    if (-1 == new_location.indexOf("focus=revert"))
    {
        new_location += "&focus=revert";
    }

    try {
        window.location.replace(new_location);
    } catch (e) {}
};

var map_preview_close = function()
{
    try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
};

var map_show_edit = function()
{
    var cur_location = window.location.href;
    var new_location = cur_location.replace("preview.php", "popup.php");
    new_location = new_location.replace("&focus=default", "");
    new_location = new_location.replace("&focus=revert", "");
    try {
        window.location.replace(new_location);
    } catch (e) {}
};

    </script>


<?php
$map_width = 0;
$map_height = 0;

$focus_info = Geo_Preferences::GetFocusInfo();
$auto_focus = (bool) $focus_info["json_obj"]["auto_focus"];

$map_fixed_info = "";
$map_focused_info = "";

$focus_other_label = $map_fixed_label;
$focus_current_label = $map_focused_info;
if ($focus_default && (!$auto_focus)) {$focus_other_label = $map_focused_label; $focus_current_label = $map_fixed_info;}
if ((!$focus_default) && $auto_focus) {$focus_other_label = $map_focused_label; $focus_current_label = $map_fixed_info;}

if (!$focus_default) {$auto_focus = !$auto_focus;}

$map_options = array('auto_focus' => $auto_focus, 'load_common' => true);
echo Geo_Map::GetMapTagHeader($f_article_number, $f_language_id, $map_width, $map_height, $map_options);
?>
</head>
<body onLoad="return false;">
<div class="map_preview clearfix">
<!--Toolbar-->
<div id="map_toolbar_part" class="toolbar clearfix map_preview_toolbar">

    <div class="save-button-bar">
<?php
  $canEdit = $g_user->hasPermission('ChangeArticle');
  if ($canEdit)
  {
        $edit_str = getGS('Edit');
        if ("map" == strtolower($loaded_from)) {
            $edit_str = getGS('Return to edit');
        }
?>
        <input id="map_button_edit" type="submit" onClick="map_show_edit(); return false;" class="default-button" value="<?php echo $edit_str; ?>" name="edit" />
<?php
  }
?>
        <input id="map_button_close" type="submit" onClick="map_preview_close(); return false;" class="default-button" value="<?php putGS('Close'); ?>" name="close" />
    </div>
    <div id="map_preview_info" class="map_preview_info">
      <?php
        putGS('Map preview');
      ?>
    </div>
    <!-- end of map_save_part -->
  </div>
<!--END Toolbar-->
</div>
<div class="clear" style="height:10px;"></div>
<!-- Map Preview Begin -->
<div class="geomap_container">
  <div class="geomap_locations">
    <?php echo Geo_Map::GetMapTagList($f_article_number, $f_language_id); ?>
  </div>
  <div class="geomap_menu">
    <a href="#" class="ui-state-default text-button" onClick="<?php echo Geo_Map::GetMapTagCenter($f_article_number, $f_language_id); ?> return false;"><?php putGS('show initial map view'); ?></a>
  </div>
  <div class="geomap_map">
    <div class="geomap_menu">
        <?php echo Geo_Map::GetMapTagBody($f_article_number, $f_language_id); ?>
    </div>
  </div>
</div>
<div style="clear:both" ></div>
<!-- Map Preview End //-->
</body>
</html>
