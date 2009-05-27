<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to upload templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
$TOL_Language = camp_session_get('TOL_Language', 'en');

if (!Template::IsValidPath($Path)) {
	camp_html_goto_page("/$ADMIN/templates/");
}
$languages = Language::GetLanguages();

$fullPath = $Campsite['TEMPLATE_DIRECTORY'].$Path;
if (!is_writable($fullPath)) {
	camp_html_add_msg(getGS("Unable to $1 template.", 'upload'));
	camp_html_add_msg(camp_get_error_message(CAMP_ERROR_WRITE_DIR, $fullPath));
	camp_html_goto_page("/$ADMIN/templates/?Path=".urlencode($Path));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Upload template"), "");
echo camp_html_breadcrumbs($crumbs);

camp_html_display_msgs();
?>
<P>
<FORM METHOD="POST" ACTION="do_upload_templ.php" ENCTYPE="multipart/form-data" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input" width="600px">
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("File"); ?>:</TD>
	<TD>
	<P><INPUT TYPE="FILE" NAME="f_file" SIZE="32" class="input_file">
	</TD>
</TR>
<tr>
	<td colspan="2" align="center" style="padding-top: 15px;">
		<?php p(wordwrap(getGS("If the file you specified is a text file, you can convert its character set using the dropdown below."), 60, "<br>")); ?>
	</td>
</tr>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Template charset"); ?>:</TD>
	<TD>
		<INPUT TYPE="HIDDEN" NAME="f_path" VALUE="<?php  p(htmlspecialchars($Path)); ?>">
		<SELECT NAME="f_charset" class="input_select">
		<OPTION VALUE="">-- <?php putGS("Select a language/character set") ?> --</OPTION>
		<OPTION VALUE="UTF-8"><?php putGS("All languages"); ?>/UTF-8</OPTION>
		<?PHP
		foreach ($languages as $language) { ?>
			<option value="<?php p($language->getCodePage()); ?>"><?php p($language->getNativeName().'/'.$language->getCodePage()); ?></OPTION>
			<?PHP
		}
		?>
		</SELECT>
		<?php putGS("(optional)"); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>