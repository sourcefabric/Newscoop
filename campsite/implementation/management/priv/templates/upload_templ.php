<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

if (!$g_user->hasPermission('ManageTempl')) {
	camp_html_display_error(getGS("You do not have the right to upload templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
$TOL_Language = Input::Get('TOL_Language');

if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$languages = Language::GetLanguages();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($Path));
$crumbs[] = array(getGS("Upload template"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_upload_templ.php" ENCTYPE="multipart/form-data" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Template charset"); ?>:</TD>
	<TD>
		<INPUT TYPE="HIDDEN" NAME="Path" VALUE="<?php  p(htmlspecialchars($Path)); ?>">
		<INPUT TYPE="HIDDEN" NAME="UNIQUE_ID" VALUE="1">
		<SELECT NAME="Charset" class="input_select">
		<OPTION VALUE="">-- <?php putGS("Select a language/character set") ?> --</OPTION>
		<OPTION VALUE="UTF-8"><?php putGS("All languages"); ?>/UTF-8</OPTION>
		<?PHP
		foreach ($languages as $language) { ?>
			<option value="<?php p($language->getCodePage()); ?>" <?php if ($TOL_Language == $language->getCode()) { ?>selected<?php } ?> ><?php p($language->getNativeName().'/'.$language->getCodePage()); ?></OPTION>
			<?PHP
		}
		?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("File"); ?>:</TD>
	<TD>
	<P><INPUT TYPE="FILE" NAME="File" SIZE="32" MAXLENGTH="128" class="input_file">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>