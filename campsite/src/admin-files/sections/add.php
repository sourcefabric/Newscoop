<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_tinymce.php");

if (!$g_user->hasPermission('ManageSection')) {
	camp_html_display_error(getGS("You do not have the right to add sections."));
	exit;
}

$f_publication_id = Input::Get('Pub', 'int', 0);
$f_issue_number = Input::Get('Issue', 'int', 0);
$f_language_id = Input::Get('Language', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$newSectionNumber = Section::GetUnusedSectionNumber($f_publication_id, $f_issue_number);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top(getGS('Add new section'), $topArray, true, true, array(getGS("Sections") => "/$ADMIN/sections/?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id"));

$languageObj = new Language($f_language_id);
if (!is_object($languageObj)) {
  $languageObj = new Language(1);
}
$editorLanguage = camp_session_get('TOL_Language', $languageObj->getCode());
editor_load_tinymce('f_description', $g_user, 0, $editorLanguage, 'section');
?>
<P>
<FORM NAME="section_add" METHOD="POST" ACTION="do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new section"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" SIZE="32" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', "'".getGS('Name')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" VALIGN="TOP"><?php putGS("Description"); ?>:</TD>
	<TD>
	  <TEXTAREA NAME="f_description"
		ID="f_description"
		rows="20" cols="80"></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_number" VALUE="<?php  p($newSectionNumber); ?>" SIZE="5"  alt="number|0" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('Number')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_url_name" SIZE="32" VALUE="<?php  p($newSectionNumber); ?>" alt="blank" emsg="<?php putGS('You must fill in the $1 field.',"'".getGS('URL Name')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
	<TD>
	<INPUT TYPE="checkbox" NAME="f_add_subscriptions" class="input_checkbox"> <?php  putGS("Add section to all subscriptions."); ?>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
        <INPUT TYPE="HIDDEN" NAME="f_language_selected" ID="f_language_selected" VALUE="<?php p($editorLanguage); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.section_add.f_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
