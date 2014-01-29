<?php
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('DeleteLanguages')) {
	camp_html_display_error($translator->trans("You do not have the right to delete languages.", array(), 'languages'));
	exit;
}

$Language = Input::Get('Language', 'int');
if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $BackLink);
	exit;
}

$languageObj = new Language($Language);
if (!$languageObj->exists()) {
	camp_html_goto_page("/$ADMIN/logout.php");
}

$doDelete = true;
$errorMsgs = array();
$numPublications = $g_ado_db->GetOne("SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=$Language");
if ($numPublications > 0) {
	$doDelete = false;
	$errorMsgs[] = $translator->trans('There are $1 publication(s) left.', array('$1' => $numPublications));
}

$numIssues = $g_ado_db->GetOne("SELECT COUNT(*) FROM Issues WHERE IdLanguage=$Language");
if ($numIssues > 0) {
    $doDelete = false;
    $errorMsgs[] = $translator->trans('There are $1 issue(s) left.', array('$1' => $numIssues));
}

$numSections = $g_ado_db->GetOne("SELECT COUNT(*) FROM Sections WHERE IdLanguage=$Language");
if ($numSections > 0) {
    $doDelete = false;
    $errorMsgs[] = $translator->trans('There are $1 section(s) left.', array('$1' => $numSections));
}

$numArticles = $g_ado_db->GetOne("SELECT COUNT(*) FROM Articles WHERE IdLanguage=$Language");
if ($numArticles > 0) {
    $doDelete = false;
    $errorMsgs[] = $translator->trans('There are $1 article(s) left.', array('$1' => $numArticles));
}

$numCountries = $g_ado_db->GetOne("SELECT COUNT(*) FROM Countries WHERE IdLanguage=$Language");
if ($numCountries > 0) {
    $doDelete = false;
    $errorMsgs[] = $translator->trans('There are $1 countries left.', array('$1' => $numCountries));
}

if ($doDelete) {
	$result = $languageObj->delete();
	if (!PEAR::isError($result)) {
		camp_html_goto_page("/$ADMIN/languages/index.php");
	} else {
		$errorMsgs[] = $result->getMessage();
	}

}

$crumbs = array();
$crumbs[] = array($translator->trans("Configure"), "");
$crumbs[] = array($translator->trans("Languages"), "/$ADMIN/languages/");
$crumbs[] = array($translator->trans("Deleting language", array(), 'languages'), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Deleting language", array(), 'languages'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	   <BLOCKQUOTE>
		<LI><?php  echo $translator->trans('The language $1 could not be deleted.', array('$1' => '<B>'.$languageObj->getNativeName().'</B>'), 'languages'); ?></LI>
        <?php
        foreach ($errorMsgs as $error) { ?>
            <LI><?php p($error); ?></LI>
            <?php
        }
        ?>
	   </BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">
        	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/languages/'">
    	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>