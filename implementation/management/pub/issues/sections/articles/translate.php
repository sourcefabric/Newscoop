<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/", true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}
$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'), $BackLink);
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('No such issue.'), $BackLink);
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('No such section.'), $BackLink);
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'), $BackLink);
	exit;
}

if (!$articleObj->userCanModify($User)) {
	$errorStr = getGS('You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.');
	CampsiteInterface::DisplayError($errorStr, $BackLink);
	exit;	
}

$translations =& $articleObj->getTranslations();
foreach ($translations as $translation) {
	$articleNames[] = $translation->getName();
}
$outputName = implode(', ', $articleNames);

$allLanguages = Language::getAllLanguages();
$articleLanguages = $articleObj->getLanguages();
$articleLanguages = DbObjectArray::GetColumn($articleLanguages, "Id");

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
CampsiteInterface::ContentTop(getGS('Translate article'), $topArray, true, true);
?>
<table>
<tr>
	<td>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/back.png" BORDER="0"></A></TD>
			<TD><A HREF="edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS('Back to article details'); ?></B></A></TD>
		</TR>
		</TABLE>
	</td>
</tr>
</table>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" >
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
<INPUT TYPE="HIDDEN" NAME="ArticleLanguage" VALUE="<?php  p($articleObj->getLanguageId()); ?>">
<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<?php echo $BackLink; ?>">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Translate article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Article"); ?>:</TD>
	<TD><?php p(htmlspecialchars($outputName)); ?></td>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	<SELECT NAME="cLanguage" class="input_select">
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
		pcomboVar($tmpLangId, '', $nativeName);
	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="32" MAXLENGTH="255" class="input_text">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='<?php  p($BackLink); ?>'">
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>