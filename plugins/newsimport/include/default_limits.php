<?php
/**
 * event import configurations
 */

if (!function_exists('newsimport_getSwissRegions')) {

    function newsimport_getSwissRegions() {
        return array(
    '1xxx', // Region Westschweiz (Süd)
    '2xxx', // Region Westschweiz (Nord)
    '3xxx', // Region Bern/Oberwallis
    '4xxx', // Region Basel
    '5xxx', // Region Aarau
    '6xxx', // Region Zentralschweiz, Tessin
    '7xxx', // Region Graubünden
    '8xxx', // Region Zürich
    '9xxx', // Region Ostschweiz
        );
    }

}

if (!function_exists('newsimport_getBaselRegions')) {

    function newsimport_getBaselRegions() {
        return array(
    '4xxx', // Region Basel
        );
    }

}

if (!function_exists('newsimport_getBaselTowns')) {

    function newsimport_getBaselTowns() {
        return array(
    'Basel',
    'Aesch (BL)','Aesch',
    'Allschwil',
    'Anwil',
    'Arboldswil',
    'Arisdorf',
    'Arlesheim',
    'Augst',
    'Bennwil',
    'Biel-Benken',
    'Binningen',
    'Birsfelden',
    'Blauen',
    'Böckten',
    'Bottmingen',
    'Bretzwil',
    'Brislach',
    'Bubendorf',
    'Buckten',
    'Burg im Leimental',
    'Buus',
    'Diegten',
    'Diepflingen',
    'Dittingen',
    'Duggingen',
    'Eptingen',
    'Ettingen',
    'Frenkendorf',
    'Füllinsdorf',
    'Gelterkinden',
    'Giebenach',
    'Grellingen',
    'Häfelfingen',
    'Hemmiken',
    'Hersberg',
    'Hölstein',
    'Itingen',
    'Känerkinden',
    'Kilchberg (BL)','Kilchberg',
    'Lampenberg',
    'Langenbruck',
    'Läufelfingen',
    'Laufen',
    'Lausen',
    'Lauwil',
    'Liedertswil',
    'Liesberg',
    'Liestal',
    'Lupsingen',
    'Maisprach',
    'Münchenstein','Münchenstein BS',
    'Muttenz',
    'Nenzlingen',
    'Niederdorf',
    'Nusshof',
    'Oberdorf (BL)','Oberdorf',
    'Oberwil (BL)','Oberwil',
    'Oltingen',
    'Ormalingen',
    'Pfeffingen',
    'Pratteln',
    'Ramlinsburg',
    'Reigoldswil',
    'Reinach (BL)','Reinach',
    'Rickenbach (BL)','Rickenbach',
    'Roggenburg',
    'Röschenz',
    'Rothenfluh',
    'Rümlingen',
    'Rünenberg',
    'Schönenbuch',
    'Seltisberg',
    'Sissach',
    'Tecknau',
    'Tenniken',
    'Therwil',
    'Thürnen',
    'Titterten',
    'Wahlen',
    'Waldenburg',
    'Wenslingen',
    'Wintersingen',
    'Wittinsburg',
    'Zeglingen',
    'Ziefen',
    'Zunzgen',
    'Zwingen',
        );
    }

}

$event_data_cancel = array(
    'events_1' => array(
        array('field' => 'evett1', 'value' => 'Abgesagt'),
    ),
);

$event_data_limits = array(
    'events_1' => array(
        'categories' => array(
            'party' => array('regions' => newsimport_getBaselRegions()),
            'music' => array('regions' => newsimport_getBaselRegions()),
            'circus' => array('regions' => newsimport_getBaselRegions()),
            'other' => array('regions' => newsimport_getBaselRegions()),
        ),
        'dates' => array(
            'past' => 31, // 30 days old
            //'past' => 2, // 2 days old, testing
            'next' => 93, // 93 days adv
        ),
    ),
    'movies_1' => array(
        'categories' => array(
            '*' => array('regions' => newsimport_getSwissRegions()),
            //'*' => array('regions' => newsimport_getBaselRegions()),
        ),
        'dates' => array(
            'past' => 1, // 1 day old
            'next' => 7, // 7 days adv
        ),
    ),
);

?>