<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");

require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/SQLSelectClause.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
class Geo_Preferences extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = '';
	var $m_columnNames = array('id');

	/**
	 * The geo locations class is auxiliar class for e.g. preferences access on geo things.
	 */
	public function Geo_Preferences()
	{
	} // constructor


	/**
	 * Gets info on map view
	 *
	 * @param string $p_htmlDir
	 * @param string $p_websiteUrl
	 *
	 * @return array
	 */
public static function GetMapInfo($p_htmlDir, $p_websiteUrl, $p_mapProvider = "")
{
    $map_width = SystemPref::Get("MapViewWidthDefault");
    if (!$map_width) {$map_width = 600;}
    $map_height = SystemPref::Get("MapViewHeightDefault");
    if (!$map_height) {$map_height = 400;}

    $map_view_long = SystemPref::Get("MapCenterLongitudeDefault");
    $map_view_lat = SystemPref::Get("MapCenterLatitudeDefault");
    $map_view_resol = SystemPref::Get("MapDisplayResolutionDefault");
    //echo "map_view_long: $map_view_long\n";

    if (!$map_view_long) {$map_view_long = "14.424133";}
    if (!$map_view_lat) {$map_view_lat = "50.089926";}
    if (!$map_view_resol) {$map_view_resol = "4";}

    //echo "mp: '$p_mapProvider'\n";
    $use_single_provider = false; // whether we already know which single map provider has to be used
    if ("" != $p_mapProvider)
    {
        $use_single_provider = true;
    }

    $map_prov_default = "";
    if ($use_single_provider)
    {
        $map_prov_default = $p_mapProvider;
    }
    else
    {
        $map_prov_default = SystemPref::Get("MapProviderDefault");
        if (!$map_prov_default) {$map_prov_default = "";}
        else {$map_prov_default = strtolower($map_prov_default);}
    }

    // we only have support for googlev3 and osm for now
    //$map_prov_names_str = SystemPref::Get("MapProviderNames");
    //$map_prov_names_arr = explode(",", $map_prov_names_str);

    $map_prov_names_arr = array("googlev3", "osm");
    if ($use_single_provider)
    {
        $map_prov_names_arr = array($p_mapProvider);
    }

    $map_prov_includes = array();
    $map_prov_info_arr = array();

    $known_providers = array("googlev3" => false, "osm" => false);
    $sys_pref_names = array("googlev3" => "GoogleV3", false, "osm" => "OSM");
    $usage_providers_count = 0;

    $map_prov_first = "";
    $map_prov_default_found = false;
    //print_r($map_prov_names_arr);
    foreach ($map_prov_names_arr as $one_prov_name)
    {
        if ("" == $one_prov_name) {continue;}

        //$one_prov_usage = "true";
        if (!$use_single_provider)
        {
            //if ("googlev3" == $one_prov_name) {continue;}
            //echo "MapProviderAvailable" . ucfirst($sys_pref_names[$one_prov_name]);
            $one_prov_usage = SystemPref::Get("MapProviderAvailable" . ucfirst($sys_pref_names[$one_prov_name]));
            //echo " opu: $one_prov_usage\n ";
            if (!$one_prov_usage) {continue;}
            if (in_array(strtolower($one_prov_usage), array("0", "false", "no"))) {continue;}
        }

        //$one_prov_include = SystemPref::Get("MapProviderInclude" . ucfirst($one_prov_name));
        $one_prov_include = "";
        if ("googlev3" == $one_prov_name)
        {
            $one_prov_include = "http://maps.google.com/maps/api/js?sensor=false";
        }

        // up to now, we know how to deal with just a few map providers
        $one_prov_label = strtolower($one_prov_name);
        //print_r($map_prov_names_arr);
        //echo "opl: '$one_prov_label'";
        //if (!array_key_exists($one_prov_label, $map_prov_names_arr)) {continue;}
        if (!in_array($one_prov_label, $map_prov_names_arr)) {continue;}

        $known_providers[$one_prov_label] = true;
        if ($one_prov_include && ("" != $one_prov_include))
        {
            $map_prov_includes[] = $one_prov_include;
        }

        if ("" == $map_prov_first) {$map_prov_first = $one_prov_label;}
        //echo "eq: $one_prov_label == $map_prov_default\n";
        if ($one_prov_label == $map_prov_default) {$map_prov_default_found = true;}

        $usage_providers_count += 1;
    }
    //echo " usp: $usage_providers_count\n";

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

        $map_prov_default = "googlev3";
        $map_prov_includes[] = "http://maps.google.com/maps/api/js?sensor=false";

    }

    //$cen_ini = array("long" => $map_view_long, "lat" => $map_view_lat, "res" => $map_view_resol);

    $providers_usage_arr = array();
    foreach ($known_providers as $provider => $usage)
    {
        if ($usage) {$providers_usage_arr[] = $provider;}
    }

    $res_map_info = array();
    $res_map_info["default"] = $map_prov_default;
    $res_map_info["longitude"] = $map_view_long;
    $res_map_info["latitude"] = $map_view_lat;
    $res_map_info["resolution"] = $map_view_resol;
    $res_map_info["providers"] = $providers_usage_arr;
    $res_map_info["width"] = $map_width;
    $res_map_info["height"] = $map_height;

    return array("json_obj" => $res_map_info, "incl_obj" => $map_prov_includes);
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
    $map_includes = "";
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
    $no_arr = array("json_obj" => array('webdir' => "", 'default' => "", 'icons' => array()));

    $use_icons = array();

    $icons_subpath = SystemPref::Get("MapMarkerDirectory");
    if (!$icons_subpath)
    {
        $icons_subpath = "/javascript/geocoding/markers";
    }

    $icons_subdir = $p_htmlDir . $icons_subpath;
    //echo $icons_subdir;
    $icons_webdir = $p_websiteUrl . $icons_subpath;
    if (!is_dir($icons_subdir))
    {
        return $no_arr;
    }

    //$icons_default_label = SystemPref::Set("MapMarkerSourceDefault", 'blue');
    $icons_default_label = SystemPref::Get("MapMarkerSourceDefault");
    //echo "\nmmsd: $icons_default_label<br />\n";
    if (!$icons_default_label) {$icons_default_label = "";}
    $icons_default_name = "";

    $sys_icons_arr = array();
    $sys_icons_labels_str = SystemPref::Get("MapMarkerNames");
    if ($sys_icons_labels_str)
    {
        $sys_icons_labels_arr = explode(",", $sys_icons_labels_str);
        foreach ($sys_icons_labels_arr as $one_icon_pref_label)
        {
            if ("" == $one_icon_pref_label) {continue;}

            $one_icon_pref_source = SystemPref::Get("MapMarkerSource" . ucfirst($one_icon_pref_label));
            if (!$one_icon_pref_source)
            {
                continue;
            }
            $one_icon_pref_offx = SystemPref::Get("MapMarkerOffsetX" . ucfirst($one_icon_pref_label));
            $one_icon_pref_offy = SystemPref::Get("MapMarkerOffsetY" . ucfirst($one_icon_pref_label));
            //echo "$one_icon_pref_offx";
            $sys_icons_arr[$one_icon_pref_source] = array('label' => $one_icon_pref_label, 'offx' => $one_icon_pref_offx, 'offy' => $one_icon_pref_offy);

            if ($one_icon_pref_label == $icons_default_label)
            {
                $icons_default_name = $one_icon_pref_source;
            }
        }
    }

    $icons_first_name = "";
    $icons_default_name_exists = false;

    $icons_arr = scandir($icons_subdir);
    //print_r($sys_icons_arr);
    foreach ($icons_arr as $one_name)
    {
        $img_label_arr = explode(".", $one_name);
        if (2 != count($img_label_arr)) {continue;} // we expect that regyukar image files shall be icon_name.suffix
        $img_label = $img_label_arr[0] . " (" . $img_label_arr[1] . ")";

        $web_path = $icons_webdir . "/" . $one_name;
        $one_path = $icons_subdir . "/" . $one_name;
        if (is_file($one_path))
        {
            $img_info = getimagesize($one_path);
            //echo "$one_path<br />\n";
            //echo "$web_path<br />\n";
            if (!$img_info) {continue;}

            $img_width = "" . $img_info[0];
            $img_height = "" . $img_info[1];
            $img_width_off = "-" . floor($img_info[0] / 2);
            $img_height_off = "-" . ($img_info[1] - 0); // the "-5" is done so that the default icons fit
            //echo "$img_width_off x $img_height_off<br />\n";

            //$img_label = $one_name;
            //echo $one_name . " \n";
            if (array_key_exists($one_name, $sys_icons_arr))
            {
                $one_use_pref = $sys_icons_arr[$one_name];
                if ($one_use_pref['label']) {$img_label = $one_use_pref['label'];}
                if ($one_use_pref['offx']) {$img_width_off = $one_use_pref['offx'];}
                if ($one_use_pref['offy']) {$img_height_off = $one_use_pref['offy'];}
            }

            //array_push($use_icons, array('label' => $img_label, 'path' => $web_path, 'width_off' => $img_width_off, 'height_off' => $img_height_off));
            $use_icons[] = array('label' => $img_label, 'name' => $one_name, 'path' => $web_path, 'width_off' => $img_width_off, 'height_off' => $img_height_off, 'width' => $img_width, 'height' => $img_height);

            if ("" == $icons_first_name) {$icons_first_name = $one_name;}
            if ($one_name == $icons_default_name) {$icons_default_name_exists = true;}
        }
    }

    if (!$icons_default_name_exists)
    {
        if ("" != $icons_first_name) {$icons_default_name = $icons_first_name;}
    }

    $res_icons_info = array('webdir' => $icons_webdir, 'default' => $icons_default_name, 'icons' => $use_icons);
    return array("json_obj" => $res_icons_info);
} // fn GetIconsInfo


public static function GetSearchInfo($p_htmlDir, $p_websiteUrl)
{
    $no_arr = array("json_obj" => array('webdir' => "", 'default' => "", 'icons' => array()));

    $use_icons = array();

    $icons_subpath = "/javascript/geocoding/search";
    $icon_filename = "search.png";

    $icons_subdir = $p_htmlDir . $icons_subpath;
    //echo $icons_subdir;
    $icons_webdir = $p_websiteUrl . $icons_subpath;

    $icons_default_name = "search";
    $search_icon = array(
        "label" => "search",
        "name" => $icon_filename,
        "path" => $icons_webdir . "/" . $icon_filename,
        "width" => "200",
        "height" => "150",
        "width_off" => "-100",
        "height_off" => "-75",
    );

    $use_icons[] = $search_icon;

    $res_icons_info = array('webdir' => $icons_webdir, 'default' => $icons_default_name, 'icons' => $use_icons);
    return array("json_obj" => $res_icons_info);
} // fn GetIconsInfo


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
    //$popup_width = SystemPref::Get("MapPopupWidthDefault");
    $popup_width = SystemPref::Get("MapPopupWidthMin");
    if (!$popup_width) {$popup_width = 300;}
    //$popup_height = SystemPref::Get("MapPopupHeightDefault");
    $popup_height = SystemPref::Get("MapPopupHeightMin");
    if (!$popup_height) {$popup_height = 200;}

    $size_info = array("width" => $popup_width, "height" => $popup_height);

    //$video_default = SystemPref::Get("MapVideoDefault");
    //if (!$video_default) {$video_default = "YouTube";}

    //$video_names_str = SystemPref::Get("MapVideoNames");
    //if (!$video_names_str) {$video_names_str = "";}

    //$video_names_arr = explode(",", $video_names_str);
    $video_names_arr = array("YouTube", "Vimeo", "Flash", "Flv");
    $video_names_info = array();
    $video_names_info["YouTube"] = array("width" => '425', "height" => '350');
    $video_names_info["Vimeo"] = array("width" => '400', "height" => '225');
    $video_names_info["Flash"] = array("width" => '300', "height" => '200');
    $video_names_info["Flv"] = array("width" => '300', "height" => '280');

    //$video_name_first = "";
    //$video_default_found = false;
    foreach ($video_names_arr as $one_video_label)
    {
        if ("" == $one_video_label) {continue;}

        //$video_avail = SystemPref::Get("MapVideoAvailable_" . $one_video_label);
        //if (!$video_avail) {continue;}
        //if (in_array(strtolower($video_avail), array("0", "false", "no"))) {continue;}

        //$video_source = SystemPref::Get("MapVideoSource" . ucfirst($one_video_label));
        $video_width = SystemPref::Get("MapVideoWidth" . ucfirst($one_video_label));
        $video_height = SystemPref::Get("MapVideoHeight" . ucfirst($one_video_label));
        //if (!$video_source) {continue;}
        if ((!$video_width) && ("" == $video_width)) {continue;}
        if ((!$video_height) && ("" == $video_height)) {continue;}

        //$video_names_info[] = array("label" => $one_video_label, "source" => $video_source, "width" => $video_width, "height" => $video_height);
        $video_names_info[$one_video_label]["width"] = $video_width;
        $video_names_info[$one_video_label]["height"] = $video_height;

        //if ("" == $video_name_first) {$video_name_first = $one_video_label;}
        //if ($one_video_label == $video_default) {$video_default_found = true;}
    }
    //if (!$video_default_found) {$video_default = $video_name_first;}

    $youtube_src_default = '<object width="%%w%%" height="%%h%%"><param name="movie" value="http://www.youtube.com/v/%%id%%"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/%%id%%" type="application/x-shockwave-flash" wmode="transparent" width="%%w%%" height="%%h%%"></embed></object>';
    $vimeo_src_default = '<object width="%%w%%" height="%%h%%"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" /><embed src="http://www.vimeo.com/moogaloop.swf?clip_id=%%id%%&server=www.vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="%%w%%" height="%%h%%"></object>';
    //$flash_src_default = '<object width="%%w%%" height="%%h%%"><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="%%s%%%%d%%%%id%%"/><embed src="%%s%%%%d%%%%id%%" width="%%w%%" height="%%h%%" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"/></object>';
    //$flv_src_default = '<object width="%%w%%" height="%%h%%"><param name="movie" value="%%s%%%%d%%player.swf"></param><param name="flashvars" value="src=%%s%%%%d%%%%id%%&amp;poster=%%s%%%%d%%%%ps%%&amp;controlBarAutoHide=true"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="%%s%%%%d%%player.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%w%%" height="%%h%%" flashvars="src=%%s%%%%d%%%%id%%&amp;poster=%%s%%%%d%%%%ps%%&amp;controlBarAutoHide=true"></embed></object>';
    $flash_src_default = '<object width="%%w%%" height="%%h%%"><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent"/><param name="movie" value="%%path%%%%id%%"/><embed src="%%path%%%%id%%" width="%%w%%" height="%%h%%" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"/></object>';
    $flv_src_default = '<object width="%%w%%" height="%%h%%"><param name="movie" value="%%path%%player.swf"></param><param name="flashvars" value="src=%%path%%%%id%%&amp;poster=%%path%%%%ps%%&amp;controlBarAutoHide=true"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="%%path%%player.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%w%%" height="%%h%%" flashvars="src=%%path%%%%id%%&amp;poster=%%path%%%%ps%%&amp;controlBarAutoHide=true"></embed></object>';

    // if nothing configured, use the default
    //if (0 == count($video_names_usage))
    $flash_server = "";
    $flash_directory = "";
    {
        $flash_server_setting = SystemPref::Get("FlashServer");
        $flash_directory_setting = SystemPref::Get("FlashDirectory");
        //echo "--- $flash_directory_setting ---\n";

        // if not a flash server set, use the cs server
        if ((!$flash_server_setting) || ("" == $flash_server_setting))
        {
            $flash_server = $p_websiteUrl;
            if ("/" != strrchr($flash_server, "/"))
            {
                $flash_server .= "/";
            }

/*
            //echo "$p_websiteUrl";
            $domain_end = strpos($p_websiteUrl, "/", 8);
            if (false === $domain_end)
            {
                $flash_server = $p_websiteUrl;
                $flash_directory = "/";
            }
            else
            {
                $flash_server = substr($p_websiteUrl, 0, $domain_end);
                $flash_directory = substr($p_websiteUrl, $domain_end);
                if (1 < strlen(strrchr($flash_directory, "/")))
                {
                    $flash_directory .= "/";
                }
            }

            if ((!$flash_directory_setting) || ("" == $flash_directory_setting))
            {
                $flash_directory .= "videos/";
            }
            else
            {
                $flash_directory .= $flash_directory_setting;
            }
*/
        }
        else // use the set server for the flash files
        {
            $flash_server = $flash_server_setting;
        }

        // if not flash directory set, just assure that the server/dir ends with a '/'
        if ((!$flash_directory_setting) || ("" == $flash_directory_setting))
        {
            if ("/" == strrchr($flash_server, "/"))
            {
                $flash_directory = "";
            }
            else
            {
                $flash_directory = "/";
            }
        }
        else
        {
            $flash_directory = $flash_directory_setting;
        }

        $flash_path = $flash_server. $flash_directory;
        if ("/" != strrchr($flash_path, "/"))
        {
            $flash_path .= "/";
        }

        //$video_default = "YouTube";
        $cur_info = $video_names_info["YouTube"];
        $video_names_usage[] = array("label" => "YouTube", "source" => $youtube_src_default, "width" => $cur_info['width'], "height" => $cur_info['height']);
        $cur_info = $video_names_info["Vimeo"];
        $video_names_usage[] = array("label" => "Vimeo", "source" => $vimeo_src_default, "width" => $cur_info['width'], "height" => $cur_info['height']);
        $cur_info = $video_names_info["Flash"];
        //$video_names_usage[] = array("label" => "Flash", "source" => $flash_src_default, "width" => $cur_info['width'], "height" => $cur_info['height'], "path" => $flash_path, "server" => $flash_server, "directory" => $flash_directory);
        $video_names_usage[] = array("label" => "Flash", "source" => $flash_src_default, "width" => $cur_info['width'], "height" => $cur_info['height'], "path" => $flash_path);
        $cur_info = $video_names_info["Flv"];
        //$video_names_usage[] = array("label" => "Flv", "source" => $flv_src_default, "width" => $cur_info['width'], "height" => $cur_info['height'], "path" => $flash_path, "server" => $flash_server, "directory" => $flash_directory);
        $video_names_usage[] = array("label" => "Flv", "source" => $flv_src_default, "width" => $cur_info['width'], "height" => $cur_info['height'], "path" => $flash_path);
    }

    //$video_info = array("default" => $video_default, "labels" => $video_names_usage);
    $video_info = array("labels" => $video_names_usage);

/*
    $audio_default = SystemPref::Get("MapAudioTypeDefault");
    if (!$audio_default) {$audio_default = "ogg";}

    $audio_names_str = SystemPref::Get("MapAudioNames");
    if (!$audio_names_str) {$audio_names_str = "";}

    $audio_names_arr = explode(",", $audio_names_str);
    $audio_names_usage = array();

    $audio_types_usage = array();

    $audio_name_first = "";
    $audio_default_found = false;
    foreach ($audio_names_arr as $one_audio_label)
    {
        if ("" == $one_audio_label) {continue;}

        $one_audio_type = SystemPref::Get("MapAudioType" . ucfirst($one_audio_label));
        if (!$one_audio_type) {continue;}

        $audio_types_usage[] = array('type' => $one_audio_label, 'mime' => $one_audio_type);

        if ("" == $audio_name_first) {$audio_name_first = $one_audio_label;}
        if ($one_audio_label == $audio_default) {$audio_default_found = true;}
    }
    if (!$audio_default_found) {$audio_default = $audio_name_first;}

    // if no audio types, use the default
    if (0 == count($audio_types_usage))
    {
        $audio_types_usage = array();
        $audio_types_usage[] = array('type' => 'ogg', 'mime' => 'audio/ogg');
        $audio_types_usage[] = array('type' => 'mp3', 'mime' => 'audio/mpeg');
        $audio_types_usage[] = array('type' => 'wav', 'mime' => 'audio/vnd.wave');
        $audio_default = 'ogg';
    }

    $audio_start = SystemPref::Get("MapAudioAutoStart");
    if (!$audio_start) {$audio_start = "false";}
    if (in_array(strtolower($audio_start), array("0", "false", "no"))) {$audio_start = "false";}
    else {$audio_start = "true";}

    $audio_site = SystemPref::Get("MapAudioSite");
    if (!$audio_site) {$audio_site = $p_websiteUrl . '/audio/';}

    $audio_object = SystemPref::Get("MapAudioObject");
    if (!$audio_object) {$audio_object = '<object><param name="src" value="%%site%%%%track%%"><param name="autostart" value="%%auto%%"><param name="autoplay" value="%%auto%%"><param name="controller" value="true"><embed src="%%site%%%%track%%" controller="true" autoplay="%%auto%%" autostart="%%auto%%" type="%%type%%" /></object>';}


    $audio_info = array("default" => $audio_default, "types" => $audio_types_usage, "auto" => $audio_start, "site" => $audio_site, "object" => $audio_object);
*/

    //$res_popups_info = array("width" => $size_info["width"], "height" => $size_info["height"], "video" => $video_info, "audio" => $audio_info);
    $res_popups_info = array("width" => $size_info["width"], "height" => $size_info["height"], "video" => $video_info);
    return array("json_obj" => $res_popups_info);
} // fn GetPopupsInfo


} // class Geo_Locations

?>
