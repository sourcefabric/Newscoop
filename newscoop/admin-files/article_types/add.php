<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

$translator = \Zend_Registry::get('container')->getService('translator');
// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error($translator->trans('You do not have the right to add article types.', array(), 'article_types'));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes(true);

$crumbs = array();
$crumbs[] = array($translator->trans('Configure'), "");
$crumbs[] = array($translator->trans('Article Types'), "/$ADMIN/article_types/");
$crumbs[] = array($translator->trans('Add new article type', array(), 'article_types'), "");
echo camp_html_breadcrumbs($crumbs);
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php echo $translator->trans('Article Types', array(), 'article_types'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
</TR>
<TR><TD COLSPAN="2"><?php echo $translator->trans('The template name may only contain letters and the underscore (_) character.', array(), 'article_types'); ?><BR>
</TD></TR>
<TR>
	<TD ALIGN="LEFT" ><?php echo $translator->trans('Template Type Name', array(), 'article_types'); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" ALT="alnum|1|A|0|0|_" emsg="<?php echo $translator->trans("The template name may only contain letters and the underscore (_) character.", array(), 'article_types'); ?>" SIZE="15" MAXLENGTH="15">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  echo $translator->trans('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
