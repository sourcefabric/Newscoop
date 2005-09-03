<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Subscription.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('DeleteSection')) {
	CampsiteInterface::DisplayError(getGS('You do not have the right to delete sections.'));	
	exit;
}
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$f_deleteSubscriptions = Input::Get('f_delete_subscriptions', 'string', '', true);
$f_deleteArticles = Input::Get('f_delete_articles', 'string', '', true);
$f_deleteArticles = ($User->hasPermission('DeleteArticle') && ($f_deleteArticles != ''));

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

## added by sebastian
if (function_exists ("incModFile")) {
    incModFile ();
}

$articles =& Article::GetArticles($Pub, $Issue, $Section, $Language);
$numArticles = count($articles);
$doDelete = false;
if ($f_deleteArticles || (!$f_deleteArticles && ($numArticles <= 0))) {
    $doDelete = true;
}
$numSubscriptionsDeleted = 0;
$numArticlesDeleted = 0;
if ($doDelete) {
    $numArticlesDeleted = $sectionObj->delete($f_deleteArticles);
    if ($f_deleteSubscriptions != "") {
        $numSubscriptionsDeleted = Subscription::DeleteSubscriptionsInSection($Pub, $Section);
    }
    $logtext = getGS('Section $1 deleted from issue $2. $3 $4 of $5',
        $sectionObj->getName(),
        $issueObj->getIssueId(),
        $issueObj->getName(),
        $issueObj->getLanguageName(),
        $publicationObj->getName());
    Log::Message($logtext, $User->getUserName(), 22);
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
CampsiteInterface::ContentTop(getGS('Delete section'), $topArray);
?>
    
<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting section"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
        <?php 
        if (!$doDelete) { ?>
            <LI><?php  putGS('There are $1 article(s) left.', $numArticles); ?></LI>
            <LI><?php  putGS('The section $1 could not be deleted.','<B>'.htmlspecialchars($sectionObj->getName()).'</B>'); ?></LI>
            <?php 
        }
        else { ?>
            <LI><?php  putGS('The section $1 has been deleted.','<B>'.htmlspecialchars($sectionObj->getName()).'</B>'); ?></LI>
			<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.$numSubscriptionsDeleted.'</B>'); ?></LI>
            <?php
            if ($f_deleteArticles) { ?>
    			<LI><?php  putGS('A total of $1 articles were deleted.','<B>'.$numArticlesDeleted.'</B>'); ?></LI>
    		<?php 
            }
    	}
        ?>	
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">
        <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
</HTML>
