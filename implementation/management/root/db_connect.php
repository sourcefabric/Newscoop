<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');

mysql_connect($Campsite['DATABASE_SERVER_ADDRESS'], $Campsite['DATABASE_USER'],
	$Campsite['DATABASE_PASSWORD']);
mysql_select_db($Campsite['DATABASE_NAME']);

?>