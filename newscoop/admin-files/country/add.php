<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error($translator->trans("You do not have the right to add countries." , array(), 'country'));
	exit;
}

$languages = Language::GetLanguages(null, null, null, array(), array(), true);

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Countries"), "/$ADMIN/country/");
$crumbs[] = array($translator->trans("Add new country"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/country/do_add.php">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  echo $translator->trans("Add new country"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Code"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_country_code" SIZE="2" MAXLENGTH="2">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_country_name" SIZE="32">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Language"); ?>:</TD>
	<TD>
		<SELECT NAME="f_country_language" class="input_select">
		<?php
		foreach ($languages as $language) {
			camp_html_select_option($language->getLanguageId(), 0, $language->getNativeName());
    	} ?>
    	</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php echo $translator->trans('Save'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  echo $translator->trans('Cancel'); ?>" ONCLICK="location.href='/admin/country/'">-->
		</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
