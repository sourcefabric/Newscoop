<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SubscriptionDefaultTime.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Country.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to change publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');
$cPaidTime = Input::Get('cPaidTime', 'int', 0);
$cTrialTime = Input::Get('cTrialTime', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$defaultTime = new SubscriptionDefaultTime($CountryCode, $Pub);

$defaultTime->setTrialTime($cTrialTime);
$defaultTime->setPaidTime($cPaidTime);
camp_html_add_msg(getGS("Country subscription settings updated."), "ok");
$logtext = getGS('Default subscription time for $1 changed', $publicationObj->getName().':'.$CountryCode);
Log::Message($logtext, $g_user->getUserId(), 6);
camp_html_goto_page("/$ADMIN/pub/deftime.php?Pub=$Pub&Language=$Language");
?>