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

    // get show locations list parameter
    $showLocationsList = FALSE;
    if (isset($p_params['show_locations_list'])
            && (strtolower(trim((string) $p_params['show_locations_list'])) == 'true')) {
        $showLocationsList = TRUE;
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

    //var_dump($campsite->map_dynamic);
    if (!is_null($campsite->map_dynamic)) {
    // language must be set in context
        if (!$campsite->language->defined) {
            return;
        }

/* -- testing --
        $run_article = ($campsite->article->defined) ? $campsite->article : null;
        $run_language = $campsite->language;
        //var_dump($campsite->language->code);

        if ($run_article && $run_language) {
            $run_authors = $run_article->authors;
            foreach ($run_authors as $one_author) {
                $con_authors[] = $one_author->name;
            }
            echo json_encode($con_authors);
            $run_issue = $run_article->issue;
            var_dump($run_issue->number);
            $run_section = $run_article->section;
            var_dump($run_section->number);

            $run_topics = $run_article->topics;
            //var_dump($run_topics);
            foreach ($run_topics as $art_topic) {
                $con_topics[] = $art_topic . ":" . $run_language->code;
            }
            //echo json_encode($con_topics);
            //var_dump(TopicName::BuildTopicIdsQuery(array("Local News:en", "Hot Properties:en")));
            var_dump(TopicName::BuildTopicIdsQuery($con_topics));

        }
*/

        $map_part = "<!-- Begin Map //-->\n";
        $map_body = "";

        //var_dump($campsite->map_dynamic);
        //return json_encode($campsite->map_dynamic);

        $offset = 0;
        $limit = 200;
        //$map_width = 300;
        //$map_height = 400;

        $map_language = (int) $campsite->language->number;

        $map_constraints = $campsite->map_dynamic;

/*
        $leftOperand = 'as_array';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $map_constraints[] = $constraint;

        $leftOperand = 'preview';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $map_constraints[] = $constraint;

        $leftOperand = 'text_only';
        $rightOperand = false;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $map_constraints[] = $constraint;

        $leftOperand = 'language';
        $rightOperand = $map_language;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $map_constraints[] = $constraint;

        $leftOperand = 'constrained';
        $rightOperand = true;
        $operator = new Operator('is', 'php');
        $constraint = new ComparisonOperation($leftOperand, $operator, $rightOperand);
        $map_constraints[] = $constraint;

        $poi_count = 0;
        $poi_array = Geo_MapLocation::GetListExt($map_constraints, array(), 0, 200, $poi_count);
        $poi_array["retrieved"] = true;
*/

        //$map_header = Geo_Map::GetMultiMapTagHeader($map_language, $poi_array, $offset, $limit, $width, $height);
        //$poi_list = Geo_Map::GetMultiMapTagList($map_language, $poi_array, $offset, $limit);
        $map_header = Geo_Map::GetMultiMapTagHeader($map_language, $map_constraints, $offset, $limit, $width, $height);
        $poi_list = Geo_Map::GetMultiMapTagList($map_language, $map_constraints, $offset, $limit);

        //$map_div = Geo_Map::GetMultiMapTagBody($language);

        $map_body .= '
        <div class="geomap_container">';
    if ($showLocationsList == TRUE) {
        $map_body .= '
            <div class="geomap_locations">
                ' . $poi_list . '
            </div>';
    }
    if ($showResetLink == TRUE) {
        $map_body .= '
            <div class="geomap_menu">
                <a href="#" onClick="' . Geo_Map::GetMultiMapTagCenter($map_language) . 'return false;">' . camp_javascriptspecialchars($resetLinkText) . '</a>
            </div>';
    }
        $map_body .= '
            <div class="geomap_map">
                ' . Geo_Map::GetMultiMapTagBody($map_language) . '
            </div>
        </div>
        <div style="clear:both" ></div>
        <!--End Map //-->
';

        $map_part .= $map_header . $map_body;

        //var_dump(htmlspecialchars($map_part));

        return $map_part;
        //return "multi-map test";
    }

    //return "";

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
    $mapHeader = MetaMap::GetMapTagHeader($article, $language, $width, $height, $auto_focus);
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

    return $html;
}

?>
