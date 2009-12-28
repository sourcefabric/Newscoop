<?php

require_once($GLOBALS['g_campsiteDir']."/include/phorum_load.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Phorum_user.php");

if (!$g_user->hasPermission('SyncPhorumUsers')) {
    camp_html_display_error(getGS("You do not have the right to sync Campsite and Phorum users."));
	exit;
}

$queryStr = "SELECT u.Id, pu.user_id, u.UName, u.Password, u.EMail "
            . "FROM liveuser_users AS u LEFT JOIN phorum_users AS pu "
            . "ON u.UName = pu.username "
            . "WHERE fk_campsite_user_id IS NULL OR fk_campsite_user_id != u.Id";
$nullUsers = $g_ado_db->GetAll($queryStr);
if (is_array($nullUsers) && sizeof($nullUsers) > 0) {
    foreach ($nullUsers as $nullUser) {
        if (empty($nullUser['user_id'])) {
            $phorumUser = new Phorum_user();
            $phorumUser->create($nullUser['UName'], $nullUser['Password'],
                                $nullUser['EMail'], $nullUser['Id'], true);
        } else {
            $sql = "UPDATE phorum_users SET fk_campsite_user_id = NULL"
                 . " WHERE fk_campsite_user_id = ".$nullUser['Id'];
            $g_ado_db->Execute($sql);
        	$sql = "UPDATE phorum_users SET fk_campsite_user_id = ".$nullUser['Id']
                 . " WHERE user_id = ".$nullUser['user_id'];
            $g_ado_db->Execute($sql);
        }
    }
}

?>
