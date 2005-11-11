<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_article_code = Input::Get('f_article_code', 'string', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/", true);

list($articleNumber, $languageId) = split("_", $f_article_code);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}
$publicationObj =& new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'), $BackLink);
	exit;	
}

$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('No such issue.'), $BackLink);
	exit;	
}

$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('No such section.'), $BackLink);
	exit;		
}

$articleObj =& new Article($languageId, $articleNumber);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;
}

if (!$User->hasPermission("AddArticle")) {
	$errorStr = getGS('You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users.');
	camp_html_display_error($errorStr, $BackLink);
	exit;	
}

$allLanguages = Language::GetLanguages();
$articleLanguages = $articleObj->getLanguages();
$articleLanguages = DbObjectArray::GetColumn($articleLanguages, "Id");

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top(getGS('Translate article'), $topArray, true, true);
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<INPUT TYPE="HIDDEN" NAME="f_article_code" VALUE="<?php  p($f_article_code); ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Translate article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Article name"); ?>:</TD>
	<TD><?PHP p(htmlspecialchars($articleObj->getTitle())); ?></td>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New article name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_translation_title" SIZE="32" MAXLENGTH="64" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	<SELECT NAME="f_translation_language" class="input_select" alt="select" emsg="<?php putGS("You must choose a language"); ?>">
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
		camp_html_select_option($tmpLangId, '', $nativeName);
	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
	<!--<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='<?php  p($BackLink); ?>'">-->
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>