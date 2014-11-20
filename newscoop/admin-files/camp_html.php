<?php
/**
 * @package Campsite
 */

/**
 * includes
 */
$ADMIN_DIR = "admin-files";
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/lib_campsite.php");

/**
 * Print out an HTML OPTION element.
 *
 * @param string $p_value
 * @param string $p_selectedValue
 * @param string $p_printValue
 * @return boolean
 * 		Return TRUE if the option is selected, FALSE if not.
 */
function camp_html_select_option($p_value, $p_selectedValue, $p_printValue, $p_attributes=array())
{
	$selected = false;
	$str = '<option value="'.htmlspecialchars($p_value, ENT_QUOTES).'"';
	if (is_array($p_selectedValue)) {
		if (in_array($p_value, $p_selectedValue)) {
			$str .= ' selected="selected"';
			$selected = true;
		}
	} else {
		if (!strcmp($p_value, $p_selectedValue)) {
			$str .= ' selected="selected"';
			$selected = true;
		}
	}
	foreach ($p_attributes as $k => $v) {
	   $str .= " $k=\"$v\"";
	}
	$str .= '>'.htmlspecialchars($p_printValue)."</option>\n";
	echo $str;
	return $selected;
} // fn camp_html_select_option


/**
 * Display the copyright notice and close the HTML page.
 */
function camp_html_copyright_notice($p_displayBorder = true)
{
    global $Campsite;
    $campVersion = new CampVersion();
    if ($p_displayBorder) {
?>
    <div class="footer">
    <?php
    } else {
    ?>
    <div class="footer_plain">
    <?php
    }
    ?>
    <a href="http://newscoop.sourcefabric.org/" target="_blank">
    <?php
      echo $campVersion->getPackage();
      ?></a>, the open content management system for professional journalists.
      <br />
      <?php
      echo $campVersion->getCopyright() . '&nbsp;';
      echo $campVersion->getPackage();
    ?>
       is distributed under
    <?php echo $campVersion->getLicense(); ?>
    </div>
	<?php
} // fn camp_html_copyright_notice


/**
 * Create a HTML HREF link to an article.
 *
 * @param Article $p_articleObj
 *		The article we want to display.
 *
 * @param int $p_interfaceLanguageId
 *		The section language ID.
 *
 * @param string $p_targetFileName
 *		Which file in the "articles" directory to call.
 *
 * @param string $p_backLink
 *		I'm not entirely sure what this is for.  I put it in for backward compatibility.
 */
function camp_html_article_link($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "")
{
	$str = '<A HREF="'.camp_html_article_url($p_articleObj, $p_interfaceLanguageId, $p_targetFileName, $p_backLink).'">';
	return $str;
} // fn camp_html_article_link


/**
 * Create a link to an article.
 *
 * @param Article $p_articleObj
 *		The article we want to display.
 *
 * @param int $p_sectionLanguageId
 *		The language ID for the publication/issue/section.
 *
 * @param string $p_targetFileName
 *		Which file in the "articles" directory to call.
 *
 * @param string $p_backLink
 *		A URL to get back to the previous page the user was on.
 *
 * @param string $p_extraParams
 */
function camp_html_article_url($p_articleObj, $p_sectionLanguageId, $p_targetFileName = "",
	$p_backLink = "", $p_extraParams = null, $p_securityParameter = false)
{
	global $ADMIN;
	$str = "/$ADMIN/articles/".$p_targetFileName
		."?f_publication_id=".$p_articleObj->getPublicationId()
		."&f_issue_number=".$p_articleObj->getIssueNumber()
		."&f_section_number=".$p_articleObj->getSectionNumber()
		."&f_article_number=".$p_articleObj->getArticleNumber()
		."&f_language_id=".$p_sectionLanguageId
		."&f_language_selected=".$p_articleObj->getLanguageId();
	if ($p_securityParameter) {
		$str .= '&'.SecurityToken::URLParameter();
	}
	if ($p_backLink != "") {
		$str .="&Back=".urlencode($p_backLink);
	}
	if (!is_null($p_extraParams)) {
	    $str .= $p_extraParams;
	}
	return $str;
} // fn camp_html_article_url


/**
 * Redirect to the error page and show the given error message.
 * You can also give a back link for the user to go back to when they
 * click OK on that screen.
 *
 * @param mixed $p_errorStr
 *		This can be a string or an array.  An array is for the case when the
 *		error string requires arguments.
 *
 * @param string $p_backLink
 *
 * @return void
 */
function camp_html_display_error($p_errorStr, $p_backLink = null, $popup = false)
{
	global $ADMIN;
	$script = $popup ? 'ad_popup.php' : 'ad.php';
	$location = "/$ADMIN/$script?ADReason=".urlencode($p_errorStr);
	if (!is_null($p_backLink)) {
		$location .= '&Back='.urlencode($p_backLink);
	}
	header("Location: $location");
	exit;
} // fn camp_html_display_error



/**
 * Common header for all content screens.
 *
 * @param string $p_title
 *		The title of the page.  This should have a translation in the language
 *		files.
 *
 * @param array $p_objArray
 *		This represents your current location in the content tree.  This
 * 		can have the following index values, each containing the appropriate
 *		object: 'Pub', 'Issue', 'Section', 'Article'
 *
 * @param boolean $p_includeLinks
 *		Whether to include the links underneath the title or not.  Default TRUE.
 *
 * @param boolean $p_fValidate
 *		Whether to include the fValidate javascript files in the HTML header.
 *      Default FALSE.
 *
 * @param array $p_extraBreadcrumbs
 *		An array in the form 'text' => 'link' for more breadcrumbs.
 *
 * @return void
 */
function camp_html_content_top($p_title, $p_objArray, $p_includeLinks = true,
							   $p_fValidate = false, $p_extraBreadcrumbs = null)
{
	global $Campsite;
	global $ADMIN;
	global $ADMIN_DIR;
    $translator = \Zend_Registry::get('container')->getService('translator');
	$publicationObj = camp_array_get_value($p_objArray, 'Pub', null);
	$issueObj = camp_array_get_value($p_objArray, 'Issue', null);
	$sectionObj = camp_array_get_value($p_objArray, 'Section', null);
	$articleObj = camp_array_get_value($p_objArray, 'Article', null);

	$breadcrumbs = array();
	$breadcrumbs[] = array($translator->trans("Content"), "");
	if (!is_null($publicationObj)) {
	    $prompt =  $translator->trans("Publication").":";
	    $name = $publicationObj->getName();
    	$breadcrumbs[] = array($prompt, "/$ADMIN/pub/", false);
    	$breadcrumbs[] = array($name, "/$ADMIN/pub/edit.php?Pub=".$publicationObj->getPublicationId());
	}

	if ($issueObj instanceof Issue) {
	    $prompt = $translator->trans("Issue").":";
    	$breadcrumbs[] = array($prompt,
    	       "/$ADMIN/issues/"
    	       ."?Pub=".$issueObj->getPublicationId()
    	       ."&Issue=".$issueObj->getIssueNumber()
    	       ."&Language=".$issueObj->getLanguageId(),
    	       false);
	    $name = $issueObj->getName() . ' (' . $issueObj->getLanguageName() . ')';
        $breadcrumbs[] = array($name,
    	       "/$ADMIN/issues/edit.php"
    	       ."?Pub=".$issueObj->getPublicationId()
    	       ."&Issue=".$issueObj->getIssueNumber()
    	       ."&Language=".$issueObj->getLanguageId());
	}
	if ($sectionObj instanceof Section) {
	    $prompt = $translator->trans("Section").":";
		$breadcrumbs[] = array($prompt,
		        "/$ADMIN/sections/"
		        ."?Pub=".$sectionObj->getPublicationId()
                ."&Issue=".$sectionObj->getIssueNumber()
                ."&Language=".$sectionObj->getLanguageId()
                ."&Section=".$sectionObj->getSectionNumber(),
                false);
	    $name = $sectionObj->getName();
        $breadcrumbs[] = array($name,
                "/$ADMIN/sections/edit.php"
                ."?Pub=".$sectionObj->getPublicationId()
                ."&Issue=".$sectionObj->getIssueNumber()
                ."&Language=".$sectionObj->getLanguageId()
                ."&Section=".$sectionObj->getSectionNumber());
	}
	if ($articleObj instanceof Article) {
	    $prompt = $translator->trans("Article").":";
		$breadcrumbs[] = array($prompt,
                "/$ADMIN/articles/index.php"
                ."?f_publication_id=" . $articleObj->getPublicationId()
                ."&f_issue_number=".$articleObj->getIssueNumber()
                ."&f_language_id=".$articleObj->getLanguageId()
                ."&f_section_number=".$articleObj->getSectionNumber()
                ."&f_article_number=".$articleObj->getArticleNumber(),
                false);
	    $name = $articleObj->getName() . ' (' . $articleObj->getLanguageName() . ')';
        $breadcrumbs[] = array($name,
                "/$ADMIN/articles/edit.php"
                ."?f_publication_id=" . $articleObj->getPublicationId()
                ."&f_issue_number=".$articleObj->getIssueNumber()
                ."&f_language_id=".$articleObj->getLanguageId()
                ."&f_section_number=".$articleObj->getSectionNumber()
                ."&f_article_number=".$articleObj->getArticleNumber()
                ."&f_language_selected=".$articleObj->getLanguageId());
	}
	if (is_array($p_extraBreadcrumbs)) {
		foreach ($p_extraBreadcrumbs as $text => $link) {
		    $breadcrumbs[] = array($text, $link);
		}
	}
	$breadcrumbs[] = array($p_title, "");
	if ($p_fValidate) {
		include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
	}
	echo camp_html_breadcrumbs($breadcrumbs);
} // fn camp_html_content_top


/**
 * Renders page title.
 * @param string $title
 * @param bool $toString
 * @return string|void
 */
function camp_html_title($title, $toString = FALSE)
{
    if (strpos($_SERVER['REQUEST_URI'], 'admin/articles/edit.php') !== false) {
        return ''; // no title on article edit screen
    }

    ob_start();
    echo '<div class="toolbar clearfix"><span class="article-title">';
    echo $title;
    echo '</span></div>';
    $content = ob_get_clean();

    if ($toString) {
        return $content;
    }

    echo $content;
}


/**
 * Create a set of breadcrumbs.
 *
 * @param array $p_crumbs
 *		An array in the form 'text' => 'link' for breadcrumbs.
 *      Farthest-away link comes first, increasing in specificity.
 *
 * @param bool $showTitle
 *
 * @return string
 */
function camp_html_breadcrumbs($p_crumbs, $showTitle = TRUE)
{
    $lastCrumb = array_pop($p_crumbs);
    $str = '<div class="breadcrumb-bar clearfix">' . "\n";
    if (count($p_crumbs) > 0) {
	   	$str .= '<ul class="breadcrumbs clearfix">' . "\n";
		foreach ($p_crumbs as $crumb) {
		    if (count($crumb) == 2) {
		        $str .= camp_html_breadcrumb($crumb[0], $crumb[1]);
		    } else {
		        $str .= camp_html_breadcrumb($crumb[0], $crumb[1], $crumb[2]);
		    }
		}
		$str .= '</ul>' . "\n";
    }
    $str .= "</div>\n";

    if ($showTitle) {
        $str .= camp_html_title($lastCrumb[0], TRUE);
    }

    return $str;
} // fn camp_html_breadcrumbs


/**
 * Create one breadcrumb.
 *
 * @param string $p_text
 * @param mixed $p_link
 * @param boolean $p_active
 * @param boolean $p_separator
 * @return string
 */
function camp_html_breadcrumb($p_text, $p_link, $p_separator = true, $p_active = false) {
    $tmpStr = '<li';
    $classStr = '';
	if ($p_separator) {
	    $tmpStr .= ' class="separator">';
	} else {
	    $tmpStr .= '>';
        $classStr = ' class="category"';
    }
    if ($p_link != '') {
        $tmpStr .= '<a href="' . htmlspecialchars($p_link) . '"';
        $tmpStr .= (!empty($classStr)) ? $classStr : '';
        $tmpStr .= '>';
        $tmpStr .= htmlspecialchars($p_text, ENT_NOQUOTES) . '</a>';
    } else {
        $tmpStr .= '<a href="#">' . htmlspecialchars($p_text, ENT_NOQUOTES) . '</a>';
    }
	$tmpStr .= '</li>' . "\n";

    return $tmpStr;
} // fn camp_html_breadcrumb


/**
 * Send the user to the given page.
 *
 * @param string $p_link
 * @param bool $p_exit
 * @param array $p_params
 * @return void
 */
function camp_html_goto_page($p_link, $p_exit = true, array $p_params = array())
{
    global $Campsite;

    if (!empty($p_params)) {
        $p_link .= strpos($p_link, '?') === FALSE ? '?' : '&';
        foreach ($p_params as $key => $val) {
            $p_link .= "$key=$val&";
        }
        $p_link = rtrim($p_link, '&');
    }

    preg_match("`http(s?)://`", $p_link, $linkm);
    //if (strpos($p_link, 'http://') === FALSE) { // location header must be absolute for ie
    if (!count($linkm)) {
        $p_link = (!empty($_SERVER['HTTPS']) ? 'https' :'http') .'://'. $Campsite['HOSTNAME'] . $p_link;
    }

	header("Location: $p_link");
	if ($p_exit) {
	    exit;
	}
} // fn camp_html_goto_page

// This is a simple global to tell us whether messages have been added
// during this page request.
global $g_camp_msg_added;
$g_camp_msg_added = false;

/**
 * Add a message to be sent to a page when camp_html_goto_page() is called.
 * The messages can be displayed on the destination screen by using
 * camp_html_display_msgs().
 *
 * @param mixed $p_errorMsg
 * 		This can be a string or an array of strings.
 * @param string $p_type
 */
function camp_html_add_msg($p_errorMsg, $p_type = "error")
{
	global $g_camp_msg_added;
	$p_type = strtolower($p_type);
	if (!in_array($p_type, array("error", "ok"))) {
		return;
	}
	if (!is_string($p_errorMsg) && !is_array($p_errorMsg)) {
		return;
	}
	if (is_string($p_errorMsg)) {
		$p_errorMsg = array($p_errorMsg);
	}

	foreach ($p_errorMsg as $errorMsg) {
		if (is_string($errorMsg) && (trim($errorMsg) != "")) {
			$g_camp_msg_added = true;
			if (camp_html_has_msg($errorMsg)) {
				return;
			}
			$_SESSION["camp_user_msgs"][] = array("msg" => $errorMsg,
												  "type" => $p_type);
		}
	}
} // fn camp_html_add_msg


/**
 * Return true if the given message was already to the list
 * @param string $p_msg
 * @return boolean
 */
function camp_html_has_msg($p_msg)
{
	if (isset($_SESSION['camp_user_msgs']) && is_array($_SESSION['camp_user_msgs'])) {
		foreach ($_SESSION['camp_user_msgs'] as $msg) {
			if ($msg['msg'] == $p_msg) {
				return true;
			}
		}
	}
	return false;
} // camp_html_has_msg


/**
 * Return true if there are response messages to show to the user.
 *
 * @return boolean
 */
function camp_html_has_msgs()
{
	return (isset($_SESSION['camp_user_msgs']) && count($_SESSION['camp_user_msgs']) > 0);
} // fn camp_html_has_msgs


/**
 * Delete all pending user messages.  This is called at the end of
 * the next page request in admin.php.  This means that messages do not last
 * past one page request.
 *
 * @param boolean $p_calledByAdmin
 *		This is only used by the admin script.  If it is set to true it
 * 		will check if any messages have been posted during this request,
 * 		and if so, it does not delete the messages.
 * @return void
 */
function camp_html_clear_msgs($p_calledByAdmin = false)
{
	global $g_camp_msg_added;
	if (!$p_calledByAdmin || ($p_calledByAdmin && !$g_camp_msg_added)) {
		$_SESSION['camp_user_msgs'] = array();
	}
} // fn camp_html_clear_msgs


/**
 * Display any user messages stored in the session.
 *
 * @param string $p_spacingTop
 * 		How much spacing to put above and below the error message
 * 		(e.g. 10px, 1em, etc...).
 *
 * @return void
 */
function camp_html_display_msgs($p_spacingTop = "1em", $p_spacingBottom = "1em")
{
	if (isset($_SESSION['camp_user_msgs']) && count($_SESSION['camp_user_msgs']) > 0) { ?>
		<table border="0" cellpadding="0" cellspacing="0" class="action_buttons" style="padding-top: <?php echo $p_spacingTop; ?>; padding-bottom: <?php echo $p_spacingBottom; ?>;" width="800px">
		<?php
		$count = 1;
		foreach ($_SESSION['camp_user_msgs'] as $message) {
			?>
			<tr>
				<?php if ($message['type'] == 'ok') { ?>
				<td class="info_message" id="camp_message_<?php p($count); ?>">
				<?php } elseif ($message['type'] == 'error') { ?>
				<td class="error_message" id="camp_message_<?php p($count); ?>">
				<?php } ?>
					<?php echo $message['msg']; ?>
					<script type="text/javascript">
					$('#camp_message_<?php p($count); ?>').delay(3000).fadeOut();
					</script>
				</td>
			</tr>
			<?php
			$count++;
		} ?>
		</table>
		<?php
		$_SESSION['camp_user_msgs'] = array();
	}
} // fn camp_html_display_msgs


/**
 * One common form validate function.
 *
 */
function camp_html_fvalidate()
{
	echo "validateForm(this, 0, 1, 0, 1, 8)";
} // fn camp_html_fvalidate

?>
