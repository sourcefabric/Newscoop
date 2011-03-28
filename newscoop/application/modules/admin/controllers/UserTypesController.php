<?php

use Newscoop\Entity\Acl\Rule;

class Admin_UserTypesController extends Zend_Controller_Action
{
    /** @var array */
    private $ruleTypes;

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
        $this->ruleRepository = $this->_helper->em->getRepository('Newscoop\Entity\Acl\Rule');
        $this->roleRepository = $this->_helper->em->getRepository('Newscoop\Entity\Acl\Role');
        $this->resourceRepository = $this->_helper->em->getRepository('Newscoop\Entity\Acl\Resource');
        $this->actionRepository = $this->_helper->em->getRepository('Newscoop\Entity\Acl\Action');

        // set rule types
        $this->ruleTypes = array(
            'allow' => getGS('Allow'),
            'deny' => getGS('Deny'),
        );
    }

    public function preDispatch()
    {
        if (!$this->_helper->acl->isAllowed('User', 'edit')) {
            $this->_forward('deny', 'error', 'admin', array(
                getGS("You do not have the right to change user type permissions."),
            ));
        }
    }

    public function indexAction()
    {
        $this->view->roles = $this->roleRepository->findAll();
    }

    public function editAction()
    {
        $role = $this->getRole();

        $form = $this->getAddRuleForm();
        $form->setDefaults(array(
                'type' => 'allow',
                'role' => $role->getId(),
            ));

        // form handle
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $rule = new Rule();
                $this->ruleRepository->save($rule, $form->getValues());

                $this->_helper->flashMessenger->addMessage(getGS('Rule saved.'));
                $this->_helper->redirector('edit', 'user-types', 'admin', array('role' => $role->getId()));
            } catch (InvalidArgumentException $e) {
                $this->view->error = $e->getMessage();
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

    public function deleteRuleAction()
    {
        $params = $this->getRequest()->getParams();
        $this->ruleRepository->delete($params['rule']);
        $this->_helper->flashMessenger->addMessage(getGS('Rule removed.'));
        $this->_helper->redirector('edit', 'user-types', 'admin', array('role' => $params['role']));
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
}
