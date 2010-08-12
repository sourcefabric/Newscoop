<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('DeleteArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to delete article types."));
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
	camp_html_goto_page("/$ADMIN/article_types/");
} else {
	$errorMsgs[] = getGS('The article type $1 could not be deleted.', '<B>'.htmlspecialchars($articleTypeName).'</B>');
}


$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Delete article type $1", $articleTypeName), "");

echo camp_html_breadcrumbs($crumbs);


?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete article type $1", $articleTypeName); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>