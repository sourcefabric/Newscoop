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

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ImageId = Input::Get('ImageId', 'int', 0);
$ImageTemplateId = Input::Get('ImageTemplateId', 'int', 0, true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$languageObj =& new Language($Language);
$imageObj =& new Image($ImageId);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE><?php  putGS('Change image information'); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">	
</HEAD>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS('Change image information'); ?>
	</TD>
	<TD ALIGN="RIGHT">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>" class="breadcrumb" ><?php  putGS('Images');  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" class="breadcrumb"><?php  putGS('Articles');  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb"><?php  putGS('Sections');  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php  p($Pub); ?>"  class="breadcrumb"><?php  putGS('Issues');  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php  putGS('Publications');  ?></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS('Publication'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($publicationObj->getName()); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS('Issue'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($issueObj->getIssueId()); ?>. <?php  echo htmlspecialchars($issueObj->getName()); ?> (<?php echo htmlspecialchars($languageObj->getName()); ?>)</TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS('Section'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($sectionObj->getSectionId()); ?>. <?php echo htmlspecialchars($sectionObj->getName()); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS('Article'); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php echo htmlspecialchars($articleObj->getTitle()); ?></TD>
</TR>
</TABLE>
<P>
<CENTER>
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>">
</CENTER>
<?php if ($User->hasPermission('ChangeImage')) { ?>
<p>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS('Change image information'); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<?php if ($ImageTemplateId > 0) { ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Number'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php echo $ImageTemplateId; ?>" class="input_text" SIZE="32" MAXLENGTH="10">
		</TD>
	</TR>
	<?php } ?>
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
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Image" VALUE="<?php  p($ImageId); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<P>
<?php 
}

CampsiteInterface::CopyrightNotice(); ?>