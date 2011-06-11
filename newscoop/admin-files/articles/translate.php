<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');

// Optional input parameters
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_article_code = Input::Get('f_article_code', 'string', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id", true);

list($articleNumber, $languageId) = explode("_", $f_article_code);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

$articleObj = new Article($languageId, $articleNumber);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;
}

$f_publication_id = ($f_publication_id > 0) ? $f_publication_id : $articleObj->getPublicationId();
$f_issue_number = ($f_issue_number > 0) ? $f_issue_number : $articleObj->getIssueNumber();
$f_section_number = ($f_section_number > 0) ? $f_section_number : $articleObj->getSectionNumber();

if ($f_publication_id > 0) {
	$publicationObj = new Publication($f_publication_id);
	if (!$publicationObj->exists()) {
		camp_html_display_error(getGS('Publication does not exist.'), $BackLink);
		exit;
	}

	$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
	if (!$issueObj->exists()) {
		camp_html_display_error(getGS('No such issue.'), $BackLink);
		exit;
	}

	$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
	if (!$sectionObj->exists()) {
		camp_html_display_error(getGS('No such section.'), $BackLink);
		exit;
	}
}

if (!$g_user->hasPermission("TranslateArticle")) {
	$errorStr = getGS('You do not have the right to translate articles.');
	camp_html_display_error($errorStr, $BackLink);
	exit;
}

// When the user selects a language the form is submitted to the same page (translation.php).
// Read article translation form input values for the case when the page has been reloaded
// because of language select.
$f_translation_title = Input::Get('f_translation_title', 'string', '', true);
$f_language_selected = Input::Get('f_translation_language', 'int', 0, true);
$f_translation_language = Input::Get('f_translation_language', 'int', 0, true);

if ($f_publication_id > 0) {
	$f_translation_issue_name = Input::Get('f_issue_name', 'string', $issueObj->getName(), true);
	$f_translation_issue_urlname = Input::Get('f_issue_urlname', 'string', $issueObj->getUrlName(), true);
	$f_translation_section_name = Input::Get('f_section_name', 'string', $sectionObj->getName(), true);
	$f_translation_section_urlname = Input::Get('f_section_urlname', 'string', $sectionObj->getUrlName(), true);
}

$allLanguages = Language::GetLanguages(null, null, null, array(),
array(array('field'=>'byname', 'dir'=>'asc')), true);
$articleLanguages = $articleObj->getLanguages();
$articleLanguages = DbObjectArray::GetColumn($articleLanguages, "Id");

if ( ($f_language_selected > 0) && ($f_issue_number > 0) ) {
	$translationIssueObj = new Issue($f_publication_id, $f_language_selected, $f_issue_number);
	$translationSectionObj = new Section($f_publication_id, $f_issue_number, $f_language_selected, $f_section_number);
}

if ($f_publication_id > 0) {
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
					  'Section' => $sectionObj, 'Article'=>$articleObj);
	camp_html_content_top(getGS('Translate article'), $topArray, true, true);
} else {
	$crumbs = array();
	$crumbs[] = array(getGS("Actions"), "");
	$crumbs[] = array(getGS('Translate article'), "");
	echo camp_html_breadcrumbs($crumbs);
}
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
	<?php if ($f_publication_id > 0) { ?>
	<td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo "index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><b><?php putGS("Article List"); ?></b></a></td>
	<?php } ?>
	<td <?php if ($f_publication_id > 0) { ?>style="padding-left: 20px;"<?php } ?>><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php putGS("Back to Edit Article"); ?></b></a></td>
</table>

<?php camp_html_display_msgs(); ?>
<P>
<FORM NAME="article_translate" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/articles/do_translate.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT TYPE="HIDDEN" NAME="f_article_code" VALUE="<?php  p($f_article_code); ?>">
<?php if ($f_publication_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<input type='hidden' name='f_publication_id' value="<?php p($f_publication_id); ?>">
<input type='hidden' name='f_issue_number' value="<?php p($f_issue_number); ?>">
<input type='hidden' name='f_section_number' value="<?php p($f_section_number); ?>">
<?php } ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Translate article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Article name ($1)", $articleObj->getLanguageName()); ?>:</TD>
	<TD><?PHP p(htmlspecialchars($articleObj->getTitle())); ?></td>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New article name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_translation_title" SIZE="32" value="<?php echo htmlspecialchars($f_translation_title); ?>" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
    <SELECT NAME="f_translation_language" class="input_select" alt="select" <?php if ($f_publication_id > 0) { ?>ONCHANGE="this.form.action = '/<?php echo $ADMIN; ?>/articles/translate.php'; this.form.submit();"<?php } ?> emsg="<?php putGS("You must choose a language"); ?>">
	<option></option>
	<?php
	// Show all the languages that have not yet been translated.
	$displayLanguages = array();
	foreach ($allLanguages as $language) {
		if (!in_array($language->getLanguageId(), $articleLanguages)) {
			$displayLanguages[$language->getLanguageId()] = $language->getNativeName();
		}
	}
	asort($displayLanguages);
	foreach ($displayLanguages as $tmpLangId => $nativeName) {
		camp_html_select_option($tmpLangId, $f_language_selected, $nativeName);
	}
	?>
	</SELECT>
	</TD>
</TR>
<?php
	$canCreate = true;
	if ( ($f_language_selected > 0) && ($f_issue_number > 0) ) {
		// Every article must live inside a cooresponding issue of the same language.
		if (!$translationIssueObj->exists()) {

			if ($g_user->hasPermission("ManageIssue")) {

				// If a section needs to be translated, but the user doesnt have the permission
				// to create a section, then we dont want to display anything here.  Even
				// if they can create the issue, they still need to create a cooresponding section.
				// If they dont have the permission to do that, then no use in creating the issue.
				if ($translationSectionObj->exists() || $g_user->hasPermission("ManageSection")) {
?>
<TR>
	<TD colspan="2" align="left" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><strong><?php putGS("An issue must be created for the selected language.  Please enter the issue name and URL name."); ?></strong></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New issue name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_issue_name" SIZE="32" value="<?php echo htmlspecialchars($f_translation_issue_name) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Issue Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New issue URL name"); ?>:</TD>
	<TD valign="bottom">
	<INPUT TYPE="TEXT" NAME="f_issue_urlname" SIZE="20" value="<?php echo htmlspecialchars($f_translation_issue_urlname) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Issue URL Name')); ?>">
	</TD>
</TR>
<?php
				}
			} else {
				$canCreate = false;
?>
<TR>
	<TD colspan="2" align="left" class="error_message" style="padding-left: 40px; padding-right: 40px; padding-top: 20px; padding-bottom: 20px;"><?php putGS('An issue must be created for the selected language but you do not have the right to create an issue.'); ?></TD>
</TR>
<?php
			}
		}

		if (!$translationSectionObj->exists()) {

			if ($g_user->hasPermission("ManageSection")) {

				// If an issue needs to be translated, but the user doesnt have the permission
				// to create an issue, then we dont want to display anything here.  Even
				// if they can create the section, they still need to create a cooresponding issue.
				if ($translationIssueObj->exists() || $g_user->hasPermission("ManageIssue")) {
?>
<TR>
	<TD colspan="2" align="left" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><strong><?php putGS("A section must be created for the selected language.  Please enter the section name and URL name."); ?></strong></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New section name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_section_name" SIZE="32" maxlength="255" value="<?php echo htmlspecialchars($f_translation_section_name) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Section Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New section URL name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_section_urlname" SIZE="20" value="<?php echo htmlspecialchars($f_translation_section_urlname) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Section URL Name')); ?>">
	</TD>
</TR>
<?php
				}
			} else {
				$canCreate = false;
?>
<TR>
	<TD colspan="2" align="left" class="error_message" style="padding-left: 40px; padding-right: 40px; padding-top: 20px; padding-bottom: 20px;"><?php putGS('A section must be created for the selected language but you do not have the right to create a section.'); ?></TD>
</TR>
<?php
			}
		}
?>
<?php
	}
	if ($canCreate) {
?>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
<?php
	}
?>
</TABLE>
</FORM>
<P>
<script>
document.article_translate.f_translation_title.focus();
</script>
<?php camp_html_copyright_notice(); ?>
