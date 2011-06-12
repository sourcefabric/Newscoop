<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Acl\Role,
    Newscoop\Entity\Acl\Rule,
    Newscoop\Entity\User\Staff;

/**
 * @Acl(ignore="1")
 */
class Admin_AclController extends Zend_Controller_Action
{
    /** @var Resource_Acl */
    private $acl;

    /** @var array */
    private $ruleTypes;

    /** @var Doctrine\ORM\EntityRepository */
    private $ruleRepository;

    public function init()
    {
        camp_load_translation_strings('user_types');

        $this->ruleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Rule');

        $this->ruleTypes = array(
            'allow' => getGS('Allow'),
            'deny' => getGS('Deny'),
        );

        $this->_helper->contextSwitch()
            ->addActionContext('actions', 'json')
            ->initContext();

        $this->acl = Zend_Registry::get('acl');
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
                $this->_helper->entity->flushManager();

                $this->_helper->flashMessenger->addMessage(getGS('Rule saved.'));
                $this->redirect();
            } catch (PDOException $e) {
                $form->role->addError(getGS('Rule for this resource/action exists already.'));
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
//        $this->view->jQueryReady( "$.registry.set('test','test');" );
        
        $role = $this->_helper->entity->get(new Role, 'role');
        $resources = array('' => getGS('Global'));
        foreach (array_keys($this->acl->getResources()) as $resource) {
            $resources[$resource] = $this->formatName($resource);
        }

        // get rules
        $rules = array();
        foreach ($role->getRules() as $rule) {
            $resource = $rule->getResource();
            if (!isset($rules[$resource])) {
                $rules[$resource] = array();
            }

            $rules[$resource][] = (object) array(
                'id' => $rule->getId(),
                'class' => $rule->getType(),
                'type' => $this->ruleTypes[$rule->getType()],
                'action' => $this->formatName($rule->getAction()),
            );
        }

        $rulesParents = array();
        $user = $this->_getParam('user', false);
        if ($user) {
            $staff = $this->_helper->entity->get(new Staff, 'user', FALSE);
            foreach ($staff->getGroups() as $group) {
                foreach ($group->getRoleRules() as $rule) {
                    $resource = $rule->getResource();
                    if (!isset($rulesParents[$resource])) {
                        $rulesParents[$resource] = array();
                    }

                    $rulesParents[$resource][] = (object) array(
                        'id' => $rule->getId(),
                        'class' => $rule->getType(),
                        'type' => $this->ruleTypes[$rule->getType()],
                        'action' => $this->formatName($rule->getAction()),
                    );
                }
            }
        }

        $this->view->role = $role;
        $this->view->resources = $resources;
        $this->view->rules = $rules;
        $this->view->rulesParents = $rulesParents;

        $this->_helper->sidebar(array(
            'label' => getGS('Add new rule'),
            'module' => 'admin',
            'controller' => 'acl',
            'action' => 'form',
        ), true);
    }

    public function deleteAction()
    {
        $this->ruleRepository->delete($this->_getParam('rule'));
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger->addMessage(getGS('Rule removed.'));
        $this->redirect();
    }

    /**
     * Get actions for resource
     */
    public function actionsAction()
    {
        $actions = array();
        $resource = $this->_getParam('resource', '');
        if (!empty($resource)) {
            $actions = Saas::singleton()->filterPrivileges($resource, $this->acl->getActions($resource));
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

        $form->addElement('hidden', 'role');
        $form->addElement('hidden', 'group');
        $form->addElement('hidden', 'user');

        // get resources
        $resources = array('' => getGS('Any resource'));
        foreach (array_keys($this->acl->getResources()) as $resource) {
            $resources[$resource] = $this->formatName($resource);
        }

        $form->addElement('select', 'resource', array(
            'multioptions' => $resources,
            'label' => getGS('Resource'),
        ));

        // get actions
        $actions = array('' => getGS('Any action'));
        foreach ($this->acl->getActions() as $action) {
            $actions[$action] = $this->formatName($action);
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

    /**
     * Format name
     *
     * @param string $name
     * @return string
     */
    private function formatName($name)
    {
        $parts = explode('-', $name);
        $parts = array_map('ucfirst', $parts);
        return implode(' ', $parts);
    }
}
