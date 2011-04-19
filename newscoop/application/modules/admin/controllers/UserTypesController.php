<?php

use Newscoop\Entity\Acl\Rule,
    Newscoop\Entity\User\Group;

class Admin_UserTypesController extends Zend_Controller_Action
{
    /** @var array */
    private $ruleTypes;

    /** @var Doctrine\ORM\EntityRepository */
    private $groupRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $ruleRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $roleRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $resourceRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $actionRepository;

    public function init()
    {
        camp_load_translation_strings('user_types');

        // get repositories
        $this->ruleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Rule');
        $this->roleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Role');
        $this->resourceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Resource');
        $this->actionRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Action');
        $this->groupRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');

        // set rule types
        $this->ruleTypes = array(
            'allow' => getGS('Allow'),
            'deny' => getGS('Deny'),
        );
    }

    public function preDispatch()
    {
        $this->_helper->acl->check('userType', 'manage');
    }

    public function indexAction()
    {
        $this->view->groups = $this->groupRepository->findAll();
    }

    public function editRoleAction()
    {
        $role = $this->getRole();

        $form = $this->getAddRuleForm()
            ->setAction('')
            ->setMethod('post')
            ->setDefaults(array(
                'type' => 'allow',
                'role' => $role->getId(),
            ));

        // form handle
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $rule = new Rule();
                $this->ruleRepository->save($rule, $form->getValues());

                $this->_helper->flashMessenger->addMessage(getGS('Rule saved.'));
                $this->_helper->redirector('edit-role', 'user-types', 'admin', array(
                    'role' => $role->getId(),
                ));
            } catch (Exception $e) {
                $this->view->error = getGS('Rule for this resource/action exists already.');
            }
        }

        // get rules grouped by resource
        $resources = array();
        foreach ($role->getRules() as $rule) {
            $id = $rule->getResourceId();
            if (!isset($resources[$id])) {
                $resources[$id] = (object) array(
                    'name' => $id ? $rule->getResource()->getName() : '',
                    'rules' => array(),
                );
            }

            $resources[$rule->getResourceId()]->rules[] = $rule;
        }

        $this->view->role = $role;
        $this->view->form = $form;
        $this->view->resources = $resources;
        $this->view->ruleTypes = $this->ruleTypes;
    }

    public function addGroupAction()
    {
        $form = $this->getGroupForm()
            ->setMethod('post')
            ->setAction('');

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $group = new Group;
                $this->groupRepository->save($group, $form->getValues());
                $this->_helper->flashMessenger->addMessage(getGS('User type added.'));
                $this->_helper->redirector('index');
            } catch (Exception $e) {
                $form->getElement('name')->addError(getGS('Name taken.'));
            }
        }

        $this->view->form = $form;
    }

    public function deleteGroupAction()
    {
        $this->groupRepository->delete((int) $this->getRequest()->getParam('group', 0));
        $this->_helper->flashMessenger->addMessage(getGS('User type deleted.'));
        $this->_helper->redirector('index');
    }

    public function deleteRuleAction()
    {
        $this->ruleRepository->delete($this->getRequest()->getParam('rule', 0));
        $this->_helper->flashMessenger->addMessage(getGS('Rule removed.'));
        $this->_helper->redirector('edit-role', 'user-types', 'admin', array(
            'role' => $this->getRequest()->getParam('role', 0),
        ));
    }

    /**
     * Get actions for resource
     */
    public function actionsAction()
    {
        $params = $this->getRequest()->getParams();
        $resourceId = isset($params['resource']) ? $params['resource'] : 0;
        $resource = $this->resourceRepository->find((int) $resourceId);
        if (!$resource) {
            $this->view->actions = array();
            return;
        }

        $actions = array();
        foreach ($resource->getActions() as $action) {
            $actions[] = $action->getId();
        }

        $this->view->actions = $actions;
    }

    /**
     * Get role
     *
     * @return Newscoop\Entity\Acl\Role
     */
    private function getRole()
    {
        $id = $this->getRequest()->getParam('role');
        $role = $this->roleRepository->find($id);

        if (empty($role)) {
            $this->_helper->FlashMessenger(getGS('Not found.'));
            $this->_forward('index');
        }

        return $role;
    }

    /**
     * Get add rule form
     *
     * @return Zend_Form
     */
    private function getAddRuleForm()
    {
        $form = new Zend_Form();

        $form->addElement('radio', 'type', array(
            'multioptions' => $this->ruleTypes,
            'label' => getGS('Add Rule'),
        ));

        // get resources
        $resources = array(getGS('Any resource'));
        foreach ($this->resourceRepository->findAll() as $resource) {
            $resources[$resource->getId()] = $resource->getName();
        }
        $form->addElement('select', 'resource', array(
            'multioptions' => $resources,
            'label' => getGS('Resource'),
        ));

        // get actions
        $actions = array(getGS('Any action'));
        foreach ($this->actionRepository->findAll() as $action) {
            $actions[$action->getId()] = $action->getName();
        }
        $form->addElement('select', 'action', array(
            'multioptions' => $actions,
            'label' => getGS('Action'),
        ));

        $form->addElement('hidden', 'role');

        $form->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => getGS('Add'),
        ));

        return $form;
    }

    /**
     * Get group form
     *
     * @return Zend_Form
     */
    private function getGroupForm()
    {
        $form = new Zend_Form;

        $form->addElement('text', 'name', array(
            'required' => true,
            'label' => getGS('Name'),
        ));

        $form->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => getGS('Add'),
        ));

        return $form;
    }
}
