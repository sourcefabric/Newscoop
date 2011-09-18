<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Services\IngestService;

/**
 * @Acl(ignore=1)
 */
class Admin_IngestController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\IngestService */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('ingest');
    }

    public function indexAction()
    {
        $this->view->auto_mode = $this->service->isAutoMode();
        $this->view->feeds = $this->service->getFeeds();
        $this->view->entries = $this->service->findBy(array(
            'published' => null,
            'status' => 'Usable',
        ), array('updated' => 'desc'), 25, 0);
    }

    public function widgetAction()
    {
        $entries = $this->service->findBy(array(
            'published' => null,
            'status' => 'Usable',
        ), array('updated' => 'desc'), 8, 0);

        $this->view->entries = $entries;
    }

    public function detailAction()
    {
        $this->_helper->layout->setLayout('iframe');
        $this->view->entry = $this->service->find($this->_getParam('entry'));
    }

    public function switchModeAction()
    {
        $this->service->switchAutoMode();
        $this->_helper->redirector('index');
    }

    public function publishAction()
    {
        try {
            $entry = $this->service->find($this->_getParam('entry'));
            $this->service->publish($entry);
            $this->_helper->flashMessenger(getGS("Entry '$1' published", $entry->getTitle()));
            $this->_helper->redirector('index', $this->_getParam('return', 'ingest'));
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function prepareAction()
    {
        try {
            $entry = $this->service->find($this->_getParam('entry'));
            $article = $this->service->publish($entry, 'N');
            $this->_helper->flashMessenger(getGS("Entry '$1' prepared for publishing", $entry->getTitle()));
            $this->_helper->redirector->gotoUrl($this->getArticleEditLink($article));
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    /**
     * Get article edit link
     *
     * @param Article $article
     * @return string
     */
    private function getArticleEditLink($article)
    {
        $params = array(
            'f_publication_id' => $article->getPublicationId(),
            'f_issue_number' => $article->getIssueNumber(),
            'f_section_number' => $article->getSectionNumber(),
            'f_article_number' => $article->getArticleNumber(),
            'f_language_id' => $article->getLanguageId(),
            'f_language_selected' => $article->getLanguageId(),
        );

        $paramsStrings = array();
        foreach ($params as $key => $val) {
            $paramsStrings[] = "$key=$val";
        }

		return '/admin/articles/edit.php?' . implode('&', $paramsStrings);
    }
}
