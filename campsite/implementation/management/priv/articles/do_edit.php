<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleImage.php");

// This is used in TransformSubheads() in order to figure out when
// a SPAN tag closes.
global $g_spanCounter;
$g_spanCounter = -1;

// This is used in TransformInternalLinks() to figure out when
// the internal link tag closes.
global $g_internalLinkCounter;
$g_internalLinkCounter = 0;

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
	global $g_internalLinkCounter;
	// This matches '</a>'
	if (preg_match("/<\s*\/a\s*>/i", $p_match[0])) {
		// Check if we are closing an internal link
		if ($g_internalLinkCounter > 0) {
			// Replace the HTML tag with a template tag
			$retval = "<!** EndLink>";
			$g_internalLinkCounter = 0;
			return $retval;
		}
		else {
			// Leave the HTML tag as is (for external links).
			return '</a>';
		}
	}
	// This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
	elseif (preg_match("/<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>/i", $p_match[0])) {
		
		// Get the URL
		preg_match("/href\s*=\s*[\"'](campsite_internal_link[?][\w&=;]*)[\"']/i", $p_match[0], $url);
		$url = isset($url[1])?$url[1]:'';
		$parsedUrl = parse_url($url);
		$parsedUrl = str_replace("&amp;", "&", $parsedUrl);
		
		// Get the target, if there is one
		preg_match("/target\s*=\s*[\"']([_\w]*)[\"']/i", $p_match[0], $target);
		$target = isset($target[1])?$target[1]:null;
		
		// Replace the HTML tag with a template tag
		$retval = "<!** Link Internal ".$parsedUrl["query"];
		if (!is_null($target)) {
			$retval .= " TARGET ".$target;
		}
		$retval .= ">";
		
		// Mark that we are now inside an internal link.
		$g_internalLinkCounter = 1;
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
//function TransformExternalLinks($p_match) {
//	// This matches '</a>'
//	if (preg_match("/<\s*\/a\s*>/i", $p_match[0])) {
//		$retval = "<!** EndLink>";
//		return $retval;
//	}
//	// This matches '<a href="xyz.com" ...>'
//	elseif (preg_match("/<\s*a\s*href=[\"'][^'\"]*[\"']\s*(target\s*=\s['\"][_\w]*['\"])?[\s\w\"']*>/i", $p_match[0])) {
//		$url = split("\"", $p_match[0]);
//		$link = $url[1];
//		$target = null;
//		if (isset($url[2]) && (stristr($url[2], 'target') !== false)) {
//			$target = $url[3];
//		}
//		$retval = '<!** Link external "'.$link.'"';
//		if (!is_null($target)) {
//			$retval .= 'target="'.$target.'"';
//		}
//		$retval .= '>';
//		return $retval;
//	}	
//} // fn TransformExternalLinks


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
$cOnFrontPage = Input::Get('cOnFrontPage', 'string', '', true);
$cOnSection = Input::Get('cOnSection', 'string', '', true);
$cPublic = Input::Get('cPublic', 'string', '', true);
$cKeywords = Input::Get('cKeywords');
$cName = Input::Get('cName');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

// Fetch article
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('No such article.'), $BackLink);
}

$articleTypeObj =& $articleObj->getArticleData();
$dbColumns = $articleTypeObj->getUserDefinedColumns();

$articleFields = array();
foreach ($dbColumns as $dbColumn) {
	if (isset($_REQUEST[$dbColumn->getName()])) {
		$articleFields[$dbColumn->getName()] = Input::Get($dbColumn->getName());
	}
}

$BackLink = "/$ADMIN/articles/index.php?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section";

if (!$articleObj->userCanModify($User)) {
	$errorStr = getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only changed by authorized users.");
	camp_html_display_error($errorStr, $BackLink);
	exit;
}
// Only users with a lock on the article can change it.
if ($articleObj->isLocked() && ($User->getId() != $articleObj->getLockedByUser())) {
	$diffSeconds = time() - strtotime($articleObj->getLockTime());
	$hours = floor($diffSeconds/3600);
	$diffSeconds -= $hours * 3600;
	$minutes = floor($diffSeconds/60);
	$lockUser =& new User($articleObj->getLockedByUser());
	$errorStr = getGS('Could not save the article. It has been locked by $1 $2 hours and $3 minutes ago.', $lockUser->getName(), $hours, $minutes);
	camp_html_display_error($errorStr, $BackLink);
	exit;
}

$languageObj =& new Language($Language);

// Update the article
$hasChanged = false;

// Update the article & check if it has been changed.
$hasChanged |= $articleObj->setOnFrontPage(!empty($cOnFrontPage));
$hasChanged |= $articleObj->setOnSectionPage(!empty($cOnSection));
$hasChanged |= $articleObj->setIsPublic(!empty($cPublic));
$hasChanged |= $articleObj->setKeywords($cKeywords);
$hasChanged |= $articleObj->setTitle($cName);
$hasChanged |= $articleObj->setIsIndexed(false);
foreach ($articleFields as $dbColumnName => $text) {
    echo $text."<br>";
	// Replace <span class="subhead"> ... </span> with <!** Title> ... <!** EndTitle>
	$text = preg_replace_callback("/(<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>|<\s*span|<\s*\/\s*span\s*>)/i", "TransformSubheads", $text);
	
	// Replace <a href="campsite_internal_link?IdPublication=1&..." ...> ... </a>
	// with <!** Link Internal IdPublication=1&...> ... <!** EndLink>
	//
	$text = preg_replace_callback("/(<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformInternalLinks", $text);
	//$hasChanged |= $articleTypeObj->setProperty($dbColumnName, $text);

	// Replace <a href="http://xyz.com" target="_blank"> ... </a>
	// with <!** Link external "http://xyz.com" TARGET "_blank"> ... <!** EndLink>
	//
	//$text = preg_replace_callback("/(<\s*a\s*href=[\"'][^\"']*[\"']\s*(target\s*=\s['\"][_\w]*['\"])?[\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformExternalLinks", $text);
	//$hasChanged |= $articleTypeObj->setProperty($dbColumnName, $text);

	// Replace <img src="A" align="B" alt="C" sub="D">
	// with <!** Image [image_template_id] align=B alt="C" sub="D">
	//
	$srcAttr = "(src\s*=\s*[\"'][^'\"]*[\"'])";
	$altAttr = "(alt\s*=\s*['\"][^'\"]*['\"])";
	$alignAttr = "(align\s*=\s*['\"][^'\"]*['\"])";
	$subAttr = "(sub\s*=\s*['\"][^'\"]*['\"])";
	$text = preg_replace_callback("/<\s*img\s*(($srcAttr|$altAttr|$alignAttr|$subAttr)\s*)*[\s\w\"']*\/>/i", "TransformImageTags", $text);
	$hasChanged |= $articleTypeObj->setProperty($dbColumnName, $text);
}

if ($hasChanged) {
	$Saved = 1;
}
else {
	$Saved = 2;
}

// added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}

header("Location: ". camp_html_article_url($articleObj, $Language, 'edit.php')."&Saved=$Saved");
?>