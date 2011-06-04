<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$articleTypes = ArticleType::GetArticleTypes();

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/merge2.php">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<b><?php putGS("Merge Article Types: Step $1 of $2", "1", "3"); ?></b>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD align="center"><?php putGS("Source Article Type"); ?></td>
	<TD align="center" style="padding-left: 25px;"><?php putGS("Destination Article Type"); ?></td>
</tr>
<tr>
	<td align="center">
	<SELECT NAME="f_src" CLASS="input_select">
	<?php
	foreach ($articleTypes as $at) {
		print '<OPTION VALUE="'. $at .'"';
		if ($f_src == $at) { print " SELECTED "; }
		print '>'. $at .'</OPTION>';

	}
	?>
	</SELECT>
	</TD>

	<td align="center">
	<SELECT NAME="f_dest" CLASS="input_select">
	<?php
	foreach ($articleTypes as $at) {
		print '<OPTION VALUE="'. $at .'"';
		if ($f_dest == $at) { print " SELECTED "; }
		print '>'. $at .'</OPTION>';

	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Go to Step 2'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
