<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue  = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/index.php", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError('Article does not exist.', $BackLink);
	exit;		
}

// You are allowed to change the status of an article
// if you have publish permissions, OR you created the article
// and it is new.  
$access = ($User->hasPermission('Publish') || $articleObj->userCanModify($User));
if (!$access) {
	CampsiteInterface::DisplayError("You do not have the right to change this article status. Once submitted an article can only changed by authorized users.", $BackLink);
	exit;	
}

if ($articleObj->getPublished() == "Y") {
	$statusWord = getGS('Published');
}
elseif ($articleObj->getPublished() == "S") {
	$statusWord = getGS('Submitted');
}
else {
	$statusWord = getGS('New');
}

ArticleTop($articleObj, $Language, "Change article status");
?>

<p>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Change article status"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Change the status of article $1 ($2) from $3 to', '<B>'.htmlspecialchars($articleObj->getTitle()),  htmlspecialchars($articleObj->getLanguageName()) .'</B>', '<B>'.$statusWord.'</B>' ); ?></LI></BLOCKQUOTE></TD>
</TR>

<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<FORM METHOD="POST" ACTION="do_status.php">
		<TABLE>
		<TR>
			<TD ALIGN=LEFT>
				<INPUT TYPE="RADIO" NAME='Status' value='Y' class="input_radio" <?php if ($articleObj->getPublished() == 'S') { ?>CHECKED<?php } ?> <?php if (!$User->hasPermission('Publish')) { ?>disabled<?php } ?>><B><?php putGS('Published'); ?></B>
			</TD>
		</TR>
		
		<TR>
			<TD ALIGN=LEFT>
				<INPUT TYPE="RADIO" NAME='Status' value='S' class="input_radio" <?php if ($articleObj->getPublished() == 'N') { ?>CHECKED<?php } ?>><B><?php  putGS('Submitted'); ?></B>
			</TD>
		</TR> 
		
		<TR>
			<TD ALIGN=LEFT><INPUT TYPE="RADIO" NAME='Status' value='N' class="input_radio" <?php if ($articleObj->getPublished() == 'Y') { ?>CHECKED<?php } ?>> <B><?php  putGS('New'); ?></B>
			</TD>
		</TR>
		</TABLE>

		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>"><P>
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php p($BackLink); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='<?php echo $BackLink; ?>'">
		</FORM>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>

<P>
<?php CampsiteInterface::CopyrightNotice(); ?>