<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues/sections/articles/images");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Section.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$PublicationId = Input::get('PublicationId', 'int', 0);
$IssueId = Input::get('IssueId', 'int', 0);
$SectionId = Input::get('SectionId', 'int', 0);
$InterfaceLanguageId = Input::get('InterfaceLanguageId', 'int', 0);
$ArticleLanguageId = Input::get('ArticleLanguageId', 'int', 0);
$ArticleId = Input::get('ArticleId', 'int', 0);
$ImageId = Input::get('ImageId', 'int', 0);
$ImageTemplateId = Input::get('ImageTemplateId', 'int', 0);

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
	exit;	
}

$publicationObj =& new Publication($PublicationId);
$issueObj =& new Issue($PublicationId, $InterfaceLanguageId, $IssueId);
$sectionObj =& new Section($PublicationId, $IssueId, $InterfaceLanguageId, $SectionId);
$articleObj =& new Article($PublicationId, $IssueId, $SectionId, $ArticleLanguageId, $ArticleId);
$languageObj =& new Language($InterfaceLanguageId);
$imageObj =& new Image($ImageId);

// This file can only be accessed if the user has the right to change articles
// or the user created this article and it hasnt been published yet.
if (!$User->hasPermission('ChangeArticle') 
	|| (($articleObj->getUserId() == $User->getId()) && ($articleObj->getPublished() == 'N'))) {
	header("Location: /$ADMIN/logout.php");
	exit;		
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Change image information'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['website_url'] ?>/css/admin_stylesheet.css">	
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS('Change image information'); ?>
	</TD>
	<TD ALIGN="RIGHT">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS('Images'); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><B><?php  putGS('Images');  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&Section=<?php  p($SectionId); ?>" ><B><?php  putGS('Articles');  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS('Sections'); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Language=<?php  p($InterfaceLanguageId); ?>" ><B><?php  putGS('Sections');  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($PublicationId); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS('Issues'); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($PublicationId); ?>" ><B><?php  putGS('Issues');  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS('Publications'); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><B><?php  putGS('Publications');  ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS('Publication'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($publicationObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS('Issue'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($issueObj->getIssueId()); ?>. <?php  echo htmlspecialchars($issueObj->getName()); ?> (<?php echo htmlspecialchars($languageObj->getName()); ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS('Section'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($sectionObj->getSectionId()); ?>. <?php echo htmlspecialchars($sectionObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS('Article'); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php echo htmlspecialchars($articleObj->getTitle()); ?></B></TD>

</TR></TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS('Change image information'); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Number'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php echo $ImageTemplateId; ?>" class="input_text" SIZE="32" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Description'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" class="input_text" SIZE="32" MAXLENGTH="128">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Photographer'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" class="input_text" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Place'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cPlace" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" class="input_text" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Date'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" class="input_text" SIZE="11" MAXLENGTH="10"> <?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($PublicationId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($IssueId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($SectionId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($ArticleId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($InterfaceLanguageId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($ArticleLanguageId); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Image" VALUE="<?php  p($ImageId); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($PublicationId); ?>&Issue=<?php  p($IssueId); ?>&Article=<?php  p($ArticleId); ?>&Language=<?php  p($InterfaceLanguageId); ?>&sLanguage=<?php  p($ArticleLanguageId); ?>&Section=<?php  p($SectionId); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>
<?php CampsiteInterface::CopyrightNotice(); ?>