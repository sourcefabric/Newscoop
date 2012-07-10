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
    const API_URL = 'https://abo.tageswoche.ch/dmpro/ws/subscriber/NMBA';
    const CUSTOMER_API_URL = 'https://abo.tageswoche.ch/dmpro/ws/NMBA/subscriber/NMBA';

    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
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
        
        $urlList = array(
            self::API_URL . '/0000001?userkey=0000001',
            self::API_URL . '?email=test@test.com&firstname=test&lastname=test',
            self::API_URL . '/0000001',
        );

        foreach ($urlList as $url) {
            $client = new \Zend_Http_Client($url);
            $response = $client->request();
            if (!$response->isSuccessful()) {
                throw new \Exception('Connection failed.');
            }
        }
        
        restore_error_handler();
    }

    /**
     * Generate user key
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    public function createKey(User $user)
    {
        $key = md5($user->getId().$user->getEmail().time());
        return($key);
    }

    /**
     * Set user key
     *
     * @param Newscoop\Entity\User $user
     * @param string $key
     * @return bool
     */
    public function setKey(User $user, $key)
    {
        $subscriber = $this->fetchSubscriber($user);
        if (!$subscriber) {
            return false;
        }

        try {
            $client = new \Zend_Http_Client(self::API_URL . '/' . $subscriber);
            $client->setParameterGet(array('userkey' => $key));
            $client->setMethod(\Zend_Http_Client::PUT);
            $response = $client->request();
            return $response->isSuccessful();
        } catch (\Zend_Exception $e) {
            return false;
        }
    }

    /**
     * Fetch subscriber info
     *
     * @param Newscoop\Entity\User $user
     * @return string
     */
    public function fetchSubscriber(User $user)
    {
        try {
            $xml = $this->fetchXml(self::API_URL, array(
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
            ));

            if (!$xml) {
                return false;
            }
        } catch (\Zend_Exception $e) {
            return false;
        }

        $subscriber = $xml->subscriber[0] ? (int) $xml->subscriber[0]->subscriberId : false;
        if (is_numeric($subscriber)) {
            $user->setSubscriber($subscriber);
            $this->em->persist($user);
            $this->em->flush();
            return $subscriber;
        }

        return false;
    }

    /**
     * Fetch user subscriptions
     *
     * @param Newscoop\Entity\User $user
     * @return array
     */
    public function fetchSubscriptions(User $user)
    {
        try {
            $subscriber = $user->getSubscriber();
            $xml = $this->fetchXml(self::API_URL . '/' . $subscriber);
            if (!$xml) {
                return false;
            }

            return $xml->subscriber ? $xml->subscriber->subscriptions->subscription : false;
        } catch (\Zend_Exception $e) {
            return false;
        }
    }

    /**
     * Fetch xml response for given url
     *
     * @param string $url
     * @param array $params
     * @return SimpleXMLElement
     */
    private function fetchXml($url, array $params = array())
    {
        $client = new \Zend_Http_Client($url);
        $client->setParameterGet($params);

        $response = $client->request();
        if (!$response->isSuccessful()) {
            return null;
        }

        $xml = simplexml_load_string($response->getBody());
        return $xml ?: null;
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

    /**
     * Get current subscription valid until date
     *
     * @param Newscoop\Entity\User $user
     * @return DateTime
     */
    public function getValidUntilDate(User $user)
    {
        $subscription = $this->fetchSubscriptions($user);

        if (empty($subscription) || $subscription->statusCode !== self::STATUS_ACTIVE) {
            return null;
        }

        return date_create($subscription->validUntilFormated);
    }

    /**
     * Test if given id is valid customer
     *
     * @param string $id
     * @return bool
     */
    public function isValidCustomer($id)
    {
        $xml = $this->fetchXml(self::CUSTOMER_API_URL . '/' . $id);
        if (!$xml) {
            return false;
        }

        foreach ($xml->subscriber as $subscriber) {
            foreach ((array) $subscriber->subscriptions as $subscription) {
                if ((string) $subscription->statusCode === (string) self::STATUS_ACTIVE) {
                    return true;
                }
            }
        }

        return false;
    }
}
