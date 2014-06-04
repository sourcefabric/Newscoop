<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Subscription.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Issue.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Section.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Article.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeletePub')) {
	camp_html_display_error($translator->trans("You do not have the right to delete publications.", array(), 'pub'));
	exit;
}

$Pub = Input::Get('Pub', 'int');

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$doDelete = true;

$publicationObj = new Publication($Pub);

$issuesRemaining = Issue::GetNumIssues($Pub);
$errorMsgs = array();
if ($issuesRemaining > 0) {
	$errorMsgs[] = $translator->trans('There are $1 issue(s) left.', array('$1' => $issuesRemaining));
	$doDelete = false;
}

$sectionsRemaining = Section::GetSections($Pub, null, null, null, null, null, true);
if (count($sectionsRemaining) > 0) {
	$errorMsgs[] = $translator->trans('There are $1 section(s) left.', array('$1' => count($sectionsRemaining)));
	$doDelete = false;
}

$articlesRemaining = Article::GetNumUniqueArticles($Pub);
if ($articlesRemaining > 0) {
	$errorMsgs[] = $translator->trans('There are $1 article(s) left.', array('$1' => $articlesRemaining));
	$doDelete = false;
}

$subscriptionsRemaining = Subscription::GetNumSubscriptions($Pub);
if ($subscriptionsRemaining > 0) {
	$errorMsgs[] = $translator->trans('There are $1 subscription(s) left.', array('$1' => $subscriptionsRemaining));
	$doDelete = false;
}

if ($doDelete) {
	$publicationObj->delete();
	$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
	$cacheService->clearNamespace('publication');
	camp_html_goto_page("/$ADMIN/pub");
} else {
	$errorMsgs[] = $translator->trans('The publication $1 could not be deleted.',
						 array('$1' => '<B>'.htmlspecialchars($publicationObj->getName()).'</B>'), 'pub');
}
echo camp_html_content_top($translator->trans("Deleting publication", array(), 'pub'), array("Pub" => $publicationObj));


?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Deleting publication", array(), 'pub'); ?> </B>
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
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/'">
		</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
