<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
abstract class AbstractController extends Zend_Controller_Action
{
    const DIGITAL_UPGRADE = '_digital_upgrade';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const SECRET_PARAM = '_secret';

    /**
     * Get user by username and password params
     *
     * @return Newscoop\Entity\User
     */
    protected function getUser()
    {
        $username = $this->_getParam('username');
        $password = $this->_getParam('password');
        if (empty($username) || empty($password)) {
            $this->getResponse()->getHttpResponseCode(401);
            $this->_helper->json(array('status' => 'invalid_credentials'));
        }

        $user = $this->_helper->service('auth.adapter')->findByCredentials($username, $password);
        if (empty($user)) {
            $this->getResponse()->getHttpResponseCode(401);
            $this->_helper->json(array('status' => 'invalid_credentials'));
        }

        return $user;
    }

    /**
     * Send error and exit
     *
     * @param string $body
     * @param int $code
     * @return void
     */
    protected function sendError($body = '', $code = 400)
    {
        $this->getResponse()->setHttpResponseCode($code);
        $this->_helper->json(array(
            'code' => $code,
            'message' => $body,
        ));
    }

    /**
     * Assert request is secure
     *
     * @return void
     */
    protected function assertIsSecure()
    {
        if (APPLICATION_ENV === 'development') {
            return;
        }

        if (!$this->getRequest()->isSecure()) {
            $this->sendError('Secure connection required.');
        }
    }

    /**
     * Assert request is post
     *
     * @return void
     */
    protected function assertIsPost()
    {
        if (!$this->getRequest()->isPost()) {
            $this->sendError('POST required.');
        }
    }

    /**
     * Get client and version params
     *
     * @param bool $onlyParams
     * @return string
     */
    protected function getClientVersionParams($onlyParams = true)
    {
        return sprintf('%sclient=%s&version=%s', $onlyParams ? '?' : '&', $this->_getParam('client', 'ipad'), $this->_getParam('version', '1.0'));
    }

    /**
     * Assert that user is subscriber and can consume premium content
     *
     * @return void
     */
    protected function assertIsSubscriber()
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('offline');
        if (isset($options['secret']) && $this->_getParam(self::SECRET_PARAM) === $options['secret']) {
            return;
        }

        if ($this->_getParam('receipt_data') && $this->_getParam('device_id')) {
            if ($this->_helper->service('mobile.purchase')->isValid($this->_getParam('receipt_data'))) {
                return;
            }
        }

        if ($this->_getParam('username') && $this->_getParam('password')) {
            $user = $this->getUser();
            if ($this->_helper->service('mobile.free_upgrade')->hasIssue($user, $this->_helper->service('mobile.issue')->getCurrentIssueId())) {
                return;
            }

            if ($this->_helper->service('user_subscription')->isActiveSubscriber($user)) {
                return;
            }
        }

        $this->sendError('Unauthorized.', 401);
    }
}
