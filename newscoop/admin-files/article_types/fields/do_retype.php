<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to reassign article type fields.", array(), 'article_type_fields'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = trim(Input::Get('f_field_name'));
$fieldType = trim(Input::Get('f_article_field_type'));

$field = new ArticleTypeField($articleTypeName, $fieldName);

$correct = true;
$errorMsgs = array();

if (!$field->exists()) {
	$errorMsgs[] = $translator->trans('The field $1 does not exist.', array('$1' => '<B>'.urlencode($fieldName).'</B>'), 'article_type_fields');
	$correct = false;
}

if (array_search($fieldType, $field->getConvertibleToTypes()) === false) {
	$errorMsgs[] = $translator->trans('Can not convert the field $1 from $2 to type $3.', array('$1' => $fieldName, '$2' => $field->getType(), '$3' => $fieldType), 'article_type_fields');
	$correct = false;
}

if ($correct) {
	$field->setType($fieldType);

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
		<B> <?php  echo $translator->trans("Adding new field", array(), 'article_type_fields'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE><ul>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?php
		}
		?>
		</ul></BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
