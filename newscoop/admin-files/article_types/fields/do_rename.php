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
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to rename article type fields.", array(), 'article_type_fields'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$f_oldName = Input::Get('f_old_field_name');
$f_name = Input::Get('f_new_field_name');

if ($f_oldName == $f_name) {
   	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=". urlencode($articleTypeName));
}
$correct = true;
$created = false;

$errorMsgs = array();
if (empty($f_name)) {
    $correct = false;
    $errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>'));
} else {
	$valid = ArticleType::IsValidFieldName($f_name);
	if (!$valid) {
		$correct = false;
		$errorMsgs[] = $translator->trans('The $1 field may only contain letters and underscore (_) character.', array('$1' => '<B>'.$translator->trans('Name').'</B>'), 'article_type_fields');
    }

    if ($correct) {
    	$old_articleTypeField = new ArticleTypeField($articleTypeName, $f_oldName);
    	if (!$old_articleTypeField->exists()) {
		    $correct = false;
		    $errorMsgs[] = $translator->trans('The field $1 does not exist.', array('$1' => '<B>'.htmlspecialchars($f_oldName).'</B>'), 'article_type_fields');
		}
    }

	if ($correct) {
		$articleTypeField = new ArticleTypeField($articleTypeName, $f_name);
		if ($articleTypeField->exists()) {
			$correct = false;
			$errorMsgs[] = $translator->trans('The field $1 already exists.', array('$1' => '<B>'.htmlspecialchars($f_name). '</B>'), 'article_type_fields');
		}
	}

	if ($correct) {
		$article = new MetaArticle();
		if ($article->has_property($f_name) || method_exists($article, $f_name)) {
			$correct = false;
			$errorMsgs[] = $translator->trans("The property $1 is already in use.", array('$1' => $f_name), 'article_type_fields');
		}
	}

    if ($correct) {
    	$old_articleTypeField->rename($f_name);
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('article_type');

    	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=". urlencode($articleTypeName));
	}
}

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array($translator->trans("Article type fields", array(), 'article_type_fields'), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array($translator->trans("Renaming article type field", array(), 'article_type_fields'), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Renaming article type field", array(), 'article_type_fields'); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/rename.php?f_article_type=<?php print $articleTypeName; ?>&f_field_name=<?php p($f_oldName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
