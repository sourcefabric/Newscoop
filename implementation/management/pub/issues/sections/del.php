<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Template.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('DeleteSection')) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to delete sections.'));	
	exit;
}
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Section = Input::Get('Section', 'int', 0);


$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

## added by sebastian
if (function_exists ("incModFile")) {
  incModFile ();
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
CampsiteInterface::ContentTop(getGS('Delete section'), $topArray);

?>
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Delete section"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
		<FORM METHOD="POST" ACTION="do_del.php">
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Are you sure you want to delete the section $1?','<B>'.htmlspecialchars($sectionObj->getName()).'</B>'); ?></LI></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Subscriptions"); ?>:</TD>
		<TD>
		<INPUT TYPE="checkbox" checked NAME="cSubs" class="input_checkbox"> <?php  putGS("Delete section from all subscriptions."); ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>">
		<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/admin/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
		</FORM>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>