<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * TODO:
     * * demo login
     * 
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $em = $this->container->get('em');

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $languages = $em->getRepository('Newscoop\Entity\Language')->getLanguages();

        \LoginAttempts::DeleteOldLoginAttempts();

        return array(
            'last_username'     => $request->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'             => $error,
            'languages'         => $languages,
            'defaultLanguage'   => $this->getDefaultLanguage($request, $languages),
            'maxLoginAttemptsExceeded' => \LoginAttempts::MaxLoginAttemptsExceeded()
        );
    }

    private function getDefaultLanguage($request, $languages)
    {
        $defaultLanguage = 'en';

        if ($request->request->has('TOL_Language')) {
            $defaultLanguage = $request->request->get('TOL_Language');
        } elseif ($request->cookies->has('TOL_Language')) {
            $defaultLanguage = $request->cookies->get('TOL_Language');
        } else {
            // Get the browser languages
            $browserLanguageStr = $request->server->get('HTTP_ACCEPT_LANGUAGE', '');
            $browserLanguageArray = preg_split("/[,;]/", $browserLanguageStr);
            $browserLanguagePrefs = array();
            foreach ($browserLanguageArray as $tmpLang) {
                if (!(substr($tmpLang, 0, 2) == 'q=')) {
                    $browserLanguagePrefs[] = $tmpLang;
                }
            }
            // Try to match preference exactly.
            foreach ($browserLanguagePrefs as $pref) {
                if (array_key_exists($pref, $languages)) {
                    $defaultLanguage = $pref;
                    break;
                }
            }
            // Try to match two-letter language code.
            if (is_null($defaultLanguage)) {
                foreach ($browserLanguagePrefs as $pref) {
                    if (substr($pref, 0, 2) != "" && array_key_exists(substr($pref, 0, 2), $languages)) {
                        $defaultLanguage = $pref;
                        break;
                    }
                }
            }

            // HACK: the function regGS() strips off the ":en" from
            // english language strings, but only if it knows that
            // the language being displayed is english...and it knows
            // via the cookie.
            $request->request->set('TOL_Language', $defaultLanguage);
            $request->cookies->set('TOL_Language', $defaultLanguage);
        }

        return $defaultLanguage;
    }
}
