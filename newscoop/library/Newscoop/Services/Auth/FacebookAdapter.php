<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Auth;

use Doctrine\ORM\EntityManager;
use Newscoop\Http\ClientFactory;

/**
 */
class FacebookAdapter
{
    const GRAPH_URI = 'https://graph.facebook.com/me?access_token={token}';
    const PROVIDER = 'Facebook';

    /**
     * @var Newscoop\Http\ClientFactory
     */
    private $clientFactory;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param Newscoop\Http\ClientFactory $clientFactory
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(ClientFactory $clientFactory, EntityManager $em)
    {
        $this->clientFactory = $clientFactory;
        $this->em = $em;
    }

    /**
     * Find user by given auth token
     *
     * @param string $token
     * @return Newscoop\Entity\User
     */
    public function findByAuthToken($token)
    {
        $identity = $this->em->getRepository('Newscoop\Entity\UserIdentity')
            ->findOneBy(array(
                'provider' => self::PROVIDER,
                'provider_user_id' => $this->getFacebookUserId($token),
            ));

        $identity->getUser()->setLastLogin(new \DateTime());
        $this->em->flush();

        return $identity ? $identity->getUser() : null;
    }

    /**
     * Fetch user id via graph info for given token
     *
     * @param string $token
     * @return string
     */
    private function getFacebookUserId($token)
    {
        $client = $this->clientFactory->getClient();

        try {
            $response = $client->get(array(self::GRAPH_URI, array(
                'token' => $token,
            )))->send();

            $data = json_decode($response->getBody(true));
            return !empty($data->id) ? $data->id : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
