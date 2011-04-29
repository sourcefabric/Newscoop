<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Theme\Loader\LocalLoader;

/**
 */
class Admin_ThemesController extends Zend_Controller_Action
{
    private $repository;

    public function init()
    {
        $loader = new LocalLoader(APPLICATION_PATH . '/../templates');
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Theme');
        $this->repository->setLoader($loader);
    }

    public function indexAction()
    {
        $this->view->themes = $this->repository->findAll();
    }

    public function installAction()
    {
        $this->repository->install($this->_getParam('offset'));
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('Theme $1', getGS('installed')));
        $this->_helper->redirector('index');
    }

    public function uninstallAction()
    {
        $this->repository->uninstall($this->_getParam('id'));
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('Theme $1', getGS('deleted')));
        $this->_helper->redirector('index');
    }
}

