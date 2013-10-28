<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 */
class Admin_LogController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\AuditService */
    private $auditService;

    public function init()
    {
        $this->auditService = $this->_helper->service('audit');
    }

    /**
     * @Acl(action="view")
     */
    public function indexAction()
    {
        $this->_helper->layout->setLayout('admin');
        $this->view->form = $this->getForm()->setMethod('get')->setAction('');
        
        $criteria = array();
        $orderBy = array('id' => 'desc');
        $limit = 10;
        $offset = 0;
        
        if ($this->view->form->isValid($this->getRequest()->getParams())) {
            $resourceType = $this->getRequest()->getParam('resource_type', '');
            $actionType = $this->getRequest()->getParam('action_type', '');
            
            $resourceTypes = $this->auditService->getResourceTypes();
            $actionTypes = $this->auditService->getActionTypes();
            
            if ($resourceType != '') {
                $criteria['resource_type'] = $resourceTypes[$resourceType];
            }
            if ($actionType != '') {
                $criteria['action'] = $actionTypes[$actionType];
            }
            $offset = $this->getRequest()->getParam('offset', 0);
        }
        
        $count = $this->auditService->countBy($criteria);
        $this->view->events = $this->auditService->findBy($criteria, $orderBy, $limit, $offset);
        
        $this->view->pager = new SimplePager($count, $limit, 'offset', '?');
    }

    /**
     * Get priority form
     *
     * @return \Zend_Form
     */
    private function getForm()
    {
        $form = new Zend_Form;

        $translator = \Zend_Registry::get('container')->getService('translator');
        $resourceTypes = $this->auditService->getResourceTypes();
        $actionTypes = $this->auditService->getActionTypes();
        
        $form->addElement('select', 'resource_type', array(
            'multioptions' => array('' => $translator->trans('All')) + $resourceTypes,
            'label' => $translator->trans('Resource Type:', array(), 'logs'),
            'onChange' => 'this.form.submit();',
        ));
        
        $form->addElement('select', 'action_type', array(
            'multioptions' => array('' => $translator->trans('All')) + $actionTypes,
            'label' => $translator->trans('Action Type:', array(), 'logs'),
            'onChange' => 'this.form.submit();',
        ));
        
        return $form;
    }
}
