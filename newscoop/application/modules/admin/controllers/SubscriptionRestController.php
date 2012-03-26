<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Image\Rendition,
    Newscoop\Package\PackageService;

/**
 * @Acl(ignore=True)
 */
class Admin_SubscriptionRestController extends Zend_Rest_Controller
{
    public function indexAction()
    {
    }

    public function getAction()
    {
        $subscription = $this->_helper->service('subscription')->find($this->_getParam('id'));
        $data = $this->view->subscriptionJson($subscription);

        $data['sections'] = array();
        foreach ($subscription->getSections() as $section) {
            $data['sections'][] = array(
                'id' => $section->getId(),
                'language' => $section->hasLanguage() ? $section->getLangaugeName() : null,
                'start' => $section->getStartDate()->format('Y-m-d'),
                'days' => $section->getDays(),
                'paid_days' => $section->getPaidDays(),
                'name' => $this->_helper->service('content.section')->getName($section->getSectionNumber(), $subscription->getPublication(), $section->getLanguage()),
            );
        }

        $this->_helper->json($data);

        var_dump('tic'); exit;
    }

    public function postAction()
    {
        $subscription = $this->_helper->service('subscription')->save($this->getValues());
        $this->_helper->json($this->view->subscriptionJson($subscription));
    }

    public function putAction()
    {
        $subscription = $this->_helper->service('subscription')->find($this->_getParam('id'));
        $this->_helper->service('subscription')->save($this->getValues(), $subscription);
        $this->_helper->json($this->view->subscriptionJson($subscription));
    }

    public function deleteAction()
    {
        $this->_helper->service('subscription')->delete($this->_getParam('id'));
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

        if (is_array($values['publication'])) {
            $values['publication'] = $values['publication']['id'];
        }

        return $values;
    }
}
