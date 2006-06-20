<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php");
$crumbs[] = array(getGS('Add new image'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;
?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" ENCTYPE="multipart/form-data">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php putGS('Add new image'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('Description'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_description" VALUE="Image <?php echo Image::GetMaxId(); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('Photographer'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_photographer" VALUE="<?php echo htmlspecialchars($g_user->getRealName()); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('Place'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_place" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('Date'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_date" VALUE="<?php  p($q_now); ?>" SIZE="11" MAXLENGTH="10" class="input_text"> <?php  putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('URL'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_url" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS('Image'); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="FILE" NAME="f_image_file" SIZE="32" class="input_file">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>