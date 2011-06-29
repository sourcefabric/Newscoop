<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

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
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');

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
        $countries = array('' => getGS('Select country'));
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
        try {
            $staff = new Staff();
            $this->handleForm($this->form, $staff);
        } catch (PDOException $e) {
            $this->form->getElement('username')->addError(getGS('That user name already exists, please choose a different login name.'));
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        }

        $this->view->form = $this->form;
    }

    /**
     * @Acl(ignore="1")
     */
    public function editAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');

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
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        }

        $this->view->form = $this->form;

        $this->view->actions = array(
            array(
                'label' => getGS('Permissions'),
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
        $this->_helper->acl->check('user', 'delete');

        $staff = $this->_helper->entity->get(new Staff, 'user');

        $permitted = Zend_Auth::getInstance()->getIdentity() != $staff->getId();
        if ($permitted) {
            $this->repository->delete($staff);

            $this->_helper->entity->getManager()->flush();

            $this->_helper->flashMessenger(getGS('Staff member deleted.'));
            $this->_helper->redirector->gotoSimple('index');
        }
        else {
            $this->_helper->flashMessenger(getGS('Self-delete is not permitted.')); // should be translateable
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Staff');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Staff $staff) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Edit staff member $1', $staff->getName()),
                $staff->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Delete staff member $1', $staff->getName()),
                getGS('Delete')
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
                'label' => getGS('Add new staff member'),
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
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            try {
                $this->repository->save($staff, $form->getValues());
                $this->_helper->entity->getManager()->flush();
            // TODO bad design, redirect should not be here.
            } catch (\PDOException $e) {
                $this->_helper->flashMessenger(array(
                	'error',
                    getGS("Could not save user '$1'. Please make sure it doesn't already exist", $this->_request->getPost('username')))
                );
                $this->_helper->redirector->gotoSimple('add', 'staff', 'admin');
            } catch (\Exception $e) {
                $this->_helper->flashMessenger(array('error', getGS("Changing user type would prevent you to manage users. Aborted.")));
                $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                    'user' => $staff->getId(),
                ));
            }

            // add default widgets for new staff
            if ($this->_getParam('action') == 'add') {
                WidgetManager::SetDefaultWidgets($staff->getId());
            }

            $this->_helper->flashMessenger(getGS('Staff member saved.'));
            $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                'user' => $staff->getId(),
            ));
        }
    }
}
