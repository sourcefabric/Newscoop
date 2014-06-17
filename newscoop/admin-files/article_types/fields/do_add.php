<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to add article type fields.", array(), 'article_type_fields'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = trim(Input::Get('f_field_name'));
$fieldType = trim(Input::Get('f_article_field_type'));
$rootTopicId = Input::Get('f_root_topic_id', 'int', 0);
$isContent = Input::Get('f_is_content');
$precision = Input::Get('f_precision');
$editorSize = Input::Get('f_editor_size');
$editorSizeCustom = Input::Get('f_editor_size_custom');
$maxsize = Input::Get('f_maxsize');
$eventColor = Input::Get('f_event_color');

$field = new ArticleTypeField($articleTypeName, $fieldName);

$correct = true;
$errorMsgs = array();

if (!ArticleType::IsValidFieldName($fieldName)) {
	$errorMsgs[] = $translator->trans('The $1  must not be void and may only contain letters and underscore (_) character.', array('$1' => $translator->trans('Name')), 'article_type_fields');
	$correct = false;
}
if ($field->exists()) {
	$errorMsgs[] = $translator->trans('The field $1 already exists.', array('$1' => '<B>'.urlencode($fieldName).'</B>'), 'article_type_fields');
	$correct = false;
}

$validTypes = array_keys(ArticleTypeField::DatabaseTypes());
if (!in_array($fieldType, $validTypes)) {
	$errorMsgs[] = $translator->trans('Invalid field type.', array(), 'article_type_fields');
	$correct = false;
}

$article = new MetaArticle();
if ($article->has_property($fieldName) || method_exists($article, $fieldName)) {
	$correct = false;
	$errorMsgs[] = $translator->trans("The property $1 is already in use.", array('$1' => $fieldName), 'article_type_fields');
}

if ($correct) {
    if ($editorSize == 'small') $editorSize = ArticleTypeField::BODY_ROWS_SMALL;
    else if ($editorSize == 'medium') $editorSize = ArticleTypeField::BODY_ROWS_MEDIUM;
    else if ($editorSize == 'large') $editorSize = ArticleTypeField::BODY_ROWS_LARGE;
    else if ($editorSize == 'custom') $editorSize = $editorSizeCustom;
    else $editorSize = ArticleTypeField::BODY_ROWS_MEDIUM;
    
    $params = array('root_topic_id'=>$rootTopicId, 'is_content'=>strtolower($isContent) == 'on',
	'precision'=>$precision, 'maxsize'=>$maxsize, 'editor_size' => $editorSize, 'event_color' => $eventColor);
	$field->create($fieldType, $params);
    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('article_type');
	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
}

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array($translator->trans("Article type fields", array(), 'article_type_fields'), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array($translator->trans("Adding new field", array(), 'article_type_fields'), "");

echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
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
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
