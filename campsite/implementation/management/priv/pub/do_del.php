<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Subscription.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Article.php");

// Check permissions
if (!$g_user->hasPermission('DeletePub')) {
	camp_html_display_error(getGS("You do not have the right to delete publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$doDelete = true;

$publicationObj =& new Publication($Pub);

$issuesRemaining = Issue::GetNumIssues($Pub);
$errorMsgs = array();
if ($issuesRemaining > 0) {
	$errorMsgs[] = getGS('There are $1 issue(s) left.', $issuesRemaining);
	$doDelete = false;
}

$sectionsRemaining = Section::GetSections($Pub);
if (count($sectionsRemaining) > 0) {
	$errorMsgs[] = getGS('There are $1 section(s) left.', count($sectionsRemaining));
	$doDelete = false;
}

$articlesRemaining = Article::GetNumUniqueArticles($Pub);
if ($articlesRemaining > 0) {
	$errorMsgs[] = getGS('There are $1 article(s) left.', $articlesRemaining);
	$doDelete = false;
}

$subscriptionsRemaining = Subscription::GetNumSubscriptions($Pub);
if ($subscriptionsRemaining > 0) {
	$errorMsgs[] = getGS('There are $1 subscription(s) left.', $subscriptionsRemaining);
	$doDelete = false;
}

if ($doDelete) {
	$publicationObj->delete();
	header("Location: /$ADMIN/pub");
	exit;
} else {
	$errorMsgs[] = getGS('The publication $1 could not be deleted.',
						 '<B>'.htmlspecialchars($publicationObj->getName()).'</B>');
}
echo camp_html_content_top(getGS("Deleting publication"), array("Pub" => $publicationObj));


?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting publication"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?PHP
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php echo $errorMsg; ?></li>
			<?PHP
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/'">
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>
<?php camp_html_copyright_notice(); ?>
