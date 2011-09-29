<?php
/**
 * topics for categories
 */
$newsimport_default_cat_names = array();

// the first one item in each subarray shall be a root topic
$newsimport_default_cat_names['general'] = array(
    // do not change keys; num ids are for topic names by language
    'event' => array(1 => 'Event', 5 => 'Veranstaltung',), // the root of event topics
    'theater' => array(1 => 'Theater', 5 => 'Theater',),
    'exhibition' => array(1 => 'Exhibition', 5 => 'Ausstellung',),
    'party' => array(1 => 'Party', 5 => 'Party',),
    'music' => array(1 => 'Music', 5 => 'Musik',),
    'concert' => array(1 => 'Concert', 5 => 'Konzert',),
    'movie' => array(1 => 'Movie', 5 => 'Movie',),
    'circus' => array(1 => 'Circus', 5 => 'Zirkus'),
    'other' => array(1 => 'Other', 5 => 'Andere'),
);

// changes via SysPrefs: EventCat$LangId$EventCat, e.g.
// EventCat1Event, EventCat1Theater, EventCat5Concert, ...

$newsimport_default_cat_names['movie'] = array(
    // do not change keys; num ids are for topic names by language
    'cinema' => array(1 => 'Cinema', 5 => 'Kino',), // the root of cinema topics
    'adventure' => array(1 => 'Adventure', 5 => 'Abenteuer',),
    'action' => array(1 => 'Action', 5 => 'Action',),
    'adult' => array(1 => 'Adult', 5 => 'Adult',),
    'animation' => array(1 => 'Animation', 5 => 'Animation',),
    'biography' => array(1 => 'Biography', 5 => 'Biografie',),
    'crime' => array(1 => 'Crime', 5 => 'Crime',),
    'documentary' => array(1 => 'Documentary', 5 => 'Dokumentation',),
    'drama' => array(1 => 'Drama', 5 => 'Drama',),
    'family' => array(1 => 'Family', 5 => 'Familienfilm',),
    'fantasy' => array(1 => 'Fantasy', 5 => 'Fantasy',),
    'film_noir' => array(1 => 'Film-Noir', 5 => 'Film-Noir',),
    'history' => array(1 => 'History', 5 => 'Historisch',),
    'horror' => array(1 => 'Horror', 5 => 'Horror',),
    'comedy' => array(1 => 'Comedy', 5 => 'Komödie',),
    'war' => array(1 => 'War', 5 => 'Kriegsfilm',),
    'short' => array(1 => 'Short', 5 => 'Kurzfilm',),
    'musical' => array(1 => 'Musical', 5 => 'Musical',),
    'music' => array(1 => 'Music', 5 => 'Musikfilm',),
    'mystery' => array(1 => 'Mystery', 5 => 'Mystery',),
    'romance' => array(1 => 'Romance', 5 => 'Romanze',),
    'sci_fi' => array(1 => 'Sci-Fi', 5 => 'Sci-Fi',),
    'sport' => array(1 => 'Sport', 5 => 'Sport',),
    'thriller' => array(1 => 'Thriller', 5 => 'Thriller',),
    'western' => array(1 => 'Western', 5 => 'Western',),
);
