<?php

$newsimport_default_cat_names = array(
    // do not change keys; num ids are for topic names by language
    'event' => array(1 => 'Event', 5 => 'Veranstaltung',), // the root of event topics
    'theater' => array(1 => 'Theater', 5 => 'Theater',),
    'exhibition' => array(1 => 'Exhibition', 5 => 'Ausstellung',),
    'party' => array(1 => 'Party', 5 => 'Party',),
    'music' => array(1 => 'Music', 5 => 'Musik',),
    'concert' => array(1 => 'Concert', 5 => 'Konzert',),
    'movie' => array(1 => 'Movie', 5 => 'Movie',),
);

// changes via SysPrefs: EventCat$LangId$EventCat, e.g.
// EventCat1Event, EventCat1Theater, EventCat5Concert, ...

