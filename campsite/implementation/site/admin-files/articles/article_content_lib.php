<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleImage.php");

// This is used in TransformSubheads() in order to figure out when
// a SPAN tag closes.
global $g_spanCounter;
$g_spanCounter = -1;

// This is used in TransformInternalLinks() to figure out when
// the internal link tag closes.
global $g_internalLinkCounter;
$g_internalLinkCounter = 0;

// This is used in TransformInternalLinks() to remember the opening link
// tag.
global $g_internalLinkStartTag;
$g_internalLinkStartTag = 0;

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
    global $g_internalLinkStartTag;

    // This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
    $internalLinkStartRegex = "/<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>/i";

    // This matches '</a>'
    $internalLinkEndRegex = "/<\s*\/a\s*>/i";

    if (preg_match($internalLinkEndRegex, $p_match[0])) {
        // Check if we are closing an internal link
        if ($g_internalLinkCounter > 0) {
	    $g_internalLinkCounter = 0;
	    // Make sure the starting link was not blank (a blank
	    // indicates it was a link to no where)
	    if ($g_internalLinkStartTag != "") {
	        // Replace the HTML tag with a template tag
	        $retval = "<!** EndLink>";
		$g_internalLinkStartTag = "";
		return $retval;
	    } else {
	        // The starting link was blank, so we return blank for the
	        // ending link.
	        return "";
	    }
	} else {
	    // Leave the HTML tag as is (for external links).
	    return '</a>';
	}
    } elseif (preg_match($internalLinkStartRegex, $p_match[0])) {
        // Get the URL
        preg_match("/href\s*=\s*[\"'](campsite_internal_link[?][\w&=;]*)[\"']/i", $p_match[0], $url);
	$url = isset($url[1]) ? $url[1] : '';
	$parsedUrl = parse_url($url);
	$parsedUrl = str_replace("&amp;", "&", $parsedUrl);

	$retval = "";
	// It's possible that there isnt a query string - in which case
	// its a link to no where, so we remove it ($retval is empty
	// string).
	if (isset($parsedUrl["query"])) {
	    // Get the target, if there is one
	    preg_match("/target\s*=\s*[\"']([_\w]*)[\"']/i", $p_match[0], $target);
	    $target = isset($target[1]) ? $target[1] : null;

	    // Replace the HTML tag with a template tag
	    $retval = "<!** Link Internal ".$parsedUrl["query"];
	    if (!is_null($target)) {
	        $retval .= " TARGET ".$target;
	    }
	    $retval .= ">";
	}

	// Mark that we are now inside an internal link.
	$g_internalLinkCounter = 1;
	// Remember the starting link tag
	$g_internalLinkStartTag = $retval;

	return $retval;
    }
} // fn TransformInternalLinks


/**
 * This function is a callback for preg_replace_callback().
 * It will replace <img src="http://[hostname]/[image_dir]/cms-image-000000001.jpg" align="center" alt="alternate text" sub="caption text" id="5">
 * with <!** Image [image_template_id] align=CENTER alt="alternate text" sub="caption text">
 * @param array p_match
 * @return string
 */
function TransformImageTags($p_match) {
    global $f_article_number;
    array_shift($p_match);
    $attrs = array();
    foreach ($p_match as $attr) {
        $attr = split('=', $attr);
	if (isset($attr[0]) && !empty($attr[0])) {
	    $attrName = trim(strtolower($attr[0]));
	    $attrValue = isset($attr[1]) ? $attr[1] : '';
	    // Strip out the quotes
	    $attrValue = str_replace('"', '', $attrValue);
	    //			$attrValue = str_replace("'", '', $attrValue);
	    $attrs[$attrName] = $attrValue;
	}
    }

    if (!isset($attrs['id'])) {
        return '';
    } else {
        list($templateId, $imageRatio) = explode('_', $attrs['id']);
	$articleImage = new ArticleImage($f_article_number, null, $templateId);
	if (!$articleImage->exists()) {
	    return '';
	}
    }
    $alignTag = '';
    if (isset($attrs['align'])) {
        $alignTag = 'align="'.$attrs['align'].'"';
    }
    $altTag = '';
    if (isset($attrs['alt']) && strlen($attrs['alt']) > 0) {
        $altTag = 'alt="'.$attrs['alt'].'"';
    }
    $captionTag = '';
    if (isset($attrs['title']) && strlen($attrs['title']) > 0) {
        $captionTag = 'sub="'.$attrs['title'].'"';
    }
    if (isset($attrs['width']) && strlen($attrs['width']) > 0) {
        $widthTag = 'width="'.$attrs['width'].'"';
    }
    if (isset($attrs['height']) && strlen($attrs['height']) > 0) {
        $heightTag = 'height="'.$attrs['height'].'"';
    }
    $ratioTag = '';
    if (isset($imageRatio) && ($imageRatio > 0 && $imageRatio < 100)) {
        $ratioTag = 'ratio="'.$imageRatio.'"';
    }
    $imageTag = "<!** Image $templateId $alignTag $altTag $captionTag $widthTag $heightTag $ratioTag>";
    return $imageTag;
} // fn TransformImageTags

?>