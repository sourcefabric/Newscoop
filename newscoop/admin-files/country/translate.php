<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error(getGS("You do not have the right to translate country names."));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');

$country = new Country($f_country_code, $f_country_language);
$languages = Language::GetLanguages(null, null, null, array(), array(), true);
$countryTranslations = Country::GetCountries(null, $f_country_code);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Countries"), "/$ADMIN/country/");
$crumbs[] = array(getGS("Translate country name"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/country/do_translate.php"  >
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Translate country name"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Country"); ?>:</TD>
	<TD>
	<?php
	$names = array();
	foreach ($countryTranslations as $item) {
		$names[] = $item->getName();
	}
	echo implode(", ", $names);
	?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_country_name" SIZE="32" >
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
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
	<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/country/'">-->
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
