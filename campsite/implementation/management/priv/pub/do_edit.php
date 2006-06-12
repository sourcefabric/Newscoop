<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/UrlType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to change publication information."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$TOL_Language = Input::Get('TOL_Language');
$f_name = trim(Input::Get('f_name'));
$f_default_alias = Input::Get('f_default_alias', 'int');
$f_language = Input::Get('f_language', 'int');
$f_url_type = Input::Get('f_url_type', 'int');
$f_time_unit = Input::Get('f_time_unit');
$f_unit_cost = trim(Input::Get('f_unit_cost', 'float', '0.0'));
$f_unit_cost_all_lang = trim(Input::Get('f_unit_cost_all_lang', 'float', '0.0'));
$f_currency = trim(Input::Get('f_currency'));
$f_paid = Input::Get('f_paid', 'int');
$f_trial = Input::get('f_trial', 'int');
$f_comments_enabled = Input::Get('f_comments_enabled', 'checkbox', 'numeric');
$f_comments_article_default = Input::Get('f_comments_article_default', 'checkbox', 'numeric');
$f_comments_public_moderated = Input::Get('f_comments_public_moderated', 'checkbox', 'numeric');
$f_comments_subscribers_moderated = Input::Get('f_comments_subscribers_moderated', 'checkbox', 'numeric');

$errorMsgs = array();
$correct = true;
$updated = false;
if (empty($f_name)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
}
if (empty($f_default_alias)) {
	$correct = false;
	$errorMsgs = getGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>');
}

$publicationObj =& new Publication($f_publication_id);
if ($correct) {
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
					 'comments_public_moderated' => $f_comments_public_moderated);
	$updated = $publicationObj->update($columns);
	$gotoPage = "/$ADMIN/pub/edit.php?Pub=$f_publication_id";
	if ($updated) {
		camp_html_add_msg(getGS("Publication updated"), "ok");
	} else {
		$errorMsg = getGS('The publication information could not be updated.')
				  .' '.getGS('Please check if another publication with the same name or the same site name does not already exist.');
		camp_html_add_msg($errorMsg);
	}
	camp_html_goto_page($gotoPage);
}

echo camp_html_content_top(getGS("Changing publication information"), array("Pub" => $publicationObj));
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing publication information"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?> </li>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/edit.php?Pub=<?php  p($f_publication_id); ?>'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
