<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User;

/**
 * User service
 */
class UserSubscriptionService
{
    const STATUS_ACTIVE = 1;

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
    
    public function testConnection()
    {
        set_error_handler(
            function($number, $message, $file, $line) {
                throw new \Exception('Connection failed.');
            }
        );
        
        $urlList = array();
        $urlList[] = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA/0000001?userkey=0000001';
        $urlList[] = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA?email=test@test.com&firstname=test&lastname=test';
        $urlList[] = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA/0000001';
        
        foreach ($urlList as $url) {
            $client = new \Zend_Http_Client();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::GET);
            $response = $client->request();
            if (!$response->isSuccessful()) {
                throw new \Exception('Connection failed.');
            }
        }
        
        restore_error_handler();
    }
    
    public function createKey($user)
    {
        $key = md5($user->getId().$user->getEmail().time());
        return($key);
    }
    
    public function setKey($user, $key)
    {
        $subscriber = $this->fetchSubscriber($user);
        
        if ($subscriber != false) {
            try {
                $url = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA/' . $subscriber . '?userkey=' . $key;        
                $client = new \Zend_Http_Client();
                $client->setUri($url);
                $client->setMethod(\Zend_Http_Client::PUT);
                $response = $client->request();
                
                return(true);
            }
            catch (\Zend_Exception $e) {
                return(false);
            }
        }
        else {
            return(false);
        }
    }
    
    public function fetchSubscriber($user)
    {
        try {
            $url = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA?email='.urlencode($user->getEmail()).'&firstname='.urlencode($user->getFirstName()).'&lastname='.urlencode($user->getLastName());
            $client = new \Zend_Http_Client();
            $client->setUri($url);
            $client->setMethod(\Zend_Http_Client::GET);
            $response = $client->request();
        }
        catch (\Zend_Exception $e) {
            return(false);
        }
        
        try {
            $xml = new \SimpleXMLElement($response->getBody());
        }
        catch (\Exception $e) {
            return(false);
        }
        
        $subscriber = $xml->subscriber[0] ? (int) $xml->subscriber[0]->subscriberId : false;
        if (is_numeric($subscriber)) {
            $user->setSubscriber($subscriber);
            $this->em->persist($user);
            $this->em->flush();
            return($subscriber);
        }
        else {
            return(false);
        }
    }
    
    public function fetchSubscriptions($user)
    {
        try {
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
        catch (\Zend_Exception $e) {
            return(false);
        }
    }

    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\UserSubscription');
    }

    /**
     * Get active subscription
     *
     * @param Newscoop\Entity\User $user
     * @return array
     */
    public function getActiveSubscription(User $user)
    {
        return null;
    }

    /**
     * Test if user is active subscriber
     *
     * @param Newscoop\Entity\User $user
     * @return bool
     */
    public function isActiveSubscriber(User $user)
    {
        $subscription = $this->fetchSubscriptions($user);
        return !empty($subscription) ? (int) $subscription->statusCode === self::STATUS_ACTIVE : false;
    }
}
