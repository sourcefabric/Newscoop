<?php

$event_data_sources = array(
    'events_1' => array(
        'provider_id' => 1,
        'provider_name' => 'Werbeverlags AG',
        'event_type' => 'event_general',
        'publication_id' => 2,
        'language_id' => 1,
        'issue_number' => 13,
        'section_number' => 30,
        'source_dirs' => array(
            'new' => '/usr/local/var/www/fabric/events/input/events/',
            'old' => '/usr/local/var/www/fabric/events/processed/events/',
        ),
        'categories' => array(
            'theater' => array('theater', 'theatre',),
            'exhibition' => array('exhibition', 'ausstellung', 'ausstellungen',), // both museums and galleries
            'party' => array('party',),
            'music' => array('music', 'musik',),
            'concert' => array('concert', 'konzerte',),
        ),
        'status' => array(
            'public' => true,
            'comments' => false,
            'publish' => true,
        ),
/*
        'geo' => array(
            'search_old' => true,
            'center_latitude' => 47.556876,
            'center_longitude' => 7.577676,
        ),
*/
    ),
    'movies_1' => array(
        'provider_id' => 2,
        'provider_name' => 'Werbeverlags AG',
        'event_type' => 'event_general',
        //'event_type' => 'event_movie',
        'publication_id' => 1,
        'issue_number' => 1,
        'section_number' => 1,
        'language_id' => 1,
        'source_dirs' => array(
            'new' => '/usr/local/var/www/fabric/events/input/movies/',
            'old' => '/usr/local/var/www/fabric/events/processed/movies/',
        ),
        'categories' => array(
            'movie' => '*',
        ),
        'status' => array(
            'public' => true,
            'comments' => false,
            'publish' => true,
        ),
/*
        'geo' => array(
            'search_old' => true,
            'center_latitude' => 47.556876,
            'center_longitude' => 7.577676,
        ),
*/
    ),
);

?>