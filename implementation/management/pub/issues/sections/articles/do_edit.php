<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/priv/pub/issues/sections/articles/article_common.php");

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
    
$languageObj =& new Language($Language);

// Update the article
$hasChanged = false;
if (($errorStr == "") && $access && $hasAccess) {
	$articleTypeObj =& $articleObj->getArticleTypeObject();
	$dbColumns = $articleTypeObj->getUserDefinedColumns();
	
	// TODO: Verify the input
	
	// Update the article & check if it has been changed.
	$hasChanged |= $articleObj->setOnFrontPage(isset($_REQUEST["cOnFrontPage"]));
	$hasChanged |= $articleObj->setOnSection(isset($_REQUEST["cOnSection"]));
	$hasChanged |= $articleObj->setIsPublic(isset($_REQUEST["cPublic"]));
	$hasChanged |= $articleObj->setKeywords($_REQUEST['cKeywords']);
	$hasChanged |= $articleObj->setTitle($_REQUEST["cName"]);
	$hasChanged |= $articleObj->setIsIndexed(false);
	foreach ($dbColumns as $dbColumn) {
		if (isset($_REQUEST[$dbColumn->getName()])) {
			$_REQUEST[$dbColumn->getName()] = str_replace("<!--** Title-->", "<!** Title>", $_REQUEST[$dbColumn->getName()]);
			$_REQUEST[$dbColumn->getName()] = str_replace("<!--** EndTitle-->", "<!** EndTitle>", $_REQUEST[$dbColumn->getName()]);
			$hasChanged |= $articleTypeObj->setColumnValue($dbColumn->getName(),
													   $_REQUEST[$dbColumn->getName()]);
		}
	}
	
	## added by sebastian
	if (function_exists ("incModFile")) {
		incModFile ();
	}
}

ArticleTop($articleObj, $languageObj->getLanguageId(), "Changing article details", $access);

if (!$access) { 
	?>
	</HTML>
	<?PHP
	return;
}

if ($errorStr != "") {
	CampsiteInterface::DisplayError($errorStr);
	return;
}

if ($hasAccess) {
	?><P>
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
			    if ($hasChanged) { 
			    	?>
			    	<LI><?php putGS('The article has been updated.'); ?></LI>
					<?php  
			    } 
			    else { 
			    	?>
			    	<LI><?php putGS('The article cannot be updated or no changes have been made.'); ?></LI>
					<?php  
			    } 
			    ?>
			    </BLOCKQUOTE>
			</TD>
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
	<?php  
} 
else { ?>    
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
	<?php  
} 
CampsiteInterface::CopyrightNotice();
?>