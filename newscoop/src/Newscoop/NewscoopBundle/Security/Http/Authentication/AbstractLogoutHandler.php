<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace  Newscoop\NewscoopBundle\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

/**
 * Abstract logout success handler
 */
abstract class AbstractLogoutHandler extends DefaultLogoutSuccessHandler
{
    /**
     * Unsets NO_CACHE cookie. Sets its to "NO".
     *
     * @param Request $request Request
     */
    public function unsetNoCacheCookie(Request $request)
    {
        setcookie('NO_CACHE', 'NO', null, '/', '.'.$this->extractDomain($request->getHost()));
    }

    private function extractDomain($domain)
    {
        if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)) {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }
}
