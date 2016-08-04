<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition,
    Newscoop\Package\PackageService;

/**
 * @Acl(resource="subscription", action="manage")
 */
class Admin_SubscriptionIpRestController extends Zend_Rest_Controller
{
    public function headAction()
    {
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
    }

    public function putAction()
    {
    }

    public function postAction()
    {
        try {
            $ip = $this->_helper->service('subscription.ip')->save($this->getValues());
            $this->_helper->json($this->view->subscriptionIpJson($ip));
        } catch (\PDOException $e) {
            $this->getResponse()->setHttpResponseCode(400);
            $this->_helper->json(array());
        }
    }

    public function deleteAction()
    {
        list($user, $ip) = explode(':', $this->_getParam('id'));
        $this->_helper->service('subscription.ip')->delete(array(
            'user' => $user,
            'ip' => $ip,
        ));
        $this->_helper->json(array());
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
