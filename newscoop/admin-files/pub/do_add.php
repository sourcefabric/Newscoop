<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManagePub')  || !SaaS::singleton()->hasPermission("AddPub")) {
	camp_html_display_error(getGS("You do not have the right to add publications."));
	exit;
}

$f_name = trim(Input::Get('f_name'));
$f_default_alias = trim(Input::Get('f_default_alias'));
$f_language = Input::Get('f_language', 'int');
$f_url_type = Input::Get('f_url_type', 'int', 0);


$f_time_unit = Input::Get('f_time_unit', 'string', null, true);
$f_unit_cost = Input::Get('f_unit_cost', 'string', null, true);
$f_unit_cost_all_lang = Input::Get('f_unit_cost_all_lang', 'string', null, true);
$f_currency = Input::Get('f_currency', 'string', null, true);
$f_paid = Input::Get('f_paid', 'int', null, true);
$f_trial = Input::Get('f_trial', 'int', null, true);
$f_comments_enabled = Input::Get('f_comments_enabled', 'checkbox', 'numeric');
$f_comments_article_default = Input::Get('f_comments_article_default', 'checkbox', 'numeric');
$f_comments_public_enabled = Input::Get('f_comments_public_enabled', 'checkbox', 'numeric');
$f_comments_public_moderated = Input::Get('f_comments_public_moderated', 'checkbox', 'numeric');
$f_comments_subscribers_moderated = Input::Get('f_comments_subscribers_moderated', 'checkbox', 'numeric');
$f_comments_captcha_enabled = Input::Get('f_comments_captcha_enabled', 'checkbox', 'numeric');
$f_comments_spam_blocking_enabled = Input::Get('f_comments_spam_blocking_enabled', 'checkbox', 'numeric');
$f_comments_moderator_to = Input::Get('f_comments_moderator_to', 'text', 'string');
$f_comments_moderator_from = Input::Get('f_comments_moderator_from', 'text', 'string');
$f_seo = Input::Get('f_seo', 'array', array(), true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$backLink = "/$ADMIN/pub/add.php";

if (empty($f_name)) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
}

if (empty($f_default_alias)) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Site').'</B>'));
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}

camp_is_alias_conflicting($f_default_alias);
camp_is_publication_conflicting($f_name);
if (camp_html_has_msgs()) {
      camp_html_goto_page($backLink);
}

$alias = new Alias();
$alias->create(array('Name' => $f_default_alias));
$publicationObj = new Publication();
$columns = array('Name' => $f_name,
				 'IdDefaultAlias'=> $alias->getId(),
				 'IdDefaultLanguage' => $f_language,
				 'IdURLType' => $f_url_type,
				 'TimeUnit' => $f_time_unit,
				 'PaidTime' => $f_paid,
				 'TrialTime' => $f_trial,
				 'UnitCost' => $f_unit_cost,
				 'UnitCostAllLang' => $f_unit_cost_all_lang,
				 'Currency' => $f_currency,
                 'comments_enabled' => $f_comments_enabled,
			     'comments_article_default_enabled'=> $f_comments_article_default,
			     'comments_subscribers_moderated' => $f_comments_subscribers_moderated,
			     'comments_public_moderated' => $f_comments_public_moderated,
                 'comments_public_enabled' => $f_comments_public_enabled,
			     'comments_captcha_enabled' => $f_comments_captcha_enabled,
				 'comments_spam_blocking_enabled' => $f_comments_spam_blocking_enabled,
                 'comments_moderator_to' => $f_comments_moderator_to,
                 'comments_moderator_from' => $f_comments_moderator_from,
                 'seo' => serialize($f_seo));

$created = $publicationObj->create($columns);
if ($created) {
	$alias->setPublicationId($publicationObj->getPublicationId());
 	camp_html_add_msg("Publication created.", "ok");
	camp_html_goto_page("/$ADMIN/pub/edit.php?Pub=".$publicationObj->getPublicationId());
} else {
	$alias->delete();
	camp_html_add_msg(getGS('The publication could not be added.'));
	camp_html_goto_page($backLink);
}

?>