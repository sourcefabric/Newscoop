<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid Input: $1', Input::GetErrorString()));
	exit;
}

if (!$User->hasPermission('ManageIssue') || !$User->hasPermission('Publish')) {
	$BackLink ="/admin/pub/issues/?Pub=$Pub&Language=$Language";
	CampsiteInterface::DisplayError('You do not have the right to change issues.');
	exit;
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$issueObj->setPublished();

$logtext = getGS('Issue $1 Published: $2  changed status',$issueObj->getIssueId().'. '.$issueObj->getName().' ('.$issueObj->getLanguageName().')',$issueObj->getPublished()); 
Log::Message($logtext, $User->getUserName(), 14);

if ($issueObj->getPublished() == 'Y') {
	$t2=getGS('Not published');
	$t3=getGS('Published');
}
else {
	$t2=getGS('Published');
	$t3=getGS('Not published');
} 

CampsiteInterface::ContentTop('Changing issue status', array('Pub' => $publicationObj, 'Issue' => $issueObj));
?> 

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing issue status"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE><LI>
		<?php  putGS('Status of the issue $1 has been changed from $2 to $3','<B>'.$issueObj->getIssueId().'. '.htmlspecialchars($issueObj->getName()).' ('.htmlspecialchars($issueObj->getLanguageName()).')</B>',"<B>$t2</B>","<B>$t3</B>"); ?>
		</LI></BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
   		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>