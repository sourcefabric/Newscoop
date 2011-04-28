<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_ThemesController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        $this->path = APPLICATION_PATH . '/../templates';
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Theme');
    }

    public function indexAction()
    {
        $this->view->themes = $this->repository->findAll($this->path);
    }

    public function installAction()
    {
        $id = $this->_getParam('theme');
        $this->repository->install($id, $this->path);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('Theme $1', getGS('installed')));
        $this->_helper->redirector('index');
    }

    public function deleteAction()
    {
        $id = $this->_getParam('theme');
        $this->repository->delete($id, $this->path);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('Theme $1', getGS('deleted')));
        $this->_helper->redirector('index');
    }
}

