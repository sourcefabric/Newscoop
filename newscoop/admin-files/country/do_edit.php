<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageCountries')) {
	camp_html_display_error($translator->trans("You do not have the right to change country names.", array(), 'country'));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');
$f_country_name = trim(Input::Get('f_country_name'));

$country = new Country($f_country_code, $f_country_language);
$language = new Language($f_country_language);

if (empty($f_country_name)) {
	$errorMsgs[] = $translator->trans("You must fill in the $1 field.", array('$1' => "<B>".$translator->trans("Name")."</B>"));
} else {
	if ($country->setName($f_country_name)) {
		camp_html_goto_page("/$ADMIN/country/index.php");
	} else {
		$errorMsgs[] = $translator->trans('The country name $1 could not be changed', array('$1' =>  '<B>'.htmlspecialchars($country->getName()).'</B>'), 'country');
	}
}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Countries"), "/$ADMIN/country/");
$crumbs[] = array($translator->trans("Changing country name", array(), 'country'), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php echo $translator->trans("Changing country name", array(), 'country'); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/country/edit.php?f_country_code=<?php print urlencode($f_country_code); ?>&f_country_language=<?php  print $f_country_language; ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
