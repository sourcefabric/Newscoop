<?php
/**
 * Note: These functions should eventually become templates.
 *
 */
class CampsiteInterface {

	function CopyrightNotice() {
		?>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
		<a style='font-size:8pt;color:#000000' 
		   href='http://www.campware.org' 
		   target='campware'>
		CAMPSITE  2.1.5 &copy 1999-2004 MDLF, 
		maintained and distributed under GNU GPL by CAMPWARE
		</a>
		</BODY>
		</HTML>		
		<?php
	} // fn CopyrightNotice
	
	
	function ArticleLink($p_articleObj, $p_adminLanguageId, $p_targetFileName = "", $backlink = "") {
		?>
		<A HREF="/priv/pub/issues/sections/articles/<?php echo $p_targetFileName ?>?Pub=<?php  echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Section=<?php echo $p_articleObj->getSectionId(); ?>&Article=<?php echo $p_articleObj->getArticleId(); ?>&Language=<?php echo $p_adminLanguageId; ?>&sLanguage=<?php echo $p_articleObj->getLanguageId(); ?><?php if ($backlink != "") { ?>&Back=<?php echo urlencode($backlink); } ?>">
		<?php
	} // fn ArticleLink
	
	
	function DisplayError($errorStr) {
		?>
		<BLOCKQUOTE>
		<LI><?php putGS($errorStr); ?></LI>
		</BLOCKQUOTE>
		<?php
		CampsiteInterface::CopyrightNotice();
	} // fn DisplayError
	
} // class CampsiteInterface
?>