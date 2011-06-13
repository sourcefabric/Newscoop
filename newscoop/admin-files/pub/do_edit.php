<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/TimeUnit.php");
require_once($GLOBALS['g_campsiteDir']."/classes/UrlType.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Language.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to change publication information."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$TOL_Language = camp_session_get('TOL_Language', 'en');
$f_name = trim(Input::Get('f_name'));
$f_default_alias = Input::Get('f_default_alias', 'int');
$f_language = Input::Get('f_language', 'int');
$f_url_type = Input::Get('f_url_type', 'int');

$publicationObj = new Publication($f_publication_id);
if( Saas::singleton()->hasPermission("ManagePublicationSubscriptions") ) {
    $f_time_unit = Input::Get('f_time_unit');
	$f_unit_cost = trim(Input::Get('f_unit_cost', 'float', '0.0'));
	$f_unit_cost_all_lang = trim(Input::Get('f_unit_cost_all_lang', 'float', '0.0'));
	$f_currency = trim(Input::Get('f_currency'));
	$f_paid = Input::Get('f_paid', 'int');
	$f_trial = Input::get('f_trial', 'int');
} else {
	if (isset($publicationObj)) {
    	$f_time_unit = $publicationObj->getTimeUnit();
		$f_unit_cost = p($publicationObj->getUnitCost());
		$f_unit_cost_all_lang = p($publicationObj->getUnitCostAllLang());
		$f_currency = p(htmlspecialchars($publicationObj->getCurrency()));
		$f_paid = p($publicationObj->getPaidTime());
		$f_trial = p($publicationObj->getTrialTime());
    } else {
		$f_time_unit = 'D';
		$f_unit_cost = '';
		$f_unit_cost_all_lang = '';
		$f_currency	= '';
		$f_paid = '';
		$f_trial = '';
    }
}

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

$backLink = "/$ADMIN/pub/edit.php?Pub=$f_publication_id";
$errorMsgs = array();
$updated = false;
if (empty($f_name)) {
    camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'));
}
if (empty($f_default_alias)) {
	camp_html_add_msg(getGS('You must fill in the $1 field.','<B>'.getGS('Site').'</B>'));
}

$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_add_msg(getGS('Publication does not exist.'));
}

if ($f_default_alias != $publicationObj->getDefaultAliasId()) {
	camp_is_alias_conflicting($f_default_alias);
}
if ($f_name != $publicationObj->getName()) {
	camp_is_publication_conflicting($f_name);
}

if (camp_html_has_msgs()) {
      camp_html_goto_page($backLink);
}

<<<<<<< HEAD
$forum = new Phorum_forum($publicationObj->getForumId());
if (!$forum->exists()) {
	$forum = camp_forum_create($publicationObj);
}
$forum->setName($f_name);
$forum->setIsVisible($f_comments_enabled);
$publicationObj->setPublicComments($f_comments_public_enabled);

$setting = new Phorum_setting('mod_emailcomments', 'S');
if (!$setting->exists()) {
    $setting->create();
}
$setting->update(array('addresses' => array($forum->getForumId() => $f_comments_moderator_to)));
$setting->update(array('from_addresses' => array($forum->getForumId() => $f_comments_moderator_from)));
=======
//$publicationObj->setPublicComments($f_comments_public_enabled);
>>>>>>> devel

$columns = array('Name' => $f_name,
				 'IdDefaultAlias' => $f_default_alias,
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
                 'seo' => serialize($f_seo),
            );

$updated = $publicationObj->update($columns);
if ($updated) {
	camp_html_add_msg(getGS("Publication updated"), "ok");
} else {
	$errorMsg = getGS('The publication information could not be updated.');
	camp_html_add_msg($errorMsg);
}
camp_html_goto_page($backLink);
?>