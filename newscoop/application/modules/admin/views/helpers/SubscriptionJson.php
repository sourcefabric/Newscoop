<?php

/**
 * Subscription Json
 */
class Admin_View_Helper_SubscriptionJson extends Zend_View_Helper_Abstract
{
    /**
     * Get json representation of subscription
     *
     * @param Newscoop\Entity\Subscription $subscription
     * @return array
     */
    public function SubscriptionJson(\Newscoop\Subscription\Subscription $subscription)
    {
        return array(
            'id' => $subscription->getId(),
            'publication' => array(
                'id' => $subscription->getPublicationId(),
                'name' => $subscription->getPublicationName(),
            ),
            'link' => array(
                'rel' => 'edit',
                'href' => $this->view->url(array(
                    'module' => 'admin',
                    'controller' => 'subscription',
                    'action' => 'edit',
                    'subscription' => $subscription->getId(),
                    'user' => $subscription->getUser()->getId(),
                ), 'default'),
            ),
            'toPay' => $subscription->getToPay(),
            'type' => $subscription->getType(),
            'active' => $subscription->isActive(),
        );
    }
}
