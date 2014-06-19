<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Subscription.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeleteIssue')) {
	camp_html_display_error($translator->trans('You do not have the right to delete issues.', array(), 'issues'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = Input::Get('f_issue_number', 'int');
$f_language_id = Input::Get('f_language_id', 'int');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid Input: $1', array('$1' => Input::GetErrorString()), 'issues'));
	exit;
}
$publicationObj = new Publication($f_publication_id);
$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);

$tmpArray = array("Pub" => $publicationObj, "Issue"=> $issueObj);
camp_html_content_top($translator->trans("Deleted issue", array(), 'issues'), $tmpArray);

$issueName = htmlspecialchars($issueObj->getName());
$numArticlesDeleted = $issueObj->delete(true, true);

$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
$cacheService->clearNamespace('issue');

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD>
		<B> <?php echo $translator->trans("Deleted issue", array(), 'issues'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD>
		<BLOCKQUOTE>
        <LI><?php echo $translator->trans('The issue $1 has been deleted.', array('$1' => '<B>'.$issueName.'</B>'), 'issues'); ?></LI>
		<LI><?php echo $translator->trans('A total of $1 articles were deleted.', array('$1' => '<B>'.$numArticlesDeleted.'</B>'), 'issues'); ?></LI>
		</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php echo $f_publication_id; ?>'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
