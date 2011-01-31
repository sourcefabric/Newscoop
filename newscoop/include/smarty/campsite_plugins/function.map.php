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
    $auto_focus = isset($p_params['auto_focus']) ? (bool) $p_params['auto_focus'] : false;

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
