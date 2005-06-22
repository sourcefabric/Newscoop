<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteIssue')) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to delete issues.'));
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid Input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

CampsiteInterface::ContentTop(getGS('Delete issue'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete issue"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Are you sure you want to delete the issue $1?','<B>'.htmlspecialchars($issueObj->getName()).'</B>'); ?></LI></BLOCKQUOTE></TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<FORM METHOD="POST" ACTION="do_del.php">
	<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php p($Pub); ?>">
	<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php p($Language); ?>">
	<INPUT TYPE="submit" class="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>">
	<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php p($Pub); ?>'">
	</FORM>
	</DIV>
	</TD>
</TR>
</TABLE></CENTER>
<P>
<?php CampsiteInterface::CopyrightNotice(); ?>