<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/ArticleImage.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Image.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Input.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("AddImage")) {
	header("Location: /$ADMIN/ad.php?ADReason=".encURL(getGS("You do not have the right to add images" )));
	exit;
}
$maxId = Image::GetMaxId();
$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$sLanguage = Input::get('sLanguage', 'int', 0);
$Article = Input::get('Article', 'int', 0);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$languageObj =& new Language($Language);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

$ImageTemplateId = ArticleImage::GetUnusedTemplateId($Article);

query ("SELECT LEFT(NOW(), 10)", 'q_now');
fetchRowNum($q_now);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>

<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("Add new image"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">
	<script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.validators.js"></script>
    <script>
    function checkAddForm(form) {
    	retval = ((form.cURL.value != '') || (form.cImage.value != ''));
    	retval = retval && validateForm(form, 0, 0, 0, 1, 8);
    	return retval;
    } // fn checkAddForm
    </script>
</HEAD>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("Add new image"); ?>
	</TD>
	<TD ALIGN="RIGHT" style="padding-right: 10px; padding-top: 0px;">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" class="breadcrumb"><?php  putGS("Images");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" class="breadcrumb"><?php  putGS("Articles");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb"><?php  putGS("Sections");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($Pub); ?>" class="breadcrumb"><?php  putGS("Issues");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php  putGS("Publications");  ?></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($publicationObj->getName()); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo $issueObj->getIssueId(); ?>. <?php  echo htmlspecialchars($issueObj->getName()); ?> (<?php echo $languageObj->getName(); ?>)</TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Section"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo $sectionObj->getSectionId(); ?>. <?php  echo htmlspecialchars($sectionObj->getName());; ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Article"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($articleObj->getTitle()); ?></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/do_add.php" ENCTYPE="multipart/form-data" onsubmit="return checkAddForm(this);">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new image"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Number"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php p($ImageTemplateId); ?>" SIZE="5" MAXLENGTH="5" class="input_text" alt="number|0" emsg="<?php putGS('Please enter a number for the image.'); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cDescription" VALUE="Image <?php  p($maxId); ?>" SIZE="32" MAXLENGTH="128" class="input_text" alt="blank" emsg="<?php putGS("Please enter a description for the image."); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPhotographer" SIZE="32" MAXLENGTH="64" VALUE="<?php echo $User->getName(); ?>" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cPlace" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php  pgetNumVar($q_now,0); ?>" class="input_text" SIZE="11" MAXLENGTH="10"> <?php  putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("URL"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cURL" VALUE="" class="input_text" SIZE="32"> 
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("Image"); ?>:</TD>
	<TD>
		<INPUT TYPE="FILE" NAME="cImage" SIZE="32" MAXLENGTH="64" class="input_file" alt="file|jpg,jpeg,jpe,gif,png,tif,tiff|bok" emsg="<?php putGS("You must select an image file to upload."); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="PublicationId" VALUE="<?php  p($Pub); ?>">
    <INPUT TYPE="HIDDEN" NAME="IssueId" VALUE="<?php  p($Issue); ?>">
    <INPUT TYPE="HIDDEN" NAME="SectionId" VALUE="<?php  p($Section); ?>">
    <INPUT TYPE="HIDDEN" NAME="ArticleId" VALUE="<?php  p($Article); ?>">
    <INPUT TYPE="HIDDEN" NAME="InterfaceLanguageId" VALUE="<?php  p($Language); ?>">
    <INPUT TYPE="HIDDEN" NAME="ArticleLanguageId" VALUE="<?php  p($sLanguage); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
	<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>"  class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>