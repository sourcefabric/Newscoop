<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Article.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Subscription.php");

if (!$g_user->hasPermission('DeleteSection')) {
	camp_html_display_error(getGS('You do not have the right to delete sections.'));
	exit;
}
$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_language_id= Input::Get('f_language_id', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_deleteSubscriptions = Input::Get('f_delete_subscriptions', 'string', '', true);

$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$articles = Article::GetArticles($f_publication_id, $f_issue_number, $f_section_number, $f_language_id);
$numArticles = count($articles);
$numSubscriptionsDeleted = 0;
$numArticlesDeleted = 0;
$numArticlesDeleted = $sectionObj->delete(true);

if ($f_deleteSubscriptions != "") {
    $numSubscriptionsDeleted = Subscription::DeleteSubscriptionsInSection($f_publication_id, $f_section_number);
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 'Section' => $sectionObj);
camp_html_content_top(getGS('Delete section'), $topArray);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting section"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
        <LI><?php putGS('The section $1 has been deleted.','<B>'.htmlspecialchars($sectionObj->getName()).'</B>'); ?></LI>
		<LI><?php putGS('A total of $1 subscriptions were updated.','<B>'.$numSubscriptionsDeleted.'</B>'); ?></LI>
		<LI><?php putGS('A total of $1 articles were deleted.','<B>'.$numArticlesDeleted.'</B>'); ?></LI>
        </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
        <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/sections/?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
