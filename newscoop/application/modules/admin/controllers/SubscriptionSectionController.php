<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Subscription,
    Newscoop\Entity\SubscriptionSection,
    Newscoop\Entity\User\Subscriber;

/**
 * @Acl(resource="subscription", action="manage")
 */
class Admin_SubscriptionSectionController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\SubscriptionSectionRepository */
    private $repository;

    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');
        camp_load_translation_strings('user_subscription_sections');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\SubscriptionSection');
    }

    public function indexAction()
    {
        $subscription = $this->_helper->entity->get('Newscoop\Entity\Subscription', 'subscription');
        $this->view->subscription = $subscription;

        $this->view->actions = array(
            array(
                'label' => getGS('Add new section'),
                'module' => 'admin',
                'controller' => 'subscription-section',
                'action' => 'add',
                'resource' => 'subscription',
                'privilege' => 'manage',
                'reset_params' => FALSE,
                'class' => 'add',
            ),
            array(
                'label' => getGS('Edit all sections'),
                'module' => 'admin',
                'controller' => 'subscription-section',
                'action' => 'edit-all',
                'resource' => 'subscription',
                'privilege' => 'manage',
                'reset_params' => FALSE,
            ),
        );
    }

    public function addAction()
    {
        $form = new Admin_Form_Subscription_SectionAddForm;
        $form->setAction('')->setMethod('post');

        // get form section options
        $subscription = $this->_helper->entity->get('Newscoop\Entity\Subscription', 'subscription');
        $publication = $subscription->getPublication();
        $sectionRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Section');

        $individualSections = $sectionRepository->getAvailableSections($publication, $subscription);
        $allSections = $sectionRepository->getAvailableSections($publication, $subscription, TRUE);

        $form->getElement('sections_select')->setMultioptions($this->getOptions($individualSections, TRUE));
        $form->getElement('sections_all')->setMultioptions($this->getOptions($allSections));

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $subscriptionRepository = $this->_helper->entity->getRepository($subscription);
            $subscriptionRepository->addSections($subscription, $form->getValues());
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Section $1', getGS('saved')));
            $this->_helper->redirector('index', 'subscription-section', 'admin', array(
                'user' => $this->_getParam('user'),
                'subscription' => $this->_getParam('subscription'),
            ));
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $section = $this->_helper->entity->get('Newscoop\Entity\SubscriptionSection', 'section');
        $form = new Admin_Form_Subscription_SectionEditForm;
        $form->setAction('')->setMethod('post');
        $form->setDefaults(array(
            'name' => $section->getSectionName(),
            'language' => $section->getLanguageName(),
            'start_date' => $section->getStartDate()->format('Y-m-d'),
            'days' => $section->getDays(),
            'paid_days' => $section->getPaidDays(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($section, $form->getValues());
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Section $1', getGS('saved')));
            $this->_helper->redirector('index', 'subscription-section', 'admin', array(
                'user' => $this->_getParam('user'),
                'subscription' => $this->_getParam('subscription'),
            ));
        }

        $this->view->form = $form;
    }

    public function editAllAction()
    {
        $subscription = $this->_helper->entity->get('Newscoop\Entity\Subscription', 'subscription');
        $sections = $subscription->getSections();
        if (empty($sections)) {
            $this->_helper->flashMessenger(getGS('No sections to edit'));
            $this->_helper->redirector('index', 'subscription-section', 'admin', array(
                'user' => $this->_getParam('user'),
                'subscription' => $this->_getParam('subscription'),
            ));
        }

        $section = $sections[0];

        $form = new Admin_Form_Subscription_SectionEditForm;
        $form->setAction('')->setMethod('post');
        $form->setDefaults(array(
            'name' => getGS('All'),
            'language' => getGS('N/A'),
            'start_date' => $section->getStartDate()->format('Y-m-d'),
            'days' => $section->getDays(),
            'paid_days' => $section->getPaidDays(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            foreach ($sections as $section) {
                $this->repository->save($section, $form->getValues());
            }
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Sections saved'));
            $this->_helper->redirector('index', 'subscription-section', 'admin', array(
                'user' => $this->_getParam('user'),
                'subscription' => $this->_getParam('subscription'),
            ));
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $section = $this->_helper->entity->get('Newscoop\Entity\SubscriptionSection', 'section');
        $this->repository->delete($section);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS('Section $1', getGS('deleted')));
        $this->_helper->redirector('index', 'subscription-section', 'admin', array(
            'user' => $this->_getParam('user'),
            'subscription' => $this->_getParam('subscription'),
        ));
    }

    private function getOptions(array $sections, $language = FALSE)
    {
        $options = array();
        foreach ($sections as $section) {
            $key = $section->getNumber() . ($language ? '_' . $section->getLanguageId() : '');
            $options[$key] = $section->getNumber() . ' - ' . $section->getName();
            if ($language) {
                $options[$key] .= ' (' . $section->getLanguageName() . ')';
            }
        }

        return $options;
    }
}
