<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SubscriptionDefaultTime.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Country.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to change publication information.", array(), 'pub'));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Language = Input::Get('Language', 'int', 1, true);
$CountryCode = Input::Get('CountryCode');
$cPaidTime = Input::Get('cPaidTime', 'int', 0);
$cTrialTime = Input::Get('cTrialTime', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$defaultTime = new SubscriptionDefaultTime($CountryCode, $Pub);

$defaultTime->setTrialTime($cTrialTime);
$defaultTime->setPaidTime($cPaidTime);
$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('publication');
camp_html_add_msg($translator->trans("Country subscription settings updated.", array(), 'pub'), "ok");
camp_html_goto_page("/$ADMIN/pub/deftime.php?Pub=$Pub&Language=$Language");
?>