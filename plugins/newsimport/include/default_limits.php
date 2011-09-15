<?php
/**
 * event import configurations
 */

$event_data_cancel = array(
    'events_1' => array(
        array('field' => 'evett1', 'value' => 'Abgesagt'),
    ),
);

$event_data_limits = array(
    'events_1' => array(
        'categories' => array(
            'party' => array('towns' => newsimport_getBaselTowns()), // array('Basel')),
            'music' => array('towns' => newsimport_getBaselTowns()), // array('Basel')),
            'circus' => array('towns' => newsimport_getBaselTowns()), // array('Basel')),
            'other' => array('towns' => newsimport_getBaselTowns()), // array('Basel')),
        ),
        'dates' => array(
            'past' => 31, // 30 days old
            'next' => 93, // 93 days adv
        ),
    ),
);

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


?>