<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/SystemPref.php';

/**
 * @package Campsite
 */
class Geo_Preferences extends DatabaseObject {

    /**
     * Gets the default available map provider
     *
     * @return string
     */
public static function GetMapProviderDefault()
{
    $map_prov_default = SystemPref::Get('MapProviderDefault');
    if (!$map_prov_default) {$map_prov_default = 'googlev3';}
    else {$map_prov_default = strtolower($map_prov_default);}

    $sys_pref_names = array('googlev3' => 'GoogleV3', 'osm' => 'OSM', 'mapquest' => 'MapQuest');

    $provider_available = true;

    $one_prov_usage = SystemPref::Get('MapProviderAvailable' . ucfirst($sys_pref_names[$map_prov_default]));
    if (!$one_prov_usage) {$provider_available = false;}
    if (in_array(strtolower($one_prov_usage), array('0', 'false', 'no'))) {$provider_available = false;}

    if (!$provider_available)
    {
        foreach ($sys_pref_names as $one_provider => $one_prov_name)
        {
            $one_prov_usage = SystemPref::Get('MapProviderAvailable' . ucfirst($sys_pref_names[$one_prov_name]));

            if (!$one_prov_usage) {continue;}
            if (in_array(strtolower($one_prov_usage), array('0', 'false', 'no'))) {continue;}

            $map_prov_default = $one_provider;
            $provider_available = true;
        }
    }

    if (!$provider_available)
    {
        $map_prov_default = 'googlev3';
    }

    return $map_prov_default;
} // fn GetMapProviderDefault

    /**
     * Gets info on map view
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetMapInfo($p_htmlDir = '', $p_websiteUrl = '', $p_mapProvider = '')
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}


    $map_width = SystemPref::Get('MapViewWidthDefault');
    if (!$map_width) {$map_width = 600;}
    $map_height = SystemPref::Get('MapViewHeightDefault');
    if (!$map_height) {$map_height = 400;}

    $map_view_long = SystemPref::Get('MapCenterLongitudeDefault');
    $map_view_lat = SystemPref::Get('MapCenterLatitudeDefault');
    $map_view_resol = SystemPref::Get('MapDisplayResolutionDefault');

    if (!$map_view_long) {$map_view_long = '14.424133';}
    if (!$map_view_lat) {$map_view_lat = '50.089926';}
    if (!$map_view_resol) {$map_view_resol = '4';}

    $use_single_provider = false; // whether we already know which single map provider has to be used
    if ('' != $p_mapProvider)
    {
        $use_single_provider = true;
    }

    $map_prov_default = '';
    if ($use_single_provider)
    {
        $map_prov_default = $p_mapProvider;
    }
    else
    {
        $map_prov_default = SystemPref::Get('MapProviderDefault');
        if (!$map_prov_default) {$map_prov_default = '';}
        else {$map_prov_default = strtolower($map_prov_default);}
    }

    // we only have support for googlev3 and osm/mapquest for now
    $map_prov_names_arr = array('googlev3', 'osm', 'mapquest');
    if ($use_single_provider)
    {
        $map_prov_names_arr = array($p_mapProvider);
    }

    $map_prov_includes = array();
    $map_prov_includes_async = array();
    $map_prov_info_arr = array();
    $map_prov_gv3_async = false;
    $map_prov_gv3_async_method = '';

    $known_providers = array('googlev3' => false, 'osm' => false);
    $sys_pref_names = array('googlev3' => 'GoogleV3', 'osm' => 'OSM', 'mapquest' => 'MapQuest');
    $usage_providers_count = 0;

    $map_prov_first = '';
    $map_prov_default_found = false;

    foreach ($map_prov_names_arr as $one_prov_name)
    {
        if ('' == $one_prov_name) {continue;}

        if (!$use_single_provider)
        {
            $one_prov_usage = SystemPref::Get('MapProviderAvailable' . ucfirst($sys_pref_names[$one_prov_name]));

            if (!$one_prov_usage) {continue;}
            if (in_array(strtolower($one_prov_usage), array('0', 'false', 'no'))) {continue;}
        }

        $one_prov_include = '';
        $one_prov_include_async = '';
        if ('googlev3' == $one_prov_name)
        {
            $one_prov_include = 'http://maps.google.com/maps/api/js?v=3.2&sensor=false';
            $one_prov_include_async = 'http://maps.google.com/maps/api/js?v=3.2&sensor=false&callback=initialize_gv3async';
            $map_prov_gv3_async_method = 'initialize_gv3async';
            $map_prov_gv3_async = true;
        }

        // up to now, we know how to deal with just a few map providers
        $one_prov_label = strtolower($one_prov_name);
        if (!in_array($one_prov_label, $map_prov_names_arr)) {continue;}

        $known_providers[$one_prov_label] = true;
        if ($one_prov_include && ('' != $one_prov_include))
        {
            $map_prov_includes[] = $one_prov_include;
        }
        if ($one_prov_include_async && ('' != $one_prov_include_async))
        {
            $map_prov_includes_async[] = $one_prov_include_async;
        }

        if ('' == $map_prov_first) {$map_prov_first = $one_prov_label;}
        if ($one_prov_label == $map_prov_default) {$map_prov_default_found = true;}

        $usage_providers_count += 1;
    }

    if (!$map_prov_default_found)
    {
        $map_prov_default = $map_prov_first;
    }

    // if nothing set to usage, use the all ones, with the default configuration
    if (0 == $usage_providers_count)
    {
        foreach ($known_providers as $one_prov => $one_state)
        {
            $known_providers[$one_prov] = true;
        }

        $map_prov_default = 'googlev3';
        $map_prov_includes[] = 'http://maps.google.com/maps/api/js?v=3.2&sensor=false';
        $map_prov_includes_async[] = 'http://maps.google.com/maps/api/js?v=3.2&sensor=false&callback=initialize_gv3async';
        $map_prov_gv3_async_method = 'initialize_gv3async';
        $map_prov_gv3_async = true;

    }

    $providers_usage_arr = array();
    foreach ($known_providers as $provider => $usage)
    {
        if ($usage) {$providers_usage_arr[] = $provider;}
    }

    $res_map_info = array();
    $res_map_info['default'] = $map_prov_default;
    $res_map_info['longitude'] = $map_view_long;
    $res_map_info['latitude'] = $map_view_lat;
    $res_map_info['resolution'] = $map_view_resol;
    $res_map_info['providers'] = $providers_usage_arr;
    $res_map_info['width'] = $map_width;
    $res_map_info['height'] = $map_height;

    return array('json_obj' => $res_map_info, 'incl_obj' => $map_prov_includes, 'incl_obj_async' => $map_prov_includes_async, 'incl_gv3' => $map_prov_gv3_async, 'incl_gv3_init' => $map_prov_gv3_async_method);
} // fn GetMapInfo


    /**
     * Prepares js script include tags
     *
     * @param array $p_inclInfo
     *
     * @return string
     */
public static function PrepareMapIncludes($p_inclInfo)
{
    $map_includes = '';
    foreach ($p_inclInfo as $js_source)
    {
        $map_includes .= "<script type=\"text/javascript\" src=\"$js_source\"></script>\n";
    }

    return $map_includes;
} // fn PrepareMapIncludes

    /**
     * Gets info on marker icons
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetIconsInfo($p_htmlDir, $p_websiteUrl)
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}

    $no_arr = array('json_obj' => array('webdir' => '', 'default' => '', 'icons' => array()));

    $use_icons = array();

    $icons_subpath = SystemPref::Get('MapMarkerDirectory');
    if (!$icons_subpath)
    {
        $icons_subpath = '/js/geocoding/markers';
    }

    $icons_subdir = $cnf_html_dir . $icons_subpath;

    $icons_webdir = $cnf_website_url . $icons_subpath;
    if (!is_dir($icons_subdir))
    {
        return $no_arr;
    }

    $icons_default_name = SystemPref::Get('MapMarkerSourceDefault');
    if (!$icons_default_name) {$icons_default_name = '';}

    $img_suffixes = array('png', 'gif', 'jpg', 'jpe', 'jpeg', 'svg', 'pbm', 'bmp', 'xpm', 'xbm', 'tif', 'tiff');
    $offset_x_names = array('offsetx', 'offset_x', 'offset x', 'width offset');
    $offset_y_names = array('offsety', 'offset_y', 'offset y', 'height offset');

    $icons_first_name = '';
    $icons_default_name_exists = false;

    $icons_arr_unknown = array();
    $icons_arr_unknown[] = 'special/unknown.png';

    $icons_arr = array_merge($icons_arr_unknown, scandir($icons_subdir));

    foreach ($icons_arr as $one_name)
    {
        $img_label_arr = explode('.', $one_name);
        if (2 != count($img_label_arr)) {continue;} // we expect that regular image files shall be icon_name.suffix
        $img_label = $img_label_arr[0] . ' (' . $img_label_arr[1] . ')';

        $web_path = $icons_webdir . '/' . $one_name;
        $one_path = $icons_subdir . '/' . $one_name;
        if (is_file($one_path))
        {
            $one_name_parts = explode('.', $one_path);
            $one_name_size = count($one_name_parts);
            if (2 > $one_name_size) {continue;}
            if (!in_array($one_name_parts[$one_name_size - 1], $img_suffixes)) {continue;}

            $img_info = getimagesize($one_path);
            if (!$img_info) {continue;}

            $img_width_value = 0 + $img_info[0];
            $img_height_value = 0 + $img_info[1];
            $img_width = '' . $img_info[0];
            $img_height = '' . $img_info[1];
            $img_width_off = '-' . floor($img_info[0] / 2);
            $img_height_off = '-' . ($img_info[1] - 0);

            $one_name_parts[$one_name_size - 1] = 'ini';
            $one_name_desc = implode('.', $one_name_parts);
            $one_file_desc = fopen($one_name_desc, 'r');
            if (!$one_file_desc)
            {
                $one_name_parts[$one_name_size - 1] = 'txt';
                $one_name_desc = implode('.', $one_name_parts);
                $one_file_desc = fopen($one_name_desc, 'r');
            }

            if ($one_file_desc)
            {
                while (true)
                {
                    $one_img_info = fgets($one_file_desc);
                    if (!$one_img_info) {break;}

                    $one_img_info = trim($one_img_info);
                    if (0 == strlen($one_img_info)) {continue;}
                    if ('#' == $one_img_info[0]) {continue;}

                    $info_arr = explode(':', $one_img_info);
                    if (2 > count($info_arr))
                    {
                        $info_arr = explode(',', $one_img_info);
                    }
                    if (2 > count($info_arr)) {continue;}

                    $one_param = strtolower(trim($info_arr[0]));
                    $one_value = trim($info_arr[1]);

                    if (in_array($one_param, $offset_x_names))
                    {
                        if (is_numeric($one_value)) {$img_width_off = '' . ($one_value - $img_width_value);}
                    }
                    if (in_array($one_param, $offset_y_names))
                    {
                        if (is_numeric($one_value)) {$img_height_off = '' . ($one_value - $img_height_value);}
                    }

                }

                fclose($one_file_desc);
            }

            $use_icons[] = array('label' => $img_label, 'name' => $one_name, 'path' => $web_path, 'width_off' => $img_width_off, 'height_off' => $img_height_off, 'width' => $img_width, 'height' => $img_height);

            if ('' == $icons_first_name) {$icons_first_name = $one_name;}

            if ($one_name == $icons_default_name) {$icons_default_name_exists = true;}
        }
    }

    if (!$icons_default_name_exists)
    {
        if ('' != $icons_first_name) {$icons_default_name = $icons_first_name;}
    }

    $res_icons_info = array('webdir' => $icons_webdir, 'default' => $icons_default_name, 'icons' => $use_icons);
    return array('json_obj' => $res_icons_info);
} // fn GetIconsInfo


    /**
     * Gets info on the search map
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return string
     */
public static function GetSearchInfo($p_htmlDir, $p_websiteUrl)
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}


    $no_arr = array('json_obj' => array('webdir' => '', 'default' => '', 'icons' => array()));

    $use_icons = array();

    $icons_subpath = '/js/geocoding/search';
    $icon_filename = 'search.png';

    $icons_subdir = $cnf_html_dir . $icons_subpath;

    $icons_webdir = $cnf_website_url . $icons_subpath;

    $icons_default_name = 'search';
    $search_icon = array(
        'label' => 'search',
        'name' => $icon_filename,
        'path' => $icons_webdir . '/' . $icon_filename,
        'width' => '200',
        'height' => '150',
        'width_off' => '-100',
        'height_off' => '-75',
    );

    $use_icons[] = $search_icon;

    $res_icons_info = array('webdir' => $icons_webdir, 'default' => $icons_default_name, 'icons' => $use_icons);
    return array('json_obj' => $res_icons_info);
} // fn GetSearchInfo


    /**
     * Gets info on popups
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetPopupsInfo($p_htmlDir, $p_websiteUrl)
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}

    $popup_width = SystemPref::Get('MapPopupWidthMin');
    if (!$popup_width) {$popup_width = 300;}

    $popup_height = SystemPref::Get('MapPopupHeightMin');
    if (!$popup_height) {$popup_height = 200;}

    $size_info = array('width' => $popup_width, 'height' => $popup_height);

    $video_names_arr = array('YouTube', 'Vimeo', 'Flash');
    $video_names_info = array();

    $video_names_info['YouTube'] = array('width' => '320', 'height' => '240');
    $video_names_info['Vimeo'] = array('width' => '320', 'height' => '180');
    $video_names_info['Flash'] = array('width' => '320', 'height' => '240');

    foreach ($video_names_arr as $one_video_label)
    {
        if ('' == $one_video_label) {continue;}

        $video_width = SystemPref::Get('MapVideoWidth' . ucfirst($one_video_label));
        $video_height = SystemPref::Get('MapVideoHeight' . ucfirst($one_video_label));

        if ((!$video_width) && ('' == $video_width)) {continue;}
        if ((!$video_height) && ('' == $video_height)) {continue;}

        $video_names_info[$one_video_label]['width'] = $video_width;
        $video_names_info[$one_video_label]['height'] = $video_height;

    }

    $youtube_src_default = '<object width="%%w%%" height="%%h%%"><param name="movie" value="http://www.youtube.com/v/%%id%%"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/%%id%%" type="application/x-shockwave-flash" wmode="transparent" width="%%w%%" height="%%h%%"></embed></object>';
    $vimeo_src_default = '<object width="%%w%%" height="%%h%%"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" /><embed src="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="%%w%%" height="%%h%%"></object>';

    $flash_src_default = '<object width="%%w%%" height="%%h%%"><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="%%path%%%%id%%"/><embed src="%%path%%%%id%%" width="%%w%%" height="%%h%%" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"/></object>';
    $flv_src_default = '<object width="%%w%%" height="%%h%%"><param name="movie" value="%%path%%player.swf"></param><param name="flashvars" value="src=%%path%%%%id%%&amp;poster=%%path%%%%ps%%&amp;controlBarAutoHide=true"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="%%path%%player.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%w%%" height="%%h%%" flashvars="src=%%path%%%%id%%&amp;poster=%%path%%%%ps%%&amp;controlBarAutoHide=true"></embed></object>';

    $flash_server = '';
    $flash_directory = '';
    {
        $flash_server_setting = SystemPref::Get('FlashServer');
        $flash_directory_setting = SystemPref::Get('FlashDirectory');

        // if not a flash server set, use the cs server
        if ((!$flash_server_setting) || ('' == $flash_server_setting))
        {
            $flash_server = $cnf_website_url;
            if ('/' != strrchr($flash_server, '/'))
            {
                $flash_server .= '/';
            }
        }
        else // use the set server for the flash files
        {
            $flash_server = $flash_server_setting;
        }

        // if not flash directory set, just assure that the server/dir ends with a '/'
        if ((!$flash_directory_setting) || ('' == $flash_directory_setting))
        {
            if ('/' == strrchr($flash_server, '/'))
            {
                $flash_directory = '';
            }
            else
            {
                $flash_directory = '/';
            }
        }
        else
        {
            $flash_directory = $flash_directory_setting;
        }

        $flash_path = $flash_server. $flash_directory;
        if ('/' != strrchr($flash_path, '/'))
        {
            $flash_path .= '/';
        }

        $cur_info = $video_names_info['YouTube'];
        $video_names_usage[] = array('label' => 'YouTube', 'source' => $youtube_src_default, 'width' => $cur_info['width'], 'height' => $cur_info['height']);
        $cur_info = $video_names_info['Vimeo'];
        $video_names_usage[] = array('label' => 'Vimeo', 'source' => $vimeo_src_default, 'width' => $cur_info['width'], 'height' => $cur_info['height']);
        $cur_info = $video_names_info['Flash'];

        $video_names_usage[] = array('label' => 'Flash', 'source' => $flash_src_default, 'width' => $cur_info['width'], 'height' => $cur_info['height'], 'path' => $flash_path);
        $cur_info = $video_names_info['Flash'];

        $video_names_usage[] = array('label' => 'Flv', 'source' => $flv_src_default, 'width' => $cur_info['width'], 'height' => $cur_info['height'], 'path' => $flash_path);
    }

    $video_info = array('labels' => $video_names_usage);

    $res_popups_info = array('width' => $size_info['width'], 'height' => $size_info['height'], 'video' => $video_info);
    return array('json_obj' => $res_popups_info);
} // fn GetPopupsInfo

    /**
     * Gets files available as marker icons
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetIconsFiles($p_htmlDir = '', $p_websiteUrl = '')
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}

    $no_arr = array();

    $use_icons = array();

    $icons_subpath = SystemPref::Get('MapMarkerDirectory');
    if (!$icons_subpath)
    {
        $icons_subpath = '/js/geocoding/markers';
    }

    $icons_subdir = $cnf_html_dir . $icons_subpath;

    $icons_webdir = $cnf_website_url . $icons_subpath;
    if (!is_dir($icons_subdir))
    {
        return $no_arr;
    }

    $img_suffixes = array('png', 'gif', 'jpg', 'jpe', 'jpeg', 'svg', 'pbm', 'bmp', 'xpm', 'xbm', 'tif', 'tiff');

    $icons_arr = scandir($icons_subdir);

    foreach ($icons_arr as $one_name)
    {
        $img_label_arr = explode('.', $one_name);
        if (2 != count($img_label_arr)) {continue;} // we expect that regular image files shall be icon_name.suffix
        $img_label = $img_label_arr[0] . ' (' . $img_label_arr[1] . ')';

        $web_path = $icons_webdir . '/' . $one_name;
        $one_path = $icons_subdir . '/' . $one_name;
        if (is_file($one_path))
        {
            $one_name_parts = explode('.', $one_path);
            $one_name_size = count($one_name_parts);
            if (2 > $one_name_size) {continue;}
            if (!in_array($one_name_parts[$one_name_size - 1], $img_suffixes)) {continue;}

            $img_info = getimagesize($one_path);
            if (!$img_info) {continue;}

            $use_icons[] = $one_name;

        }
    }

    return $use_icons;

} // fn GetIconsFiles

    /**
     * Gets info on map auto-focusing
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetFocusInfo($p_htmlDir = '', $p_websiteUrl = '')
{
    $focus_default = SystemPref::Get('MapAutoFocusDefault');
    if (!$focus_default) {$focus_default = false;}
    else {$focus_default = true;}

    $focus_maxzoom = SystemPref::Get('MapAutoFocusMaxZoom');
    if (!$focus_maxzoom) {$focus_maxzoom = 18;}

    $focus_border = SystemPref::Get('MapAutoFocusBorder');
    if (!$focus_border) {$focus_border = 100;}

    $res_focus_info = array('auto_focus' => $focus_default, 'max_zoom' => $focus_maxzoom, 'border' => $focus_border);
    return array('json_obj' => $res_focus_info);
} // fn GetFocusInfo


    /**
     * Gets CSS file(s) to be included for map (pre)view
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return array
     */
public static function GetIncludeCSS($p_htmlDir = '', $p_websiteUrl = '')
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}

    $css_files = array();

    $css_map_file = SystemPref::Get('MapAutoCSSFile');
    if ($css_map_file)
    {
        $css_map_file = '' . $css_map_file;
        if (0 < strlen($css_map_file))
        {
            if (0 != strpos($css_map_file, '/')) {$css_map_file = '/' . $css_map_file;}
            $css_files[] = $p_websiteUrl . $css_map_file;
        }
    }

    return array('css_files' => $css_files);
} // fn GetIncludeCSS


    /**
     * Gets icons directory for map (pre)view
     *
     * @param string $p_htmlDir
     * @param string $p_websiteUrl
     *
     * @return string
     */
public static function GetIconsWebDir($p_htmlDir = '', $p_websiteUrl = '')
{
    global $Campsite;
    $cnf_html_dir = $Campsite['HTML_DIR'];
    $cnf_website_url = $Campsite['WEBSITE_URL'];

    if ('' != $p_htmlDir) {$cnf_html_dir = $p_htmlDir;}
    if ('' != $p_websiteUrl) {$cnf_website_url = $p_websiteUrl;}

    $icons_subpath = SystemPref::Get('MapMarkerDirectory');
    if (!$icons_subpath)
    {
        $icons_subpath = '/javascript/geocoding/markers';
    }

    $icons_webdir = $cnf_website_url . $icons_subpath;
    return $icons_webdir;

} // GetIconsWebDir

    /**
     * Gets translated strings for the geo-map templates
     * This is used for having translated those strings, even when they are not used via this function
     * Look at include/smarty/plugins/function.math.php for the usage
     *
     * @return array
     */
public static function TemplateGeoStrings()
{
    $show_original_map = getGS('Show original map');
    $open_large_map = getGS('Open large map');
    $map = getGS('Map');
    $center = getGS('Center');

    return array('show_original_map' => $show_original_map, 'open_large_map' => $open_large_map, 'map' => $map, 'center' => $center);

} // fn TemplateGeoStrings

} // class Geo_Preferences

?>
