<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir']."/classes/Topic.php");

$translator = \Zend_Registry::get('container')->getService('translator');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans("You do not have the right to rename article type fields.", array(), 'article_type_fields'));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$articleTypeFieldName = Input::Get('f_field_name');
//$oldName = Input::Get('f_oldName');
//$newName = Input::Get('f_newName');

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($articleTypeName, '');
$crumbs[] = array($translator->trans("Article type fields", array(), 'article_type_fields'), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array($translator->trans("Rename field", array(), 'article_type_fields'), "");

echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
?>
<P>
<FORM NAME="add_field_form" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/fields/do_rename.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR><TD COLSPAN="2"><?php echo $translator->trans('The template name may only contain letters and the underscore (_) character.', array(), 'article_type_fields'); ?></TD></TR>
<TR>
	<TD ALIGN="LEFT" ><?php echo $translator->trans("Template Field Name", array(), 'article_type_fields'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_new_field_name" SIZE="20" VALUE="<?php print htmlspecialchars($articleTypeFieldName); ?>" MAXLENGTH="32" alt="alnum|1|A|false|false|_" emsg="<?php 
	echo $translator->trans('The template name may only contain letters and the underscore (_) character.', array(), 'article_type_fields'); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_article_type" VALUE="<?php  print htmlspecialchars($articleTypeName); ?>">
	<INPUT TYPE="HIDDEN" NAME="f_old_field_name" VALUE="<?php print htmlspecialchars($articleTypeFieldName); ?>">
	<INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
