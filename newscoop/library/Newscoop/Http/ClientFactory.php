<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Http;

use Guzzle\Http\Plugin\CurlAuthPlugin;

/**
 */
class ClientFactory
{
    /**
     * @var array
     */
    private $options = array(
        'curl.options' => array(
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 30,
        ),
    );

    /**
     * Create Client
     *
     * @param string $url
     * @return Newscoop\Http\Client
     */
    public function createClient($url = null)
    {
        return $this->getClient($url);
    }

    /**
     * Get client
     *
     * @return Newscoop\Http\Client
     */
    public function getClient($url = null)
    {
        return new Client($url, $this->options);
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
