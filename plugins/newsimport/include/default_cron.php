<?php

$newsimport_cron = array(
    'general' => array(
        'hour' => '4',
        //'hour' => '4,22',
        //'hour' => '4-23/4',
        'min' => '45',
    ),
    'trailers' => array(
        //'hour' => '1,23',
        //'hour' => '1-23/2',
        'hour' => '1,2,3,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23',
        'min' => '20,50',
    ),
    'preload' => array(
        'hour' => '0-23',
        'min' => '15,45',
    ),
);

