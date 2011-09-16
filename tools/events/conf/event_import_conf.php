<?php

$event_data_sources = array(
    array(
        'provider_id' => 1,
        'event_type' => 'events',
        'publication_id' => 1,
        'language_id' => 1,
        'issue_number' => 1,
        'section_number' => 1,
        'source_dirs' => array(
            'new' => '/usr/local/var/www/fabric/events/input/events/',
            'old' => '/usr/local/var/www/fabric/events/processed/events/',
        ),
        'categories' => array(
            'EventsCategoryTheater' => array('theater', 'theatre',),
            'EventsCategoryExhibition' => array('exhibition', 'ausstellung', 'ausstellungen',), // both museums and galleries
            'EventsCategoryParty' => array('party',),
            'EventsCategoryMusic' => array('music', 'musik',),
            'EventsCategoryConcert' => array('concert', 'konzerte',),
        )
        'geo' => array(
            'search_old' => true,
            'center_latitude' => 47.556876,
            'center_longitude' => 7.577676,
        ),
    ),
    array(
        'provider_id' => 2,
        'event_type' => 'movies',
        'publication_id' => 1,
        'issue_number' => 1,
        'section_number' => 1,
        'language_id' => 1,
        'source_dirs' => array(
            'new' => '/usr/local/var/www/fabric/events/input/movies/',
            'old' => '/usr/local/var/www/fabric/events/processed/movies/',
        ),
        'categories' => array(
            'EventsCategoryMovies' => '*',
        ),
        'geo' => array(
            'search_old' => true,
            'center_latitude' => 47.556876,
            'center_longitude' => 7.577676,
        ),
    ),
);

/*
$event_data_properties = array(
    //'nominatim_url' => 'http://nominatim.openstreetmap.org/search/',
    //'publications' => array(1);
    // providers (with providers' ids and other info) per publications
    'providers' => array(
        1 => array(
            'events' => array(
                'provider_id' => 1,
                'language_id' => 1,
                'source_dirs' => array(
                    'new' => '/usr/local/var/www/fabric/events/input/events/',
                    'old' => '/usr/local/var/www/fabric/events/processed/events/',
                ),
                'categories' => array(
                    //'fixed' => 'pref:EventsCategoryAll',
                    //'match' => 'EventsCategoryGenRoot',
                    'EventsCategoryTheater' => array('theater', 'theatre',),
                    'EventsCategoryExhibition' => array('exhibition', 'ausstellung', 'ausstellungen',), // both museums and galleries
                    'EventsCategoryParty' => array('party',),
                    'EventsCategoryMusic' => array('music', 'musik',),
                    'EventsCategoryConcert' => array('concert', 'konzerte',),
                    //'EventsCategory' => array(,),
                )
            ),
            'movies' => array(
                'provider_id' => 2,
                'language_id' => 1,
                'source_dirs' => array(
                    'new' => '/usr/local/var/www/fabric/events/input/movies/',
                    'old' => '/usr/local/var/www/fabric/events/processed/movies/',
                ),
                'categories' => array(
                    //'fixed' => 'pref:EventsCategoryAll',
                    //'fixed' => 'EventsCategoryMovies',
                    'EventsCategoryMovies' => '*',
                )
            ),
        ),
    ),
    //'provider_ids' => array(
    //    1 => array('events' => 1, 'movies' => 2),
    //),
    //'source_dirs' => array(
    //    'events' => array(
    //        'new' => '/usr/local/var/www/fabric/events/input/events/',
    //        'old' => '/usr/local/var/www/fabric/events/processed/events/',
    //    ),
    //    'movies' => array(
    //        'new' => '/usr/local/var/www/fabric/events/input/kinos/',
    //        'old' => '/usr/local/var/www/fabric/events/processed/kinos/',
    //    ),
    //),
    //'categories'
);
*/

$event_newscoop_inst = array(
    'newscoop_dir' => '/usr/local/var/www/fabric/newscoop',
    'db_access_conf' => '/usr/local/var/www/fabric/newscoop/conf/database_conf.php',
    //'publication_id' => 1,
);

?>