<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$BackLink = Input::get('Back', 'string', "/$ADMIN/pub/issues/sections/articles/", true);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}
$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Article does not exist.')));
	exit;
}

$access = false;
if ($User->hasPermission('ChangeArticle') || (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	$access= true;
}
if (!$access) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.")));
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

ArticleTop($articleObj, $Language, "Translate article", true, true);
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" >
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($articleObj->getLanguageId()); ?>">
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
	foreach ($allLanguages as $language) {
		if (!in_array($language->getLanguageId(), $articleLanguages)) {
			pcomboVar($language->getLanguageId(), '', $language->getName());
		}
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