<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/country/country_common.php");

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error(getGS("You do not have the right to change country names."));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');
$f_country_name = trim(Input::Get('f_country_name'));

$country = new Country($f_country_code, $f_country_language);
$language = new Language($f_country_language);

if (empty($f_country_name)) {
	$errorMsgs[] = getGS("You must complete the $1 field.", "<B>".getGS("Name")."</B>");
} else {
	if ($country->setName($f_country_name)) {
		camp_html_goto_page("/$ADMIN/country/index.php");
	} else {
		$errorMsgs[] = getGS('The country name $1 could not be changed','<B>'.htmlspecialchars($country->getName()).'</B>');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Countries"), "/$ADMIN/country/");
$crumbs[] = array(getGS("Changing country name"), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing country name"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/country/edit.php?f_country_code=<?php print urlencode($f_country_code); ?>&f_country_language=<?php  print $f_country_language; ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
