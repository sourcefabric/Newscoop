<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleImage.php");

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
 * @param array p_match
 * @return string
 */
function TransformInternalLinks($p_match) {
	// This matches '</a>'
	if (preg_match("/<\s*\/a\s*>/i", $p_match[0])) {
		$retval = "<!** EndLink>";
		return $retval;
	}
	// This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
	elseif (preg_match("/<\s*a\s*href=[\"']campsite_internal_link[?][\w&=]*[\"'][\s\w\"']*>/i", $p_match[0])) {
		$url = split("\"", $p_match[0]);
		$parsedUrl = parse_url($url[1]);
		$retval = "<!** Link Internal ".$parsedUrl["query"].">";
		return $retval;
	}	
} // fn TransformInternalLinks


/**
 * This function is a callback for preg_replace_callback().
 * It will replace <a href="http://xyz.com" target="_blank">...</a>
 * with <!** Link external "http://xyz.com" TARGET "_blank"> ... <!** EndLink>
 * @param array p_match
 * @return string
 */
function TransformExternalLinks($p_match) {
	// This matches '</a>'
	if (preg_match("/<\s*\/a\s*>/i", $p_match[0])) {
		$retval = "<!** EndLink>";
		return $retval;
	}
	// This matches '<a href="xyz.com" ...>'
	elseif (preg_match("/<\s*a\s*href=[\"'][^'\"]*[\"']\s*(target\s*=\s['\"][_\w]*['\"])?[\s\w\"']*>/i", $p_match[0])) {
		$url = split("\"", $p_match[0]);
		$link = $url[1];
		$target = null;
		if (isset($url[2]) && (stristr($url[2], 'target') !== false)) {
			$target = $url[3];
		}
		$retval = '<!** Link external "'.$link.'"';
		if (!is_null($target)) {
			$retval .= 'target="'.$target.'"';
		}
		$retval .= '>';
		return $retval;
	}	
} // fn TransformExternalLinks


/**
 * This function is a callback for preg_replace_callback().
 * It will replace <img src="http://[hostname]/[image_dir]/cms-image-000000001.jpg" align="center" alt="alternate text" sub="caption text">
 * with <!** Image [image_template_id] align=CENTER alt="alternate text" sub="caption text">
 * @param array p_match
 * @return string
 */
function TransformImageTags($p_match) {
	global $Article;
	array_shift($p_match);
	$attrs = array();
	foreach ($p_match as $attr) {
		$attr = split('=', $attr);
		$attrName = trim(strtolower($attr[0]));
		$attrValue = $attr[1];
		// Strip out the quotes
		$attrValue = str_replace('"', '', $attrValue);
		$attrValue = str_replace("'", '', $attrValue);
		$attrs[$attrName] = $attrValue;
	}	
	if (!isset($attrs['src'])) {
		return '';
	}
	else {
		// Figure out if it is a local or remote image
		if (strstr($attrs['src'], 'cms-image-')) {
			// It is a local image
			// Get the image ID.
			preg_match_all("/[\w\/:]*cms-image-(\d*)[.\w]*/i", $attrs['src'], $srcParts);
			// Lookup the image by ID
			$articleImage =& new ArticleImage($Article, $srcParts[1][0]);
			$templateId = $articleImage->getTemplateId();
		}
		else {
			$image =& Image::GetByUrl($attrs['src']);
			$articleImage =& new ArticleImage($Article, $image->getImageId());
			$templateId = $articleImage->getTemplateId();
		}
	}
	$alignTag = '';
	if (isset($attrs['align'])) {
		$alignTag = ' align='.$attrs['align'];
	}
	$altTag = '';
	if (isset($attrs['alt'])) {
		$altTag = ' alt="'.$attrs['alt'].'"';
	}
	$captionTag = '';
	if (isset($attrs['sub'])) {
		$captionTag = ' sub="'.$attrs['sub'].'"';
	}
	$imageTag = "<!** Image $templateId $alignTag $altTag $captionTag>";
	return $imageTag;
} // fn TransformImageTags


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

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()));
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
			$text = preg_replace_callback("/(<\s*a\s*href=[\"']campsite_internal_link[?][\w&=]*[\"'][\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformInternalLinks", $text);
			$hasChanged |= $articleTypeObj->setProperty($dbColumn->getName(), $text);

			// Replace <a href="http://xyz.com" target="_blank"> ... </a>
			// with <!** Link external "http://xyz.com" TARGET "_blank"> ... <!** EndLink>
			//
			$text = preg_replace_callback("/(<\s*a\s*href=[\"'][^\"']*[\"']\s*(target\s*=\s['\"][_\w]*['\"])?[\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformExternalLinks", $text);
			$hasChanged |= $articleTypeObj->setProperty($dbColumn->getName(), $text);

			// Replace <img src="A" align="B" alt="C" sub="D">
			// with <!** Image [image_template_id] align=B alt="C" sub="D">
			//
			$srcAttr = "(src\s*=\s*[\"'][^'\"]*[\"'])";
			$altAttr = "(alt\s*=\s*['\"][^'\"]*['\"])";
			$alignAttr = "(align\s*=\s*['\"][^'\"]*['\"])";
			$subAttr = "(sub\s*=\s*['\"][^'\"]*['\"])";
			$text = preg_replace_callback("/<\s*img\s*(($srcAttr|$altAttr|$alignAttr|$subAttr)\s*)*[\s\w\"']*\/>/i", "TransformImageTags", $text);
			$hasChanged |= $articleTypeObj->setProperty($dbColumn->getName(), $text);
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