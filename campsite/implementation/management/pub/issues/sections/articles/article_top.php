<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");

require_once($_SERVER['DOCUMENT_ROOT']."/priv/lib_campsite.php");

function article_top($p_articleObj, $p_issueLanguageId, $p_title, $p_access) {
	global $Campsite;
	
    // Fetch section
    $sectionObj =& new Section($p_articleObj->getPublicationId(), 
    	$p_articleObj->getIssueId(), 
    	$p_issueLanguageId,
    	$p_articleObj->getSectionId());
    	
    // Fetch issue
    $issueObj =& new Issue($p_articleObj->getPublicationId(), 
    	$p_issueLanguageId, 
    	$p_articleObj->getIssueId());

    // Fetch publication
    $publicationObj =& new Publication($p_articleObj->getPublicationId());
    
    $articleLanguageObj =& new Language($p_articleObj->getLanguageId());
    $issueLanguageObj =& new Language($p_issueLanguageId);

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/stylesheet.css">
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
	<TITLE><?php putGS("Edit article details"); ?></TITLE>
	<?php if (!$access) { ?>	
		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
	<?php  } ?>
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
	<TD>
	    <DIV STYLE="font-size: 12pt"><B><?php putGS($p_title); ?></B></DIV>
	    <HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="right">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<!-- "Articles" Link -->
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Language=<?php echo $p_articleObj->getLanguageId(); ?>&Section=<?php echo $p_articleObj->getSectionId(); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Language=<?php echo $p_articleObj->getLanguageId(); ?>&Section=<?php echo $p_articleObj->getSectionId(); ?>" ><B><?php putGS("Articles");  ?></B></A></TD>
			
			<!-- "Sections" link -->
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Language=<?php $p_articleObj->getLanguageId(); ?>"><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Language=<?php echo $p_articleObj->getLanguageId(); ?>"><B><?php putGS("Sections"); ?></B></A></TD>
			
			<!-- "Issues" Link -->
			<TD><A HREF="/priv/pub/issues/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/issues/?Pub=<?php echo $p_articleObj->getPublicationId(); ?>"><B><?php putGS("Issues"); ?></B></A></TD>
			
			<!-- "Publications" Link -->
			<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
			
			<!-- "Home" Link -->
			<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD>
			<TD><A HREF="/priv/home.php" ><B><?php putGS("Home"); ?></B></A></TD>
			
			<!-- "Logout" Link -->
			<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php putGS("Logout"); ?>"></A></TD>
			<TD><A HREF="/priv/logout.php" ><B><?php putGS("Logout");  ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Publication"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($publicationObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Issue"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($issueObj->getIssueId()); ?>. <?php  print htmlspecialchars($issueObj->getName()); ?> (<?php print htmlspecialchars($issueLanguageObj->getName()) ?>)</B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Section"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print $sectionObj->getSectionId(); ?>. <?php  print htmlspecialchars($sectionObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Article"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print htmlspecialchars($p_articleObj->getTitle()); ?> (<?php print htmlspecialchars($articleLanguageObj->getName()); ?>)</B></TD>
</TR>
</TABLE>
	<?php
} // fn article_top
?>
