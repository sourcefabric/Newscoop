<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Subscription.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Section.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Article.php");

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeletePub')) {
	camp_html_display_error(getGS("You do not have the right to delete publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$doDelete = true;

$publicationObj = new Publication($Pub);

$issuesRemaining = Issue::GetNumIssues($Pub);
$errorMsgs = array();
if ($issuesRemaining > 0) {
	$errorMsgs[] = getGS('There are $1 issue(s) left.', $issuesRemaining);
	$doDelete = false;
}

$sectionsRemaining = Section::GetSections($Pub, null, null, null, null, null, true);
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
	camp_html_goto_page("/$ADMIN/pub");
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
<P>
<?php camp_html_copyright_notice(); ?>
