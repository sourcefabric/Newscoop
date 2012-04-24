<?php

/**
 * Subscription IP Json
 */
class Admin_View_Helper_SubscriptionIpJson extends Zend_View_Helper_Abstract
{
    /**
     * Get json representation of subscription ip
     *
     * @param Newscoop\Entity\User\Ip $ip
     * @return array
     */
    public function SubscriptionIpJson(\Newscoop\Entity\User\Ip $ip)
    {
        return array(
            'ip' => $ip->getIp(),
            'number' => $ip->getNumber(),
            'id' => implode(':', array(
                $ip->getUserId(),
                $ip->getIp(),
            )),
        );
    }
}
