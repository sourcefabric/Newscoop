<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');

if (!$g_user->hasPermission('SyncPhorumUsers')) {
    camp_html_display_error(getGS('You do not have the right to sync Campsite and Phorum users.'));
	exit;
}

User::SyncPhorumUsers();

?>
