<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
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
$f_comments_public_moderated = Input::Get('f_comments_public_moderated', 'checkbox', 'numeric');
$f_comments_subscribers_moderated = Input::Get('f_comments_subscribers_moderated', 'checkbox', 'numeric');

$created = false;
$errorMsgs = array();
$backLink = "/$ADMIN/pub/add.php";

if (empty($f_name)) {
	camp_html_add_msg(getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'));
}

if (empty($f_default_alias)) {
	camp_html_add_msg(getGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'));
}

if (camp_html_has_msgs()) {
	camp_html_goto_page($backLink);
}

$aliasId = Alias::AliasExists($f_default_alias);
if (!empty($aliasId)) {
      $alias =& new Alias($aliasId);
      $pub = $alias->getPublicationId();
      $pubObj =& new Publication($pub);
      $aliasLink = "<A HREF=\"/$ADMIN/pub/edit.php?Pub=$pub\">". $pubObj->getName() ."</A>";      
      $msg = getGS('The publication alias conflicts with another publication');
      $msg .= ': '. $aliasLink;
      camp_html_add_msg($msg);
      camp_html_goto_page($backLink);
}

$aliases = Alias::GetAliases(null, null, $f_default_alias);

if (count($aliases) <= 0) {
 
    $alias =& new Alias();
	$alias->create(array('Name' => $f_default_alias));
	$newPub =& new Publication();
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
				     'comments_public_moderated' => $f_comments_public_moderated);
	$created = $newPub->create($columns);
	if ($created) {
		$alias->setPublicationId($newPub->getPublicationId());
		camp_html_goto_page("/$ADMIN/pub/edit.php?Pub=".$newPub->getPublicationId());
	} else {
		$alias->delete();
		camp_html_add_msg(getGS('The publication could not be added.').' '.getGS('Please check if another publication with the same name or the same site name does not already exist.'));
	}
} else {
	camp_html_add_msg(getGS('The publication could not be added.').' '.getGS('Please check if another publication with the same name or the same site name does not already exist.'));
}
 
camp_html_goto_page($backLink);
?>