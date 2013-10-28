<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\User\Staff;

/**
 * @Acl(resource="user", action="manage")
 */
class Admin_StaffController extends Zend_Controller_Action
{
    private $repository;

    private $form;

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');

        $this->form = new Admin_Form_Staff($this->_helper->acl->isAllowed('user', 'manage'));
        $this->form->setAction('')->setMethod('post');

        if ($this->_helper->acl->isAllowed('user', 'manage')) { // set form user groups
            $groups = array();
            $groupRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
            foreach ($groupRepository->findAll() as $group) {
                $groups[$group->getId()] = $group->getName();
            }
            $this->form->getElement('groups')->setMultioptions($groups);
        }

        // set form countries
        $countries = array('' => $translator->trans('Select country', array(), 'user_subscriptions'));
        foreach (Country::GetCountries(1) as $country) {
            $countries[$country->getCode()] = $country->getName();
        }
        $this->form->getElement('country')->setMultioptions($countries);
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        try {
            $staff = new Staff();
            $this->handleForm($this->form, $staff);
        } catch (PDOException $e) {
            $this->form->getElement('username')->addError($translator->trans('That user name already exists, please choose a different login name.', array(), 'users'));
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError($translator->trans("That $1 already exists, please choose a different $2.", array('$1' => $field, '$2' => $field), 'user_subscriptions'));
        }

        $this->view->form = $this->form;
    }

    /**
     * @Acl(ignore="1")
     */
    public function editAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');
        $translator = \Zend_Registry::get('container')->getService('translator');
        // check permission
        $auth = Zend_Auth::getInstance();
        if ($staff->getId() != $auth->getIdentity()) { // check if user != current
            $this->_helper->acl->check('user', 'manage');
        }

        try {
            $this->form->setDefaultsFromEntity($staff);
            $this->handleForm($this->form, $staff);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError($translator->trans("That $1 already exists, please choose a different $2.", array('$1' => $field, '$2' => $field), 'user_subscriptions'));
        }

        $this->view->form = $this->form;

        $this->view->actions = array(
            array(
                'label' => $translator->trans('Permissions', array(), 'user_types'),
                'module' => 'admin',
                'controller' => 'staff',
                'action' => 'edit-access',
                'params' => array(
                    'user' => $staff->getId(),
                ),
                'resource' => 'user',
                'privilege' => 'manage',
            ),
        );
    }

    public function editAccessAction()
    {
//        $this->view->jQueryUtils()->token = 'sdfhgfgthrgesrefwrtgdtgsvet@#$RWESDFC@#4erws';

        $staff = $this->_helper->entity(new Staff, 'user');
        $this->view->staff = $staff;

//        $this->view->jQueryReady( "$.registry.set('another','test');" );

        $this->_helper->actionStack('edit', 'acl', 'admin', array(
            'role' => $staff->getRoleId(),
            'user' => $staff->getId(),
        ));
    }

    /**
     * @Acl(action="delete")
     */
    public function deleteAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_helper->acl->check('user', 'delete');

        $staff = $this->_helper->entity->get(new Staff, 'user');

        $permitted = Zend_Auth::getInstance()->getIdentity() != $staff->getId();
        if ($permitted) {
            $this->repository->delete($staff);

            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger($translator->trans('Staff member deleted.'));
            $this->_helper->redirector->gotoSimple('index');
        }
        else {
            $this->_helper->flashMessenger($translator->trans('Self-delete is not permitted.')); // should be translateable
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    public function tableAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Staff');

        $table->setCols(array(
            'name' => $translator->trans('Full Name', array(), 'user_subscriptions'),
            'username' => $translator->trans('Accout Name', array(), 'user_subscriptions'),
            'email' => $translator->trans('E-Mail', array(), 'user_subscriptions'),
            'timeCreated' => $translator->trans('Creation Date', array(), 'user_subscriptions'),
            $translator->trans('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Staff $staff) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                $translator->trans('Edit staff member $1', array('$1' => $staff->getName())),
                $staff->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                $translator->trans('Delete staff member $1', array('$1' => $staff->getName())),
                $translator->trans('Delete')
            );

            return array(
                $editLink,
                $staff->getUsername(),
                $staff->getEmail(),
                $staff->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();

        $this->view->actions = array(
            array(
                'label' => $translator->trans('Add new staff member'),
                'module' => 'admin',
                'controller' => 'staff',
                'action' => 'add',
                'resource' => 'user',
                'privilege' => 'manage',
                'class' => 'add',
            ),
        );
    }

    private function handleForm(Zend_Form $form, Staff $staff)
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            try {
                $this->repository->save($staff, $form->getValues());
                $this->_helper->entity->getManager()->flush();
            // TODO bad design, redirect should not be here.
            } catch (\PDOException $e) {
                $this->_helper->flashMessenger(array(
                	'error',
                    $translator->trans('Could not save user $1. Please make sure it does not already exist', array('$1' => $this->_request->getPost('username'))))
                );
                $this->_helper->redirector->gotoSimple('add', 'staff', 'admin');
            } catch (\InvalidArgumentException $e) {
                if ($e->getMessage() == 'email') {
                    $this->_helper->flashMessenger(array(
                        'error',
                        $translator->trans('Could not save user with e-mail address $1. Please make sure it does not already exist', array('$1' => $this->_request->getPost('email'))))
                    );
                }
                $this->_helper->redirector->gotoSimple('add', 'staff', 'admin');
            } catch (\Exception $e) {
                $this->_helper->flashMessenger(array('error', $translator->trans("Changing user type would prevent you to manage users. Aborted.")));
                $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                    'user' => $staff->getId(),
                ));
            }

            // add default widgets for new staff
            if ($this->_getParam('action') == 'add') {
                WidgetManager::SetDefaultWidgets($staff->getId());
            }

            $this->_helper->flashMessenger($translator->trans('Staff member saved.'));
            $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                'user' => $staff->getId(),
            ));
        }
    }
}
