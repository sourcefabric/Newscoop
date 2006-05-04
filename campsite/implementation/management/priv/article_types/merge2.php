<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));
$src =& new ArticleType($f_src);
$dest =& new ArticleType($f_dest);

$tmp = Input::get('f_src_Fe');
foreach ($dest->m_dbColumns as $destColumn) {
	$f_src_c[$destColumn->getName()] = trim(Input::get('f_src_'. $destColumn->getName()));
}
$srcNumArticles = $src->getNumArticles();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="merge3.php?f_src=<?php print $f_src; ?>&f_dest=<?php print $f_dest; ?>">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">Merge Article Types<BR>Step 2 of 3<BR>
		<b>There are <?php print $srcNumArticles; ?> articles associated with <?php print $src->getDisplayName(); ?> that will be merged.</b>
	</TD>
</TR>
<TR>
	<TD>Source Article Type: <?php print $src->getDisplayName(); ?>
	</TD>
	<TD>Destination Article Type: <?php print $dest->getDisplayName(); ?>
	</TD>
</TR>
<?php foreach ($dest->m_dbColumns as $destColumn) { ?>
<TR><TD><SELECT NAME="f_src_<?php print $destColumn->getName(); ?>">
		<?php foreach ($src->m_dbColumns as $srcColumn) { ?>
			<OPTION <?php if ($f_src_c[$destColumn->getName()] == $srcColumn->getPrintName()) { print "SELECTED"; } ?>><?php print $srcColumn->getDisplayName(); ?></OPTION>
		<?php } ?>
			<OPTION <?php if ($f_src_c[$destColumn->getName()] == '--None--') { print "SELECTED"; } ?>>--None--</OPTION>
		</SELECT>
	</TD>
	<TD>= <?php print $destColumn->getDisplayName(); ?></TD>
</TR>
<?php } ?>

<TR>	
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="hidden" NAME="f_src" VALUE="<?php print $f_src; ?>">
	<INPUT TYPE="hidden" NAME="f_dest" VALUE="<?php print $f_dest; ?>">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Back to Step 1'); ?>">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Go to Step 3'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>