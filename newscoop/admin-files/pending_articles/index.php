<?php
require_once LIBS_DIR . '/ArticleList/ArticleList.php';

$translator = \Zend_Registry::get('container')->getService('translator');

$f_publication_id = Input::Get('f_publication_id', 'int', null);
$f_language_id = Input::Get('f_language_id', 'int', 1);
if (isset($_SESSION['f_language_selected'])) {
    $f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
    $f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);

camp_html_content_top($translator->trans('Pending articles', array(), 'articles'), NULL);

// set up
$articlelist = new ArticleList();
$articlelist->setPublication($f_publication_id);
$articlelist->setWorkflowStatus('pending');
$articlelist->setLanguage($f_language_id);

$articlelist->setColVis(TRUE);
$articlelist->setSearch(TRUE);

$articlelist->setHidden('Status');
$articlelist->setHidden('OnFrontPage');
$articlelist->setHidden('OnSectionPage');
$articlelist->setHidden('Comments');
$articlelist->setHidden('Reads');
$articlelist->setHidden('UseMap');
$articlelist->setHidden('Locations');
$articlelist->setHidden('PublishDate');
$articlelist->setHidden('Preview');

// render
//$articlelist->renderFilters();
$articlelist->renderActions();
$articlelist->render();

camp_html_copyright_notice();

?>
</body>
</html>
