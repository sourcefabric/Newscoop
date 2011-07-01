<?php
camp_load_translation_strings("article_type_fields");
camp_load_translation_strings("api");
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to translate article types."));
	exit;
}


$f_field_id = Input::Get('f_field_id');
$f_field_language_id = Input::Get('f_field_language_id', 'int', 0);
$f_field_translation_name = trim(Input::Get('f_field_translation_name'));
$f_article_type = Input::Get('f_article_type');
$correct = true;
$created = false;
//$topicParent = new Topic($f_topic_parent_id);
//$Path = camp_topic_path($topicParent, $f_topic_language_id);

$errorMsgs = array();
if (empty($f_field_translation_name)) {
	$correct = false;
	$errorMsgs[] = getGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>');
}

if ($f_field_language_id <= 0) {
	$correct = false;
	$errorMsgs[] = getGS('You must choose a language for the field.');
}

if ($correct) {
	// Translate existing type
	$field = new ArticleTypeField($f_article_type, $f_field_id);
	$created = $field->setName($f_field_language_id, $f_field_translation_name);
	if ($created) {
		camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=". $f_article_type);
		exit;
	}
	else {
		$errorMsgs[] = getGS('The translation could not be added.');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($f_article_type, '');
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($f_article_type));
$crumbs[] = array(getGS("Adding new article type"), "");

echo camp_html_breadcrumbs($crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Translating field"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/?f_article_type=<?php p($f_article_type); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
