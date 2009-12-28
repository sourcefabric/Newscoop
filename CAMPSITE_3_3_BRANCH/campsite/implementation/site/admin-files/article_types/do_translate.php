<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article types."));
	exit;
}


$f_type_id = Input::Get('f_type_id');
$f_type_language_id = Input::Get('f_type_language_id', 'int', 0);
$f_type_translation_name = trim(Input::Get('f_type_translation_name'));
$correct = true;
$created = false;
//$topicParent = new Topic($f_topic_parent_id);
//$Path = camp_topic_path($topicParent, $f_topic_language_id);

$errorMsgs = array();


if ($f_type_language_id <= 0) {
	$correct = false;
	$errorMsgs[] = getGS('You must choose a language for the article type.');
}

if ($correct) {
	// Translate existing type
	$type = new ArticleType($f_type_id);
	$created = $type->setName($f_type_language_id, $f_type_translation_name);
	if ($created) {
		camp_html_goto_page("/$ADMIN/article_types/index.php");
	}
	else {
		$errorMsgs[] = getGS('The translation could not be added.');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Adding new article type"), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new article type"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) {
			echo "<li>".$errorMsg."</li>";
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
