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
    public function init()
    {
    }

    public function indexAction()
    {
        $this->view->feeds = $this->_helper->service('ingest.feed')->findBy(array());
        $this->view->items = $this->_helper->service('ingest.item')->findBy(array(
            'itemMeta.pubStatus' => \Newscoop\News\ItemMeta::STATUS_USABLE,
        ), array(
            'itemMeta.firstCreated' => 'desc',
        ), 50);
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
        $this->view->item = $this->_helper->service('ingest.item')->find($this->_getParam('item'));
        if (!$this->view->item) {
            var_dump($this->_getParam('item'));
            exit;
        }
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

    public function addFeedAction()
    {
        $form = new Admin_Form_Ingest();

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $feed = $this->_helper->service('ingest.feed')->save($form->getValues());
            $this->_helper->flashMessenger(getGS("Feed added"));
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
    }

    public function updateAction()
    {
        $this->_helper->service('ingest.feed')->updateAll();
        $this->_helper->redirector('index');
    }
}
