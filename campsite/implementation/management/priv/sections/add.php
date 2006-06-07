<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");

if (!$g_user->hasPermission('ManageSection')) {
	camp_html_display_error(getGS("You do not have the right to add sections."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$newSectionNumber = Section::GetUnusedSectionNumber($Pub, $Issue, $Language);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top(getGS('Add new section'), $topArray, true, true, array(getGS("Sections") => "/$ADMIN/sections/?Pub=$Pub&Issue=$Issue&Language=$Language"));

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new section"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" alt="blank" emsg="<?php putGS('You must complete the $1 field.', "'".getGS('Name')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cNumber" VALUE="<?php  p($newSectionNumber); ?>" SIZE="5" MAXLENGTH="5" alt="number|0" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('Number')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" VALUE="<?php  p($newSectionNumber); ?>" alt="blank" emsg="<?php putGS('You must complete the $1 field.',"'".getGS('URL Name')."'"); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
	<TD>
	<INPUT TYPE="checkbox" NAME="cSubs" class="input_checkbox"> <?php  putGS("Add section to all subscriptions."); ?>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>