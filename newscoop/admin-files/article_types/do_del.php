<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeleteArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to delete article types.", array(), 'article_types'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$doDelete = true;
$errorMsgs = array();

if ($doDelete) {
	$articleType = new ArticleType($articleTypeName);
	$articles = Article::GetArticlesOfType($articleTypeName);
	foreach ($articles as $a) {
		$a->delete();
	}

	$articleType->delete();

	$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('article_type');

	camp_html_goto_page("/$ADMIN/article_types/");
} else {
	$errorMsgs[] = $translator->trans('The article type $1 could not be deleted.', array('$1' => '<B>'.htmlspecialchars($articleTypeName).'</B>'), 'article_types');
}


$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($translator->trans("Delete article type $1", array('$1' => $articleTypeName), 'article_types'), "");

echo camp_html_breadcrumbs($crumbs);


?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Delete article type $1", array('$1' => $articleTypeName), 'article_types'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?PHP
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?PHP
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>