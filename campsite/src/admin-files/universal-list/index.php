<?php
require_once($GLOBALS['g_campsiteDir']."/classes/SystemPref.php");
require_once($GLOBALS['g_campsiteDir']."/classes/XR_CcClient.php");
require_once(dirname(__FILE__) . '/../smartlist/Smartlist.php');

camp_load_translation_strings("articles");

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 1);
if (isset($_SESSION['f_language_selected'])) {
    $f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
    $f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0); 

$crumbs = array();
$crumbs[] = array(getGS('Content'), '');
$crumbs[] = array(getGS('Article List'), '');
echo camp_html_breadcrumbs($crumbs);

// set up
$smartlist = new Smartlist();
$smartlist->setPublication($f_publication_id);
$smartlist->setIssue($f_issue_id);
$smartlist->setSection($f_section_id);
$smartlist->setLanguage($f_language_id);

// render
$smartlist->renderFilters();
$smartlist->renderActions();
$smartlist->render();

camp_html_copyright_notice();
?>
</body>
</html>
