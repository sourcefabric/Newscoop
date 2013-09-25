<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Language;

/**
 * @Acl(resource="language", action="manage")
 */
class Admin_LanguagesController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\LanguageRepository */
    private $repository= NULL;

    /** 
     * Init
     *
     * @return void
     */
    public function init()
    {
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Language');
    }

    public function indexAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->view->languages = $this->repository->getLanguages();

        $this->view->actions = array(
            array(
                'label' => $translator->trans('Add new Language', array(), 'languages'),
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'add',
                'resource' => 'language',
                'privilege' => 'manage',
            ),
        );
    }

    public function addAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_helper->acl->check('language', 'manage');

        $form = new Admin_Form_Language;
        $form->setMethod('post')->setAction('');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $language = new Language;
                $this->repository->save($language, $form->getValues());
                $this->_helper->flashMessenger->addMessage($translator->trans('Language added.', array(), 'languages'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError($translator->trans('Name taken.', array(), 'languages'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $language = $this->getLanguage();

        $form = new Admin_Form_Language;
        $form->setAction('')
            ->setMethod('post')
            ->setDefaultsFromEntity($language);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $this->repository->save($language, $form->getValues());

                $this->_helper->flashMessenger->addMessage($translator->trans('Language saved.', array(), 'languages'));
                $this->_helper->redirector('edit', 'languages', 'admin', array('language' => $language->getId()));
            } catch (InvalidArgumentException $e) {
                $this->view->error = $e->getMessage();
            }
        }

        $this->view->language = $language;
        $this->view->form = $form;
    }

    /**
     * @Acl(action="delete")
     */
    public function deleteAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_helper->acl->check('language', 'delete');

        $language = $this->getLanguage();
        if ($language->getCode() === 'en') {
            $this->_helper->flashMessenger->addMessage($translator->trans('English language cannot be removed.', array(), 'languages'));
            $this->_helper->redirector('index', 'languages', 'admin');
        }

        if ($this->repository->isUsed($language)) {
            $this->_helper->flashMessenger->addMessage($translator->trans('Language is in use and cannot be removed.', array(), 'languages'));
            $this->_helper->redirector('index', 'languages', 'admin');
        }

        Localizer::DeleteLanguageFiles($language->getCode());
        $this->repository->delete($language->getId());
        $this->_helper->flashMessenger->addMessage($translator->trans('Language removed.', array(), 'languages'));
        $this->_helper->redirector('index', 'languages', 'admin');
    }

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    private function getLanguage()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $id = (int) $this->getRequest()->getParam('language');
        if (!$id) {
            $this->_helper->flashMessenger(array('error', $translator->trans('Language id not specified', array(), 'languages')));
            $this->_helper->redirector('index');
        }

        $language = $this->repository->findOneBy(array('id' => $id));
        if (empty($language)) {
            $this->_helper->flashMessenger->addMessage($translator->trans('Language not found.', array(), 'languages'));
            $this->_helper->redirector('index');
        }

        return $language;
    }
}
