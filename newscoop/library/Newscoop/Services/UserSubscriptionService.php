<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\UserSubscription;

/**
 * User service
 */
class UserSubscriptionService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     *
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function createKey($user)
    {
        $key = md5($user->getId().$user->getEmail().time());
        return($key);
    }
    
    public function setKey($user, $key)
    {
        // send request
        //$subscriber = $user->getSubscriber();
        
        $subscriber = $this->fetchSubscriber($user);
        
        $url = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA/' . $subscriber . '?userkey=' . $key;        
        $client = new \Zend_Http_Client();
        $client->setUri($url);
        $client->setMethod(\Zend_Http_Client::PUT);
        $response = $client->request();
    }
    
    public function fetchSubscriber($user)
    {
        try {
            $url = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA?email='.urlencode($user->getEmail());
            $client = new \Zend_Http_Client();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::GET);
            $response = $client->request();
        }
        catch (\Exception $e) {
            return(false);
        }
        
        $xml = new \SimpleXMLElement($response->getBody()); 
        
        $subscriber = $xml->subscriber[0] ? (int) $xml->subscriber[0]->subscriberId : false;
        if (is_numeric($subscriber)) {
            if (!$user->getSubscriber()) {
                $user->setSubscriber($subscriber);
                $this->em->persist($user);
                $this->em->flush();
            }   
            return($subscriber);
        }
        else {
            return(false);
        }
    }
    
    public function fetchSubscriptions($user)
    {
        $subscriber = $user->getSubscriber();
        
        $url = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA/' . $subscriber;
        $client = new \Zend_Http_Client();
        $client->setUri($url);
        $client->setMethod(\Zend_Http_Client::GET);
        $response = $client->request();
        
        $xml = new \SimpleXMLElement($response->getBody());
        $subscriptions = $xml->subscriber ? $xml->subscriber->subscriptions->subscription : false;
        
        return($subscriptions);
    }

    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\UserSubscription');
    }
}
