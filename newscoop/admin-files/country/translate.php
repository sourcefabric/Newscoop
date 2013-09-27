<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error($translator->trans("You do not have the right to translate country names.", array(), 'country'));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');

$country = new Country($f_country_code, $f_country_language);
$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$countryTranslations = Country::GetCountries(null, $f_country_code);

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Countries"), "/$ADMIN/country/");
$crumbs[] = array($translator->trans("Translate country name", array(), 'country'), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/country/do_translate.php"  >
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php echo $translator->trans("Translate country name", array(), 'country'); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Country"); ?>:</TD>
	<TD>
	<?php
	$names = array();
	foreach ($countryTranslations as $item) {
		$names[] = $item->getName();
	}
	echo implode(", ", $names);
	?>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_country_name" SIZE="32" >
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php echo $translator->trans("Language"); ?>:</TD>
	<TD>
		<SELECT NAME="f_country_new_language" class="input_select">
		<?php
		foreach ($languages as $language) {
			$num = Country::GetNumCountries($language->getLanguageId(), $f_country_code);
			if ($num == 0) { ?>
				<OPTION VALUE="<?php p($language->getLanguageId()); ?>"><?php p(htmlspecialchars($language->getNativeName())); ?>
				<?php
			}
		} ?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="HIDDEN" NAME="f_country_code" VALUE="<?php print $f_country_code; ?>">
	<INPUT TYPE="HIDDEN" NAME="f_country_orig_language" VALUE="<?php  print $f_country_language; ?>">
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  echo $translator->trans('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  echo $translator->trans('Cancel'); ?>" ONCLICK="location.href='/admin/country/'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
