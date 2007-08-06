<?php
camp_load_translation_strings("languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

if (!$g_user->hasPermission('DeleteLanguages')) {
    camp_html_display_error(getGS("You do not have the right to delete languages."));
    exit;
}

$Language = Input::Get('Language', 'int');
if (!Input::IsValid()) {
    camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
    exit;
}

$languageObj =& new Language($Language);
if (!$languageObj->exists()) {
    camp_html_goto_page("/$ADMIN/logout.php");
}

$doDelete = true;
$errorMsgs = array();
$numPublications = $g_ado_db->GetOne("SELECT COUNT(*) FROM Publications WHERE IdDefaultLanguage=$Language");
if ($numPublications > 0) {
    $doDelete = false;
    $errorMsgs[] = getGS('There are $1 publication(s) left.', $numPublications);
}

$numIssues = $g_ado_db->GetOne("SELECT COUNT(*) FROM Issues WHERE IdLanguage=$Language");
if ($numIssues > 0) {
    $doDelete = false;
    $errorMsgs[] = getGS('There are $1 issue(s) left.', $numIssues);
}

$numSections = $g_ado_db->GetOne("SELECT COUNT(*) FROM Sections WHERE IdLanguage=$Language");
if ($numSections > 0) {
    $doDelete = false;
    $errorMsgs[] = getGS('There are $1 section(s) left.', $numSections);
}

$numArticles = $g_ado_db->GetOne("SELECT COUNT(*) FROM Articles WHERE IdLanguage=$Language");
if ($numArticles > 0) {
    $doDelete = false;
    $errorMsgs[] = getGS('There are $1 article(s) left.', $numArticles);
}

$numCountries = $g_ado_db->GetOne("SELECT COUNT(*) FROM Countries WHERE IdLanguage=$Language");
if ($numCountries > 0) {
    $doDelete = false;
    $errorMsgs[] = getGS('There are $1 countries left.', $numCountries);
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
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Languages"), "/$ADMIN/languages/");
$crumbs[] = array(getGS("Deleting language"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
    <TD COLSPAN="2">
        <B> <?php  putGS("Deleting language"); ?> </B>
        <HR NOSHADE SIZE="1" COLOR="BLACK">
    </TD>
</TR>
<TR>
    <TD COLSPAN="2">
       <BLOCKQUOTE>
        <LI><?php  putGS('The language $1 could not be deleted.','<B>'.$languageObj->getNativeName().'</B>'); ?></LI>
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
            <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/languages/'">
        </DIV>
    </TD>
</TR>
</TABLE></CENTER>
<P>

<?php camp_html_copyright_notice(); ?>