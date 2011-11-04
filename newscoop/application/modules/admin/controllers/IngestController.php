<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Services\IngestService;

/**
 * @Acl(resource="ingest", action="manage")
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
        $feed_id = $this->_getParam('feed', null);
        $criteria = array(
            'published' => null,
            'status' => 'Usable',
        );

        if (isset($feed_id)) {
            $criteria['feed'] = $feed_id;
        }

        $this->view->feeds = $this->service->getFeeds();
        $this->view->entries = $this->service->findBy($criteria, array('updated' => 'desc'), 25, 0);

        $publisher = $this->_helper->service('ingest.publisher');
        $this->view->sections = array();
        foreach ($this->view->entries as $entry) {
            $section = new Section($publisher->getPublication(), $publisher->getIssue(), $publisher->getLanguage($entry->getLanguage()), $publisher->getSection($entry));
            $this->view->sections[$entry->getId()] = $section;
        }
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
        $feed_id = $this->_getParam('feed', null);

        if (is_null($feed_id)) {
            $this->_helper->redirector('index');
        }

        $this->service->switchMode($feed_id);

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
            $this->_helper->redirector->gotoUrl($this->_helper->article->getEditLink($article));
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function deleteAction()
    {
        try {
            $this->service->deleteEntryById($this->_getParam('entry'));
            $this->_helper->flashMessenger(getGS("Entry deleted"));
            $this->_helper->redirector('index', 'ingest');
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }
}
