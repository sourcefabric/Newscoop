<?php  
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
$Pub = isset($_REQUEST["Pub"])?$_REQUEST["Pub"]:0;
$Issue = isset($_REQUEST["Issue"])?$_REQUEST["Issue"]:0;
$Section = isset($_REQUEST["Section"])?$_REQUEST["Section"]:0;
$Language = isset($_REQUEST["Language"])?$_REQUEST["Language"]:0;
$sLanguage = isset($_REQUEST["sLanguage"])?$_REQUEST["sLanguage"]:0;
$Article = isset($_REQUEST["Article"])?$_REQUEST["Article"]:0;

$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$issueObj =& new Issue($Pub, $Language, $Issue);
$templateObj =& new Template($issueObj->getArticleTemplateId());

setcookie("TOL_Access", "all", null, "/");
setcookie("TOL_Preview", "on", null, "/");

?>
<?php  
if ($access) {
	if (($articleObj->exists()) && ($templateObj->getName() != "")) {
		if ($User->hasPermission("ManageTempl") || $User->hasPermission("DeleteTempl")) {
			// Show dual-pane view for those with template management priviledges
			?>
			<FRAMESET ROWS="60%,*" BORDER="2">
				<FRAME SRC="<?php print "/look/".$templateObj->getName(); ?>?IdPublication=<?php p($articleObj->getPublicationId()); ?>&NrIssue=<?php p($articleObj->getIssueId()); ?>&NrSection=<?php p($articleObj->getSectionId()); ?>&NrArticle=<?php p($articleObj->getArticleId()); ?>&IdLanguage=<?php p($articleObj->getLanguageId()); ?>" NAME="body" FRAMEBORDER="1">
				<FRAME NAME="e" SRC="empty.php" FRAMEBORDER="1">
			</FRAMESET>
			<?php  
		}
		else {
			// Show single pane for everyone else.
			?>
			<FRAMESET ROWS="100%">
				<FRAME SRC="<?php print "/look/".$templateObj->getName(); ?>?IdPublication=<?php p($articleObj->getPublicationId()); ?>&NrIssue=<?php p($articleObj->getIssueId()); ?>&NrSection=<?php p($articleObj->getSectionId()); ?>&NrArticle=<?php p($articleObj->getArticleId()); ?>&IdLanguage=<?php p($articleObj->getLanguageId()); ?>" NAME="body" FRAMEBORDER="1">
			</FRAMESET>
			<?php
		}
	} 
	else { 
		ArticleTop($articleObj, $issueObj->getLanguageId(), "Preview article", false);
		CampsiteInterface::DisplayError('This article cannot be previewed. Please make sure it has a <B><I>single article</I></B> template selected.');
	}
} 
?>