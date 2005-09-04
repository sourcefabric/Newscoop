<?php
/**
 * @package Campsite
 */

/**
 * includes
 */
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");

/**
 * Display the copyright notice and close the HTML page.
 */
function camp_html_copyright_notice($p_displayBorder = true)
{
	global $Campsite;
	if ($p_displayBorder) {
	?>
	<table width="100%" align="center" style="border-top: 1px solid black; margin-top: 15px;">
	<?php
	} else {
	?>
	<table width="100%" align="center" style="margin-top: 5px;">
	<?php
	}
	?>
	<tr>
		<td style="padding-left: 5px; padding-top: 10px;" align="center">
			<a style="font-size:8pt; color: black;" href="http://www.campware.org" target="campware">
			Campsite <?php echo $Campsite['VERSION'] ?> &copy 1999-2005 MDLF, 
			maintained and distributed under GNU GPL by CAMPWARE
			</a>
		</td>
	</tr>
	</table>
	<?php
} // fn camp_html_copyright_notice


/**
 * Create a HTML SELECT drop down box.
 *
 */
function camp_html_create_select($p_name, $p_options, $p_selected = null, $p_extras ="", $p_valuesIncluded = false) 
{
	?>
	<select name="<?php echo $p_name ?>" <?php echo $p_extras ?>>
	<?php
	foreach ($p_options as $key => $value) {
		if ($p_valuesIncluded) {
			?>
			<option value="<?php echo $key; ?>" <?php if (!is_null($p_selected) && ($p_selected == $key)) { echo "selected"; } ?>><?php echo htmlspecialchars($value); ?></option>
			<?php
		}
		else {
			?>
			<option <?php if (!is_null($p_selected) && ($p_selected == $value)) { echo "selected"; } ?>><?php echo htmlspecialchars($value); ?></option>
			<?php    			
		}
	}
	?>
	</select>
	<?php
} // fn camp_html_create_select


/**
 * Create a HTML HREF link to an article.
 *
 * @param Article $p_articleObj
 *		The article we want to display.
 *
 * @param int $p_sectionLanguageId
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
 * @param int $p_interfaceLanguageId
 *		The language ID for the interface language. 
 *
 * @param string $p_targetFileName
 *		Which file in the "articles" directory to call.
 *
 * @param string $p_backLink
 *		A URL to get back to the previous page the user was on.
 *
 * @param string $p_extraParams
 */
function camp_html_article_url($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "", $p_extraParams = null) 
{
	global $ADMIN;
	$str = "/$ADMIN/articles/".$p_targetFileName
		."?Pub=".$p_articleObj->getPublicationId()
		."&Issue=".$p_articleObj->getIssueId()
		."&Section=".$p_articleObj->getSectionId()
		."&Article=".$p_articleObj->getArticleId()
		."&Language=".$p_interfaceLanguageId
		."&sLanguage=".$p_articleObj->getLanguageId();
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
 *		The title of the page.  This should have a translation in the language files.
 *
 * @param array $p_objArray
 *		This represents your current location in the content tree.  This
 * 		can have the following index values, each containing the appropriate object:
 *		'Pub', 'Issue', 'Section', 'Article'
 *
 * @param boolean $p_includeLinks
 *		Whether to include the links underneath the title or not.  Default TRUE.
 *
 * @param boolean $p_fValidate
 *		Whether to include the fValidate javascript files in the HTML header. Default FALSE.
 *
 * @param array $p_extraBreadcrumbs
 *		An array in the form 'text' => 'link' for more breadcrumbs.
 *
 * @return void
 */
function camp_html_content_top($p_title, $p_objArray, $p_includeLinks = true, $p_fValidate = false, $p_extraBreadcrumbs = null) 
{
	global $Campsite;
	global $ADMIN;
	$publicationObj = camp_array_get_value($p_objArray, 'Pub', null);
	$issueObj = camp_array_get_value($p_objArray, 'Issue', null);
	$sectionObj = camp_array_get_value($p_objArray, 'Section', null);
	$articleObj = camp_array_get_value($p_objArray, 'Article', null);
	?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<?php if ($p_fValidate) { ?>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>	
	<?php } ?>
	<TITLE><?php p($p_title); ?></TITLE>
</HEAD>

<BODY>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php p($p_title); ?>
	</TD>
<?php 
if ($p_includeLinks) {
?>
	<TD ALIGN="right">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<?php 
			if (is_array($p_extraBreadcrumbs)) {
				foreach ($p_extraBreadcrumbs as $text => $link) {
					?>
					<TD><A HREF="<?php echo $link; ?>" class="breadcrumb" ><?php p($text); ?></A></TD>
					<td class="breadcrumb_separator">&nbsp;</td>
					<?php
				}
			}
			if (!is_null($articleObj)) {
			?>
			<!-- "Articles" Link -->
			<TD><A HREF="/<?php echo $ADMIN; ?>/articles/?Pub=<?php p($sectionObj->getPublicationId()); ?>&Issue=<?php p($sectionObj->getIssueId()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>&Section=<?php p($sectionObj->getSectionId()); ?>" class="breadcrumb" ><?php putGS("Articles");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<?php
			}
			if (!is_null($sectionObj)) { ?>
			<!-- "Sections" link -->
			<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($issueObj->getPublicationId()); ?>&Issue=<?php p($issueObj->getIssueId()); ?>&Language=<?php p($issueObj->getLanguageId()); ?>" class="breadcrumb"><?php putGS("Sections"); ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<?PHP
			}
			if (!is_null($issueObj)) { ?>
			<!-- "Issues" Link -->
			<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php p($issueObj->getPublicationId()); ?>" class="breadcrumb"><?php putGS("Issues"); ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<?PHP
			}
			?>
			<!-- "Publications" Link -->
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
		</TR>
		</TABLE>
	</TD>
<?php
} // if ($p_includeLinks)
?>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<?php if (!is_null($publicationObj)) { ?>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%" class="current_location_title">&nbsp;<?php putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php print htmlspecialchars($publicationObj->getName()); ?></TD>
	<?php
	}
	if (!is_null($issueObj)) { ?>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%" class="current_location_title">&nbsp;<?php putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php print $issueObj->getIssueId(); ?>. <?php  print htmlspecialchars($issueObj->getName()); ?> (<?php print htmlspecialchars($issueObj->getLanguageName()) ?>)</TD>
	<?PHP
	}
	if (!is_null($sectionObj)) { ?>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%" class="current_location_title">&nbsp;<?php putGS("Section"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php print $sectionObj->getSectionId(); ?>. <?php  print htmlspecialchars($sectionObj->getName()); ?></TD>
	<?PHP
	}
	if (!is_null($articleObj)) { ?>
	<TD ALIGN="RIGHT" NOWRAP VALIGN="TOP" width="1%" class="current_location_title">&nbsp;<?php putGS("Article"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php print htmlspecialchars($articleObj->getTitle()); ?> (<?php print htmlspecialchars($articleObj->getLanguageName()); ?>)</TD>
	<?PHP
	}
	?>
</TR>
</TABLE>
	<?php
} // fn camp_html_content_top

?>