<?php
	include($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
	load_common_include_files();
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

	list($access, $User, $XPerm) = check_basic_access($_REQUEST);
    $Pub = isset($_REQUEST["Pub"])?$_REQUEST["Pub"]:0;
    $Issue = isset($_REQUEST["Issue"])?$_REQUEST["Issue"]:0;
    $Section = isset($_REQUEST["Section"])?$_REQUEST["Section"]:0;
    $Language = isset($_REQUEST["Language"])?$_REQUEST["Language"]:0;
    $sLanguage = isset($_REQUEST["sLanguage"])?$_REQUEST["sLanguage"]:0;
    $Article = isset($_REQUEST["Article"])?$_REQUEST["Article"]:0;
	
   	// If the user has the ability to change the article OR
	// the user created the article and it hasnt been published.
	$hasAccess = false;
    if ($XPerm['ChangeArticle'] 
    	|| (($articleObj->getUserId() == $User['Id']) 
    		&& ($articleObj->getPublished() == 'N'))) {
    	$hasAccess = true;
    }
    
    $errorStr = "";
    
    // Fetch article
    $articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
    if (!$articleObj->exists()) {
    	$errorStr = 'No such article.';
    }
    
    // Fetch section
    $sectionObj =& new Section($Pub, $Issue, $Language, $Section);
    if (!$sectionObj->exists()) {
    	$errorStr = 'No such section.';
    }
    
    // Fetch issue
    $issueObj =& new Issue($Pub, $Language, $Issue);
    if (!$issueObj->exists()) {
    	$errorStr = 'No such issue.';
    }

    // Fetch publication
    $publicationObj =& new Publication($Pub);
    if (!$publicationObj->exists()) {
    	$errorStr = 'No such publication.';
    }
    
    $languageObj =& new Language($Language);
    $sLanguageObj =& new Language($sLanguage);

    // Update the article
    $hasChanged = false;
    if (($errorStr == "") && $access && $hasAccess) {
    	// TODO: Verify the input
    	
    	// Update the article & check if it has been changed.
		$hasChanged |= $articleObj->setOnFrontPage($_REQUEST["cOnFrontPage"] == "on");
		$hasChanged |= $articleObj->setOnSection($_REQUEST["cOnSection"] == "on");
		$hasChanged |= $articleObj->setIsPublic($_REQUEST["cPublic"] == "on");
		$hasChanged |= $articleObj->setKeywords($_REQUEST['cKeywords']);
		$hasChanged |= $articleObj->setTitle($_REQUEST["cName"]);
		$hasChanged |= $articleObj->setIsIndexed(false);
		$articleTypeObj =& $articleObj->getArticleTypeObject();
		$dbColumns = $articleTypeObj->getUserDefinedColumns();
		foreach ($dbColumns as $dbColumn) {
			$hasChanged |= $articleTypeObj->setColumnValue($dbColumn->getName(),
														   $_REQUEST[$dbColumn->getName()]);
		}
		
		## added by sebastian
		if (function_exists ("incModFile")) {
			incModFile ();
		}
    }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/stylesheet.css">
	<TITLE><?php  putGS("Changing article details"); ?></TITLE>
	<?php if (!$access) { ?>
		<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/logout.php">
	<?php  } ?>
</HEAD>

<?php if (!$access) { ?>
	</HTML>
	<?PHP
	return;
}
?>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Changing article details"); ?></B></DIV>
		    <HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR><TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
<TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/priv/pub/issues/?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
<TD><A HREF="/priv/pub/" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/priv/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
<TD><A HREF="/priv/home.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD><TD><A HREF="/priv/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
<TD><A HREF="/priv/logout.php" ><IMG SRC="/priv/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD><TD><A HREF="/priv/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?PHP
if ($errorStr != "") {
	?>
	<BLOCKQUOTE>
	<LI><?php putGS($errorStr); ?></LI>
	</BLOCKQUOTE>
	<HR NOSHADE SIZE="1" COLOR="BLACK">
	<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
	</BODY>
	</HTML>
	<?php
	return;
}
?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Publication"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($publicationObj->getName()); ?></B></TD>
	
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Issue"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($issueObj->getIssueId()); ?>. <?php print encHTML($issueObj->getName()); ?> (<?php print encHTML($languageObj->getName()); ?>)</B></TD>
	
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Section"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($sectionObj->getSectionId()); ?>. <?php  print encHTML($sectionObj->getName()); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php putGS("Article"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($articleObj->getTitle()); ?> (<?php print encHTML($sLanguageObj->getName()); ?>)</B></TD>
</TR>
</TABLE>

<?php //if($xpermrows) {
//		$xaccess=(getvar($XPerm,'ChangeArticle') == "Y");
//		if($xaccess =='') $xaccess = 0;
//	}
//	else $xaccess = 0;
	?>
<?php 
//    query ("SELECT ($xaccess != 0) or ((".getVar($q_art,'IdUser')." = ".getVar($Usr,'Id').") and ('".getVar($q_art,'Published')."' = 'N'))", 'q_xperm');
//    fetchRowNum($q_xperm);
    //if (getNumVar($q_xperm,0)) {
    if ($hasAccess) {
?><P>

<?php  //$chngd= 0; ?>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Changing article details"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
    //query ("UPDATE Articles SET Name='$cName', OnFrontPage='$cOnFrontPage', OnSection='$cOnSection', Keywords='$cKeywords', Public='$cPublic', IsIndexed='N' WHERE IdPublication=$Pub AND NrIssue=$Issue AND NrSection=$Section AND Number=$Article AND IdLanguage=$sLanguage");
//	if ($AFFECTED_ROWS > 0)
//		$chngd= 1;

	## added by sebastian
//	if (function_exists ("incModFile"))
//		incModFile ();
    
//	query ("SHOW COLUMNS FROM X".getSVar($q_art,'Type')." LIKE 'F%'", 'q_fld');
//    $nr=$NUM_ROWS;
//    $query = "";
//    $first = true;
//    for($loop=0;$loop<$nr;$loop++) {
//                fetchRowNum($q_fld);
//                $save = false;
//                $ischar=strpos(getNumVar($q_fld,1),'char');
//                $isdate=strpos(getNumVar($q_fld,1),'date');
//                if(!($ischar === false)) $save = true;
//                if(!($isdate === false)) $save = true;
//                if ($save === true) {  // only save the non-blob fields; the blobs are saves separately, by their specific editors
//                        if($first === false)
//                                $query = $query.", ";
//                        $first = false;
//                        $fld= getNumVar($q_fld,0);
//                        $query = $query." ". $fld."='".encSQL($$fld)."'";
//                }
//    }
//    //print ("<p>UPDATE X".getSVar($q_art,'Type')." SET $query WHERE NrArticle=$Article AND IdLanguage=$sLanguage<br>");
//    query ("UPDATE X".getSVar($q_art,'Type')." SET $query WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
//        if ($AFFECTED_ROWS > 0)
//	$chngd= 1;
//
    if ($hasChanged) { 
    	?>	<LI><?php  putGS('The article has been updated.'); ?></LI>
		<?php  
    } 
    else { 
    	?>	<LI><?php  putGS('The article cannot be updated or no changes have been made.'); ?></LI>
		<?php  
    } 
    ?>
    </BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&sLanguage=<?php  p($sLanguage); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>

<P>
<?php  } else { ?>    
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do not have the right to change this article status. Once submitted an article can only changed by authorized users." ); ?></li></font></BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<A HREF="/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>"><IMG SRC="/priv/img/button/ok.gif" BORDER="0" ALT="OK"></A>
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<P>

<?php  } ?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
</HTML>
