<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/country/country_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('DeleteCountries')) {
	camp_html_display_error($translator->trans("You do not have the right to delete countries.", array(), 'country'));
	exit;
}

$f_country_code = Input::Get('f_country_code');
$f_country_language = Input::Get('f_country_language');

$country = new Country($f_country_code, $f_country_language);
$language = new Language($f_country_language);
$deleted = $country->delete();
if ($deleted) {
	camp_html_goto_page("/$ADMIN/country");
}
$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Countries"), "/$ADMIN/country/");
$crumbs[] = array($translator->trans("Delete country", array(), 'country'), "");
echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php echo $translator->trans("Delete country", array(), 'country'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
	<LI><?php echo $translator->trans('The country $1 could not be deleted.' , array('$1' => '<B>'.htmlspecialchars($country->getName()).'('.htmlspecialchars($language->getNativeName()).')</B>'), 'country'); ?></LI>
	</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/country/'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
