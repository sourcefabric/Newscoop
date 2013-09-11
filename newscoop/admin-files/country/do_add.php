<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");
$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error($translator->trans("You do not have the right to add countries." , array(), 'country'));
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
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Code').'</B>'));
	$correct = false;
}
if (empty($f_country_name)) {
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Name').'</B>'));
	$correct = false;
}
if (empty($f_country_language) || ($f_country_language == 0)) {
	$correct = false;
    $errorMsgs[] = $translator->trans('You must select a language.');
}
if ($correct) {
	$country = new Country($f_country_code, $f_country_language);
	$created = $country->create(array("Name" => $f_country_name));
	if ($created) {
		camp_html_goto_page("/$ADMIN/country/");
	}
	else {
		$errorMsgs[] = $translator->trans('The country $1 could not be created.', array('$1' => '<strong>'.$f_country_name.'</strong>'), 'country');
		$errorMsgs[] = $translator->trans('Country with code $1 exists already.', array('$1' => $f_country_code), 'country');
	}
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Countries"), "/$ADMIN/country/");
$crumbs[] = array($translator->trans("Add new country"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php echo $translator->trans("Add new country"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/country/add.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
