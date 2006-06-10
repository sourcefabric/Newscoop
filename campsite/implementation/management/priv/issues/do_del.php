<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Subscription.php');

// Check permissions
if (!$g_user->hasPermission('DeleteIssue')) {
	camp_html_display_error(getGS('You do not have the right to delete issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = Input::Get('f_issue_number', 'int');
$f_language_id = Input::Get('f_language_id', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);

$numArticlesDeleted = $issueObj->delete(true, true);

$tmpArray = array("Pub" => $publicationObj, "Issue"=> $issueObj);
camp_html_content_top(getGS("Deleted issue"), $tmpArray);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD>
		<B> <?php  putGS("Deleted issue"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD>
		<BLOCKQUOTE>
        <LI><?php putGS('The issue $1 has been deleted.','<B>'.htmlspecialchars($issueObj->getName()).'</B>'); ?></LI>
		<LI><?php putGS('A total of $1 articles were deleted.','<B>'.$numArticlesDeleted.'</B>'); ?></LI>
		</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php echo $f_publication_id; ?>'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
