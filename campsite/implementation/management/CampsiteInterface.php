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
		<HR NOSHADE SIZE="1" COLOR="BLACK">
		<a style='font-size:8pt;color:#000000' 
		   href='http://www.campware.org' 
		   target='campware'>
		CAMPSITE  <?php echo $Campsite["version"] ?> &copy 1999-2004 MDLF, 
		maintained and distributed under GNU GPL by CAMPWARE
		</a>
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
	 * Create a link to an article.
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
	function ArticleLink($p_articleObj, $p_sectionLanguageId, $p_targetFileName = "", $p_backLink = "") {
		?>
		<A HREF="/priv/pub/issues/sections/articles/<?php echo $p_targetFileName ?>?Pub=<?php  echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Section=<?php echo $p_articleObj->getSectionId(); ?>&Article=<?php echo $p_articleObj->getArticleId(); ?>&Language=<?php echo $p_sectionLanguageId; ?>&sLanguage=<?php echo $p_articleObj->getLanguageId(); ?><?php if ($p_backLink != "") { ?>&Back=<?php echo urlencode($p_backLink); } ?>">
		<?php
	} // fn ArticleLink
	
	
	/**
	 * Display an error and close the page with the copyright notice.
	 */
	function DisplayError($errorStr) {
		?>
		<br>
		<BLOCKQUOTE>
		<UL><LI><?php putGS($errorStr); ?></LI></UL>
		</BLOCKQUOTE>
		<br>
		<?php
		CampsiteInterface::CopyrightNotice();
	} // fn DisplayError
	
} // class CampsiteInterface
?>