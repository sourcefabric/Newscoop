<?php
/**
 * Note: These functions should eventually become templates.
 *
 */
class CampsiteInterface {

	/**
	 * Display the copyright notice and close the HTML page.
	 */
	function CopyrightNotice() {
		global $Campsite;
		?>
		<TABLE width="100%" style="border-top: 1px solid black; margin-top: 10px;">
		<tr>
			<td style="padding-left: 5px;">
				<a style="font-size:8pt; color: black;" href="http://www.campware.org" target="campware">
				Campsite <?php echo $Campsite['VERSION'] ?> &copy 1999-2005 MDLF, 
				maintained and distributed under GNU GPL by CAMPWARE
				</a>
			</td>
		</tr>
		</table>
		</BODY>
		</HTML>		
		<?php
	} // fn CopyrightNotice
	
	
	/**
	 * Create a HTML SELECT drop down box.
	 *
	 */
	function CreateSelect($p_name, $p_options, $p_selected = null, $p_extras ="", $p_valuesIncluded = false) {
		?>
    	<select name="<?php echo $p_name ?>" <?php echo $p_extras ?>>
    	<?php
    	foreach ($p_options as $key => $value) {
    		if ($p_valuesIncluded) {
    			?>
    			<option value="<?php echo $key; ?>" <?php if (!is_null($p_selected) && ($p_selected == $key)) { echo "selected"; } ?>><?php echo $value; ?></option>
    			<?php
    		}
    		else {
    			?>
    			<option <?php if (!is_null($p_selected) && ($p_selected == $value)) { echo "selected"; } ?>><?php echo $value; ?></option>
    			<?php    			
    		}
    	}
    	?>
    	</select>
		<?php
	} // fn CreateSelect
	
	
	/**
	 * Create a HTML HREF link to an article.
	 *
	 * @param Article p_articleObj
	 *		The article we want to display.
	 *
	 * @param int p_sectionLanguageId
	 *		The section language ID. 
	 *
	 * @param string p_targetFileName
	 *		Which file in the "articles" directory to call.
	 *
	 * @param string p_backLink
	 *		I'm not entirely sure what this is for.  I put it in for backward compatibility.
	 */
	function ArticleLink($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "") {
		$str = '<A HREF="'.CampsiteInterface::ArticleUrl($p_articleObj, $p_interfaceLanguageId, $p_targetFileName, $p_backLink).'">';
		return $str;
	} // fn ArticleLink
	
	
	/**
	 * Create a link to an article.
	 *
	 * @param Article p_articleObj
	 *		The article we want to display.
	 *
	 * @param int p_interfaceLanguageId
	 *		The language ID for the interface language. 
	 *
	 * @param string p_targetFileName
	 *		Which file in the "articles" directory to call.
	 *
	 * @param string p_backLink
	 *		I'm not entirely sure what this is for.  I put it in for backward compatibility.
	 */
	function ArticleUrl($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "") {
		global $ADMIN;
		$str = "/$ADMIN/pub/issues/sections/articles/".$p_targetFileName
			."?Pub=".$p_articleObj->getPublicationId()
			."&Issue=".$p_articleObj->getIssueId()
			."&Section=".$p_articleObj->getSectionId()
			."&Article=".$p_articleObj->getArticleId()
			."&Language=".$p_interfaceLanguageId
			."&sLanguage=".$p_articleObj->getLanguageId();
		if ($p_backLink != "") { 
			$str .="&Back=".urlencode($p_backLink);
		} 
		return $str;
	} // fn ArticleUrl
	
	
	/**
	 * Redirect to the error page and show the given error message.
	 * You can also give a back link for the user to go back to when they
	 * click OK on that screen.
	 *
	 * @param mixed p_errorStr
	 *		This can be a string or an array.  An array is for the case when the
	 *		error string requires arguments.
	 *
	 * @param string p_backLink
	 *
	 * @return void
	 */
	function DisplayError($p_errorStr, $p_backLink = null, $popup = false) {
		global $ADMIN;
		$script = $popup ? 'ad_popup.php' : 'ad.php';
		if (is_array($p_errorStr)) {
			$p_errorStr = call_user_func_array('getGS', $p_errorStr);
		} else {
			$p_errorStr = getGS($p_errorStr);
		}
		$location = "/$ADMIN/$script?ADReason=".urlencode($p_errorStr);
		if (!is_null($p_backLink)) {
			$location .= '&Back='.urlencode($p_backLink);
		}
		header("Location: $location");
		exit;
	} // fn DisplayError
	
} // class CampsiteInterface
?>