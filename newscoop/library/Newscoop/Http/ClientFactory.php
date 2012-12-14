<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Http;

/**
 * Http Client Factory
 */
class ClientFactory
{
    /**
     * Create client
     *
     * @param string $url
     * @return Newscoop\Http\Client
     */
    public function createClient($url = null)
    {
        return new Client($url);
    }
}
