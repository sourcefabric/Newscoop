<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

/**
 * Publication listener for behat testing
 */
class PublicationListener implements ListenerInterface
{
    /**
     * Array with parameters appended to all requests
     * @var array
     */
    private $params;

    /**
     * Construct Listener
     * @param array $params Array with parameters appended to all requests
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * PreSend event
     * @param RequestInterface $request Request object
     */
    public function preSend(RequestInterface $request)
    {
        $url = $request->getUrl();
        $pos = strpos($url, '?');

        if ($pos !== false) {
            $url .= '&'.utf8_encode(http_build_query($this->params, '', '&'));
        } else {
            $url .= '?'.utf8_encode(http_build_query($this->params, '', '&'));
        }

        $request->fromUrl(new Url($url));
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}
