<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Language;

class Admin_LanguagesController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\LanguageRepository */
    private $languageRepository= NULL;

    public function init()
    {
        camp_load_translation_strings('languages');

        // get repositories
        $this->languageRepository = $this->_helper->em->getRepository('Newscoop\Entity\Language');
    }

    public function preDispatch()
    {
        if (!$this->_helper->acl->isAllowed('Language', 'edit')) {
            $this->_forward('deny', 'error', 'admin', array(
                getGS("You do not have the right to edit languages."),
            ));
        }
    }

    public function indexAction()
    {
        $this->view->languages = $this->languageRepository->getLanguages();
    }

    public function addAction()
    {
        $form = new Admin_Form_BaseLanguage;
        $form->setMethod('post')
            ->setAction('');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $language = new Language;
                $this->languageRepository->save($language, $form->getValues());
                $this->_helper->flashMessenger->addMessage(getGS('Language added.'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError($e->getMessage());
                $form->getElement('name')->addError(getGS('Name taken.'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $language = $this->getLanguage();

        $form = new Admin_Form_BaseLanguage;
        $form->setAction('')
            ->setMethod('post')
            ->setDefaultsFromEntity($language);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $this->languageRepository->save($language, $form->getValues());

                $this->_helper->flashMessenger->addMessage(getGS('Language saved.'));
                $this->_helper->redirector('edit', 'languages', 'admin', array('language' => $language->getId()));
            } catch (InvalidArgumentException $e) {
                $this->view->error = $e->getMessage();
            }
        }

        $this->view->language = $language;
        $this->view->form = $form;
    }

    public function deleteAction()
    {
        if (!$this->_helper->acl->isAllowed('Language', 'delete')) {
                $this->_forward('deny', 'error', 'admin', array(
                getGS("You do not have the right to delete languages."),
            ));
        }

        Localizer::DeleteLanguageFiles($language->getCode());
        $this->languageRepository->delete($language->getId());
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
        $language = $this->languageRepository->find($id);
        if (empty($language)) {
            $this->_helper->flashMessenger->addMessage(getGS('Language not found.'));
            $this->_forward('index');
        }

        return $language;
    }
 }