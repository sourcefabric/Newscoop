<?php

require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SubscriptionDefaultTime.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Country.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');

$publicationObj =& new Publication($Pub);
$defaultTime =& new SubscriptionDefaultTime($CountryCode, $Pub);
$defaultTime->delete();
// title: "Deleting subscription default time"

$logtext = getGS('Subscription default time for $1 deleted', $publicationObj->getName().':'.$CountryCode);
Log::Message($logtext, $g_user->getUserName(), 5);
header("Location: /$ADMIN/pub/deftime.php?Pub=$Pub&Language=$Language");
exit;
?>