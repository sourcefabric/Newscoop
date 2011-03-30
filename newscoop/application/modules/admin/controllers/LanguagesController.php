<?php

class Admin_LanguagesController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\LogRepository */
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

    public function editAction()
    {
        $language = $this->getLanguage();

        $form = $this->getAddLanguageForm();
        $form->setDefaults(array(
                'language' => $language->getId(),
            ));

        // form handle
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $language = new Language();
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

        $language = $this->getLanguage();
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
            $this->_helper->flashMessenger->addMessage(getGS('Not found.'));
            $this->_forward('index');
        }

        return $language;
    }

    /**
     * Get add language form
     *
     * @return Zend_Form
     */
    private function getAddLanguageForm()
    {
        $form = new Zend_Form();

        $form->addElement('text', 'name', array('label' => getGS('Name')));
        $form->addElement('text', 'native_name', array('label' => getGS('Native Name')));
        $form->addelement('text', 'code', array('label' => getGS('Code Page')));

        $form->addElement('text', 'month_1', array('label' => getGS('Month 1')));
        $form->addElement('text', 'month_2', array('label' => getGS('Month 2')));
        $form->addElement('text', 'month_3', array('label' => getGS('Month 3')));
        $form->addElement('text', 'month_4', array('label' => getGS('Month 4')));
        $form->addElement('text', 'month_5', array('label' => getGS('Month 5')));
        $form->addElement('text', 'month_6', array('label' => getGS('Month 6')));
        $form->addElement('text', 'month_7', array('label' => getGS('Month 7')));
        $form->addElement('text', 'month_8', array('label' => getGS('Month 8')));
        $form->addElement('text', 'month_9', array('label' => getGS('Month 9')));
        $form->addElement('text', 'month_10', array('label' => getGS('Month 10')));
        $form->addElement('text', 'month_11', array('label' => getGS('Month 11')));
        $form->addElement('text', 'month_12', array('label' => getGS('Month 12')));

        $form->addElement('text', 'short_month_1', array('label' => getGS('Short Month 1')));
        $form->addElement('text', 'short_month_2', array('label' => getGS('Short Month 2')));
        $form->addElement('text', 'short_month_3', array('label' => getGS('Short Month 3')));
        $form->addElement('text', 'short_month_4', array('label' => getGS('Short Month 4')));
        $form->addElement('text', 'short_month_5', array('label' => getGS('Short Month 5')));
        $form->addElement('text', 'short_month_6', array('label' => getGS('Short Month 6')));
        $form->addElement('text', 'short_month_7', array('label' => getGS('Short Month 7')));
        $form->addElement('text', 'short_month_8', array('label' => getGS('Short Month 8')));
        $form->addElement('text', 'short_month_9', array('label' => getGS('Short Month 9')));
        $form->addElement('text', 'short_month_10', array('label' => getGS('Short Month 10')));
        $form->addElement('text', 'short_month_11', array('label' => getGS('Short Month 11')));
        $form->addElement('text', 'short_month_12', array('label' => getGS('Short Month 12')));

        $form->addElement('text', 'day_1', array('label' => getGS('Day 1')));
        $form->addElement('text', 'day_2', array('label' => getGS('Day 2')));
        $form->addElement('text', 'day_3', array('label' => getGS('Day 3')));
        $form->addElement('text', 'day_4', array('label' => getGS('Day 4')));
        $form->addElement('text', 'day_5', array('label' => getGS('Day 5')));
        $form->addElement('text', 'day_6', array('label' => getGS('Day 6')));
        $form->addElement('text', 'day_7', array('label' => getGS('Day 7')));

        $form->addElement('text', 'short_day_1', array('label' => getGS('Short Day 1')));
        $form->addElement('text', 'short_day_2', array('label' => getGS('Short Day 2')));
        $form->addElement('text', 'short_day_3', array('label' => getGS('Short Day 3')));
        $form->addElement('text', 'short_day_4', array('label' => getGS('Short Day 4')));
        $form->addElement('text', 'short_day_5', array('label' => getGS('Short Day 5')));
        $form->addElement('text', 'short_day_6', array('label' => getGS('Short Day 6')));
        $form->addElement('text', 'short_day_7', array('label' => getGS('Short Day 7')));

        $form->addElement('hidden', 'language');

        $form->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => getGS('Add'),
        ));

        return $form;
    }
}