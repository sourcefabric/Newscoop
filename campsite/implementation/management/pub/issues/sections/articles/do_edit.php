<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

// This is used in TransformSubheads() in order to figure out when
// a SPAN tag closes.
global $g_spanCounter;
$g_spanCounter = -1;

/**
 * This function is a callback for preg_replace_callback();
 * It will replace <span class="campsite_subhead">...</span>
 * with <!** Title>...<!** EndTitle>
 */
function TransformSubheads($match) {
	global $g_spanCounter;
	// This matches '<span class="campsite_subhead">'
	if (preg_match("/<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>/i", $match[0])) {
		//echo "matched ".htmlspecialchars($match[0]);
		$g_spanCounter = 1;
		return "<!** Title>";
	}
	// This matches '<span'
	elseif (($g_spanCounter >= 0) && preg_match("/<\s*span/i", $match[0])) {
		$g_spanCounter += 1;
	}
	// This matches '</span>'
	elseif (($g_spanCounter >= 0) && preg_match("/<\s*\/\s*span\s*>/i", $match[0])) {
		$g_spanCounter -= 1;
	}
	if ($g_spanCounter == 0) {
		$g_spanCounter = -1;
		return "<!** EndTitle>";
	}
	return $match[0];
} // fn TransformSubheads

/**
 * This function is a callback for preg_replace_callback().
 * It will replace <a href="campsite_internal_link?...">...</a>
 * with <!** Link Internal ...> ... <!** EndLink>
 *
 */
function TransformLinks($match) {
	// This matches '</a>'
	if (preg_match("/<\s*\/a\s*>/i", $match[0])) {
		$retval = "<!** EndLink>";
		return $retval;
	}
	// This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
	elseif (preg_match("/<\s*a\s*href=[\"']campsite_internal_link[?][\w&=]*[\"'][\s\w\"']*>/i", $match[0])) {
		$url = split("\"", $match[0]);
		$parsedUrl = parse_url($url[1]);
		$retval = "<!** Link Internal ".$parsedUrl["query"].">";
		return $retval;
	}	
} // fn TransformLinks


list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
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

// Fetch article
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	$errorStr = 'No such article.';
}

// If the user has the ability to change the article OR
// the user created the article and it hasnt been published.
$hasAccess = false;
if ($User->hasPermission('ChangeArticle')
	|| (($articleObj->getUserId() == $User->getId()) 
		&& ($articleObj->getPublished() == 'N'))) {
	$hasAccess = true;
}

$errorStr = "";
    
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
			$text = $_REQUEST[$dbColumn->getName()];
			if (ini_get("magic_quotes_gpc")) {
				$text = stripslashes($text);
			}
			// Replace <span class="subhead"> ... </span> with <!** Title> ... <!** EndTitle>
			$text = preg_replace_callback("/(<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>|<\s*span|<\s*\/\s*span\s*>)/i", "TransformSubheads", $text);
			
			// Replace <a href="campsite_internal_link?IdPublication=1&..." ...> ... </a>
			// with <!** Link Internal IdPublication=1&...> ... <!** EndLink>
			//
			$text = preg_replace_callback("/(<\s*a\s*href=[\"']campsite_internal_link[?][\w&=]*[\"'][\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformLinks", $text);
			$hasChanged |= $articleTypeObj->setProperty($dbColumn->getName(),
													    $_REQUEST[$dbColumn->getName()]);
		}
	}
	
	## added by sebastian
	if (function_exists ("incModFile")) {
		incModFile ();
	}
}
ArticleTop($articleObj, $languageObj->getLanguageId(), "Changing article details");

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
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
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
			<INPUT class="button" TYPE="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&sLanguage=<?php  p($sLanguage); ?>'">
			</DIV>
			</TD>
		</TR>
	</TABLE></CENTER>	
	<P>
	<?php  
} 
else { ?>    
	<P>
	<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
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
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/button/ok.gif" BORDER="0" ALT="OK" class="button"></A>
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