<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/AbstractController.php';

/**
 * Purchage API
 */
class Api_PurchaseController extends AbstractController
{
    /**
     * Get list of products
     */
    public function productsAction()
    {
        $expires = new DateTime('+300 day');
        $lastMod = new DateTime(sprintf('@%d', filemtime(__FILE__)));
        $this->getResponse()->setHeader('Expires', $expires->format(DateTime::RFC1123), true);
        $this->getResponse()->setHeader('Last-Modified', $lastMod->format(DateTime::RFC1123), true);
        $this->getResponse()->setHeader('Cache-Control', 'public', true);

        $this->_helper->json(array(
            'single_issue' => 'ch.tageswoche.issue.2012.test',
            'digital_upgrade' => 'ch.tageswoche.subscription.digital_upgrade',
            'digital_subscriptions' => array(
                'ch.tageswoche.subscription.month',
                'ch.tageswoche.subscription.quarter',
                'ch.tageswoche.subscription.bi_annual',
                'ch.tageswoche.subscription.annual',
            ),
        ));
    }

    /**
     * Validate purchase
     */
    public function validateAction()
    {
        $this->assertIsSecure();

        $receipt = $this->_getParam('receipt_data');
        if (empty($receipt)) {
            $this->sendError("No 'receipt_data' provided");
        }

        $data = $this->_helper->service('mobile.purchase')->validate($receipt);

        if ($this->_getParam('username')) {
            $user = $this->getUser();
            if (!$this->_helper->service('user_subscription')->isActiveSubscriber($user)) {
                $this->sendError('Given user is not a susbscriber');
            }

            if ($data['receipt_valid']) {
                $user->addAttribute(self::DIGITAL_UPGRADE, time());
                $this->_helper->service('em')->flush($user);
            }
        }

        $this->_helper->json($data);
    }

    /**
     * Check free upgrade status
     */
    public function freeupgradeAction()
    {
        $this->assertIsSecure();

        $user = $this->getUser();

        if (!$this->_getParam('customer_id')
            || !$this->_helper->service('user_subscription')->isValidCustomer($this->_getParam('customer_id'))) {
            $this->sendStatus('invalid_customer_id');
        }

        if ($this->_getParam('customer_id') !== $user->getAttribute('customer_id')) {
            $this->_helper->service('user')->updateProfile(array(
                'customer_id' => $this->_getParam('customer_id'),
            ), $user);
        }

        if (!$this->_getParam('device_id')) {
            $this->sendError("No 'device_id' provided");
        }

        $issue = $this->_helper->service('mobile.issue')->getCurrentIssueId();
        if (empty($issue)) {
            $this->sendError('No issue available for upgrading', 500);
        }

        if ($this->_helper->service('mobile.free_upgrade')->addDevice($this->_getParam('device_id'), $user, $issue)) {
            $this->sendStatus('ok');
        }

        $this->sendStatus('max_devices_reached');
    }

    /**
     * Send status response
     *
     * @param string $status
     * @return void
     */
    private function sendStatus($status)
    {
        $this->_helper->json(array(
            'status' => (string) $status,
        ));
    }
}
