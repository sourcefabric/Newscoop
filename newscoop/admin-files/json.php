<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

header('Content-type: application/json');

require_once WWW_DIR . '/classes/ServerRequest.php';

// include valid callbacks files
// TODO replace with Zend_Loader
require_once WWW_DIR . '/classes/Extension/WidgetManager.php';
require_once LIBS_DIR . '/ArticleList/ArticleList.php';
require_once LIBS_DIR . '/ContextList/ContextList.php';
require_once LIBS_DIR . '/MediaList/MediaList.php';
require_once LIBS_DIR . '/ImageList/ImageList.php';
require_once LIBS_DIR . '/ImageList/NewsfeedImageList.php';
require_once WWW_DIR . '/classes/GeoNames.php';
require_once WWW_DIR . '/classes/GeoMap.php';
require_once WWW_DIR . '/classes/Article.php';
require_once WWW_DIR . '/classes/ArticleData.php';

try {
    // init request
    $serverRequest = new ServerRequest($_POST['callback'], isset($_POST['args']) ? $_POST['args'] : array());

    // set permissions
    $serverRequest->allow('ping');
    $serverRequest->allow('ArticleList::doAction'); // checked in handler
    $serverRequest->allow('ArticleList::doData');
    $serverRequest->allow('ArticleList::getFilterIssues');
    $serverRequest->allow('ArticleList::getFilterSections');
    $serverRequest->allow('ArticleList::doOrder', 'Publish');

    $serverRequest->allow('ContextList::doAction'); // checked in handler
    $serverRequest->allow('ContextList::doData');
    $serverRequest->allow('ContextList::getFilterIssues');
    $serverRequest->allow('ContextList::getFilterSections');
    $serverRequest->allow('ContextList::doOrder', 'Publish');


    $serverRequest->allow('WidgetManager::AddWidget');
    $serverRequest->allow('WidgetManagerDecorator::delete');
    $serverRequest->allow('WidgetRendererDecorator::render');
    $serverRequest->allow('WidgetManagerDecorator::getSetting');
    $serverRequest->allow('WidgetContext::setWidgets');
    $serverRequest->allow('WidgetManagerDecorator::update');
    $serverRequest->allow('Topic::UpdateOrder');
    $serverRequest->allow('Topic::add');
    $serverRequest->allow('Geo_Names::FindCitiesByPosition');
    $serverRequest->allow('Geo_Names::FindCitiesByName');
    $serverRequest->allow('Geo_Map::LoadMapData');
    $serverRequest->allow('Geo_Map::StoreMapData', 'ChangeArticle');
    $serverRequest->allow('MediaList::doData');
    $serverRequest->allow('ImageList::doData');
    $serverRequest->allow('NewsfeedImageList::doData');
    $serverRequest->allow('MediaList::doDelete');
    $serverRequest->allow('ImageList::doDelete');
    $serverRequest->allow('ImageList::doApprove');
    $serverRequest->allow('MediaList::doApprove');
    $serverRequest->allow('ImageList::doDisapprove');
    $serverRequest->allow('MediaList::doDisapprove');
    $serverRequest->allow('Article::setOnFrontPage','Publish');
    $serverRequest->allow('Article::setOnSectionPage','Publish');
    $serverRequest->allow('Article::setIsPublic','Publish');
    $serverRequest->allow('Article::setKeywords');
    $serverRequest->allow('Article::setIsLocked');
    $serverRequest->allow('ArticleData::setProperty');
    $serverRequest->allow('ArticleTypeField::SetFieldColor', 'ManageArticleTypes');
    // execute
    echo json_encode($serverRequest->execute());
} catch (Exception $e) {
    echo json_encode(array(
        'error_code' => $e->getCode(),
        'error_message' => getGS('Error') . ': ' . $e->getMessage(),
        'error_file' => $e->getFile(),
        'error_line' => $e->getLine(),
    ));
}

exit;

/**
 * Connection check function
 * @return bool
 */
function ping()
{
    return TRUE;
}
