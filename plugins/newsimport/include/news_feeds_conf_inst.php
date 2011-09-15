<?php
/**
 * event import configurations
 */
$event_data_sources = array(
    'events_1' => array(
        'admin_user_id' => 1,
        'provider_id' => 1,
        'provider_name' => 'Werbeverlags AG',
        'event_type' => 'general',
        'article_type' => 'event', // do not change this, used at e.g. plugin_newsimport_create_event_type too
        'images_local' => true,
        'publication_id' => 0, // 2
        'issue_number' => 0, // 13,
        'section_number' => 0, // 30,
        'language_id' => 1, // 5,
        'source_dirs' => array(
            //'new' => '/usr/local/var/basel-events/events/input/',
            //'use' => '/usr/local/var/basel-events/events/current/',
            //'old' => '/usr/local/var/basel-events/events/processed/',
            'new' => 'files/newsimport/events/input/',
            'use' => 'files/newsimport/events/current/',
            'old' => 'files/newsimport/events/processed/',
            'ready' => array(
                'events' => 'events_done.txt',
            ),
            'source' => array(
                'events' => array('events_all_*',),
            ),
        ),
        'categories' => array(
            'theater' => array('theater', 'theatre',),
            'exhibition' => array('exhibition', 'ausstellung', 'ausstellungen',), // both museums and galleries
            'party' => array('party',),
            'music' => array('music', 'musik',),
            'concert' => array('concert', 'konzerte',),
            'circus' => array('circus', 'zirkus',),
            'other' => 'x',
        ),
        'status' => array(
            'public' => true,
            'comments' => false,
            'publish' => true,
            'publish_date_by_event_date' => true,
        ),
        'geo' => array(
            'map_provider' => 'mapquest', // googlev3, mapquest, osm
            'map_zoom' => 15,
            'map_width' => 600,
            'map_height' => 400,
            'poi_marker_name' => 'marker-gold.png',
            'poi_desc_len' => 100,
        ),
    ),
    'movies_1' => array(
        'admin_user_id' => 1,
        'provider_id' => 2,
        'provider_name' => 'Werbeverlags AG',
        'event_type' => 'movie',
        'article_type' => 'event', // do not change this, used at e.g. plugin_newsimport_create_event_type too
        'images_local' => true,
        'publication_id' => 0,
        'issue_number' => 0,
        'section_number' => 0,
        'language_id' => 1,
        'source_dirs' => array(
            //'new' => '/usr/local/var/basel-events/input/movies/',
            //'use' => '/usr/local/var/basel-events/current/movies/',
            //'old' => '/usr/local/var/basel-events/processed/movies/',
            'new' => 'files/newsimport/movies/input/',
            'use' => 'files/newsimport/movies/current/',
            'old' => 'files/newsimport/movies/processed/',
            'ready' => array(
                'programs' => 'wvag_cine_done.txt',
                'movies' => 'ci_done.txt',
                'genres' => 'ci_done.txt',
                'timestamps' => 'ci_done.txt',
            ),
            'source' => array(
                'programs' => array('tageswoche_7d.xml.gz',),
                'movies' => array('tageswoche_mov.xml.gz',),
                'genres' => array('tageswoche_gen.xml.gz',),
                'timestamps' => array('tageswoche_tim.xml.gz',),
            ),
        ),
        'categories' => array(
            'movie' => '*',
        ),
        'status' => array(
            'public' => true,
            'comments' => false,
            'publish' => true,
            'publish_date_by_event_date' => true,
        ),
        'geo' => array(
            'map_provider' => 'mapquest', // googlev3, mapquest, osm
            'map_zoom' => 15,
            'map_width' => 600,
            'map_height' => 400,
            'poi_marker_name' => 'marker-gold.png',
            'poi_desc_len' => 100,
        ),
    ),
);

?>