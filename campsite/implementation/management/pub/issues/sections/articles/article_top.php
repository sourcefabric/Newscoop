<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/priv/lib_campsite.php");

/**
 * Common header for all article screens.
 *
 * @param Article p_articleObj
 *		The article that is being displayed.
 *
 * @param int p_interfaceLanguageId
 *		The language for the Issue that that article is contained within.
 *
 * @param string p_title
 *		The title of the page.  This should have a translation in the language files.
 *
 * @param boolean p_includeLinks
 *		Whether to include the links underneath the title or not.  Default TRUE.
 *
 * @return void
 */
function ArticleTop($p_articleObj, $p_interfaceLanguageId, $p_title, $p_includeLinks = true, $p_fValidate = false) {
	global $Campsite;
	
    // Fetch section
    $sectionObj =& new Section($p_articleObj->getPublicationId(), 
    	$p_articleObj->getIssueId(), 
    	$p_interfaceLanguageId,
    	$p_articleObj->getSectionId());
    	
    // Fetch issue
    $issueObj =& new Issue($p_articleObj->getPublicationId(), 
    	$p_interfaceLanguageId, 
    	$p_articleObj->getIssueId());

    // Fetch publication
    $publicationObj =& new Publication($p_articleObj->getPublicationId());
    
    $articleLanguageObj =& new Language($p_articleObj->getLanguageId());
    $interfaceLanguageObj =& new Language($p_interfaceLanguageId);

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">
	<script>
	<!--
	/*
	A slightly modified version of "Break-out-of-frames script"
	By JavaScript Kit (http://javascriptkit.com)
	*/
	if (window != top.fmain && window != top) {
		if (top.fmenu)
			top.fmain.location.href=location.href
		else
			top.location.href=location.href
	}
	// -->
	</script>
	<?php if ($p_fValidate) { ?>
	<script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite["website_url"] ?>/javascript/fValidate/fValidate.validators.js"></script>	
	<?php } ?>
	<TITLE><?php putGS($p_title); ?></TITLE>
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<!--<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>-->
	<TD style="padding-left: 10px; padding-top: 10px;">
	    <DIV STYLE="font-size: 12pt"><B><?php putGS($p_title); ?></B></DIV>
	    <!--<HR NOSHADE SIZE="1" COLOR="BLACK">-->
	</TD>
<!--</TR>-->
<?php 
if ($p_includeLinks) {
?>
<!--<TR>-->
	<TD ALIGN="right" style="padding-right: 10px; padding-top: 10px;">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<!-- "Articles" Link -->
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($p_articleObj->getPublicationId()); ?>&Issue=<?php p($p_articleObj->getIssueId()); ?>&Language=<?php p($p_interfaceLanguageId); ?>&Section=<?php p($p_articleObj->getSectionId()); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php p($p_articleObj->getPublicationId()); ?>&Issue=<?php p($p_articleObj->getIssueId()); ?>&Language=<?php p($p_interfaceLanguageId); ?>&Section=<?php p($p_articleObj->getSectionId()); ?>" ><B><?php putGS("Articles");  ?></B></A></TD>
			
			<!-- "Sections" link -->
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($p_articleObj->getPublicationId()); ?>&Issue=<?php p($p_articleObj->getIssueId()); ?>&Language=<?php p($p_interfaceLanguageId); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php p($p_articleObj->getPublicationId()); ?>&Issue=<?php p($p_articleObj->getIssueId()); ?>&Language=<?php p($p_interfaceLanguageId); ?>"><B><?php putGS("Sections"); ?></B></A></TD>
			
			<!-- "Issues" Link -->
			<TD><A HREF="/priv/pub/issues/?Pub=<?php p($p_articleObj->getPublicationId()); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php p($p_articleObj->getPublicationId()); ?>"><B><?php putGS("Issues"); ?></B></A></TD>
			
			<!-- "Publications" Link -->
			<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
			
			<!-- "Home" Link -->
<!--			<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD>
			<TD><A HREF="/priv/home.php" ><B><?php putGS("Home"); ?></B></A></TD>
-->			
			<!-- "Logout" Link -->
<!--			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Logout"); ?>"></A></TD>
			<TD><A HREF="/priv/logout.php" ><B><?php putGS("Logout");  ?></B></A></TD>
-->		</TR>
		</TABLE>
	</TD>
<?php
} // if ($p_includeLinks)
?>
</TR>
</TABLE>
<HR NOSHADE SIZE="1" COLOR="BLACK">

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%">&nbsp;<?php putGS("Publication"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($publicationObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%">&nbsp;<?php putGS("Issue"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($issueObj->getIssueId()); ?>. <?php  print htmlspecialchars($issueObj->getName()); ?> (<?php print htmlspecialchars($interfaceLanguageObj->getName()) ?>)</B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%">&nbsp;<?php putGS("Section"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print $sectionObj->getSectionId(); ?>. <?php  print htmlspecialchars($sectionObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%">&nbsp;<?php putGS("Article"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($p_articleObj->getTitle()); ?> (<?php print htmlspecialchars($articleLanguageObj->getName()); ?>)</B></TD>
</TR>
</TABLE>
	<?php
} // fn article_top
?>
