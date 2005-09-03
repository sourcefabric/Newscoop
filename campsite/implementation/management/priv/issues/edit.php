<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to change issue details.'));
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
$allLanguages =& Language::GetAllLanguages();
$allTemplates =& Template::GetAllTemplates();

CampsiteInterface::ContentTop(getGS('Change issue details'), array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php p($Language); ?>">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change issue details"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cName" SIZE="32" MAXLENGTH="64" value="<?php  p(htmlspecialchars($issueObj->getName())); ?>">
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	    <SELECT NAME="cLang" class="input_select">
		<?php 
		foreach ($allLanguages as $tmpLanguage) {
			pcomboVar($tmpLanguage->getLanguageId(), $issueObj->getLanguageId(), $tmpLanguage->getNativeName());
	    }
		?>
		</SELECT>
	</TD>
</TR>

<?php if ($issueObj->getPublished() == 'Y') {?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Publication date<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cPublicationDate" SIZE="11" MAXLENGTH="10" value="<?php  p(htmlspecialchars($issueObj->getPublicationDate())); ?>">
	</TD>
</TR>
<?php } ?>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Front Page Template"); ?>:</TD>
	<TD>
		<SELECT NAME="cIssueTplId" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php 
		foreach ($allTemplates as $template) {
			pcomboVar($template->getTemplateId(), $issueObj->getIssueTemplateId(), $template->getName());
		}
		?>	    
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Section Template"); ?>:</TD>
	<TD>
		<SELECT NAME="cSectionTplId" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php 
		foreach ($allTemplates as $template) {
			pcomboVar($template->getTemplateId(), $issueObj->getSectionTemplateId(), $template->getName());
			}
		?>	    
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Article Template"); ?>:</TD>
	<TD>
		<SELECT NAME="cArticleTplId" class="input_select">
		<OPTION VALUE="0">---</OPTION>
		<?php 
		foreach ($allTemplates as $template) {
			pcomboVar($template->getTemplateId(), $issueObj->getArticleTemplateId(), $template->getName());
		}
		?>
		</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="cShortName" SIZE="32" MAXLENGTH="32" value="<?php  p(htmlspecialchars($issueObj->getShortName())); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>