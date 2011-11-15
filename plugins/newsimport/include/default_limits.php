<?php
/**
 * event import configurations
 */

if (!function_exists('newsimport_getBaselRegions')) {

    function newsimport_getBaselRegions() {
        return array(
    'basel_region',
    'basel_stadt',
    'basel_landschaft',
    //'4xxx', // Region Basel
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
            'past' => 31, // removing 31 days old
            //'past' => 2, // 2 days old, testing
            'next' => 93, // 93 days adv
        ),
    ),
    'movies_1' => array(
        'categories' => array(
            //'*' => array('regions' => newsimport_getSwissRegions()),
            //'*' => array('regions' => newsimport_getBaselRegions()),
        ),
        'dates' => array(
            'past' => 1, // removing 1 day old
            'next' => 0, // no checking for days adv
        ),
    ),
);

?>