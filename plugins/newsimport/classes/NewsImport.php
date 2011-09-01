<?php


class NewsImport extends DatabaseObject
{

    public static function ProcessImport(&$p_importOnly) {

        global $Campsite;

        $p_importOnly = false;
        $output_html = ' ';

        //looking whether the request is of form used for xml import, i.e.
        //http(s)://newscoop_domain/(newscoop_dir/)_xmlimport(/...)(?...)

        $path_request_parts = explode('?', $_SERVER['REQUEST_URI']);
        $path_request = strtolower($path_request_parts[0]);
        if (('' == $path_request) || ('/' != $path_request[strlen($path_request)-1])) {
            $path_request .= '/';
        }

        $campsite_subdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/', -2));

        // the path prefix that should be considered when checking the xml import directory
        // it is an empty string for domain based installations
        $xmlimp_start = strtolower($campsite_subdir);
        if (('' == $xmlimp_start) || ('/' != $xmlimp_start[strlen($xmlimp_start)-1])) {
            $xmlimp_start .= '/';
        }

        // the path (as of request_uri) that is for the statistics part
        $xmlimp_start .= '_newsimport/';
        $xmlimp_start_len = strlen($xmlimp_start);
        // if request_uri starts with the statistics path, it is just for the statistics things
        if (substr($path_request, 0, $xmlimp_start_len) == $xmlimp_start) {
            $p_importOnly = true;
        }
        // if not on statistics, just return and let run the standard newscoop processing
        if (!$p_importOnly) {
            return true;
        }

        if (!array_key_exists('newsauth', $_GET)) {
            return false;
        }

        $news_auth = $_GET['newsauth'];
        $news_feed = null;

        if (array_key_exists('newsfeed', $_GET)) {
            $news_feed = $_GET['newsfeed'];
        }

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SystemPref.php');

        $incl_dir = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
        require($incl_dir . 'default_access.php');
        $news_auth_sys_pref = $newsimport_default_access;
        // not taking it from sysprefs since cron job does not have access there
        //$news_auth_sys_pref = SystemPref::Get('Plugin_NewsImport_CommandToken');
        if ((!empty($news_auth_sys_pref)) && ($news_auth != $news_auth_sys_pref)) {
            return false;
        }

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Topic.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'TopicName.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'GeoMap.php');
        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Article.php');

        $conf_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'include';
        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        require_once($conf_dir.DIR_SEP.'default_topics.php');
        require_once($conf_dir.DIR_SEP.'news_feeds_conf.php');
        require_once($class_dir.DIR_SEP.'NewsImport.php');

        // take the ctegory topics, as array by [language][category] of [name,id]
        $cat_topics = self::ReadEventTopics($newsimport_default_cat_names);

        $events_limit = 0;
        $events_skip = 0;
        if (array_key_exists('newslimit', $_GET)) {
            $events_limit = 0 + $_GET['newslimit'];
            $events_limit = max(0, $events_limit);
        }
        if (array_key_exists('newsoffset', $_GET)) {
            $events_skip = 0 + $_GET['newsoffset'];
            $events_skip = max(0, $events_skip);
        }

        $params_other = array(
            'skip' => $events_skip,
            'limit' => $events_limit,
        );

        $events_ignore_passed = false;
        if (array_key_exists('newsignorepassed', $_GET)) {
            if (in_array(strtolower($_GET['newsignorepassed']), array('1', 'true', 't', 'yes', 'y', 'on'))) {
                $events_ignore_passed = true;
            }
            if (in_array(strtolower($_GET['newsignorepassed']), array('0', 'false', 'f', 'no', 'n', 'off'))) {
                $events_ignore_passed = false;
            }
        }
        if ($events_ignore_passed) {
            $params_other['start_date'] = date('Y-m-d', localtime());
        }

        set_time_limit(0);
        ob_end_flush();
        flush();

        //if (self::SomeTransferRunning()) {
        //    return;
        //}

        return self::LoadEventData($event_data_sources, $news_feed, $cat_topics, $params_other);
    }

    public static function SomeTransferRunning() {
        // TODO: how to check that the files are complete
        return false;
    }


    public static function ReadEventTopics($p_defaultTopicNames) {
        $event_spec_topics = array();

        foreach ($p_defaultTopicNames as $cat_key => $event_spec_names) {
            foreach ($event_spec_names as $cat_lan_id => $cat_name) {
                $cat_key_sys_pref = 'EventCat' . $cat_lan_id . ucfirst($cat_name);
                $cat_name_sys_pref = SystemPref::Get($cat_key_sys_pref);
                if (!empty($cat_name_sys_pref)) {
                    $cat_name = $cat_name_sys_pref;
                }
                if (!array_key_exists($cat_lan_id, $event_spec_topics)) {
                    $event_spec_topics[$cat_lan_id] = array();
                }
                $topic_name_obj = new TopicName($cat_name, $cat_lan_id);
                $event_spec_topics[$cat_lan_id][$cat_key] = array('name' => $cat_name, 'id' => $topic_name_obj->getTopicId());
            }
        }

        return $event_spec_topics;
    }

    public static function ReadEventCategories($p_source, $p_catTopics) {
        if (!is_array($p_source)) {
            return false;
        }
        if (!array_key_exists('language_id', $p_source)) {
            return false;
        }
        if (!array_key_exists('categories', $p_source)) {
            return false;
        }
        $language_id = $p_source['language_id'];
        $categories_info = $p_source['categories'];
        if (!is_array($categories_info)) {
            return null;
        }

        if (!array_key_exists($language_id, $p_catTopics)) {
            return null;
        }
        $lang_topics = $p_catTopics[$language_id];

        $topics = array();
        foreach ($categories_info as $one_spec_key => $one_spec_value) {
            if (!is_string($one_spec_key)) {
                continue;
            }

            if ('*' == $one_spec_value) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('fixed' => $lang_topics[$one_spec_key]);
                }
            }
            if (is_array($one_spec_value)) {
                if (array_key_exists($one_spec_key, $lang_topics)) {
                    $topics[] = array('match_xml' => $one_spec_value, 'match_topic' => $lang_topics[$one_spec_key]);
                }
            }
        }

        return $topics;
    }

/*
    public static function ReadKinoData($p_source) {
        $event_parser = new EventData_Parser();
    
        $provider_id = $src_conf['provider_id'];
        $src_dir_name = $src_conf['event_dir_new'];
    
        if ($input_dir = opendir($src_dir_name)) {
            while (false !== ($input_name = readdir($input_dir))) {
                if (!is_file($input_name)) {
                    continue;
                }
    
                $events = $event_parser->parse($provider_id, $input_name, $src_cats);
                if ($events) {
                    $event_data[$input_name] = $events;
                }
    
            }
            closedir($input_dir);
        }
    
    }
*/
/*
    public static function ReadEventData(@$event_data, $src_conf, $src_cats) {
        $event_parser = new EventData_Parser();
    
        $provider_id = $src_conf['provider_id'];
        $src_dir_name = $src_conf['event_dir_new'];
    
        if ($input_dir = opendir($src_dir_name)) {
            while (false !== ($input_name = readdir($input_dir))) {
                if (!is_file($input_name)) {
                    continue;
                }
    
                $events = $event_parser->parse($provider_id, $input_name, $src_cats);
                if ($events) {
                    $event_data[$input_name] = $events;
                }
    
            }
            closedir($input_dir);
        }
    }
*/

    public static function AskNominatim($p_event, $p_params) {
        //$nominatim_url = 'http://nominatim.openstreetmap.org/search/ch/basel/Pestalozzistr./20?format=xml&polygon=0&addressdetails=1';
        $nominatim_url_osm = 'http://nominatim.openstreetmap.org/search/';
        //http://open.mapquestapi.com/nominatim/v1/search/ch/basel/Pestalozzistr./20?format=xml&polygon=0&addressdetails=0
        $nominatim_url_mp = 'http://open.mapquestapi.com/nominatim/v1/search/';
        //$nominatim_url = $p_params['nominatim_url'];
    
        $location_country = (empty($p_event['location_country'])) ? '' : urlencode(trim($p_event['location_country']));
        $request_country_part = '&countrycodes=' . $location_country;
        $location_town = (empty($p_event['location_town'])) ? '' : urlencode(trim($p_event['location_town']));
        //$location_street = urlencode(trim($p_event['location_street']));
        $location_street = (empty($p_event['location_street'])) ? '' : trim($p_event['location_street']);
        $location_street_sep_pos = strrpos($location_street, ' ');
        if (false !== $location_street_sep) {
            $location_street_name = urlencode(trim(substr($location_street, 0, $location_street_sep_pos)));
            $location_street_number = urlencode(trim(substr($location_street, ($location_street_sep_pos + 1))));
            $location_street = $location_street_name . '/' . $location_street_number;
        }
        else {
            $location_street = urlencode($location_street);
        }
    
        $request_spec_broad =  $location_street . '?format=json&polygon=0&addressdetails=1';
        $request_spec_city =  $location_town . '/' . $request_spec_broad;
        $request_spec_cc = $request_spec_broad . $request_country_part;
        $request_spec_city_cc = $request_spec_city . $request_country_part;
    
        $request_urls = array();
        $request_urls[] = $nominatim_url_mp . $request_spec_city_cc;
        if (!empty($location_street)) {
            $request_urls[] = $nominatim_url_mp . $request_spec_cc;
        }
        $request_urls[] = $nominatim_url_mp . $request_spec_city;
        if (!empty($location_street)) {
            $request_urls[] = $nominatim_url_mp . $request_spec_broad;
        }
        $request_urls[] = $nominatim_url_osm . $request_spec_city_cc;
        if (!empty($location_street)) {
            $request_urls[] = $nominatim_url_osm . $request_spec_cc;
        }
        $request_urls[] = $nominatim_url_osm . $request_spec_city;
        if (!empty($location_street)) {
            $request_urls[] = $nominatim_url_osm . $request_spec_broad;
        }
        //$request_url = $nominatim_url . $request_spec;
    
        $geo_info = null;
        foreach ($request_urls as $one_url) {
            // catching all the possible exceptions: getting answer, decoding answer, wrong content
            try {
                $nominatim_response = file_get_contents($request_url);
                if (empty($nominatim_response)) {
                    continue;
                }
                $nominatim_response = json_decode($nominatim_response, true);
                if (empty($nominatim_response)) {
                    continue;
                }
                // if a single match, taking that one as a real event
                if (1 == count($nominatim_response)) {
                    $geo_info = $nominatim_response;
                    break;
                }
                // trying to match the more-than-one results
                foreach ($nominatim_response as $one_location) {
                    if ($one_location['address']['postcode'] == $p_event['location_zip']) {
                        $geo_info = $one_location;
                        break;
                    }
                }
                foreach ($nominatim_response as $one_location) {
                    if ($one_location['address']['city'] == $p_event['location_town']) {
                        $geo_info = $one_location;
                        break;
                    }
                }
                foreach ($nominatim_response as $one_location) {
                    $one_zip_diff = abs(0 + $one_location['address']['postcode']) - (0 + $p_event['location_zip']);
                    if (20 >= $one_zip_diff) {
                        $geo_info = $one_location;
                        break;
                    }
                }
                foreach ($nominatim_response as $one_location) {
                    if (soundex($one_location['address']['city']) == soundex($p_event['location_town'])) {
                        $geo_info = $one_location;
                        break;
                    }
                }
                // trying the nearest one at the end (just approximate distance)
    
                if (array_key_exists('geo', $p_params) && is_array($p_params['geo'])
                    && array_key_exists('center_latitude', $p_params['geo']) && array_key_exists('center_latitude', $p_params['geo'])) {
                    //$event_rank = 0;
                    $center_lat_cos = cos((M_PI / 180) * $p_params['geo']['center_latitude']);
                    //$event_nearest_rank = -1;
                    $event_nearest_value = 100000;
                    foreach ($nominatim_response as $one_location) {
                        $one_lat_dist = abs($one_location['lat'] - $p_params['geo']['center_latitude']);
                        $one_lon_dist = abs($one_location['lon'] - $p_params['geo']['center_longitude']);
                        if (180 < $one_lon_dist) {
                            $one_lon_dist = 360 - $one_lon_dist;
                        }
                        $one_lon_dist = $center_lat_cos * $one_lon_dist;
                        $one_event_dist = ($one_lat_dist * $one_lat_dist) + ($one_lon_dist * $one_lon_dist);
                        if ($one_event_dist < $event_nearest_value) {
                            $event_nearest_value = $one_event_dist;
                            //$event_nearest_rank = $one_rank;
                            $geo_info = $one_location;
                        }
                        //$event_rank += 1;
                    }
                    //if (-1 < $event_nearest_rank) {
                    //    $geo_info = $nominatim_response[$event_nearest_rank];
                    //}
                }
    
                if (!empty($geo_info)) {
                    break;
                }
            }
            catch (Exception $exc) {
                continue;
            }
        }
    
        if ((!empty($geo_info)) && (is_array($geo_info))) {
            if (array_key_exists('lat', $geo_info) && array_key_exists('lon', $geo_info)) {
                $event_latitude = $geo_info['lat'];
                $event_longitude = $geo_info['lon'];
                if (is_numeric($event_latitude) && is_numeric($event_longitude)) {
                    return array('longitude' => (0 + $event_longitude), 'latitude' => (0 + $event_latitude));
                }
            }
        }
    
        return null;
    }

    public static function LocateEventData(&$event_data, $p_params) {
        //$event_type_name = 'event_general';
        $event_type_name = $p_params['event_type'];
        //$event_type_table = 'X' . $event_type_name;
        $event_field_name = 'location_id';
        //$location_id_field = 'F' . $event_field_name;
    
        $search_old = false;
        if (array_key_exists('geo', $p_params)) {
            if ((is_array($p_params)) && (array_key_exists('search_old', $p_params['geo']))) {
                $search_old = $p_params['geo']['search_old'];
            }
        }
        $language_id = $p_params['language_id'];
    
        $known_locations = array();
    
        foreach($event_data as $one_event_key => $one_event) {
    
            $geo_location = null;
            $location_id = $one_event['location_id'];
            $location_name = $one_event['event_location'];
    
            if (array_key_exists($location_id, $known_locations)) {
                $geo_location = $known_locations[$location_id];
            }
            elseif ($search_old) {
                $latest_map_info = Geo_Map::GetLatestMapByArticleType($event_type_name, $event_field_name);
                if (!empty($latest_map_info)) {
                    $geo_data = Geo_Map::LoadMapData($latest_map_info['map_id'], $language_id, $latest_map_info['article_number'], true, true);
                    foreach ($geo_data['pois'] as $one_poi) {
                        if ($location_name == $one_poi['title']) {
                            $geo_location = array (
                                'latitude' => $one_poi['latitude'],
                                'longitude' => $one_poi['longitude'],
                            );
                            break;
                        }
                    }
                }
            }
    
            if (empty($geo_location)) {
                $geo_location = AskNominatim($one_event, $p_params);
            }
    
            $event_data[$one_event_key] = $geo_location;
        }
    
    }

    public static function StoreEventData($p_events, $p_source) {
//echo count($p_events) . " of events:\n";
        if (empty($p_events)) {
            return;
        }

        //$art_type = 'event_general';
        $art_type = $p_source['event_type'];
        $art_publication = $p_source['publication_id'];
        $art_issue = $p_source['issue_number'];
        $art_section = $p_source['section_number'];
        $art_lang = $p_source['language_id'];

        $status_public = $p_source['status']['public'];
        $status_comments = $p_source['status']['comments'];
        $status_publish = $p_source['status']['publish'];

        $art_author = new Author($p_source['provider_name']);
        if (!$art_author->exists()) {
            $art_author->create();
        }

//var_dump($p_events);

//echo count($p_events) . " of events:\n";

        //$debug_rank = -1;
        foreach ($p_events as $one_event) {
            //$debug_rank += 1;
            //echo "rank $debug_rank: " . $one_event['date'] . "\n";
            //try {
            //    ob_end_flush();
            //} catch (Exception $exc) {}
            //flush();

            //$ev_headline = $one_event['headline'];
            //$ev_event_id = $one_event['event_id'];

//var_dump($one_event);
//return;

/*

First, try to load event (possibly created by former imports), and if there, remove it - will be put in with the current, possible more correct info.
*/

            $article = null;

            $p_count = 0;
            $event_art_list = Article::GetList(array(
                new ComparisonOperation('idlanguage', new Operator('is', 'sql'), $art_lang),
                new ComparisonOperation('IdPublication', new Operator('is', 'sql'), $art_publication),
                new ComparisonOperation('NrIssue', new Operator('is', 'sql'), $art_issue),
                new ComparisonOperation('NrSection', new Operator('is', 'sql'), $art_section),
                //new ComparisonOperation('', new Operator('is', 'sql'), 1),
                new ComparisonOperation($art_type . '.event_id', new Operator('is', 'sql'), $one_event['event_id']),
            ), null, null, 0, $p_count, true);

            if (is_array($event_art_list) && 0 <count($event_art_list)) {
                $article = $event_art_list[0];
            }

            //$art_name = $one_event['headline'] . ' (' . mt_rand() . ')';
            $art_name = $one_event['headline'] . ' - ' . $one_event['date'] . ' (' . $one_event['event_id'] . ')';

//echo "\n$art_type, $art_name, $art_publication, $art_issue, $art_section\n";

            if (!$article) {
                $article = new Article($art_lang);
                $article->create($art_type, $art_name, $art_publication, $art_issue, $art_section, false);
            }

            $art_number = $article->getArticleNumber();

//echo  $article->getArticleNumber() . ' - ' . $article->getLanguageId() . "\n" . "\n";

            //$article_data = new ArticleData($art_type, $art_number, $article->getLanguageId());
            $article_data = $article->getArticleData();

            $article_data->setProperty('Fprovider_id', $one_event['provider_id']);
            $article_data->setProperty('Fevent_id', $one_event['event_id']);
            $article_data->setProperty('Ftour_id', $one_event['tour_id']);
            $article_data->setProperty('Flocation_id', $one_event['location_id']);

            $article_data->setProperty('Fheadline', $one_event['headline']);
            $article_data->setProperty('Forganizer', $one_event['organizer']);

            $article_data->setProperty('Fcountry', $one_event['country']);
            $article_data->setProperty('Fzipcode', $one_event['zipcode']);
            $article_data->setProperty('Ftown', $one_event['town']);
            $article_data->setProperty('Fstreet', $one_event['street']);

            $article_data->setProperty('Fdate', $one_event['date']);
            $article_data->setProperty('Fdate_year', $one_event['date_year']);
            $article_data->setProperty('Fdate_month', $one_event['date_month']);
            $article_data->setProperty('Fdate_day', $one_event['date_day']);
            $article_data->setProperty('Ftime', $one_event['time']);

            $article_data->setProperty('Fdate_time_text', $one_event['date_time_text']);

            $article_data->setProperty('Fweb', $one_event['web']);
            $article_data->setProperty('Femail', $one_event['email']);
            $article_data->setProperty('Fphone', $one_event['phone']);

            $article_data->setProperty('Fdescription', $one_event['description']);
            $ev_other_info = implode("\n", $one_event['other']);
            $article_data->setProperty('Fother', $ev_other_info);

            $article_data->setProperty('Fgenre', $one_event['genre']);
            $article_data->setProperty('Flanguages', $one_event['languages']);
            $article_data->setProperty('Fprices', $one_event['prices']);
            $article_data->setProperty('Fminimal_age', $one_event['minimal_age']);

            $article_data->setProperty('Frated', ($one_event['rated'] ? 1 : 0));

            // set topics!

            $old_topics = ArticleTopic::GetArticleTopics($art_number);
            foreach ($old_topics as $one_topic) {
                ArticleTopic::RemoveTopicFromArticle($one_topic->getTopicId(), $art_number, false);
                //$one_topic->delete($art_lang);
            }

            $art_topics = null;
            if (array_key_exists('topics', $one_event)) {
                $art_topics = $one_event['topics'];
            }

            if (is_array($art_topics)) {
                foreach ($art_topics as $topic_info) {
                    $topic_id = null;
                    if (is_array($topic_info) && array_key_exists('id', $topic_info)) {
                        $topic_id = $topic_info['id'];
                    }
                    if (empty($topic_id)) {
                        continue;
                    }
                    ArticleTopic::AddTopicToArticle($topic_id, $art_number, false);
                }
            }
//echo " setting author ";
            //ArticleAuthor::GetAuthorsByArticle($art_number, $art_lang);
            ArticleAuthor::OnArticleLanguageDelete($art_number, $art_lang);

            $article->setAuthor($art_author);

            $article->setIsPublic($status_public);
            $article->setCommentsEnabled($status_comments);
            //$article->setIsIndexed(true);
            $article->setPublishDate($one_event['date']);
            if ($status_publish) {
                $article->setWorkflowStatus('Y', false);
                $article->setPublishDate($one_event['date']); // necessary to reset after changing the workflow status
                //$article->setPublishDate('2020-02-02');
            }
        }
        ;
    }

    public static function CleanEventSources($p_files, $p_params) {
        if (empty($p_files)) {
            return;
        }

        $src_dir = $p_params['source_dirs']['new'];
        $dest_dir = $p_params['source_dirs']['old'];

        foreach ($p_files as $one_file) {
            try {
                rename($src_dir . DIRECTORY_SEPARATOR . $one_file, $dest_dir . DIRECTORY_SEPARATOR . $one_file);
            }
            catch (Exception $exc) {
                var_dump($exc);
                continue;
            }
        }

    }

    //public static function LoadEventData($p_eventSources, $p_newscoopInst) {
    public static function LoadEventData($p_eventSources, $p_newsFeed, $p_catTopics, $p_otherParams) {


        //$conf_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'include';
        $class_dir = $GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'newsimport'.DIRECTORY_SEPARATOR.'classes';
        //require_once($conf_dir.DIR_SEP.'default_topics.php');
        //require_once($conf_dir.DIR_SEP.'news_feeds_conf.php');
        require_once($class_dir.DIR_SEP.'EventParser.php');


        echo "\n<pre>\n";
        //echo "aaaaaa xx zz";

//            new ComparisonOperation('event_type.event_id', new Operator('is', 'sql'), '529313'),
//            new ComparisonOperation('event_type.event_id', new Operator('greater', 'string'), '0'),
/*
        $p_count = 0;
        $test_list = Article::GetList(array(
            new ComparisonOperation('idlanguage', new Operator('is', 'sql'), 1),
            new ComparisonOperation('IdPublication', new Operator('is', 'sql'), 2),
            new ComparisonOperation('NrIssue', new Operator('is', 'sql'), 13),
            new ComparisonOperation('NrSection', new Operator('is', 'sql'), 30),
            //new ComparisonOperation('', new Operator('is', 'sql'), 1),
            new ComparisonOperation('event_type.event_id', new Operator('is', 'sql'), 529313),
        ), null, null, 0, $p_count, true);

        if (is_array($test_list)) {
            foreach ($test_list as $one_art) {
                $one_art_data = $one_art->getArticleData();
                var_dump($one_art);
                var_dump($one_art_data);
            }
        }
*/
        //var_dump($test_list);
        //return;

        //var_dump($p_eventSources);
        //var_dump($p_newsFeed);
        //var_dump($p_catTopics);

        //return false;
        //require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'GeoMap.php');

        //echo "abc\n<pre>\n";

        //var_dump($_GET);

        //var_dump($p_eventSources);

        //$a_map = new Geo_Map();
        //var_dump($a_map);

        //var_dump($p_xmlFeed);

        //return false;

        foreach ($p_eventSources as $one_source_name => $one_source) {
            if ((!empty($p_newsFeed)) && ($one_source_name != $p_newsFeed)) {
                continue;
            }
    
            $categories = self::ReadEventCategories($one_source, $p_catTopics);
            //continue;


            $event_set = null;
            if ('event_general' == $one_source['event_type']) {
                //$event_set = ReadEventData($one_source['provider_id'], $one_source['source_dirs'], $categories);
                $event_load = EventData_Parser::Parse($one_source['provider_id'], $one_source['source_dirs']['new'], $categories, $p_otherParams);
            }
            // temporarily not working with movies
            //elseif ('movies' ==  $one_source['event_type']) {
            //    $event_set = MovieData_Parser::ReadMovieData($used_event_files, $one_source, $categories);
            //}

            //var_dump($event_set);
            //continue;

            $event_set = $event_load['events'];
            //unset($event_load['events']);
//echo "\nevent count: " . count($event_set) . "\n";

            if (empty($event_set)) {
                continue;
            }

            $ev_limit = 0;
            $ev_skip = 0;
            if (array_key_exists('limit', $p_otherParams)) {
                $ev_limit = $p_otherParams['limit'];
            }
            if (array_key_exists('skip', $p_otherParams)) {
                $ev_skip = $p_otherParams['skip'];
            }

### DEBUG
$ev_limit = 1;
### DEBUG

            $ev_count = count($event_set);

            if (0 < $ev_skip) {
                if (0 < $ev_limit) {
                    $event_set = array_slice($event_set, $ev_skip, $ev_limit);
                }
                else {
                    $event_set = array_slice($event_set, $ev_skip);
                }
            }
            else {
                if (0 < $ev_limit) {
                    $event_set = array_slice($event_set, 0, $ev_limit);
                }
            }

            $omit_cleanup = false;
            if (0 < $ev_limit) {
                if (count($event_set) == $ev_limit) {
                    $omit_cleanup = true;
                }
            }

echo "\nevent count: " . count($event_set) . "\n";

            //self::LocateEventData($event_set['events'], $one_source);
            self::StoreEventData($event_set, $one_source);

            if (!$omit_cleanup) {
                //self::CleanEventSources($event_load['files'], $one_source);
            }
        }

        //echo 'asdf';

    }


} // class NewsImport
//$used_event_files = array();

?>