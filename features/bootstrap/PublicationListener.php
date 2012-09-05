<?php

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

class PublicationListener implements ListenerInterface
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function preSend(RequestInterface $request)
    {
        $url = $request->getUrl();
        $pos = strpos($url, '?');

        if ('GET' === $request->getMethod()) {
            if ($pos !== false) {
                $url .= '&'.utf8_encode(http_build_query($this->params, '', '&'));
            } else {
                $url .= '?'.utf8_encode(http_build_query($this->params, '', '&'));
            }

            $request->fromUrl(new Url($url));
        }
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}