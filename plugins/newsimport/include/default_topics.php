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
    'region' => array(1 => 'Region', 5 => 'Region',), // the root of cinema topics
    'basel_region' => array(1 => 'Region Basel', 5 => 'Region Basel',),
    'basel_stadt' => array(1 => 'Basel-Stadt', 5 => 'Basel-Stadt',),
    'basel_landschaft' => array(1 => 'Basel-Landschaft', 5 => 'Basel-Landschaft',),
    'aargau' => array(1 => 'Aargau', 5 => 'Aargau',),
    'appenzell_ausserrhoden' => array(1 => 'Appenzell Ausserrhoden', 5 => 'Appenzell Ausserrhoden',),
    'appenzell_innerrhoden' => array(1 => 'Appenzell Innerrhoden', 5 => 'Appenzell Innerrhoden',),
    'bern' => array(1 => 'Bern', 5 => 'Bern',),
    'freiburg' => array(1 => 'Freiburg', 5 => 'Freiburg',),
    'genf' => array(1 => 'Genf', 5 => 'Genf',),
    'glarus' => array(1 => 'Glarus', 5 => 'Glarus',),
    'graubuenden' => array(1 => 'Graubünden', 5 => 'Graubünden',),
    'jura' => array(1 => 'Jura', 5 => 'Jura',),
    'luzern' => array(1 => 'Luzern', 5 => 'Luzern',),
    'neuenburg' => array(1 => 'Neuenburg', 5 => 'Neuenburg',),
    'nidwalden' => array(1 => 'Nidwalden', 5 => 'Nidwalden',),
    'obwalden' => array(1 => 'Obwalden', 5 => 'Obwalden',),
    'schaffhausen' => array(1 => 'Schaffhausen', 5 => 'Schaffhausen',),
    'schwyz' => array(1 => 'Schwyz', 5 => 'Schwyz',),
    'solothurn' => array(1 => 'Solothurn', 5 => 'Solothurn',),
    'st_gallen' => array(1 => 'St. Gallen', 5 => 'St. Gallen',),
    'tessin' => array(1 => 'Tessin', 5 => 'Tessin',),
    'thurgau' => array(1 => 'Thurgau', 5 => 'Thurgau',),
    'uri' => array(1 => 'Uri', 5 => 'Uri',),
    'waadt' => array(1 => 'Waadt', 5 => 'Waadt',),
    'wallis' => array(1 => 'Wallis', 5 => 'Wallis',),
    'zug' => array(1 => 'Zug', 5 => 'Zug',),
    'zuerich' => array(1 => 'Zürich', 5 => 'Zürich',),
);
