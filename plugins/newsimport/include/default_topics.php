<?php
/**
 * topics for categories
 */
$newsimport_default_cat_names = array();

// the first one item in each subarray shall be a root topic
$newsimport_default_cat_names['general'] = array(
    // do not change keys; num ids are for topic names by language
    'event' => array(1 => 'Event', 5 => 'Veranstaltung',), // the root of event topics
    'theater' => array(1 => 'Theater Event', 5 => 'Theater Veranstaltung',),
    'exhibition' => array(1 => 'Exhibition Event', 5 => 'Ausstellung Veranstaltung',),
    'party' => array(1 => 'Party Event', 5 => 'Party Veranstaltung',),
    'music' => array(1 => 'Music Event', 5 => 'Musik Veranstaltung',),
    'concert' => array(1 => 'Concert Event', 5 => 'Konzert Veranstaltung',),
    //'movie' => array(1 => 'Movie Event', 5 => 'Movie Veranstaltung',),
    'circus' => array(1 => 'Circus Event', 5 => 'Zirkus Veranstaltung'),
    'other' => array(1 => 'Other Event', 5 => 'Andere Veranstaltung'),
);

$newsimport_default_cat_names['movie'] = array(
    // do not change keys; num ids are for topic names by language
    'cinema' => array(1 => 'Cinema', 5 => 'Kino',), // the root of cinema topics
    'adventure' => array(1 => 'Adventure Movie', 5 => 'Abenteuer Film',),
    'action' => array(1 => 'Action Movie', 5 => 'Action Film',),
    'adult' => array(1 => 'Adult Movie', 5 => 'Adult Film',),
    'animation' => array(1 => 'Animation Movie', 5 => 'Animation Film',),
    'biography' => array(1 => 'Biography Movie', 5 => 'Biografie Film',),
    'crime' => array(1 => 'Crime Movie', 5 => 'Crime Film',),
    'documentary' => array(1 => 'Documentary Movie', 5 => 'Dokumentation Film',),
    'drama' => array(1 => 'Drama Movie', 5 => 'Drama Film',),
    'family' => array(1 => 'Family Movie', 5 => 'Familienfilm Film',),
    'fantasy' => array(1 => 'Fantasy Movie', 5 => 'Fantasy Film',),
    'film_noir' => array(1 => 'Film-Noir Movie', 5 => 'Film-Noir Film',),
    'history' => array(1 => 'History Movie', 5 => 'Historischer Film',),
    'horror' => array(1 => 'Horror Movie', 5 => 'Horror Film',),
    'comedy' => array(1 => 'Comedy Movie', 5 => 'Komödie Film',),
    'war' => array(1 => 'War Movie', 5 => 'Kriegsfilm Film',),
    'short' => array(1 => 'Short Movie', 5 => 'Kurzfilm Film',),
    'musical' => array(1 => 'Musical Movie', 5 => 'Musical Film',),
    'music' => array(1 => 'Music Movie', 5 => 'Musikfilm Film',),
    'mystery' => array(1 => 'Mystery Movie', 5 => 'Mystery Film',),
    'romance' => array(1 => 'Romance Movie', 5 => 'Romanze Film',),
    'sci_fi' => array(1 => 'Sci-Fi Movie', 5 => 'Sci-Fi Film',),
    'sport' => array(1 => 'Sport Movie', 5 => 'Sport Film',),
    'thriller' => array(1 => 'Thriller Movie', 5 => 'Thriller Film',),
    'western' => array(1 => 'Western Movie', 5 => 'Western Film',),
    'other' => array(1 => 'Other Movie', 5 => 'Anderer Film'),
);

$newsimport_default_cat_names['regions'] = array(
    // do not change keys; num ids are for topic names by language
    'region' => array(1 => 'Swiss Region', 5 => 'Region der Schweiz',), // the root of cinema topics
    'basel_region' => array(1 => 'Basel Region', 5 => 'Region Basel',),
    'basel_stadt' => array(1 => 'Basel-Stadt Canton', 5 => 'Kanton Basel-Stadt',),
    'basel_landschaft' => array(1 => 'Basel-Landschaft Canton', 5 => 'Kanton Basel-Landschaft',),
    'aargau' => array(1 => 'Aargau Canton', 5 => 'Kanton Aargau',),
    'appenzell_ausserrhoden' => array(1 => 'Appenzell Ausserrhoden Canton', 5 => 'Kanton Appenzell Ausserrhoden',),
    'appenzell_innerrhoden' => array(1 => 'Appenzell Innerrhoden Canton', 5 => 'Kanton Appenzell Innerrhoden',),
    'bern' => array(1 => 'Bern Canton', 5 => 'Kanton Bern',),
    'freiburg' => array(1 => 'Fribourg Canton', 5 => 'Kanton Freiburg',),
    'genf' => array(1 => 'Geneva Canton', 5 => 'Kanton Genf',),
    'glarus' => array(1 => 'Glarus Canton', 5 => 'Kanton Glarus',),
    'graubuenden' => array(1 => 'Graubünden Canton', 5 => 'Kanton Graubünden',),
    'jura' => array(1 => 'Jura Canton', 5 => 'Kanton Jura',),
    'luzern' => array(1 => 'Lucerne Canton', 5 => 'Kanton Luzern',),
    'neuenburg' => array(1 => 'Neuchâtel Canton', 5 => 'Kanton Neuenburg',),
    'nidwalden' => array(1 => 'Nidwalden Canton', 5 => 'Kanton Nidwalden',),
    'obwalden' => array(1 => 'Obwalden Canton', 5 => 'Kanton Obwalden',),
    'schaffhausen' => array(1 => 'Schaffhausen Canton', 5 => 'Kanton Schaffhausen',),
    'schwyz' => array(1 => 'Schwyz Canton', 5 => 'Kanton Schwyz',),
    'solothurn' => array(1 => 'Solothurn Canton', 5 => 'Kanton Solothurn',),
    'st_gallen' => array(1 => 'St. Gallen Canton', 5 => 'Kanton St. Gallen',),
    'tessin' => array(1 => 'Ticino Canton', 5 => 'Kanton Tessin',),
    'thurgau' => array(1 => 'Thurgau Canton', 5 => 'Kanton Thurgau',),
    'uri' => array(1 => 'Uri Canton', 5 => 'Kanton Uri',),
    'waadt' => array(1 => 'Vaud Canton', 5 => 'Kanton Waadt',),
    'wallis' => array(1 => 'Valais Canton', 5 => 'Kanton Wallis',),
    'zug' => array(1 => 'Zug Canton', 5 => 'Kanton Zug',),
    'zuerich' => array(1 => 'Zurich Canton', 5 => 'Kanton Zürich',),
);
