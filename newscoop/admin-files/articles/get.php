<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ShortURL.php');
if (!$g_user->hasPermission('CommentModerate')) {
    camp_html_display_error(getGS("You do not have the right to moderate comments." ));
    exit;
}
// These are optional, depending on whether you are in a section
// or whether editing an article that doesnt have a location.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_unlock = Input::Get('f_unlock', 'string', false, true);

// $f_edit_mode can be "view" or "edit"
$f_edit_mode = Input::Get('f_edit_mode', 'string', 'edit', true);

// Whether to show comments at the bottom of the article
// (you may not want to show them to speed up your loading time)
// Selected language of the article
$f_language_selected = (int)camp_session_get('f_language_selected', 0);

// Fetch article
$articleObj = new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
    camp_html_display_error(getGS('No such article.'));
    exit;
}

$articleInfo = array();
$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(false, true);
foreach ($dbColumns as $dbColumn) {
  $articleInfo[htmlspecialchars($dbColumn->getDisplayName(0))] = $articleData->getProperty($dbColumn->getName());
}
echo $this->view->json($articleInfo);
