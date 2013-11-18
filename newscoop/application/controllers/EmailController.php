<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class EmailController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $uri = CampSite::GetURIInstance();
        $themePath = $uri->getThemePath();
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        $this->view = new Newscoop\SmartyView();
        $this->view
            ->addScriptPath(APPLICATION_PATH . '/views/scripts/')
            ->addScriptPath(realpath(APPLICATION_PATH . "/../themes/$themePath"));

        $this->view->addPath(realpath(APPLICATION_PATH . "/../themes/$themePath"));

        $this->getHelper('viewRenderer')
            ->setView($this->view)
            ->setViewScriptPathSpec(':controller_:action.:suffix')
            ->setViewSuffix('tpl');

        $this->getHelper('layout')
            ->disableLayout();

        $this->view->publication = $this->getRequest()->getServer('SERVER_NAME', 'localhost');
        $this->view->site = $preferencesService->SiteTitle;

        $this->_helper->contextSwitch()
            ->addActionContext('comment-notify', 'xml')
            ->initContext();
    }

    public function confirmAction()
    {
        $this->view->user = $this->_getParam('user');
        $this->view->token = $this->_getParam('token');
    }

    public function passwordRestoreAction()
    {
        $this->view->user = $this->_getParam('user');
        $this->view->token = $this->_getParam('token');
    }

    public function commentNotifyAction()
    {
        if ($this->_getParam('user', false)) {
            $this->view->username = $this->_getParam('user')->getUsername();
        }

        $article = $this->_getParam('article');

        $this->view->articleLink = $this->getArticleLink($article);
        $this->view->article = new \MetaArticle($article->getLanguageId(), $article->getArticleNumber());
        $this->view->comment = $this->_getParam('comment');
    }

    private function getArticleLink(Article $article)
    {
        $params = array(
            'f_publication_id' => $article->getPublicationId(),
            'f_issue_number' => $article->getIssueNumber(),
            'f_section_number' => $article->getSectionNumber(),
            'f_article_number' => $article->getArticleNumber(),
            'f_language_id' => $article->getLanguageId(),
            'f_language_selected' => $article->getLanguageId(),
        );

        $queryString = implode('&', array_map(function($property) use ($params) {
            return $property . '=' . $params[$property];
        }, array_keys($params)));

        return $this->view->baseUrl("/admin/articles/edit.php?$queryString");
    }
}
