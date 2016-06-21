<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Subscription.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaSubscriptions extends MetaDbObject {

    public function __construct($publicationId, $userId)
    {
        $this->m_dbObject = Subscription::GetSubscriptions($publicationId, $userId);
        if (count($this->m_dbObject) == 0) {
            $this->m_dbObject = array();
        }
    }

    public function has_section($sectionNumber)
    {
        foreach ($this->m_dbObject as $subscription) {
            $subscription = new MetaSubscription($subscription->getSubscriptionId());
            if ($subscription->has_section($sectionNumber) && $subscription->is_active) {
                return $subscription;
            }
        }

        return false;
    }

    public function has_article($articleNumber)
    {
        foreach ($this->m_dbObject as $subscription) {
            $subscription = new MetaSubscription($subscription->getSubscriptionId());
            if ($subscription->has_article($articleNumber) && $subscription->is_valid) {
                return $subscription;
            }
        }

        return false;
    }

    public function has_issue($issueNumber)
    {
        foreach ($this->m_dbObject as $subscription) {
            $subscription = new MetaSubscription($subscription->getSubscriptionId());
            if ($subscription->has_issue($issueNumber) && $subscription->is_valid) {
                return $subscription;
            }
        }

        return false;
    }

    public function has_publication($publicationId)
    {
        foreach ($this->m_dbObject as $subscription) {
            $subscription = new MetaSubscription($subscription->getSubscriptionId());
            if ($subscription->publication->identifier == $publicationId && $subscription->is_valid) {
                return $subscription;
            }
        }

        return false;
    }
}
