<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
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
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php  putGS("Issue List"); ?></B></A></TD>
	<TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issueObj->getIssueNumber()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>"><B><?php  echo getGS("Issue").": ".htmlspecialchars($issueObj->getName()); ?></B></A></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
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
			camp_html_select_option($tmpLanguage->getLanguageId(),'',htmlspecialchars($tmpLanguage->getNativeName()));
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
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php  p($Pub); ?>'">-->
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<P>
<?php camp_html_copyright_notice(); ?>