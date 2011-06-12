<?php
camp_load_translation_strings("article_type_fields");
camp_load_translation_strings("api");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

if (!SecurityToken::isValid()) {
    camp_html_display_error(getGS('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article type fields."));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = trim(Input::Get('f_field_name'));
$fieldType = trim(Input::Get('f_article_field_type'));
$rootTopicId = Input::Get('f_root_topic_id', 'int', 0);
$isContent = Input::Get('f_is_content');
$precision = Input::Get('f_precision');
$maxsize = Input::Get('f_maxsize');

$field = new ArticleTypeField($articleTypeName, $fieldName);

$correct = true;
$errorMsgs = array();

if (!ArticleType::IsValidFieldName($fieldName)) {
	$errorMsgs[] = getGS('The $1  must not be void and may only contain letters and underscore (_) character.','<B>'.getGS('Name').'</B>');
	$correct = false;
}
if ($field->exists()) {
	$errorMsgs[] = getGS('The field $1 already exists.', '<B>'.urlencode($fieldName).'</B>');
	$correct = false;
}

$validTypes = array_keys(ArticleTypeField::DatabaseTypes());
if (!in_array($fieldType, $validTypes)) {
	$errorMsgs[] = getGS('Invalid field type.');
	$correct = false;
}

$article = new MetaArticle();
if ($article->has_property($fieldName) || method_exists($article, $fieldName)) {
	$correct = false;
	$errorMsgs[] = getGS("The property '$1' is already in use.", $fieldName);
}

if ($correct) {
    $params = array('root_topic_id'=>$rootTopicId, 'is_content'=>strtolower($isContent) == 'on',
	'precision'=>$precision, 'maxsize'=>$maxsize);
    $field->create($fieldType, $params);
	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array(getGS("Adding new field"), "");

echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new field"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<ul>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?php
		}
		?>
		</ul>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
