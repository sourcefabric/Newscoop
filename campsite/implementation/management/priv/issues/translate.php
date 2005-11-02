<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$allIssues = Issue::GetIssues($Pub, null, $Issue);
$unusedLanguages = $issueObj->getUnusedLanguages();

camp_html_content_top(getGS('Add new translation'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" >
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new translation"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Issue"); ?>:</TD>
	<TD>
		<?php  
		$comma = 0;
		foreach ($allIssues as $tmpIssue) {
			if ($comma) {
			    print '<br>';
			}
			p(htmlspecialchars($tmpIssue->getName() .' ('.$tmpIssue->getLanguageName().')'));
			$comma =1;
		}
		?>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" size="32" maxlength="64" value="<?php echo htmlspecialchars($issueObj->getUrlName()); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
		<SELECT NAME="cLang" class="input_select"><?php 
		foreach ($unusedLanguages as $tmpLanguage) { 
			pcomboVar($tmpLanguage->getLanguageId(),'',htmlspecialchars($tmpLanguage->getNativeName()));
        }
	    ?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub);?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php  p($Pub); ?>'">-->
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>