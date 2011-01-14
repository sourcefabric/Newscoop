<?php
camp_load_translation_strings("user_subscription_sections");
require_once($GLOBALS['g_campsiteDir']. '/classes/Input.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Subscription.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/SubscriptionSection.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Language.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir']."/db_connect.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageSubscriptions')) {
	camp_html_display_error(getGS("You do not have the right to delete subscriptions."));
	exit;
}

$f_user_id = Input::Get('f_user_id', 'int', 0);
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_subscription_id = Input::Get('f_subscription_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);

$publicationObj = new Publication($f_publication_id);
$languageObj = new Language($publicationObj->getDefaultLanguageId());
$manageUser = new User($f_user_id);

$subscriptionSection = new SubscriptionSection($f_subscription_id, $f_section_number, $f_language_id);
$subscriptionSection->delete();
camp_html_goto_page("/$ADMIN/users/subscriptions/sections/?f_user_id=$f_user_id&f_subscription_id=$f_subscription_id&f_publication_id=$f_publication_id");

?>