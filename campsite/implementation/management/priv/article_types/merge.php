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

$articleTypes = ArticleType::GetArticleTypes();

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));
#$src =& new ArticleType($f_src);
#$dest =& new ArticleType($f_dest);


$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="merge2.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">Merge Article Types<BR>Step 1 of 3</TD>
</TR>
<TR>
	<TD>Source Article Type<BR>

	<SELECT NAME="f_src">
	<?php
	foreach ($articleTypes as $at) {
		print '<OPTION VALUE="'. $at .'"';
		if ($f_src == $at) { print " SELECTED "; }
		print '">'. $at .'</OPTION>';

	} 
	?>
	</SELECT>
	</TD>

	<TD>Destination Article Type<BR>
	<SELECT NAME="f_dest">
	<?php
	foreach ($articleTypes as $at) {
		print '<OPTION VALUE="'. $at .'"';
		if ($f_dest == $at) { print " SELECTED "; }
		print '">'. $at .'</OPTION>';

	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>	
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Go to Step 2'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>