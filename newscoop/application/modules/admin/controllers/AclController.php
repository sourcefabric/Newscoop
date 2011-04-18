<?php

use Newscoop\Entity\Acl\Rule,
    Newscoop\Entity\Acl\Role,
    Newscoop\Entity\Acl\Resource,
    Newscoop\Entity\User\Staff;


class Admin_AclController extends Zend_Controller_Action
{
    /** @var array */
    private $ruleTypes;

    /** @var Doctrine\ORM\EntityRepository */
    private $ruleRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $resourceRepository;

    /** @var Doctrine\ORM\EntityRepository */
    private $actionRepository;

    public function init()
    {
        camp_load_translation_strings('user_types');

        $this->ruleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Rule');
        $this->resourceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Resource');
        $this->actionRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Action');

        $this->ruleTypes = array(
            'allow' => getGS('Allow'),
            'deny' => getGS('Deny'),
        );

        $this->_helper->contextSwitch()
            ->addActionContext('actions', 'json')
            ->initContext();
    }

    public function formAction()
    {
        $form = $this->getForm()
            ->setAction('')
            ->setMethod('post')
            ->setDefaults(array(
                'type' => 'allow',
                'role' => $this->_getParam('role', 0),
                'group' => $this->_getParam('group', 0),
                'user' => $this->_getParam('user', 0),
            ));

        // form handle
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            try {
                $rule = new Rule();
                $this->ruleRepository->save($rule, $form->getValues());

                $this->_helper->flashMessenger->addMessage(getGS('Rule saved.'));
                $this->redirect();
            } catch (Exception $e) {
                $this->view->error = getGS('Rule for this resource/action exists already.');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        // populate resources
        $global = new Resource;
        $global->setName(getGS('Global'))
            ->setActions($this->actionRepository->findAll());
        $resources = array(0 => $global);
        foreach ($this->resourceRepository->findAll() as $resource) {
            $resources[$resource->getId()] = $resource;
        }

        // get rules
        $role = $this->_helper->entity->get(new Role, 'role');
        foreach ($role->getRules() as $rule) {
            $resources[$rule->getResourceId()]->addRule($rule);
        }

        // set user if any
        $staff = $this->_helper->entity->get(new Staff, 'user', FALSE);
        if ($staff) { // get inherited rules
            foreach ($staff->getGroups() as $group) {
                foreach ($group->getRole()->getRules() as $rule) {
                    $resources[$rule->getResourceId()]->addRule($rule, TRUE);
                }
            }
        }

        $this->_helper->sidebar(array(
            'label' => getGS('Add new rule'),
            'module' => 'admin',
            'controller' => 'acl',
            'action' => 'form',
        ));

        $this->view->role = $role;
        $this->view->resources = $resources;
        $this->view->ruleTypes = $this->ruleTypes;

        $this->render();
    }

    public function deleteAction()
    {
        $this->ruleRepository->delete($this->_getParam('rule'));
        $this->_helper->flashMessenger->addMessage(getGS('Rule removed.'));
        $this->redirect();
    }

    /**
     * Get actions for resource
     */
    public function actionsAction()
    {
        $resource = $this->resourceRepository->find((int) $this->_getParam('resource'));

        $actions = array();
        if (is_object($resource)) {
            foreach ($resource->getActions() as $action) {
                $actions[] = $action->getId();
            }
        }

        $this->view->actions = $actions;
    }

    /**
     * Get rule form
     *
     * @return Zend_Form
     */
    private function getForm()
    {
        $form = new Zend_Form();

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

        $form->addElement('radio', 'type', array(
            'label' => getGS('Add Rule'),
            'multioptions' => $this->ruleTypes,
            'class' => 'acl type',
        ));

        $form->addElement('submit', 'submit', array(
            'label' => getGS('Add'),
        ));

        $form->addElement('hidden', 'role');
        $form->addElement('hidden', 'group');
        $form->addElement('hidden', 'user');

        return $form;
    }

    /**
     * Redirect after action
     *
     * @return void
     */
    private function redirect()
    {
        $params = $this->getRequest()->getParams();
        $entity = !empty($params['group']) ? 'group' : 'user';
        
        $this->_helper->redirector('edit-access', $entity == 'group' ? 'user-group' : 'staff', 'admin', array(
            $entity => $params[$entity],
        ));
    }
}
