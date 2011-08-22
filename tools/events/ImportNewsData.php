<?php

$conf_dir = dirname(__FILE__) . '/conf/';
require($conf_dir . 'event_import_conf.php');
require($conf_dir . 'event_import_cats.php');

$incl_dir = dirname(__FILE__) . '/incl/';
require($incl_dir . 'EventParser.php');

$classes_dir = $event_data_properties['newscoop_dir'] . '/classes/';
require($incl_dir . 'GeoMapLocation.php');

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

function AskNominatim($p_event, $p_params) {
    //$nominatim_url = 'http://nominatim.openstreetmap.org/search/ch/basel/Pestalozzistr./20?format=xml&polygon=0&addressdetails=1';
    $nominatim_url_osm = 'http://nominatim.openstreetmap.org/search/';
    //http://open.mapquestapi.com/nominatim/v1/search/ch/basel/Pestalozzistr./20?format=xml&polygon=0&addressdetails=0
    $nominatim_url_mp = 'http://open.mapquestapi.com/nominatim/v1/search/';
    //$nominatim_url = $p_params['nominatim_url'];

    $location_country = urlencode(trim($p_event['location_country']));
    $location_town = urlencode(trim($p_event['location_town']));
    $location_street = urlencode(trim($p_event['location_street']));

    $request_spec_broad =  $location_street . '?format=json&polygon=0&addressdetails=1';
    $request_spec_city =  $location_town . '/' . $request_spec_broad;
    $request_spec_cc = $request_spec . '&countrycodes=' . $location_country;

    $request_urls = array(
        $nominatim_url_mp . $request_spec_cc,
        $nominatim_url_mp . $request_spec_city,
        $nominatim_url_mp . $request_spec_broad,
        $nominatim_url_osm . $request_spec_cc,
        $nominatim_url_osm . $request_spec_city,
        $nominatim_url_osm . $request_spec_broad,
    );

    //$request_url = $nominatim_url . $request_spec;

    $geo_info = null;
    foreach ($request_urls as $one_url) {
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
            //$event_distances = array();
            $event_rank = 0;
            $center_lat_cos = cos((M_PI / 180) * $p_params['center_latitude'])
            $event_nearest_rank = -1;
            $event_nearest_value = 100000;
            foreach ($nominatim_response as $one_location) {
                $one_lat_dist = abs($one_location['lat'] - $p_params['center_latitude']);
                $one_lon_dist = abs($one_location['lon'] - $p_params['center_longitude']);
                if (180 < $one_lon_dist) {
                    $one_lon_dist = 360 - $one_lon_dist;
                }
                $one_lon_dist = $center_lat_cos * $one_lon_dist;
                $one_event_dist = ($one_lat_dist * $one_lat_dist) + ($one_lon_dist * $one_lon_dist);
                if ($one_event_dist < $event_nearest_value) {
                    $event_nearest_value = $one_event_dist;
                    $event_nearest_rank = $one_rank;
                }
                //$event_distances['' . $event_rank] = ($one_lat_dist * $one_lat_dist) + ($one_lon_dist * $one_lon_dist);
                $event_rank += 1;
            }
            if (-1 < $event_nearest_rank) {
                $geo_info = $nominatim_response[$event_nearest_rank];
            }
            if (!empty($geo_info)) {
                break;
            }
        }
        catch (Exception $exc) {
            continue;
        }
    }

    return $geo_info;
}

function LocateEventData(@$event_data, $p_params) {
    $event_type_name = 'event_type';
    //$event_type_table = 'X' . $event_type_name;
    $event_field_name = 'location_id';
    //$location_id_field = 'F' . $event_field_name;

    $search_old = $p_params['search_old'];
    $language_id = $p_params['language_id'];

    $known_locations = array();

    foreach($event_data as $one_event) {

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

    }

}

function CleanEventSources() {

}

$used_event_files = array();


function ReadEventData($used_event_files, $event_data_properties, $event_data_categories);


?>