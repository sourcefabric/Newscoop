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
        'article_type' => 'event',
        'images_local' => true,
        'publication_id' => 0, // 2
        'issue_number' => 0, // 13,
        'section_number' => 0, // 30,
        'language_id' => 5, // 1,
        'source_dirs' => array(
            'new' => 'ftp/werbeverlags/',
            'use' => 'ftp/newsimport/events/current/',
            'old' => 'ftp/newsimport/events/processed/',
            'lock' => 'delivery.lock',
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
            'music' => array('music', 'musik', 'concert', 'konzerte',),
            'concert' => array('concert', 'konzerte',),
            //'circus' => array('circus', 'zirkus',),
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
        'article_type' => 'screening',
        'images_local' => true,
        'publication_id' => 0,
        'issue_number' => 0,
        'section_number' => 0,
        'language_id' => 5,
        'source_dirs' => array(
            'new' => 'ftp/werbeverlags/',
            'use' => 'ftp/newsimport/movies/current/',
            'old' => 'ftp/newsimport/movies/processed/',
            'lock' => 'delivery.lock',
            'ready' => array(
                'programs' => 'wvag_cine_done.txt',
                'movies' => 'ci_done.txt',
                'genres' => 'ci_done.txt',
                'timestamps' => 'ci_done.txt',
            ),
            'source' => array(
                'programs' => array('tageswoche_7d.xml.gz',),
                'movies' => array('tageswoche_mov_all.zip', 'tageswoche_mov.xml.gz',),
                'genres' => array('tageswoche_gen_all.zip','tageswoche_gen.xml.gz',),
                'timestamps' => array('tageswoche_tim_all.zip', 'tageswoche_tim.xml.gz',),
            ),
        ),
        'categories' => array(
            //'movie' => '*',
            'adventure' => array('adventure', 'abenteuer',),
            'action' => array('action', 'action',),
            'adult' => array('adult', 'adult',),
            'animation' => array('animation', 'animation',),
            'biography' => array('biography', 'biografie',),
            'crime' => array('crime', 'crime',),
            'documentary' => array('documentary', 'dokumentation',),
            'drama' => array('drama', 'drama',),
            'family' => array('family', 'familienfilm',),
            'fantasy' => array('fantasy', 'fantasy',),
            'film_noir' => array('film-noir', 'film-noir',),
            'history' => array('history', 'historisch',),
            'horror' => array('horror', 'horror',),
            'comedy' => array('comedy', 'komödie',),
            'war' => array('war', 'kriegsfilm',),
            'short' => array('short', 'kurzfilm',),
            'musical' => array('musical', 'musical',),
            'music' => array('music', 'musikfilm',),
            'mystery' => array('mystery', 'mystery',),
            'romance' => array('romance', 'romanze',),
            'sci_fi' => array('sci-fi', 'sci-fi',),
            'sport' => array('sport', 'sport',),
            'thriller' => array('thriller', 'thriller',),
            'western' => array('western', 'western',),
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
);

?>