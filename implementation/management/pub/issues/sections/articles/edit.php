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
    $LockOk = isset($_REQUEST["LockOk"])?$_REQUEST["LockOk"]:0;

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

   	// If the user has the ability to change the article OR
	// the user created the article and it hasnt been published.
	$hasAccess = false;
    if ($XPerm['ChangeArticle'] || (($articleObj->getUserId() == $User['Id']) && ($articleObj->getPublished() == 'N'))) {
    	$hasAccess = true;
		$edit_ok= 0;
		// If the article is not locked by a user or its been locked by the current user.
		if (($articleObj->getLockedByUser() == 0) 
			|| ($articleObj->getLockedByUser() == $User['Id'])) {
			// Lock the article
			$articleObj->lock($User['Id']);
		    $edit_ok= 1;
		} 
	}
	
    $articleType =& $articleObj->getArticleTypeObject();
    
	// Check if everything needed for Article Import is available.
	$zipLibAvailable = function_exists("zip_open");
	$xsltLibAvailable = function_exists("xslt_create");
	@include("XML/Parser.php");
	$xmlLibAvailable = class_exists("XML_Parser");
	$xmlLibAvailable |= function_exists("xml_parser_create");
	// Verify this article type has the body & intro fields.
	$introSupport = false;
	$bodySupport = false;
	$dbColumns = $articleType->getUserDefinedColumns();
	foreach ($dbColumns as $dbColumn) {
		if ($dbColumn->getName() == "Fintro") {
			$introSupport = true;
		}
		if ($dbColumn->getName() == "Fbody") {
			$bodySupport = true;
		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<LINK rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/stylesheet.css">
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

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
	<TR>
		<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/priv/img/sign_big.gif" BORDER="0"></TD>
		<TD>
		    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Edit article details"); ?></B></DIV>
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
	<?
	return;
}
?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($publicationObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print encHTML($issueObj->getIssueId()); ?>. <?php  print encHTML($issueObj->getName()); ?> (<?php print encHTML($languageObj->getName()) ?>)</B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php print $sectionObj->getSectionId(); ?>. <?php  print encHTML($sectionObj->getName()); ?></B></TD>

<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Article"); ?>:</TD><TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php  print encHTML($articleObj->getTitle()); ?> (<?php print encHTML($sLanguageObj->getName()); ?>)</B></TD>

</TR></TABLE>
<?php 
if (!$hasAccess) {
	?>
	<P>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
		<TR>
			<TD COLSPAN="2">
				<B> <font color="red"><?php  putGS("Access denied"); ?> </font></B>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
			</TD>
		</TR>
		<TR>
			<TD COLSPAN="2"><BLOCKQUOTE><font color=red><li><?php  putGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users." ); ?></li></font></BLOCKQUOTE></TD>
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
	<?php	
}

// If the article is locked.
if ($hasAccess && !$edit_ok) {
	?><P>
	<CENTER>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS("Article is locked"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<?php 
	query ("SELECT *, NOW() AS Now FROM Users WHERE Id=".$articleObj->getLockedByUser(), 'q_luser');
	fetchRow($q_luser);
	?>	
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('This article has been locked by $1 ($2) at','<B>'.getHVar($q_luser,'Name'),getHVar($q_luser,'UName').'</B>' ); ?>
		<B><?php print encHTML($articleObj->getLockTime()); ?></B></LI>
		<LI><?php  putGS('Now is $1','<B>'.getHVar($q_luser,'Now').'</B>'); ?></LI>
		<LI><?php  putGS('Are you sure you want to unlock it?'); ?></LI>
		</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" ONCLICK="location.href='<?php  p($REQUEST_URI); ?>&LockOk=1'">
		<INPUT TYPE="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>'">
		</DIV>
		</TD>
	</TR>
	</TABLE></CENTER>
	<P>
	<?php  
}

if ($edit_ok) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" WIDTH="100%">
<TR><TD>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR>
<?php 
    if ($articleObj->getPublished() == "Y") { ?><?php  if ($XPerm['Publish']) { ?><TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Unpublish"); ?></B></A></TD></TR></TABLE></TD>
<?php  } ?>
<?php  } elseif ($articleObj->getPublished() == "S") { ?><?php  if ($XPerm['Publish']) { ?><TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Publish"); ?></B></A></TD></TR></TABLE></TD>
<?php  } ?>
<?php  } else { ?><TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/status.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Submit"); ?></B></A></TD></TR></TABLE></TD>
<?php  } ?>
<TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Images"); ?></B></A></TD></TR></TABLE>
</TD>
<TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/topics/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/topics/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Topics"); ?></B></A></TD></TR></TABLE>
</TD>
<TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/do_unlock.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/do_unlock.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Unlock"); ?></B></A></TD></TR></TABLE>
</TD>
</TR>
<TR>
<TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="" ONCLICK="window.open('/priv/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><B><?php  putGS("Preview"); ?></B></A></TD></TR></TABLE>
</TD>
<?php  if ($XPerm['AddArticle']) { ?><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Translate"); ?></B></A></TD></TR></TABLE>
</TD>
<?php  } ?>
<?php  if ($XPerm['DeleteArticle']) { ?><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Back=<?php  pencURL($REQUEST_URI); ?>" ><B><?php  putGS("Delete"); ?></B></A></TD></TR></TABLE>
<?php  } ?></TD>
<?php  if ($XPerm['AddArticle']) { ?><TD>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="/priv/pub/issues/sections/articles/fduplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD><TD><A HREF="/priv/pub/issues/sections/articles/fduplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Duplicate"); ?></B></A></TD></TR></TABLE>
</TD>
<?php  } ?></TR>
</TABLE>
</TD><TD ALIGN="RIGHT">
	<FORM METHOD="GET" ACTION="edit.php" NAME="">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" BGCOLOR="#C0D0FF">
	<TR>
		<TD><?php  putGS('Language'); ?>:</TD>
		<TD><SELECT NAME="sLanguage">
<?php 
	$articleLanguages = $articleObj->getLanguages();
	foreach ($articleLanguages as $articleLanguage) {
	    pcomboVar($articleLanguage->getLanguageId(), $sLanguage, encHTML($articleLanguage->getName()));
	}
?></SELECT></TD>
		<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	</TR>
	</TABLE>
</FORM>
</TD></TR>
</TABLE>

<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php">
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<!-- Paul Baranowski: BEGIN new code -->
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="left">
					<B><? putGS("Edit article details"); ?></B>
				</td>
				<td align="right">
					<? 
					if ($zipLibAvailable && $xsltLibAvailable && $xmlLibAvailable 
						&& $introSupport && $bodySupport) {
					?>
					<b><a href="/priv/article_import/index.php?Pub=<?p($Pub);?>&Issue=<?p($Issue);?>&Section=<?p($Section);?>&Article=<?p($Article)?>&Language=<?p($Language);?>&sLanguage=<?p($sLanguage);?>">Import Article</a></b>
					<?
					}
					?>
				</td>
			</tr>
			</table>
			<HR NOSHADE SIZE="1" COLOR="BLACK"> 
			<!-- Paul Baranowski: END new code -->
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" VALUE="<?php  print encHTML($articleObj->getTitle()); ?>">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
		<TD>
		<B><?php print encHTML($articleObj->getType()); ?></B>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Uploaded"); ?>:</TD>
		<TD>
		<B><?php print encHTML($articleObj->getUploadDate()); ?> <?php  putGS('(yyyy-mm-dd)'); ?></B>
		</TD>
	</TR>
	<TR>
		<TD>&nbsp;</TD><TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">

	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnFrontPage"<?php  if ($articleObj->onFrontPage()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on front page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cOnSection"<?php  if ($articleObj->onSection()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php  putGS('Show article on section page'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cPublic"<?php  if ($articleObj->isPublic()) { ?> CHECKED<?php  } ?>></TD>
		<TD>
		<?php putGS('Allow users without subscriptions to view the article'); ?>
		</TD>
	</TR>
		</TABLE>
	</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cKeywords" VALUE="<?php print encHTML($articleObj->getKeywords()); ?>" SIZE="64" MAXLENGTH="255">
	<?php 
	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ();
	?>
		</TD>
	</TR>

<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
<INPUT TYPE="HIDDEN" NAME="query" VALUE="">

<?php 
//    $fld= "";
//    $ftyp= "";
?>

<?php 
	foreach ($dbColumns as $dbColumn) {
//    query ("SHOW COLUMNS FROM X".$articleObj->getType()." LIKE 'F%'", 'q_fld');
//    $nr3=$NUM_ROWS;
//    for($loop3=0;$loop3<$nr3;$loop3++) {
//	fetchRowNum($q_fld);
//	}
	
	// table is column name
	//$table= substr ( getNumVar($q_fld,0),1);
	//$posc=strpos(getNumVar($q_fld,1),'char');
	//$posd=strpos(getNumVar($q_fld,1),'date');

	//if (!($posc === false))
		// text
	//    $type=0;
	//elseif (!($posd === false))
	//    $type=1;
	    // date
	//else
		// blob
	 //   $type=2;

//	if ($type != 2) {
//	    if ($fld != "")
//		$fld= "$fld, \"F$table\"";
//	    else
//		$fld= "\"F$table\"";
//
//	    if ($ftyp != "")
//		$ftyp= "$ftyp, $type";
//	    else
//		$ftyp= "$type";
//	}

	if (stristr($dbColumn->getType(), "char")) { ?>
		<TR>
		<TD ALIGN="RIGHT" ><?php pencHTML($dbColumn->getPrintName()); ?>:</TD>
		<TD>
		<?php  //query ("SELECT ".getNumVar($q_fld,0)." FROM X".$articleObj->getType()." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>			
        <INPUT NAME="<?php pencHTML($dbColumn->getName()); ?>" TYPE="TEXT" VALUE="<?php print $articleType->getColumnValue($dbColumn->getName()) ?>" SIZE="64" MAXLENGTH="100">
		<?php  
	} elseif (stristr($dbColumn->getType(), "date")) { 
		    //query ("SELECT F$table from X".$articleObj->getType()." where NrArticle=$Article AND IdLanguage=$sLanguage", 'q_vd');
		    //fetchRowNum($q_vd);
		    //if ($articleType->getColumnValue($dbColumn->getName()) == "0000-00-00") {
			//query ("UPDATE X".$articleObj->getType()." SET F$table=curdate() WHERE NrArticle=$Article AND IdLanguage=$sLanguage");
			//}
		?>		<TR>
		<TD ALIGN="RIGHT" ><?php pencHTML($dbColumn->getPrintName()); ?>:</TD>
		<TD>
		<?php  //query ("SELECT ".getNumVar($q_fld,0)." FROM X".$articleObj->getType()." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld'); ?>
		<INPUT NAME="<?php  pencHTML($dbColumn->getName()); ?>" TYPE="TEXT" VALUE="<?php pencHTML($articleType->getColumnValue($dbColumn->getName())); ?>" SIZE="10" MAXLENGTH="10"> 
		<?php  
		putGS('YYYY-MM-DD'); 
	} elseif (stristr($dbColumn->getType(), "blob")) {
		//query ("SELECT ".getNumVar($q_fld,0).", length(".getNumVar($q_fld,0).") FROM X".$articleObj->getType()." WHERE NrArticle=$Article AND IdLanguage=$sLanguage", 'q_afld');
		//fetchRowNum($q_afld);   
		?>
		<TR>
		<TD ALIGN="RIGHT" VALIGN="TOP"><BR><?php pencHTML($dbColumn->getPrintName()); ?>:<BR> 
			<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
			<TR>
				<TD><IMG SRC="/priv/img/tol.gif" BORDER="0"></A></TD>
				<TD><B><?php  putGS("Edit"); ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
		<TD>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
			<table width=100% border=2>
			<tr bgcolor=LightBlue>
				<td><?php print $articleType->getColumnValue($dbColumn->getName()); ?></td>
			</tr>
			</table>
		<BR><P>
		<?php  
	} 
	?>
			</TD>
	</TR>
	<?php  
	} // foreach ($dbColumns as $dbColumn)  
	?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/priv/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
</FORM>
<?php  
} // if ($edit_ok)
?>
<HR NOSHADE SIZE="1" COLOR="BLACK">
<a STYLE='font-size:8pt;color:#000000' href='http://www.campware.org' target='campware'>CAMPSITE  2.1.5 &copy 1999-2004 MDLF, maintained and distributed under GNU GPL by CAMPWARE</a>
</BODY>
</HTML>
