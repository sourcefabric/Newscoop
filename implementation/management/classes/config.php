<?php

require($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

$scheme = (substr($_SERVER['REQUEST_URI'], 0, 5) == 'https')?'https://':'http://';

$Campsite['hostname'] = $_SERVER['SERVER_NAME'];
$Campsite['website_url'] = $scheme.$Campsite['hostname'];

$Campsite['version'] = '2.2';

?>