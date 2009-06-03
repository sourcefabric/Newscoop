<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error(getGS("You do not have the right to add countries." ));
	exit;
}

$languages = Language::GetLanguages(null, null, null, array(), array(), true);

$f_country_code = trim(Input::Get('f_country_code'));
$f_country_name = trim(Input::Get('f_country_name'));
$f_country_language = trim(Input::Get('f_country_language', 'int', 0));
$correct = true;
$created = false;
$errorMsgs = array();
if (empty($f_country_code)) {
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Code').'</B>');
	$correct = false;
}
if (empty($f_country_name)) {
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
	$correct = false;
}
if (empty($f_country_language) || ($f_country_language == 0)) {
	$correct = false;
    $errorMsgs[] = getGS('You must select a language.');
}
if ($correct) {
	$country = new Country($f_country_code, $f_country_language);
	$created = $country->create(array("Name" => $f_country_name));
	if ($created) {
		camp_html_goto_page("/$ADMIN/country/");
	}
	else {
		$errorMsgs[] = getGS('The country $1 could not be created','<B>'.$f_country_name.'</B>');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Countries"), "/$ADMIN/country/");
$crumbs[] = array(getGS("Add New Country"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Add new country"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<?php
	foreach ($errorMsgs as $errorMsg) { ?>
		<li><?php p($errorMsg); ?></li>
		<?PHP
	}
	?>
	</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/country/add.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
