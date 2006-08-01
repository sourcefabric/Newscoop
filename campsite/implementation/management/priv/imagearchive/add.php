<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');

if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
$q_now = $g_ado_db->GetOne("SELECT LEFT(NOW(), 10)");

if (!is_writable($Campsite['IMAGE_DIRECTORY'])) {
	camp_html_add_msg(getGS("Unable to add new image."));
	camp_html_add_msg(getGS("Campsite is unable to write to the file/directory '$1'. Please set the permissions to allow the user '$2' to write to it.",
			$Campsite['IMAGE_DIRECTORY'], $Campsite['APACHE_USER']));
	camp_html_goto_page("/$ADMIN/imagearchive/index.php");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS('Image Archive'), "/$ADMIN/imagearchive/index.php");
$crumbs[] = array(getGS('Add new image'), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;

camp_html_display_msgs();
?>
<script>
function checkAddForm(form) {
	retval = ((form.f_image_url.value != '') || (form.f_image_file.value != ''));
	if (!retval) {
	    alert('<?php putGS("You must select an image file to upload."); ?>');
	    return retval;
	}
	retval = retval && <?php camp_html_fvalidate(); ?>;
	return retval;
} // fn checkAddForm
</script>

<P>
<FORM NAME="image_add" METHOD="POST" ACTION="do_add.php" ENCTYPE="multipart/form-data" onsubmit="return checkAddForm(this);">
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
<script>
document.forms.image_add.f_image_description.focus();
</script>
<?php camp_html_copyright_notice(); ?>