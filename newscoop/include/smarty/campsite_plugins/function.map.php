<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

require_once($GLOBALS['g_campsiteDir'] . '/admin-files/localizer/Localizer.php');
require_once($GLOBALS['g_campsiteDir'] . '/admin-files/lib_campsite.php');

/**
 * Campsite Map function plugin
 *
 * Type:     function
 * Name:     map
 * Purpose:  Render a geo map
 *
 * @param array
 *     $p_params List of parameters from template
 * @param object
 *     $p_smarty Smarty template object
 *
 * @return
 *     string The html content
 */
function smarty_function_map($p_params, &$p_smarty)
{
    // the strings are translated via Geo_Preferences::TemplateGeoStrings()
    // if you change some of the strings, put them there too
    camp_load_translation_strings('api');

    // Default text for the reset link
    define('DEFAULT_RESET_TEXT', getGS('Show original map'));

    // get the context variable
    $campsite = $p_smarty->get_template_vars('gimme');
    $html = '';

    $map_common_header_set = $campsite->map_common_header_set;
    $map_load_common_header = !$map_common_header_set;

    // get show locations list parameter
    $showLocationsList = FALSE;
    if (isset($p_params['show_locations_list'])) {
        if (is_string($p_params['show_locations_list'])) {
            if (strtolower(trim((string) $p_params['show_locations_list'])) == 'true') {
                $showLocationsList = TRUE;
            }
        }
        if (is_bool($p_params['show_locations_list'])) {
            if ($p_params['show_locations_list']) {
                $showLocationsList = TRUE;
            }
        }
    }

    // get show reset link parameter
    $showResetLink = TRUE;
    $resetLinkText = DEFAULT_RESET_TEXT;
    if (isset($p_params['show_reset_link'])) {
        $resetLinkText = trim((string) $p_params['show_reset_link']);
        if (strtolower($resetLinkText) == 'false') {
            $showResetLink = FALSE;
        }
    }
    if (strtolower($resetLinkText) == 'true') {
            $resetLinkText = DEFAULT_RESET_TEXT;
    }

    // get map width and height
    $width = isset($p_params['width']) ? (int) $p_params['width'] : 0;
    $height = isset($p_params['height']) ? (int) $p_params['height'] : 0;

    // if we shall display a multi-map
    if ((!is_null($campsite->map_dynamic_points_raw)) || (!is_null($campsite->map_dynamic_constraints))) {
        // language must be set in context
        if (!$campsite->language->defined) {
            return;
        }

        $offset = 0;
        $limit = $campsite->map_dynamic_max_points;
        if (!$limit) {$limit = 2000;}

        $multi_map_rank = $campsite->map_dynamic_id_counter;

        $map_language = (int) $campsite->language->number;

        $multi_map_part = "<!-- Begin Map //-->\n";
        $multi_map_body = "";
        $multi_map_header = "";
        $multi_poi_list = "";

        $multi_map_points = $campsite->map_dynamic_points_raw;
        $multi_map_label = $campsite->map_dynamic_map_label;

        $multi_options = array();
        $multi_options["load_common"] = $map_load_common_header;
        $multi_options["pois_retrieved"] = false;

        if ($multi_map_points) {
            $multi_options["pois_retrieved"] = true;

            $multi_map_header = Geo_Map::GetMultiMapTagHeader($map_language, $multi_map_points, $multi_options, $offset, $limit, $width, $height, $multi_map_rank);
            $multi_poi_list = Geo_Map::GetMultiMapTagList($map_language, $multi_map_points, $multi_options, $multi_map_label, $offset, $limit, $multi_map_rank);
        }
        else {
            $multi_map_constraints = $campsite->map_dynamic_constraints;
            $multi_map_header = Geo_Map::GetMultiMapTagHeader($map_language, $multi_map_constraints, $multi_options, $offset, $limit, $width, $height, $multi_map_rank);
            $multi_poi_list = Geo_Map::GetMultiMapTagList($map_language, $multi_map_constraints, $multi_options, $multi_map_label, $offset, $limit, $multi_map_rank);
        }

        $multi_map_center = Geo_Map::GetMultiMapTagCenter($map_language, $multi_map_rank);
        $multi_map_div = Geo_Map::GetMultiMapTagBody($map_language, $multi_map_rank);

        $multi_map_body .= '
        <div class="geomap_container">';

        if ($showLocationsList == TRUE) {
            $multi_map_body .= '
                <div class="geomap_locations">
                    ' . $multi_poi_list . '
                </div>';
        }
        if ($showResetLink == TRUE) {
            $multi_map_body .= '
                <div class="geomap_menu">
                    <a href="#" onClick="' . $multi_map_center . 'return false;">' . camp_javascriptspecialchars($resetLinkText) . '</a>
                </div>';
        }
        $multi_map_body .= '
            <div class="geomap_map">
                ' . $multi_map_div . '
            </div>
        </div>
        <div style="clear:both" ></div>
        <!--End Map //-->
';

        $multi_map_part .= $multi_map_header . $multi_map_body;

        $campsite->map_common_header_set = true;
        return $multi_map_part;
    }
    // the end of the multi-map part; the article map is processed below


    // language and article must be set in context
    if (!$campsite->language->defined || !$campsite->article->defined) {
        return;
    }

    // do nothing if article does not have a map
    if ($campsite->article->has_map == FALSE) {
        return;
    }

    // get article and language from context
    $article = (int) $campsite->article->number;
    $language = (int) $campsite->language->number;

    $auto_focus = isset($p_params['auto_focus']) ? (bool) $p_params['auto_focus'] : null;

    // get core pieces to display the map
    $map_options = array();
    $map_options["auto_focus"] = $auto_focus;
    $map_options["load_common"] = $map_load_common_header;
    $mapHeader = MetaMap::GetMapTagHeader($article, $language, $width, $height, $map_options);
    $mapMain = MetaMap::GetMapTagBody($article, $language);

    // build javascript code
    $html = '
        <!-- Begin Map //-->'
        . $mapHeader . '
        <div class="geomap_container">';
    if ($showLocationsList == TRUE) {
        $local = array('map' => getGS('Map'), 'center' => getGS('Center'));
        $mapLocationsList = MetaMap::GetMapTagList($article, $language, $local);
        $html .= '
            <div class="geomap_locations">'
            . $mapLocationsList . '
            </div>';
    }
    if ($showResetLink == TRUE) {
        $mapResetLink = MetaMap::GetMapTagCenter($article, $language);
        $html .= '
            <div class="geomap_menu">
                <a href="#" onClick="' . $mapResetLink . '
                 return false;">' . camp_javascriptspecialchars($resetLinkText) . '</a>
            </div>';
    }
    $html .= '
            <div class="geomap_map">'
            . $mapMain . '
            </div>
        </div>
        <div style="clear:both" ></div>
        <!--End Map //-->
    ';

    $campsite->map_common_header_set = true;
    return $html;
} // fn smarty_function_map

?>
