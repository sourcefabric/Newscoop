<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
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
$PublicationId = Input::get('Pub', 'int', 0);
$IssueId = Input::get('Issue', 'int', 0);
$SectionId = Input::get('Section', 'int', 0);
$InterfaceLanguageId = Input::get('Language', 'int', 0);
$ArticleLanguageId = Input::get('sLanguage', 'int', 0);
$ArticleId = Input::get('Article', 'int', 0);
$ImageId = Input::get('ImageId', 'int', 0);
$BackLink = Input::get('BackLink', 'string', 'index.php', true);

// Security
if ( ($BackLink != "index.php") && ($BackLink != "search.php")) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}
$publicationObj =& new Publication($PublicationId);
$issueObj =& new Issue($PublicationId, $InterfaceLanguageId, $IssueId);
$sectionObj =& new Section($PublicationId, $IssueId, $InterfaceLanguageId, $SectionId);
$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);
$languageObj =& new Language($InterfaceLanguageId);
$imageObj =& new Image($ImageId);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS("View image"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">	
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("View image"); ?>
	</TD>
	<TD ALIGN=RIGHT>
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Images"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><B><?php  putGS("Images");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($PublicationId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($PublicationId); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><B><?php echo htmlspecialchars($publicationObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><B><?php echo htmlspecialchars($issueObj->getIssueId()); ?>. <?php  echo htmlspecialchars($issueObj->getName()); ?> (<?php echo htmlspecialchars($languageObj->getName()); ?>)</B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Section"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><B><?php echo $sectionObj->getSectionId(); ?>. <?php  echo htmlspecialchars($sectionObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS("Article"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><B><?php echo htmlspecialchars($articleObj->getTitle()); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php putGS("Image"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><B><?php echo htmlspecialchars($imageObj->getDescription()); ?></B></TD>
</TR>
</TABLE>

<P>
<br>
<CENTER><IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php  echo htmlspecialchars($imageObj->getDescription()); ?>">
<br><br>
<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/<?php p($BackLink); ?>?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>" >&lt;-- Back</a>
</CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>