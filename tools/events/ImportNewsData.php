<?php

$conf_dir = dirname(__FILE__) . '/conf/';
require($conf_dir . 'event_import_conf.php');
//require($conf_dir . 'event_import_cats.php');

$incl_dir = dirname(__FILE__) . '/incl/';
require($incl_dir . 'EventParser.php');

$classes_dir = $event_data_properties['newscoop_dir'] . '/classes/';
require($incl_dir . 'GeoMap.php');



function ReadEventCategories($p_source) {
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

/*
    $provider_info = $p_conf[$p_providerId];
    if ((!is_array($provider_info)) || (!array_key_exists($p_eventType, $provider_info))) {
        return null;
    }
    $event_type_info = $provide_info[$p_eventType];
    if ((!is_array($event_type_info)) || (!array_key_exists('categories' $event_type_info))) {
        return null;
    }
    $categories_info = $event_type_info['categories'];
    if (!is_array($categories_info)) {
        return null;
    }
*/

    $topics = array();
    foreach ($categories_info as $one_spec_key => $one_spec_value) {
        //if ((!is_string($one_spec_key)) || (!is_string($one_spec_value))) {
        //    continue;
        //}
        if (!is_string($one_spec_key)) {
            continue;
        }
        if ('*' == $one_spec_value) {
            $topic_info = TopicName::GetTopicInfoByPref($one_spec_key);
            if (!empty($topic_info)) {
                $topics[] = array('fixed' => $topic_info);
            }
        }
        if (is_array($one_spec_value)) {
            $topic_info = TopicName::GetTopicInfoByPref($one_spec_key);
            if (!empty($topic_info)) {
                $topics[] = array('match_xml' => $one_spec_value, 'match_topic' => $topic_info);
            }
        }


/*
        if ('fixed' == $one_spec_key) {
            $topic_info = TopicName::GetTopicInfoByPref($one_spec_value);
            if (!empty($topic_info)) {
                $topics[] = array('fixed' => $topic_info);
            }
        }
        if ('match' == $one_spec_key) {
            $topic_info = TopicName::GetTopicTreeByPref($one_spec_value);
            if (!empty($topic_info)) {
                $topics[] = array('match' => $topic_info);
            }
        }
*/
    }

    return $topics;
}

/*
function ReadKinoData($p_source) {
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
function ReadEventData(@$event_data, $src_conf, $src_cats) {
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

function AskNominatim($p_event, $p_params) {
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
    $request_urls[] = $nominatim_url_mp . $request_spec_city_cc,
    if (!empty($location_street)) {
        $request_urls[] = $nominatim_url_mp . $request_spec_cc,
    }
    $request_urls[] = $nominatim_url_mp . $request_spec_city,
    if (!empty($location_street)) {
        $request_urls[] = $nominatim_url_mp . $request_spec_broad,
    }
    $request_urls[] = $nominatim_url_osm . $request_spec_city_cc,
    if (!empty($location_street)) {
        $request_urls[] = $nominatim_url_osm . $request_spec_cc,
    }
    $request_urls[] = $nominatim_url_osm . $request_spec_city,
    if (!empty($location_street)) {
        $request_urls[] = $nominatim_url_osm . $request_spec_broad,
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
                $center_lat_cos = cos((M_PI / 180) * $p_params['geo']['center_latitude'])
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

function LocateEventData(@$event_data, $p_params) {
    $event_type_name = 'event_type';
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
                            'latitude' = $one_poi['latitude'],
                            'longitude' = $one_poi['longitude'],
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

function StoreEventData($p_events, $p_source, $p_install) {
    if (empty($p_events)) {
        return;
    }

    $art_type = 'event_type';
    $art_publication = '';
    $art_issue = '';
    $art_section = '';
    //$art_ = '';

    foreach ($p_events as $one_event) {
        $art_name = $one_event['event_name'];

        $article = new Article($art_type, $art_name, $art_publication, $art_issue, $art_section);
        $article->create();

        $article_data = new ArticleData($art_type, $article->getArticleNumber(), $article->getLanguageId());
        $article_data->setProperty('event_name', $art_name);

        $article_data->setProperty('provider_id', $one_event['provider_id']);
        $article_data->setProperty('event_id', $one_event['event_id']);
        $article_data->setProperty('turnus_id', $one_event['turnus_id']);
        $article_data->setProperty('location_id', $one_event['location_id']);
        $article_data->setProperty('event_location', $one_event['event_location']);
        //$article_data->setProperty('event_name', $one_event['event_name']);
        $article_data->setProperty('location_country', $one_event['location_country']);
        $article_data->setProperty('location_town', $one_event['location_town']);
        $article_data->setProperty('location_zip', $one_event['location_zip']);
        $article_data->setProperty('location_street', $one_event['location_street']);
        $article_data->setProperty('event_date', $one_event['event_date']);
        $article_data->setProperty('event_time', $one_event['event_time']);
        $article_data->setProperty('event_open', $one_event['event_open']);
        $article_data->setProperty('event_texts', $one_event['event_texts']);
        $article_data->setProperty('event_web', $one_event['event_web']);
        $article_data->setProperty('event_email', $one_event['event_email']);
        $article_data->setProperty('event_phone', $one_event['event_phone']);
        $article_data->setProperty('event_images', $one_event['event_images']);
        //$article_data->setProperty('', $one_event['']);
        //$article_data->setProperty('', $one_event['']);

        // set topics!
    }
    ;
}

function CleanEventSources($p_files, $p_params) {
    if (empty($p_files)) {
        return;
    }

    $src_dir = $p_params['source_dirs']['new'];
    $dest_dir = $p_params['source_dirs']['old'];

    foreach ($p_files as $one_file) {
        rename($src_dir . '/' . $one_file, $dest_dir . '/' . $one_file);
    }
}

function LoadEventData($p_eventSources, $p_newscoopInst) {

    foreach ($p_eventSources as $one_source) {

        $categories = ReadEventCategories($one_source);
        //ReadEventCategories($p_conf, $p_providerId, $p_eventType)

        $event_set = null;
        if ('events' == $one_source['event_type']) {
            //$event_set = ReadEventData($one_source['provider_id'], $one_source['source_dirs'], $categories);
            $event_set = EventData_Parser::Parse($one_source['provider_id'], $one_source['source_dirs']['new'], $categories);
        }
        // temporarily not working with movies
        //elseif ('movies' ==  $one_source['event_type']) {
        //    $event_set = ReadMovieData($used_event_files, $one_source, $categories);
        //}

        if (empty($event_set)) {
            continue;
        }

        LocateEventData($event_set['events'], $one_source);
        StoreEventData($event_set['events'], $one_source, $p_newscoopInst);

        CleanEventSources($event_set['files'], $one_source);

    }

}


//$used_event_files = array();

?>