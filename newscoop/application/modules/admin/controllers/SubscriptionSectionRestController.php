<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore=True)
 */
class Admin_SubscriptionSectionRestController extends Zend_Rest_Controller
{
    public function headAction()
    {
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
        $subscription = $this->_helper->service('subscription')->find($this->_getParam('id'));
        $this->_helper->json($this->view->subscriptionJson($subscription));
    }

    public function postAction()
    {
        $section = $this->_helper->service('subscription.section')->save($this->getValues());
        $this->_helper->json($this->view->subscriptionSectionJson($section));
    }

    public function putAction()
    {
        $section = $this->_helper->service('subscription.section')->find($this->_getParam('id'));
        $this->_helper->service('subscription.section')->save($this->getValues(), $section);
        $this->_helper->json($this->view->subscriptionSectionJson($section));
    }

    public function deleteAction()
    {
        $this->_helper->service('subscription.section')->delete($this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(204);
        $this->_helper->json('');
    }

    /**
     * Get values
     *
     * @return array
     */
    private function getValues()
    {
        $values = json_decode($this->getRequest()->getRawBody(), true);
        return $values;
    }
}
