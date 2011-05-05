<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

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
        camp_load_translation_strings('languages');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Language');
    }

    public function indexAction()
    {
        $this->view->languages = $this->repository->getLanguages();

        $this->view->actions = array(
            array(
                'label' => getGS('Add new Language'),
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'add',
                'resource' => 'language',
                'privilege' => 'edit',
            ),
        );
    }

    public function addAction()
    {
        $this->_helper->acl->check('language', 'manage');

        $form = new Admin_Form_Language;
        $form->setMethod('post')->setAction('');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $language = new Language;
                $this->repository->save($language, $form->getValues());
                $this->_helper->flashMessenger->addMessage(getGS('Language added.'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError(getGS('Name taken.'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $language = $this->getLanguage();

        $form = new Admin_Form_Language;
        $form->setAction('')
            ->setMethod('post')
            ->setDefaultsFromEntity($language);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $this->repository->save($language, $form->getValues());

                $this->_helper->flashMessenger->addMessage(getGS('Language saved.'));
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
        $language = $this->getLanguage();

        Localizer::DeleteLanguageFiles($language->getCode());
        $this->repository->delete($language->getId());
        $this->_helper->flashMessenger->addMessage(getGS('Language removed.'));
        $this->_helper->redirector('index', 'languages', 'admin');
    }

    /**
     * Get language
     *
     * @return Newscoop\Entity\Language
     */
    private function getLanguage()
    {
        $id = $this->getRequest()->getParam('language');
        $language = $this->repository->find($id);
        if (empty($language)) {
            $this->_helper->flashMessenger->addMessage(getGS('Language not found.'));
            $this->_forward('index');
        }

        return $language;
    }
 }
