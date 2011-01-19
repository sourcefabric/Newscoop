<?php
/**
 * @package
 */
require_once($GLOBALS['g_campsiteDir']."/classes/GeoPreferences.php");
require_once($GLOBALS['g_campsiteDir']."/classes/GeoMap.php");

camp_load_translation_strings("api");
camp_load_translation_strings("geolocation");

$f_language_id = Input::Get('f_language_selected', 'int', 0);
if (0 == $f_language_id) {
    $f_language_id = Input::Get('f_language_id', 'int', 0);
}
$f_article_number = Input::Get('f_article_number', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<title><?php putGS("Map Preview"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-preview.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-popups.css" />

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>

	<script type="text/javascript">

var map_preview_close = function()
{
    try {
        if (parent.$.fancybox.reload) {
            parent.$.fancybox.message = '<?php putGS('Locations updated.'); ?>';
        }
        parent.$.fancybox.close();
    }
    catch (e) {window.close();}
}

var map_show_edit = function()
{
    var cur_location = window.location.href;
    var new_location = cur_location.replace("preview.php", "popup.php");
    try {
        window.location.replace(new_location);
    } catch (e) {}
}

	</script>


<?php
$map_width = 0;
$map_height = 0;
echo Geo_Map::GetMapTagHeader($f_article_number, $f_language_id, $map_width, $map_height);
?>
</head>
<body onLoad="return false;">
<div class="map_preview clearfix">
<!--Toolbar-->
<div id="map_toolbar_part" class="toolbar clearfix">

    <div class="save-button-bar">
<?php
  $canEdit = $g_user->hasPermission('ChangeArticle');
  if ($canEdit)
  {
?>
        <input id="map_button_edit" type="submit" onClick="map_show_edit(); return false;" class="default-button" value="<?php putGS("Edit"); ?>" name="edit" />
<?php
  }
?>
        <input id="map_button_close" type="submit" onClick="map_preview_close(); return false;" class="default-button" value="<?php putGS("Close"); ?>" name="close" />
    </div>
    <div id="map_preview_info" class="map_preview_info">
      <?php putGS("Map preview"); ?>
    </div>
    <!-- end of map_save_part -->
  </div>
<!--END Toolbar-->
</div>
<!-- Map Preview Begin -->
<div class="geomap_container">
  <div class="geomap_locations">
    <?php echo Geo_Map::GetMapTagList($f_article_number, $f_language_id); ?>
  </div>
  <div class="geomap_menu">
    <a href="#" onClick="<?php echo Geo_Map::GetMapTagCenter($f_article_number, $f_language_id); ?> return false;"><? putGS("show initial map view"); ?></a>
  </div>
  <div class="geomap_map">
    <?php echo Geo_Map::GetMapTagBody($f_article_number, $f_language_id); ?>
  </div>
</div>
<div style="clear:both" ></div>
<!-- Map Preview End //-->
</body>
</html>
