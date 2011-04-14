<?php
require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
require_once LIBS_DIR . '/ArticleList/ArticleList.php';
require_once LIBS_DIR . '/ArticleList/ArticleList.php';

camp_load_translation_strings('articles');

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

camp_html_content_top(getGS('Search'), NULL);

// set up
$articlelist = new ArticleList();
$articlelist->setPublication($f_publication_id);
$articlelist->setIssue($f_issue_id);
$articlelist->setSection($f_section_id);
$articlelist->setLanguage($f_language_id);

$articlelist->setColVis(TRUE);
$articlelist->setSearch(TRUE);

// render
$articlelist->renderFilters();
$articlelist->renderActions();
$articlelist->render();

camp_html_copyright_notice();
?>
</body>
</html>
