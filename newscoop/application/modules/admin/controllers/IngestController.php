<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Ingest\Feed;

/**
 * @Acl(resource="ingest", action="manage")
 */
class Admin_IngestController extends Zend_Controller_Action
{
    const LIMIT = 50;

    public function init()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
    }

    public function indexAction()
    {
        $constraints = array(
            'itemMeta.pubStatus' => \Newscoop\News\ItemMeta::STATUS_USABLE,
        );

        $class = $this->_getParam('class', null);
        if ($class) {
            $constraints['itemMeta.itemClass'] = $class;
        }

        $page = $this->_getParam('page', 1);
        $count = count($this->_helper->service('ingest.item')->findBy($constraints));
        $paginator = Zend_Paginator::factory($count);
        $paginator->setItemCountPerPage(self::LIMIT);
        $paginator->setCurrentPageNumber($page);
        $paginator->setView($this->view);
        $paginator->setDefaultScrollingStyle('Sliding');
        $this->view->paginator = $paginator;

        $this->view->items = $this->_helper->service('ingest.item')->findBy($constraints, array(
            'itemMeta.firstCreated' => 'desc',
        ), self::LIMIT, ($paginator->getCurrentPageNumber() - 1) * self::LIMIT);
    }

    public function detailAction()
    {
        //$this->_helper->layout->setLayout('iframe');
        $this->view->item = $this->_helper->service('ingest.item')->find($this->_getParam('item'));
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

    public function prepareAction()
    {   
        try {
            $translator = \Zend_Registry::get('container')->getService('translator');
            $entry = $this->service->find($this->_getParam('entry'));
            $article = $this->service->publish($entry, 'N');
            $this->_helper->flashMessenger($translator->trans("Entry $1 prepared for publishing", array('$1' => $entry->getTitle())));
            $this->_helper->redirector->gotoUrl($this->_helper->article->getEditLink($article));
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function deleteAction()
    {
        try {
            $translator = \Zend_Registry::get('container')->getService('translator');
            $this->service->deleteEntryById($this->_getParam('entry'));
            $this->_helper->flashMessenger($translator->trans("Entry deleted"));
            $this->_helper->redirector('index', 'ingest');
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
    }

    public function addFeedAction()
    {
        $form = new Admin_Form_Ingest();

        $translator = \Zend_Registry::get('container')->getService('translator');
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $feed = $this->_helper->service('ingest.feed')->save($form->getValues());
            $this->_helper->flashMessenger($translator->trans('Feed saved'));
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
    }

    public function updateAction()
    {
        $this->_helper->service('ingest.feed')->updateAll();
        $this->_helper->redirector('index');
    }

    public function publishAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $item = $this->_helper->service('ingest.item')->find($this->_getParam('item', null));
        $this->_helper->service('ingest.item')->publish($item);
        $this->_helper->flashMessenger($translator->trans('Item published'));
        $this->_helper->redirector('index', 'ingest', 'admin', array(
            'item' => null,
        ));
    }

    public function settingsAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $settings = $this->_helper->service('ingest.settings')->find('ingest');
        $form = new Admin_Form_IngestSettings();
        $form->publication->addMultiOptions($this->_helper->service('content.publication')->getOptions());
        $form->section->addMultiOptions($this->_helper->service('content.section')->getOptions());
        $form->setDefaults(array(
            'article_type' => $settings->getArticleTypeName(),
            'publication' => $settings->getPublicationId(),
            'section' => $settings->getSectionNumber(),
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $this->_helper->service('ingest.settings')->save($form->getValues(), $settings);
            $this->_helper->flashMessenger($translator->trans('Settings saved'));
            $this->_helper->redirector('settings');
        }

        $this->view->form = $form;
        $this->view->feeds = $this->_helper->service('ingest.feed')->findBy(array());
    }
}
