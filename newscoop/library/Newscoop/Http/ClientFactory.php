<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Http;

use Guzzle\Http\Client,
    Guzzle\Http\Plugin\CurlAuthPlugin;

/**
 */
class ClientFactory
{
    /**
     * Get client
     *
     * @return Guzzle\Http\Client
     */
    public function getClient()
    {
        return new Client();
    }

    /**
     * Get auth client
     *
     * @param string $username
     * @param string $password
     * @return Guzzle\Http\Client
     */
    public function getAuthClient($username, $password)
    {
        $client = $this->getClient();
        $client->addSubscriber(new CurlAuthPlugin($username, $password));
        return $client;
    }
}
