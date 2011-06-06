<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error(getGS("You do not have the right to change country names."));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');

$country = new Country($f_country_code, $f_country_language);
$language = new Language($f_country_language);

$countryTranslations = Country::GetCountries(null, $f_country_code);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Countries"), "/$ADMIN/country/");
$crumbs[] = array(getGS("Edit country name"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/country/do_edit.php"  >
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Edit country name"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Country"); ?>:</TD>
	<TD>
	<?php
	$names = array();
	foreach ($countryTranslations as $translation) {
		$names[] = htmlspecialchars($translation->getName());
	}
	echo implode(", ", $names);
	?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="f_country_name" SIZE="32" VALUE="<?php  p(htmlspecialchars($country->getName())); ?>">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="f_country_code" VALUE="<?php  print $country->getCode(); ?>">
		<INPUT TYPE="HIDDEN" NAME="f_country_language" VALUE="<?php  print $country->getLanguageId(); ?>">
		<INPUT TYPE="submit" class="button" NAME="OK" VALUE="<?php  putGS('Save'); ?>">
		<!--<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" ONCLICK="location.href='/admin/country/'">-->
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>
