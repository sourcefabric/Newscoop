<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article types."));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Add new article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="6">
		<B><?php putGS("Add new article type"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD COLSPAN="5">
	<INPUT TYPE="TEXT" class="input_text" NAME="f_name" SIZE="15" MAXLENGTH="15">
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