<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$f_target = Input::Get('f_target', 'string', 'index.php', true);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}

if (!$g_user->hasPermission('ManageIssue') || !$g_user->hasPermission('Publish')) {
	$BackLink ="/$ADMIN/issues/?Pub=$Pub&Language=$Language";
	camp_html_display_error($translator->trans('You do not have the right to change issues.', array(), 'issues'));
	exit;
}

$issueObj = new Issue($Pub, $Language, $Issue);
$issueObj->setWorkflowStatus();

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('issue');

camp_html_goto_page("/$ADMIN/issues/$f_target?Pub=$Pub&Issue=$Issue&Language=$Language");

?>