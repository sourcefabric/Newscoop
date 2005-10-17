<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SubscriptionDefaultTime.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Country.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to change publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');
$cPaidTime = Input::Get('cPaidTime', 'int', 0);
$cTrialTime = Input::Get('cTrialTime', 'int', 0);

$publicationObj =& new Publication($Pub);
$defaultTime =& new SubscriptionDefaultTime($CountryCode, $Pub);

$defaultTime->setTrialTime($cTrialTime);
$defaultTime->setPaidTime($cPaidTime);
$logtext = getGS('Default subscription time for $1 changed', $publicationObj->getName().':'.$CountryCode); 
Log::Message($logtext, $User->getUserName(), 6);
header("Location: /$ADMIN/pub/editdeftime.php?Pub=$Pub&Language=$Language&CountryCode=$CountryCode");
exit;
?>