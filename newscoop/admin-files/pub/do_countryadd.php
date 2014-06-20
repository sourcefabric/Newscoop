<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/SubscriptionDefaultTime.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to manage publications.", array(), 'pub'));
	exit;
}

$cPub = Input::Get('cPub', 'int');
$Language = Input::Get('Language', 'int', 1, true);
$cCountryCode = trim(Input::Get('cCountryCode'));
$cTrialTime = Input::Get('cTrialTime', 'int', 0);
$cPaidTime = Input::Get('cPaidTime', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$backLink = "/$ADMIN/pub/countryadd.php?Pub=$cPub&Language=$Language";
$publicationObj = new Publication($cPub);

if (empty($cCountryCode)) {
	camp_html_add_msg($translator->trans('You must select a country.', array(), 'pub'));
	camp_html_goto_page($backLink);
}

$values = array('TrialTime' => $cTrialTime,
				'PaidTime' => $cPaidTime);
$defaultTime = new SubscriptionDefaultTime($cCountryCode, $cPub);
if ($defaultTime->exists()) {
	$defaultTime->update($values);
} else {
	$created = $defaultTime->create($values);
    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('publication');
	if (!$created) {
    	camp_html_add_msg($translator->trans("The subscription settings for $1 could not be added.", array('$1' => $publicationObj->getName().':'.$cCountryCode), 'pub'));
    	camp_html_goto_page($backLink);
	}
}
camp_html_add_msg($translator->trans("Country subscription settings updated.", array(), 'pub'), "ok");
camp_html_goto_page("/$ADMIN/pub/deftime.php?Pub=$cPub&Language=$Language");

?>