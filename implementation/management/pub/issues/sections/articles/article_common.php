<?php

class ArticleCommon {
	function articleLink($p_articleObj, $p_adminLanguageId, $p_targetFileName = "", $backlink = "") {
		?>
		<A HREF="/priv/pub/issues/sections/articles/<?php echo $p_targetFileName ?>?Pub=<?php  echo $p_articleObj->getPublicationId(); ?>&Issue=<?php echo $p_articleObj->getIssueId(); ?>&Section=<?php echo $p_articleObj->getSectionId(); ?>&Article=<?php echo $p_articleObj->getArticleId(); ?>&Language=<?php echo $p_adminLanguageId; ?>&sLanguage=<?php echo $p_articleObj->getLanguageId(); ?><?php if ($backlink != "") { ?>&Back=<?php echo urlencode($backlink); } ?>">
		<?php
	} // fn articleLink
	
}

?>