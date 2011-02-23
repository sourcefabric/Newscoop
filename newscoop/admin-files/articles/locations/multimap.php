<?php
/**
 * @package
 */
require_once($GLOBALS['g_campsiteDir']."/classes/GeoPreferences.php");
require_once($GLOBALS['g_campsiteDir']."/classes/GeoMap.php");

require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/ComparisonOperation.php');

camp_load_translation_strings("api");
camp_load_translation_strings("geolocation");

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Expires" content="now" />
	<title><?php putGS("Map Preview"); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/admin_stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $Campsite['ADMIN_STYLE_URL']; ?>/map-preview.css" />

	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/base64.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/json2.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/jquery/jquery-1.4.2.min.js"></script>

	<script type="text/javascript">

var map_preview_close = function()
{
    try {window.close();} catch (e) {}
};


	</script>


<?php

$f_languageId = 1;

$f_users = array();
$f_users = array(5, 6);
//$f_users = array(0, 1, 2);

$f_articles = array();
$f_articles = array(34, 35, 61);
//$f_articles = array(34);

$f_issues = array();
$f_issues = array(2);
$f_sections = array();
$f_sections = array(10, 10, 60);
//$f_sections = array(10);
$f_dates = array();
$f_dates = array("2000-10-20", "2020-10-10");
$f_topics = array();
//$f_topics = array(23, 24, 58);
$f_topics = array(23, 49, 58);

$f_areas = array();
//$f_areas = array("rectangle" => array(array("longitude" => 150, "latitude" => -90), array("longitude" => -20, "latitude" => 60)));
$f_areas = array(
    array("rectangle" => array(array("longitude" => 150, "latitude" => -90), array("longitude" => -120, "latitude" => 60))),
    array("polygon" => array(array("longitude" => -179, "latitude" => -90), array("longitude" => -20, "latitude" => -90), array("longitude" => -20, "latitude" => 90), array("longitude" => -179, "latitude" => 90))),
    //array("polygon" => array(array("longitude" => 150, "latitude" => -90), array("longitude" => -120, "latitude" => -90), array("longitude" => -120, "latitude" => 60), array("longitude" => 150, "latitude" => 60))),
);
//implode(array("longitude" => 150, "latitude" => -90));
//echo implode("", $f_areas["rectangle"][0]);


$f_multimedia = array();
//$f_multimedia = array("image" => false, "video" => false, "any" => false);
//$f_multimedia = array("image" => true, "video" => false, "any" => false);
$f_multimedia = array("image" => false, "video" => false, "any" => true);

$f_mapWidth = 800;
$f_mapHeight = 600;

$limit = 100;
$offset = 0;

{
    $parameters = array();

if (2 == count($f_dates)) {
    $leftOperand = 'date';
    $rightOperand = $f_dates[0];
    $operator = new Operator('greater_equal', 'sql');
    $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
    $parameters[] = $constraint;

    $leftOperand = 'date';
    $rightOperand = $f_dates[1];
    $operator = new Operator('smaller_equal', 'sql');
    $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
    $parameters[] = $constraint;
}

    foreach ($f_users as $one_user) {
        $leftOperand = 'author';
        $rightOperand = $one_user;
        //$operator = new Operator('not', 'sql');
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($f_articles as $one_article) {
        $leftOperand = 'article';
        $rightOperand = $one_article;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($f_issues as $one_issue) {
        $leftOperand = 'issue';
        $rightOperand = $one_issue;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($f_sections as $one_section) {
        $leftOperand = 'section';
        $rightOperand = $one_section;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($f_areas as $one_area) {
        $leftOperand = 'area';
        $rightOperand = json_encode($one_area);
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    $leftOperand = 'matchanyarea';
    $rightOperand = true;
    $operator = new Operator('is', 'sql');
    $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
    $parameters[] = $constraint;

    foreach ($f_multimedia as $one_media_type => $one_media_state) {
        if (!$one_media_state) {continue;}
        $leftOperand = 'multimedia';
        $rightOperand = $one_media_type;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    foreach ($f_topics as $one_topic) {
        $leftOperand = 'topic';
        $rightOperand = $one_topic;
        $operator = new Operator('is', 'sql');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $parameters[] = $constraint;
    }

    $leftOperand = 'matchanytopic';
    $rightOperand = true;
    $operator = new Operator('is', 'sql');
    $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
    $parameters[] = $constraint;

}

echo Geo_Map::GetMultiMapTagHeader($f_languageId, $parameters, $offset, $limit, $f_mapWidth, $f_mapHeight);
?>
</head>
<body onLoad="return false;">
<div class="map_preview clearfix">
<!--Toolbar-->
<div id="map_toolbar_part" class="toolbar clearfix map_preview_toolbar">

    <div class="save-button-bar">
        <input id="map_button_close" type="submit" onClick="map_preview_close(); return false;" class="default-button" value="<?php putGS("Close"); ?>" name="close" />
    </div>
    <div id="map_preview_info" class="map_preview_info">
      <?php putGS("Map preview"); echo " - " . "Multimap"; ?>
    </div>
    <!-- end of map_save_part -->
  </div>
<!--END Toolbar-->
</div>
<!-- Map Preview Begin -->
<div class="geomap_container">
  <div class="geomap_locations">
    <?php echo Geo_Map::GetMultiMapTagList($f_languageId, $parameters, $offset, $limit); ?>
  </div>
  <div class="geomap_menu">
    <a href="#" onClick="<?php echo Geo_Map::GetMultiMapTagCenter($f_languageId); ?> return false;"><?php putGS("show initial map view"); ?></a>
  </div>
  <div class="geomap_map">
    <?php echo Geo_Map::GetMultiMapTagBody($f_languageId); ?>
  </div>
</div>
<div style="clear:both" ></div>
<!-- Map Preview End //-->
</body>
</html>
