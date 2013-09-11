<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/DbReplication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ShortURL.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!$g_user->hasPermission('CommentModerate')) {
    camp_html_display_error($translator->trans("You do not have the right to moderate comments.", array(), 'articles'));
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
    camp_html_display_error($translator->trans('No such article.', array(), 'articles'));
    exit;
}

$articleInfo = array();
$articleData = $articleObj->getArticleData();
// Get article type fields.
$dbColumns = $articleData->getUserDefinedColumns(false, true);
foreach ($dbColumns as $dbColumn) {
    if ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
        $value = $articleData->getProperty($dbColumn->getName()) ? $translator->trans('On', array(), 'articles') : $translator->trans('Off', array(), 'articles');
        $articleInfo[htmlspecialchars($dbColumn->getDisplayName($articleObj->getLanguageId()))] = $value;
    } else {
        $articleInfo[htmlspecialchars($dbColumn->getDisplayName($articleObj->getLanguageId()))] = $articleData->getProperty($dbColumn->getName());
    }
}
$articleInfo[$translator->trans('Title', array(), 'articles')] = $articleObj->getTitle();         // THIS IS REALLY BAD, NEVER LOCALIZE INTERNALLY
$articleInfo[$translator->trans('Date')] = $articleObj->getCreationDate();   // But I don't know what possibly depends on this so we leave it for now
$articleInfo['title'] = $articleObj->getTitle();
$articleInfo['date'] = $articleObj->getCreationDate();

echo $this->view->json($articleInfo);
