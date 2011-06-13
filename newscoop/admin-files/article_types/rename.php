<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to rename article types."));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes(true);
$f_name = trim(Input::get('f_name'));
if (array_search($f_name, $articleTypes) === false) {
    camp_html_display_error(getGS("Invalid article type '$1'.", $f_name));
    exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Rename article type '$1'", $f_name), "");
echo camp_html_breadcrumbs($crumbs);
include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/do_rename.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT TYPE="hidden" VALUE="<?php p($f_name); ?>" NAME="f_oldName">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR><TD COLSPAN="2"><?php putGS('The template name may only contain letters and the underscore (_) character.'); ?></TD></TR>
<TR>
	<TD ALIGN="LEFT" ><?php putGS("Template Type Name"); ?>:</TD>
	<TD ALIGN="LEFT">
	<INPUT TYPE="TEXT" VALUE="<?php p($f_name); ?>" class="input_text" NAME="f_name" ALT="alnum|1|A|false|false|_" emsg="<?php putGS("The template name may only contain letters and the underscore (_) character."); ?>" SIZE="15" MAXLENGTH="15">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Save'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
