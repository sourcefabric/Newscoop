<?
require_once("common.php");
require_once("Article.php");
require_once("Section.php");
require_once("Issue.php");
require_once("Publication.php");
require_once("Language.php");

$rootDirectory = $ADMIN_DIR;

//echo $TOL_UserId;exit;
//$access = check_basic_access($TOL_UserId, $TOL_UserKey);
$access = 1;

// Check input
if (!isset($_REQUEST["Pub"]) 
	|| (!isset($_REQUEST["Issue"]))
	|| (!isset($_REQUEST["Section"]))
	|| (!isset($_REQUEST["Language"]))
	|| (!isset($_REQUEST["sLanguage"]))
	|| (!isset($_REQUEST["Article"]))) {
	echo "Missing Input!!!<br>";exit;
}
$Pub = $_REQUEST["Pub"];
$Issue = $_REQUEST["Issue"];
$Section = $_REQUEST["Section"];
$Language = $_REQUEST["Language"];
$sLanguage = $_REQUEST["sLanguage"];
$Article = $_REQUEST["Article"];

$articleObj =& new Article($Pub, $Issue, $Section, $Article, $sLanguage);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$issueObj =& new Issue($Pub, $Issue, $Language);
$publicationObj =& new Publication($Pub);
$articleLanguage =& new Language($Language);
$issueLanguage =& new Language($sLanguage);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="/<?php echo $ADMIN; ?>/article_import/javascript/fValidate.config.js"></script>
    <script type="text/javascript" src="/<?php echo $ADMIN; ?>/article_import/javascript/fValidate.core.js"></script>
    <script type="text/javascript" src="/<?php echo $ADMIN; ?>/article_import/javascript/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="/<?php echo $ADMIN; ?>/article_import/javascript/fValidate.validators.js"></script>
	<TITLE>Article Import<?php //putGS("$1"); ?></TITLE>
	<?php  if ($access == 0) { ?>
		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=<?php echo $rootDirectory ?>/logout.php">
	<? } ?>
	<LINK rel="stylesheet" type="text/css" href="stylesheet.css">
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="<?php echo $rootDirectory ?>/img/sign_big.gif" BORDER="0"></TD>
	<TD>
	    <DIV STYLE="font-size: 12pt"><B>Article Import<?php //putGS("$1"); ?></B></DIV>
	    <HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<? putGS("Articles"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>&Section=<? p($Section); ?>" ><B><? putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<? putGS("Sections"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Language=<? p($Language); ?>" ><B><? putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<? p($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<? putGS("Issues"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<? p($Pub); ?>" ><B><? putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<? putGS("Publications"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><B><? putGS("Publications");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<? putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><? echo $publicationObj->getName(); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<? putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><? echo $issueObj->getNumber(); ?>. <? echo $issueObj->getName(); ?> (<? echo $issueLanguage->getName(); ?>)</TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<? putGS("Section"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><? echo $sectionObj->getNumber(); ?>. <? echo $sectionObj->getName(); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<? putGS("Article"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><? echo $articleObj->getTitle(); ?> (<? echo $articleLanguage->getName(); ?>)</TD>
</TR>
</TABLE>

<table width="100%" border="0">
<tr>
	<td style="padding:20px;" align="center">
		Here you can upload an article that has been written in Open Office (files with extension ".sxw").  Click <a href="CampsiteArticleTemplate.stw">here</a> to get the template.
	</td>
</tr>
</table>

<table border="0" align="center" cellspacing="0" BGCOLOR="#C0D0FF">
<form method="POST" action="CommandProcessor.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 0);" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<input type="hidden" name="form_name" value="upload_article_form">
<input type="hidden" name="Pub" value="<? echo $Pub ?>">
<input type="hidden" name="Issue" value="<? echo $Issue ?>">
<input type="hidden" name="Section" value="<? echo $Section ?>">
<input type="hidden" name="Article" value="<? echo $Article ?>">
<input type="hidden" name="Language" value="<? echo $Language ?>">
<!-- BEGIN: The following fields are needed for edit.php -->
<input type="hidden" name="sLanguage" value="<? echo $sLanguage ?>">
<!-- END -->
<tr>
	<td align="left" colspan="2" style="padding: 6px">
		<B>Article Import</B>
		<HR NOSHADE SIZE="1" COLOR="BLACK"> 		
	</td>
</tr>
<tr>
	<td style="padding: 6px;">
		Upload File:
	</td>
	
	<td style="padding: 6px;">
		<input type="file" name="filename" size="55" value="" alt="file|sxw" emsg="The file name must have an extension of .sxw" class="input_file">
	</td>
	
</tr>
<tr>
	<td colspan="2">
		<table width="100%">
		<tr>
			<td align="right" style="padding: 3px;" >
				<INPUT type="submit" name="Submit" value="Upload">
			</td>
			<td align="left" style="padding: 3px;">
				<INPUT type="button" name="Cancel" value="Cancel" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<? p($Pub); ?>&Issue=<? p($Issue); ?>&Section=<? p($Section); ?>&Article=<? p($Article) ?>&Language=<? p($Language); ?>&sLanguage=<? p($sLanguage) ?>'">
			</td>
		</tr>
		</table>
	</td>
</tr>
</form>
</table>