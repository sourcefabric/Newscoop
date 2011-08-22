<?php

$event_data_properties = array(
    'nominatim_url' => 'http://nominatim.openstreetmap.org/search/',

    'provider_id' => 1,
    'event_dir_new' => '/usr/local/var/www/fabric/events/input/events/',
    'event_dir_old' => '/usr/local/var/www/fabric/events/processed/events/',
    'kino_dir_new' => '/usr/local/var/www/fabric/events/input/kinos/',
    'kino_dir_old' => '/usr/local/var/www/fabric/events/processed/kinos/',
);

$event_newscoop_inst = array(
    'newscoop_dir' => '/usr/local/var/www/fabric/newscoop',
    'db_access_conf' => '/usr/local/var/www/fabric/newscoop/conf/database_conf.php',
    'publication_id' => 1,
);

?>